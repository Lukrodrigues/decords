<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "conexao.php";

// 🔒 Controle de expiração de sessão (30 minutos = 1800s)
$tempoMaximo = 1800;

// Evita cache
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

if (isset($_SESSION['ultimo_acesso'])) {
	if ((time() - $_SESSION['ultimo_acesso']) > $tempoMaximo) {
		session_unset();
		session_destroy();
		header("Location: login.php?msg=timeout");
		exit();
	}
}
$_SESSION['ultimo_acesso'] = time();

// Verifica login
if (!isset($_SESSION['aluno_id']) || empty($_SESSION['aluno_id'])) {
	header('Location: login.php');
	exit;
}

$alunoId = (int)$_SESSION['aluno_id'];

// Consulta nível do aluno
$stmt = $conn->prepare("SELECT nivel, nome FROM alunos WHERE id = ?");
$stmt->bind_param('i', $alunoId);
$stmt->execute();
$result = $stmt->get_result();
if (!$aluno = $result->fetch_assoc()) {
	// Usuário não existe, força logout
	session_unset();
	session_destroy();
	header("Location: login.php");
	exit;
}
$stmt->close();

$nivelAluno = (int)$aluno['nivel'];
$nomeAluno  = $aluno['nome'] ?? 'Visitante';

// Menu simples
$menuItens = [
	1 => ['nome' => 'Iniciantes', 'link' => 'iniciantes.php'],
	2 => ['nome' => 'Intermediários', 'link' => 'intermediarios.php'],
	3 => ['nome' => 'Avançados', 'link' => 'avancados.php'],
];

