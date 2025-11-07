<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "conexao.php";

// 🔒 Controle de expiração de sessão (30 minutos)
$tempoMaximo = 1800;
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
$nomeAluno = htmlspecialchars($_SESSION['aluno_nome'] ?? 'Visitante');

// --- Cálculo de desempenho dos níveis ---
$nivels = [1, 2, 3];
$levelData = [];

foreach ($nivels as $nivel) {
	$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM exercicios WHERE nivel = ?");
	$stmt->bind_param('i', $nivel);
	$stmt->execute();
	$res = $stmt->get_result();
	$row = $res->fetch_assoc();
	$total = (int)($row['total'] ?? 0);
	$stmt->close();

	$stmt = $conn->prepare("
		SELECT 
			COUNT(ae.id) AS attempted,
			SUM(CASE WHEN ae.resultado = 1 THEN 1 ELSE 0 END) AS correct
		FROM alunos_exercicios ae
		JOIN exercicios e ON e.id = ae.id_exercicios
		WHERE ae.id_usuario = ? AND e.nivel = ? AND ae.status = 1
	");
	$stmt->bind_param('ii', $alunoId, $nivel);
	$stmt->execute();
	$res = $stmt->get_result();
	$row = $res->fetch_assoc();
	$attempted = (int)($row['attempted'] ?? 0);
	$correct = (int)($row['correct'] ?? 0);
	$stmt->close();

	$percent = $attempted > 0 ? ($correct / $attempted) * 100 : 0;
	$concluded = ($attempted > 0 && $percent >= 60.0);

	$levelData[$nivel] = [
		'total_exercises' => $total,
		'attempted' => $attempted,
		'correct' => $correct,
		'percent' => $percent,
		'concluded' => $concluded
	];
}

// --- Determina níveis desbloqueados ---
$highestUnlocked = 1;
if ($levelData[1]['concluded']) {
	$highestUnlocked = 2;
	if ($levelData[2]['concluded']) {
		$highestUnlocked = 3;
		if ($levelData[3]['concluded']) {
			$highestUnlocked = 4; // Todos concluídos
		}
	}
}

// --- Redireciona automaticamente após concluir o nível 3 ---
if ($highestUnlocked === 4) {
	session_destroy();
	header('Location: conclusao.php');
	exit();
}

// --- Menu ---
$menuItens = [
	1 => ['nome' => 'Iniciantes', 'link' => 'iniciantes.php'],
	2 => ['nome' => 'Intermediários', 'link' => 'intermediarios.php'],
	3 => ['nome' => 'Avançados', 'link' => 'avancados.php'],
];

$menuStatus = [];
foreach ($menuItens as $nivel => $dados) {
	if ($highestUnlocked === 4) {
		$menuStatus[$nivel] = 'concluido';
	} elseif ($nivel < $highestUnlocked) {
		$menuStatus[$nivel] = 'concluido';
	} elseif ($nivel == $highestUnlocked) {
		$menuStatus[$nivel] = 'andamento';
	} else {
		$menuStatus[$nivel] = 'bloqueado';
	}
}
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
		body {
			margin-top: 60px;
			background-color: #f5f5f5;
			font-family: "Segoe UI", Arial, sans-serif;
			scroll-behavior: smooth;
		}

		.navbar {
			height: 50px;
			border-radius: 0;
			font-size: 15px;
		}

		.navbar a {
			color: #fff !important;
		}

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

		.tutorial-header {
			background: linear-gradient(135deg, #2980b9, #6dd5fa);
			color: #fff;
			padding: 40px 20px;
			border-radius: 0 0 25px 25px;
			text-align: center;
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

		.center-info {
			display: flex;
			flex-direction: column;
			align-items: center;
			text-align: center;
			justify-content: center;
		}
	</style>
</head>

<body>
	<nav class="navbar navbar-inverse navbar-fixed-top">
		<div class="container">
			<div class="navbar-header">
				<a class="navbar-brand" href="index.php">
					<img src="img/foto22.jpg" width="100" height="30" alt="Logo">
				</a>
			</div>
			<ul class="nav navbar-nav navbar-right">
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">Exercícios <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<?php foreach ($menuItens as $nivel => $dados):
							$classe = $menuStatus[$nivel] == 'concluido' ? 'menu-concluido' : ($menuStatus[$nivel] == 'andamento' ? 'menu-em-andamento' : 'menu-bloqueado');
							$statusTxt = [
								'concluido' => ' - Concluído ✅',
								'andamento' => ' - Em andamento 🚀',
								'bloqueado' => ' - Bloqueado 🔒'
							][$menuStatus[$nivel]];
						?>
							<?php if ($menuStatus[$nivel] == 'bloqueado' || $menuStatus[$nivel] == 'concluido'): ?>
								<li class="disabled"><span class="<?= $classe ?>"><?= $dados['nome'] . $statusTxt ?></span></li>
							<?php else: ?>
								<li><a href="<?= $dados['link'] ?>" class="<?= $classe ?>"><?= $dados['nome'] . $statusTxt ?></a></li>
							<?php endif; ?>
							<li class="divider"></li>
						<?php endforeach; ?>
					</ul>
				</li>
				<li><a href="logout.php">Sair</a></li>
			</ul>
		</div>
	</nav>

	<div class="container center-info">
		<div class="tutorial-header">
			<h1 class="tutorial-title">Bem-vindo(a), <?= $nomeAluno ?>!</h1>
			<p><span class="label">Nível Atual em andamento:</span>
				<?php
				if ($highestUnlocked === 4) {
					echo "Todos os níveis concluídos";
				} else {
					echo $menuItens[$highestUnlocked]['nome'];
				}
				?>
			</p>

			<div class="tutorial-status">
				<?php foreach ($nivels as $n): ?>
					<p>
						<strong><?= $menuItens[$n]['nome'] ?></strong>
						— Tentativas: <?= $levelData[$n]['attempted'] ?> /
						<?= $levelData[$n]['total_exercises'] ?> |
						Acertos: <?= $levelData[$n]['correct'] ?> |
						Percentual: <?= number_format($levelData[$n]['percent'], 1) ?>%
						<?php if ($levelData[$n]['concluded']): ?>
							<span class="menu-concluido"> — Concluído (bloqueado)</span>
						<?php endif; ?>
					</p>
				<?php endforeach; ?>
			</div>
		</div>
	</div>

	<script>
		$(document).ready(function() {
			document.addEventListener('visibilitychange', function() {
				if (!document.hidden) location.reload();
			});
			window.addEventListener('focus', function() {
				location.reload();
			});
		});
	</script>
</body>

</html>

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
		<a href="#braco">Braco e Notas</a>
		<a href="#alteracoes">Alterações no Braço</a>
		<a href="#compassos">Compassos Musicais</a>
		<a href="#tablatura">Tablatura</a>
		<a href="#pausas">Pausas Musicais</a>
	</nav>

	<!-- ===== CONTEÚDO PRINCIPAL ===== -->
	<div class="main-content">

		<div class="tutorial-header">
			<h1 class="tutorial-title">🎸 Bem-vindo ao Tutorial 01</h1>
			<div class="tutorial-status">
				<p><span class="label">Usuário:</span> <?= htmlspecialchars($_SESSION['aluno_nome'] ?? 'Visitante') ?></p>
				<p><span class="label">Nível atual:</span> <strong id="nivelAtual"><?= $nivel ?></strong></p>
				<p>Status:
					<?php
					if ($nivel == 1) echo "Iniciante";
					elseif ($nivel == 2) echo "Intermediário";
					else echo "Avançado";
					?>
				</p>

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
				<img src="img/maosdeviolao.png" class="img-responsive" alt="Maos Violao" width="400">
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
			<figure>
				<img src="img/pentagramaDiag.png" class="img-responsive" alt="Pentagrama com clave de sol" width="400">
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
				<img src="img/duracaonota.png" class="img-responsive" alt="Figuras musicais" width="300">
				<figcaption>Relação entre as figuras e suas durações.</figcaption>
			</figure>
		</section>

		<!-- Braço do Violão -->
		<section class="card" id="braco">
			<h2>Braço do Violão e Notas</h2>
			<p>O braço é composto por trastes (divisórias de metal). Cada casa equivale a ½ tom. As notas se repetem a cada 12 casas.</p>
			<figure>
				<img src="img/bracoviol.png" class="img-responsive" alt="Braço do violão com notas" width="400">
			</figure>
			<figure>
				<img src="img/notasViol.png" class="img-responsive" alt="Braço do violão com notas" width="500">
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
			<h4>Alterações</h4>
			<p>(b) <b>Bemol</b> – diminui ½ tom | (#) <b>Sustenido</b> – aumenta ½ tom.</p>
			<p>Para alterar notas no braço do violão, use sustenidos (\(\#\)) para subir meio tom
				(uma casa para a frente, em direção ao corpo do instrumento) e bemóis (\(b\)) para descer
				meio tom (uma casa para trás, em direção às tarraxas). Mover-se entre duas casas no
				braço do violão representa uma alteração de meio tom, o que corresponde a uma casa no
				instrumento. Por exemplo, um Sol na terceira corda se torna Sol sustenido na
				quarta casa e Sol bemol na segunda casa.</p>
			<p>Essas relações ajudam na afinação e na construção de escalas.</p>
			<h3>Alterando uma nota para sustenido (#)</h3>
			<p><span class="badge">O que é</span><b>Um sustenido (<code class="k">#</code>) aumenta a nota em meio tom.</b></p>
			<h3>Como fazer</h3>
			<p>Move-se para a casa imediatamente à frente no braço do violão, em direção ao corpo do instrumento (mais perto das casas numeradas maiores).</p>
			<p><b>Exemplo:</b> Se a nota é <em>Lá</em> (na segunda casa da sexta corda), o <em>Lá sustenido</em> estará na terceira casa.</p>
			<h3>Alterando uma nota para bemol (b)</h3>
			<p><span class="badge">O que é</span><b>Um bemol (<code class="k">b</code>) diminui a nota em meio tom.</b></p>
			<p><b>Exemplo:</b> Se a nota é <em>Lá</em> (na segunda casa da sexta corda), o <em>Lá sustenido</em> estará na terceira casa.</p>
			<h3>Como fazer</h3>
			<p>Move-se para a casa imediatamente atrás no braço do violão, em direção às tarraxas (casas numeradas menores).</p>
			<p><b>Exemplo:</b> Se a nota é <em>Si</em> (na segunda casa da quinta corda), o <em>Si bemol</em> estará na primeira casa.</p>
			<figure>
				<img src="img/alterSustBem.png" class="img-responsive" alt="Alteração de Notas" width="600">
				<figcaption>Alterações de Notas Sustenido e Bemol</figcaption>
			</figure>
		</section>


		<!-- Compasso Musical -->
		<section class="card" id="compassos">
			<h2>Compasso Musical</h2>
			<p>Um compasso musical é a divisão de uma partitura em grupos de tempos regulares,
				organizando o ritmo e a pulsação da música. Ele é representado por barras verticais que
				separam os compassos e são definidos por uma fórmula de compasso, que indica quantas batidas
				há e qual tipo de nota vale uma batida. </p>
			<p>O compasso organiza o tempo da música. No compasso 4/4, cada compasso possui 4 tempos.</p>
			<h4>Tipos de Compassos</h4>
			<ul>
				<li><b>Simples:</b> tempos divisíveis por 2</li>
				<li><b>Composto:</b> tempos divisíveis por 3</li>
				<li><b>Ternário:</b> 3 tempos</li>
				<li><b>Quaternário:</b> 4 tempos</li>
			</ul>
			<div>
				<h4>Fórmula de compasso</h4>
				<p>A fórmula (como <code class="k">4/4</code> ou <code class="k">3/4</code>) informa o número de batidas por compasso e qual nota vale a batida.</p>
				<ul>
					<li><b>4/4</b> — Quatro batidas por compasso; a semínima vale 1 tempo. Padrão de acentuação: <em>forte — fraco — médio — fraco</em>.</li>
					<li><b>3/4</b> — Três batidas; a primeira é forte (comum em valsa).</li>
					<li><b>2/4</b> — Duas batidas; sensação de movimento rápido ou marcial.</li>
				</ul>
				<p>Existem também compassos <em>compostos</em> (ex.: 6/8) e <em>mistos</em>, que agrupam tempos de forma diferente — cada tipo tem sua própria forma de subdivisão e acentuação.</p>
				</main>
				<h4>Para que serve</h4>
				<ul>
					<li><b>Organização:</b> Agrupa os tempos em blocos regulares, facilitando estudo e execução.</li>
					<li><b>Estrutura:</b> Define a pulsação e o ritmo da música, dando uma estrutura clara à peça.</li>
					<li><b>Orientação:</b> Ajuda a identificar padrões rítmicos e repetições musicais.</li>
				</ul>
				<div style="margin-top:12px;font-size:1.78rem;color:var(--muted)">
					<p><b>Nota:</b> Ao escrever partituras, use barras de compasso claramente posicionadas e indique a fórmula no início da pauta para orientar executantes.</p>
				</div>
			</div>
			<h3>Pausas Musicais em Compassos Ternários e Quaternários</h3>
			<p>
				As pausas musicais em compassos <b>ternários</b> e <b>quaternários</b> seguem as regras de duração de seus respectivos compassos.
				O compasso ternário (<code>3/4</code> ou <code>3/8</code>) tem três tempos e o quaternário (<code>4/4</code> ou <code>4/8</code>) tem quatro tempos,
				e as pausas (como a semibreve, mínima, semínima, etc.) preenchem esses tempos de acordo com o compasso.
			</p>
			<p>
				Por exemplo, em um compasso <code>4/4</code>, a pausa da semibreve preenche os quatro tempos, enquanto em um compasso <code>3/4</code>,
				a mesma pausa preencheria todo o compasso se o tempo da semibreve fosse a unidade de compasso. No entanto, isso é menos comum,
				pois cada compasso ternário normalmente usa três semínimas.
			</p>
			<h3>Compassos Ternários</h3>
			<ul>
				<li><b>Divisão:</b> 3 tempos por compasso.</li>
				<li><b>Estrutura rítmica:</b> Primeiro tempo forte, seguido de dois tempos fracos.</li>
				<li><b>Exemplos de compassos:</b> <code>3/4</code>, <code>3/2</code>, <code>3/8</code>.</li>
			</ul>
			<h3>Compassos Quaternários</h3>
			<ul>
				<li><b>Divisão:</b> 4 tempos por compasso.</li>
				<li><b>Estrutura rítmica:</b> O primeiro tempo é forte, o segundo é fraco e o terceiro é meio forte ou fraco.</li>
				<li><b>Exemplos de compassos:</b> <code>4/4</code>, <code>4/2</code>, <code>4/8</code>.</li>
			</ul>
			<h3>Considerações Adicionais</h3>
			<ul>
				<li>
					<b>Compassos compostos:</b> São aqueles em que a unidade de tempo é uma nota com ponto.
					O número superior do compasso pode ser <code>6</code> (binário composto), <code>9</code> (ternário composto) ou <code>12</code> (quaternário composto).
				</li>
				<li>
					<b>Unidade de tempo:</b> A duração de cada tempo é definida pelo número inferior da fração.
					Em <code>3/4</code>, a semínima vale um tempo; em <code>3/8</code>, a colcheia vale um tempo.
				</li>
			</ul>

			<div class="note">
				💡 <b>Dica:</b> Visualizar os compassos e pausas em um pentagrama ajuda a compreender melhor como o silêncio e o som se distribuem no tempo.
			</div>
			</main>
</body>
<figure>
	<img src="img/compasso.png" class="img-responsive" alt="Compasso 4/4 com clave de sol" width="400">
	<figcaption>Compasso 4/4 com clave de sol.</figcaption>
</figure>
</section>
<!-- Tablatura -->
<section class="card" id="tablatura">
	<h2>Tablatura</h2>
	<p>
		A tablatura é um sistema de notação musical simplificado para violão e outros instrumentos de cordas,
		indicando onde e em qual corda posicionar os dedos. Ela utiliza seis linhas horizontais que
		representam as cordas do instrumento, com números para indicar as casas a serem pressionadas.
	</p>

	<h3>Estrutura da tablatura</h3>
	<dl>
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

	<h3>Como ler as notas</h3>
	<p>
		Para traduzir a tablatura para as notas musicais, você precisa saber as notas das cordas soltas e como elas mudam a cada casa:
	</p>

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

		<li>
			<b>A progressão das notas</b>
			<p class="muted">
				Na música ocidental, existem 12 notas (dó, dó#, ré, ré#, mi, fá, fá#, sol, sol#, lá, lá# e si)
				que se repetem. No violão, cada casa que você avança corresponde à próxima nota nesta sequência.
			</p>
		</li>
	</ol>

	<div class="note">
		<b>Exemplo:</b> se a 6ª corda (E) solta é Mi, a 1ª casa é Fá, a 2ª casa é Fá#, a 3ª casa é Sol, e assim por diante.
	</div>

	<div class="tablatura-bloco">
		<h3>Exemplo de tablatura</h3>
		<p class="muted">Formato simples comum em tutoriais:</p>

		<pre class="tablatura">e|-----0-----| ← 1ª corda (mi)
B|---1---1---| ← 2ª corda (si)
G|-0-------0-| ← 3ª corda (sol)
D|-----------| ← 4ª corda (ré)
A|-----------| ← 5ª corda (lá)
E|-----------| ← 6ª corda (mi)</pre>

		<p>
			Neste exemplo, você tocaria a 3ª corda solta (G) e depois a 1ª corda solta (e), etc.
			Números empilhados verticalmente (por exemplo, <code>0</code> em várias linhas na mesma coluna)
			significam acordes.
		</p>

		<h4>Dicas práticas</h4>
		<ul>
			<li>Leia sempre da esquerda para a direita.</li>
			<li>Marque com o dedo as casas mais usadas para facilitar a posição.</li>
			<li>Se não souber uma tablatura, toque devagar e aumente a velocidade gradualmente.</li>
		</ul>

		<h3>Representação de um acorde</h3>
		<p>
			Números: Indicam qual casa você deve pressionar em cada corda. Por exemplo, um acorde pode ser representado por uma coluna de números.
		</p>
		<p><b>Zero (0)</b>: Significa que você deve tocar a corda solta (sem pressionar nenhuma casa).</p>
		<p>
			<b>Aparência</b>: Em uma tablatura simples (texto), um acorde é formado por uma coluna de números que se estendem pelas seis linhas,
			indicando a posição dos dedos em cada corda para formar o acorde.
		</p>

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
		<figcaption>Tablatura didática com números e cordas.</figcaption>
	</figure>
</section>

<!-- Pausas -->
<section class="card" id="pausas">
	<h2>Pausas Musicais (Figuras de Silêncio)</h2>
	<p>Representam o tempo de silêncio. Têm a mesma duração que as figuras equivalentes.</p>
	<p>A pausa de semínima é igual à duração de uma semínima. A pausa de colcheia é igual à duração da colcheia,
		a pausa de semicolcheia é igual à duração da semicolcheia, a pausa de trigésima segunda é igual à duração da trigésima segunda nota
		e a pausa de sexagésima quarta é igual à duração da sexagésima quarta nota.</p>
	<h3>Exemplo de pausas Quaternário</h3>
	<p><b>Compasso <code>4/4</code>:</b> Uma pausa de semibreve pode preencher todo o compasso.</p>
	<p><b>Compasso <code>4/8</code>:</b> Uma pausa de mínima preenche dois tempos, e uma pausa de semínima preenche um tempo.</p>
	<h3>Exemplo de pausas Ternario</h3>
	<p><b>Compasso <code>3/4</code>:</b> Uma pausa de semibreve não cabe em um único compasso, pois a unidade de tempo é a semínima.</p>
	<p>Você pode usar uma pausa de mínima e uma de semínima, ou uma pausa de semínima e duas de colcheia, etc.</p>
	<p><b>Compasso <code>3/8</code>:</b> Pode conter uma pausa de mínima e uma de colcheia, ou três pausas de colcheia.</p>
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