// Função status menu
function getMenuStatus(array $menuItens, int $nivelAluno): array
{
	$status = [];
	foreach (array_keys($menuItens) as $nivel) {
		if ($nivel < $nivelAluno) $status[$nivel] = 'concluido';
		elseif ($nivel == $nivelAluno) $status[$nivel] = 'andamento';
		else $status[$nivel] = 'bloqueado';
	}
	return $status;
}
$menuStatus = getMenuStatus($menuItens, $nivelAluno);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="UTF-8">
	<title>Tutorial - Decords Música e Teoria</title>
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>

	<style>
		.menu-concluido {
			color: green !important;
		}

		.menu-em-andamento {
			color: orange !important;
			font-weight: bold;
		}

		.menu-bloqueado {
			color: #ccc !important;
			cursor: not-allowed;
		}

		.menu-bloqueado a {
			pointer-events: none;
		}

		/* Remove opacidade e melhora contraste */
		.navbar-inverse .dropdown-menu li a,
		.navbar-inverse .dropdown-menu li span {
			text-shadow: 0 0 2px rgba(0, 0, 0, 0.4);
		}

		.tutorial-header {
			background: linear-gradient(135deg, #2980b9, #6dd5fa);
			color: #fff;
			padding: 40px 20px;
			border-radius: 0 0 25px 25px;
			text-align: center;
			font-family: "Segoe UI", Arial, sans-serif;
			box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
			margin-bottom: 30px;
		}

		.tutorial-title {
			font-size: 2.2rem;
			margin-bottom: 25px;
		}

		.tutorial-status {
			font-size: 1.2rem;
			max-width: 700px;
			margin: 0 auto;
		}

		.tutorial-status p {
			margin: 10px 0;
		}

		.label {
			font-weight: bold;
			color: #ffeaa7;
		}

		strong {
			color: #fff;
		}

		/* Barra de progresso */
		.progress-bar {
			margin-top: 20px;
			height: 20px;
			background: rgba(255, 255, 255, 0.3);
			border-radius: 10px;
			overflow: hidden;
		}

		.progress-fill {
			height: 100%;
			background: #27ae60;
			transition: width 0.6s ease;
		}

		body {
			margin-top: 60px;
			/* Espaço para navbar fixa */
			background-color: #f5f5f5;
			font-family: "Segoe UI", Arial, sans-serif;
			scroll-behavior: smooth;
			/* rolagem suave nativa */
		}

		/* ===== NAVBAR SUPERIOR ===== */
		.navbar {
			height: 50px;
			border-radius: 0;
			font-size: 15px;
			z-index: 1000;
		}

		.navbar a {
			color: #fff !important;
		}

		/* ===== MENU LATERAL ===== */
		.sidebar {
			position: fixed;
			top: 10px;
			/* abaixo da navbar */
			left: 0;
			width: 250px;
			height: calc(100% - 50px);
			background-color: #222;
			color: white;
			padding-top: 20px;
			overflow-y: auto;
			border-right: 2px solid #333;
			z-index: 999;
		}

		.sidebar h4 {
			color: #ddd;
			text-align: center;
			margin-bottom: 15px;
		}

		.sidebar ul {
			list-style: none;
			padding-left: 0;
		}

		.sidebar li {
			padding: 10px 20px;
			transition: background 0.2s;
		}

		.sidebar li a {
			color: #ccc;
			display: block;
			text-decoration: none;
		}

		.sidebar li a:hover {
			background-color: #444;
			color: #fff;
			border-left: 4px solid #0af;
			padding-left: 16px;
		}

		/* ===== ÁREA PRINCIPAL ===== */
		.main-content {
			margin-left: 270px;
			/* espaço da sidebar */
			padding: 20px;
			margin-top: 70px;
		}

		.tutorial-header {
			background-color: #fff;
			padding: 20px;
			margin-bottom: 20px;
			border-radius: 8px;
			box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
		}

		.tutorial-title {
			margin-top: 0;
			color: #333;
		}

		.tutorial-status .label {
			font-weight: bold;
			color: #555;
		}

		/* ===== IMAGENS ===== */
		img.tutorial-img {
			max-width: 100%;
			height: auto;
			border-radius: 6px;
			box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
			margin: 20px 0;
		}

		/* ===== TÍTULOS DE MÓDULOS ===== */
		section h2 {
			color: #0b4f8a;
			border-left: 6px solid #0af;
			padding-left: 10px;
			margin-top: 30px;
		}

		section {
			scroll-margin-top: 80px;
			/* distância da navbar */
		}
	</style>
</head>

<body>

	<!-- ===== MENU SUPERIOR ===== -->
	<nav class="navbar navbar-inverse navbar-fixed-top">
		<div class="container-fluid">
			<div class="navbar-header">
				<a class="navbar-brand" href="#">🎸 Tutorial de Violão</a>
			</div>
			<ul class="nav navbar-nav">
				<li><a href="tutorial-01.php">Tutorial 01</a></li>
				<li><a href="tutorial_02.php">Tutorial 02</a></li>

				<li class="dropdown">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#">Exercícios <b class="caret"></b></a>
					<ul class="dropdown-menu" id="menuExercicios">
						<?php foreach ($menuItens as $nivel => $dados):
							$status = $menuStatus[$nivel] ?? 'bloqueado';
							$classe = ($status === 'concluido') ? 'menu-concluido' : (($status === 'andamento') ? 'menu-em-andamento' : 'menu-bloqueado');
							$icone  = ($status === 'concluido') ? ' ✅' : (($status === 'andamento') ? ' ⏳' : ' 🔒');
						?>
							<li class="<?= $classe ?>">
								<?php if ($status === 'bloqueado'): ?>
									<span><?= htmlspecialchars($dados['nome'] . $icone) ?></span>
								<?php else: ?>
									<a href="<?= htmlspecialchars($dados['link']) ?>"><?= htmlspecialchars($dados['nome'] . $icone) ?></a>
								<?php endif; ?>
							</li>
							<li class="divider"></li>
						<?php endforeach; ?>
					</ul>
				</li>

				<li><a href="logout.php">Sair</a></li>
			</ul>
		</div>
	</nav>
	<script>
		$(document).ready(function() {
			// Atualiza menu quando volta da aba
			document.addEventListener('visibilitychange', function() {
				if (!document.hidden) location.reload();
			});
			window.addEventListener('focus', function() {
				location.reload();
			});
		});
	</script>
</body>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Tutorial 01 - Violão</title>

	<style>
		body {
			margin: 0;
			font-family: Arial, sans-serif;
			background-color: #f5f5f5;
		}

		/* ===== MENU LATERAL FIXO ===== */
		.sidebar {
			position: fixed;
			top: 0;
			left: 0;
			width: 250px;
			height: 100%;
			background-color: #222;
			color: white;
			overflow-y: auto;
			padding: 20px;
			box-sizing: border-box;
		}

		.sidebar h2 {
			text-align: center;
			font-size: 20px;
			margin-bottom: 15px;
			border-bottom: 1px solid #555;
			padding-bottom: 10px;
		}

		.sidebar a {
			display: block;
			color: #ddd;
			text-decoration: none;
			padding: 8px 12px;
			border-radius: 4px;
			margin: 4px 0;
			transition: background 0.3s;
		}

		.sidebar a:hover {
			background-color: #444;
		}

		/* ===== CONTEÚDO PRINCIPAL ===== */
		.main-content {
			margin-left: 270px;
			/* Espaço para o menu lateral */
			padding: 20px 40px;
			box-sizing: border-box;
		}

		.tutorial-header {
			background-color: #fff;
			border-radius: 8px;
			padding: 20px;
			margin-bottom: 30px;
			box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
		}

		.tutorial-title {
			margin: 0 0 10px;
			font-size: 26px;
			color: #333;
		}

		.tutorial-status {
			display: flex;
			justify-content: space-between;
			font-size: 15px;
		}

		.tutorial-status .label {
			font-weight: bold;
			color: #555;
		}

		.card {
			background-color: #fff;
			border-radius: 8px;
			padding: 20px;
			margin-bottom: 30px;
			box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
		}

		.card img {
			max-width: 100%;
			height: auto;
			display: block;
			margin: 10px auto;
			border-radius: 6px;
		}

		.card h2,
		.card h4 {
			color: #222;
		}

		@media (max-width: 768px) {
			.sidebar {
				position: relative;
				width: 100%;
				height: auto;
			}

			.main-content {
				margin-left: 0;
				padding: 15px;
			}
		}
	</style>
</head>

<body>

	<!-- ===== MENU LATERAL ===== -->
	<nav class="sidebar">
		<br><br>
		<h2>📘 Módulos</h2>
		<a href="#palheta">Técnica de Palheta</a>
		<a href="#pentagrama">Pentagrama Musical</a>
		<a href="#duracao">Duração das Notas</a>
		<a href="#alteracoes">Alterações no Braço</a>
		<a href="#compassos">Compassos Musicais</a>
		<a href="#pausas">Pausas Musicais</a>
	</nav>

	<!-- ===== CONTEÚDO PRINCIPAL ===== -->
	<div class="main-content">

		<div class="tutorial-header">
			<h1 class="tutorial-title">🎸 Bem-vindo ao Tutorial 01</h1>
			<div class="tutorial-status">
				<p><span class="label">Usuário:</span> <?= htmlspecialchars($_SESSION['aluno_nome'] ?? 'Visitante') ?></p>
				<p><span class="label">Nível atual:</span> <strong id="nivelAtual"><?= $nivelAluno ?></strong></p>
			</div>
		</div>

		<!-- Técnica de Palheta e Mãos -->
		<section class="card" id="palheta">
			<h2>Técnica de Palheta</h2>
			<p>Segure a palheta entre a polpa do polegar e o lado da primeira falange do indicador. O ângulo ideal é de 90° em relação às cordas.</p>
			<p>Evite rigidez excessiva, pois dificulta a execução rápida. Segurar frouxamente pode fazer a palheta escapar.</p>
			<p>As principais técnicas incluem a palhetada alternada (baixo-cima-baixo-cima), a palhetada econômica (que otimiza o movimento para aumentar a velocidade,
				especialmente com três notas por corda) e a palhetada direcional (usada para se mover entre as cordas de forma mais eficiente). </p>
			<h4>Técnica da Mão Direita e Esquerda</h4>
			<p>A coordenação entre as duas mãos é essencial. Pratique lentamente até adquirir precisão e ritmo.</p>
			<p>Mão Direita
				A mão direita é usada para tocar (pode ser com os dedos, unha ou palheta) as cordas, produzindo o som. Os dedos são identificados por letras, provenientes da nomenclatura em espanhol (o método é amplamente difundido com essa origem), sendo:
				P: Dedo Polegar (Pulgar)
				I: Dedo Indicador (Índice)
				M: Dedo Médio (Médio)
				A: Dedo Anular (Anular)
				O dedo mínimo (E, de Extremo) da mão direita raramente é usado no violão clássico e não está incluído na nomenclatura principal.</p>
			<h4>Técnica da Mão Esquerda</h4>
			<p>Posicione os dedos próximos aos trastes e mantenha o polegar atrás do braço do violão para oferecer suporte sem tensão.</p>
			<p>Mão Esquerda
				A mão esquerda é utilizada para pressionar as cordas no braço do violão, alterando a nota que será tocada. Os dedos são identificados por números, sendo:
				1: Dedo Indicador
				2: Dedo Médio
				3: Dedo Anular
				4: Dedo Mínimo (ou mindinho)
				O polegar da mão esquerda geralmente fica na parte de trás do braço do violão para dar apoio, mas não é usado para pressionar as cordas diretamente neste sistema de notação.
			</p>
			<figure>
				<img src="img/maosdeviolao.png" class="img-responsive" alt="Maos Violao" width="300">
				<figcaption>Mostra a posiçao de dedos no violão.</figcaption>
			</figure>
		</section>

		<!-- Pentagrama -->
		<section class="card" id="pentagrama">
			<h2>Pentagrama Musical</h2>
			<p>O pentagrama é formado por 5 linhas e 4 espaços. As notas são posicionadas conforme sua altura. A leitura inicia na <b>clave de sol</b>.</p>
			<ul>
				<li>Linhas: E (mi), G (sol), B (si), D (ré), F (fá)</li>
				<li>Espaços: F (fá), A (lá), C (dó), E (mi)</li>
			</ul>
			<h4>Alterações</h4>
			<p>(b) <b>Bemol</b> – diminui ½ tom | (#) <b>Sustenido</b> – aumenta ½ tom.</p>
			<figure>
				<img src="img/pentagrama.png" class="img-responsive" alt="Pentagrama com clave de sol" width="300">
				<figcaption>Pentagrama com clave de sol e notas de referência.</figcaption>
			</figure>
		</section>

		<!-- Duração das Notas -->
		<section class="card" id="duracao">
			<h2>Duração das Notas</h2>
			<p>Cada figura musical representa uma duração específica no compasso. Exemplo:</p>
			<ul>
				<li>Semibreve → 4 tempos</li>
				<li>Mínima → 2 tempos</li>
				<li>Semínima → 1 tempo</li>
				<li>Colcheia → ½ tempo</li>
			</ul>
			<figure>
				<img src="img/duracaonota.png" class="img-responsive" alt="Figuras musicais" width="200">
				<figcaption>Relação entre as figuras e suas durações.</figcaption>
			</figure>
		</section>

		<!-- Braço do Violão -->
		<section class="card" id="braco">
			<h2>Braço do Violão e Notas</h2>
			<p>O braço é composto por trastes (divisórias de metal). Cada casa equivale a ½ tom. As notas se repetem a cada 12 casas.</p>
			<figure>
				<img src="img/bracoviol.png" class="img-responsive" alt="Braço do violão com notas" width="400">
				<figcaption>Visualização das notas ao longo do braço.</figcaption>
			</figure>
		</section>

		<!-- Alterações no Braço -->
		<section class="card" id="alteracoes">
			<h2>Entendendo Alterações de Notas no Braço do Violão</h2>
			<p>A cada casa percorrida soma-se ½ tom:</p>
			<ul>
				<li>Casa 1 → Casa 2 = ½ tom</li>
				<li>Corda solta → Casa 1 = ½ tom</li>
				<li>Casa 1 → Casa 3 = 1 tom</li>
			</ul>
			<p>Essas relações ajudam na afinação e na construção de escalas.</p>
		</section>

		<!-- Compasso Musical -->
		<section class="card" id="compassos">
			<h2>Compasso Musical</h2>
			<p>O compasso organiza o tempo da música. No compasso 4/4, cada compasso possui 4 tempos.</p>
			<h4>Tipos de Compassos</h4>
			<ul>
				<li><b>Simples:</b> tempos divisíveis por 2</li>
				<li><b>Composto:</b> tempos divisíveis por 3</li>
				<li><b>Ternário:</b> 3 tempos</li>
				<li><b>Quaternário:</b> 4 tempos</li>
			</ul>
			<figure>
				<img src="img/compasso.png" class="img-responsive" alt="Compasso 4/4 com clave de sol" width="400">
				<figcaption>Compasso 4/4 com clave de sol.</figcaption>
			</figure>
		</section>

		<!-- Tablatura -->
		<section class="card" id="tablatura">
			<h2>Tablatura</h2>
			<p>A tablatura é um sistema de notação musical simplificado para violão e outros instrumentos de cordas,
				indicando onde e em qual corda posicionar os dedos. Ela utiliza seis linhas horizontais que representam as cordas do instrumento,
				com números para indicar as casas a serem pressionadas. </p>
			<h3>Estrutura da tablatura</h3>
			<dt>Linha de baixo</dt>
			<dd>6ª corda (Mi — a mais grossa).</dd>
			<dt>Os números</dt>
			<dd>Indicam a casa que você deve pressionar na corda correspondente.</dd>
			<dt>0 (zero)</dt>
			<dd>Tocar a corda solta, sem pressionar nenhuma casa.</dd>
			<dt>Números alinhados verticalmente</dt>
			<dd>Indica que as notas devem ser tocadas ao mesmo tempo, formando um acorde.</dd>
			<dt>Números em sequência</dt>
			<dd>Tocar as notas uma após a outra, como em um solo ou dedilhado.</dd>
			</dl>


			<div class="example">
				<h3>Como ler as notas</h3>
				<p>Para traduzir a tablatura para as notas musicais, você precisa saber as notas das cordas soltas e como elas mudam a cada casa:</p>
				<ol>
					<li>
						<b>Cordas soltas (número 0 na tablatura)</b>
						<ul>
							<li><code>e</code> (1ª corda) → Nota Mi</li>
							<li><code>B</code> (2ª corda) → Nota Si</li>
							<li><code>G</code> (3ª corda) → Nota Sol</li>
							<li><code>D</code> (4ª corda) → Nota Ré</li>
							<li><code>A</code> (5ª corda) → Nota Lá</li>
							<li><code>E</code> (6ª corda) → Nota Mi</li>
						</ul>
					</li>
					<li style="margin-top:8px">
						<b>A progressão das notas</b>
						<p class="muted">Na música ocidental, existem 12 notas (dó, dó#, ré, ré#, mi, fá, fá#, sol, sol#, lá, lá# e si) que se repetem. No violão, cada casa que você avança corresponde à próxima nota nesta sequência.</p>
					</li>
				</ol>
				<div class="note" style="margin-top:8px">
					<b>Exemplo:</b> se a 6ª corda (E) solta é Mi, a 1ª casa é Fá, a 2ª casa é Fá#, a 3ª casa é Sol, e assim por diante.
				</div>
			</div>
			<div class="grid" style="margin-top:16px">
				<div>
					<h3>Exemplo de tablatura</h3>
					<p class="muted">Formato simples comum em tutoriais:</p>
					<pre class="tablatura">e|-----0-----| ← 1ª corda (mi)
B|---1---1---| ← 2ª corda (si)
G|-0-------0-| ← 3ª corda (sol)
D|-----------| ← 4ª corda (ré)
A|-----------| ← 5ª corda (lá)
E|-----------| ← 6ª corda (mi)</pre>


					<p>Neste exemplo, você tocaria a 3ª corda solta (G) e depois a 1ª corda solta (e), etc. Números empilhados verticalmente (por exemplo, <code>0</code> em várias linhas na mesma coluna) significam acordes.</p>
				</div>


				<aside class="card" style="padding:12px">
					<h4>Dicas práticas</h4>
					<ul>
						<li>Leia sempre da esquerda para a direita.</li>
						<li>Marque com o dedo as casas mais usadas para facilitar a posição.</li>
						<li>Se não souber uma tablatura, toque devagar e aumente a velocidade gradualmente.</li>
					</ul>
				</aside>

				<h3>Representação de um acorde</h3>
				<p>Números: Indicam qual casa você deve pressionar em cada corda. Por exemplo, um acorde pode ser representado por uma coluna de números.</p>
				<p><b>Zero (0)</b>: Significa que você deve tocar a corda solta (sem pressionar nenhuma casa).</p>
				<p><b>Aparência</b>: Em uma tablatura simples (texto), um acorde é formado por uma coluna de números que se estendem pelas seis linhas, indicando a posição dos dedos em cada corda para formar o acorde.</p>


				<h3>Exemplo de acorde na tablatura (texto)</h3>
				<p>Um acorde de Mi menor pode ser representado da seguinte forma:</p>
				<pre class="tablatura">e|--0--|
B|--0--|
G|--0--|
D|--2--|
A|--2--|
E|--0--|</pre>
				<p>Neste exemplo:</p>
				<ul>
					<li>A primeira e a última corda (Mi agudo e Mi grave) são tocadas soltas (0).</li>
					<li>A terceira e a quarta cordas são pressionadas na segunda casa (2).</li>
				</ul>
				<h3>Como os acordes podem ser tocados</h3>
				<ul>
					<li><b>Simultaneamente</b>: Números alinhados verticalmente indicam que todas as notas devem ser tocadas ao mesmo tempo, como uma batida.</li>
					<li><b>Arpejado</b>: As notas do acorde podem ser tocadas sequencialmente, uma de cada vez.</li>
				</ul>
				<h3>Diagramas de acordes</h3>
				<p>Além da tablatura, os acordes também são representados por diagramas, que mostram de forma visual o braço do violão:</p>
				<ul>
					<li><b>Bolinhas pretas</b>: indicam onde colocar os dedos.</li>
					<li><b>"X"</b>: cordas que não devem ser tocadas.</li>
					<li><b>"O" ou bolinha branca</b>: cordas que devem ser tocadas soltas.</li>
				</ul>
			</div>
			<figure>
				<img src="img/tablatura.png" class="img-responsive" alt="Tablatura de violão" width="400">
				<figcaption>Tablatura didática com números e cordas.</figcaption>
			</figure>
		</section>

		<!-- Pausas -->
		<section class="card" id="pausas">
			<h2>Pausas Musicais (Figuras de Silêncio)</h2>
			<p>Representam o tempo de silêncio. Têm a mesma duração que as figuras equivalentes.</p>
			<figure>
				<img src="img/pausasmusicais.png" class="img-responsive" alt="Pausas musicais" width="400">
				<figcaption>Pausas e suas durações correspondentes.</figcaption>
			</figure>
		</section>

		<footer>
			<p>© 2025 – Projeto Educacional de Violão | Luciano Rodrigues</p>
		</footer>
		</main>

</body>

</html>

</html>