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
	foreach ($menuItens as $nivel => $dados) {
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
	</style>
</head>

<body>
	<nav class="navbar navbar-inverse">
		<div class="container">
			<ul class="nav navbar-nav">
				<!-- Tutoriais -->
				<li><a href="tutorial-01.php">Tutorial 01</a></li>
				<li><a href="tutorial_02.php">Tutorial 02</a></li>

				<!-- Exercícios -->
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

<div class="tutorial-header">
	<h1 class="tutorial-title">🎸 Bem-vindo ao Tutorial 01</h1>

	<div class="tutorial-status">
		<p><span class="label">Usuário:</span>
			<?= htmlspecialchars($_SESSION['aluno_nome'] ?? 'Visitante') ?>
		</p>
		<p><span class="label">Nível atual:</span>
			<strong id="nivelAtual"><?= $nivelAluno ?></strong>
		</p>
	</div>
</div>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<title>Tutorial 01 – Fundamentos do Violão</title>
	<style>
		body {
			font-family: "Segoe UI", Roboto, Arial, sans-serif;
			background: #f9fafb;
			color: #222;
			margin: 0;
			line-height: 1.6;
			display: flex;
			min-height: 100vh;
		}

		/* Menu lateral fixo */
		nav {
			width: 250px;
			background: #1f2937;
			color: #fff;
			position: fixed;
			top: 0;
			left: 0;
			height: 100%;
			overflow-y: auto;
			padding: 20px;
			box-shadow: 2px 0 8px rgba(0, 0, 0, 0.15);
		}

		nav h2 {
			font-size: 1.2rem;
			margin-top: 0;
			text-align: center;
			border-bottom: 1px solid #374151;
			padding-bottom: 8px;
		}

		nav ul {
			list-style: none;
			padding: 0;
			margin: 16px 0;
		}

		nav li {
			margin: 10px 0;
		}

		nav a {
			color: #e5e7eb;
			text-decoration: none;
			display: block;
			padding: 8px 12px;
			border-radius: 8px;
			transition: background 0.3s, color 0.3s;
		}

		nav a:hover {
			background: #3b82f6;
			color: #fff;
		}

		nav::-webkit-scrollbar {
			width: 6px;
		}

		nav::-webkit-scrollbar-thumb {
			background: #4b5563;
			border-radius: 4px;
		}

		/* Conteúdo principal */
		main {
			margin-left: 270px;
			padding: 30px;
			max-width: 900px;
		}

		header {
			background: #3b82f6;
			color: #fff;
			padding: 20px;
			border-radius: 12px;
			margin-bottom: 25px;
			text-align: center;
			box-shadow: 0 3px 6px rgba(0, 0, 0, 0.15);
		}

		.card {
			background: #fff;
			border-radius: 12px;
			padding: 20px 24px;
			margin-bottom: 22px;
			box-shadow: 0 3px 8px rgba(0, 0, 0, 0.06);
			border-left: 6px solid #3b82f6;
		}

		.card h2 {
			border-bottom: 2px solid #e5edff;
			padding-bottom: 8px;
			margin-bottom: 12px;
			color: #1e40af;
		}

		.card h4 {
			margin-top: 14px;
			color: #111827;
		}

		img.img-responsive {
			display: block;
			margin: 14px auto;
			max-width: 100%;
			height: auto;
		}

		figcaption {
			text-align: center;
			color: #6b7280;
			font-size: 0.9rem;
			margin-top: 6px;
		}

		footer {
			text-align: center;
			padding: 18px;
			color: #666;
			border-top: 1px solid #ddd;
			margin-top: 36px;
			font-size: 0.9rem;
		}

		/* Rolagem suave */
		html {
			scroll-behavior: smooth;
		}

		/* Responsivo */
		@media (max-width: 800px) {
			nav {
				position: relative;
				width: 100%;
				height: auto;
			}

			main {
				margin-left: 0;
				padding: 16px;
			}
		}
	</style>
</head>

<body>
	<!-- Menu lateral -->
	<nav>
		<h2>📘 Módulos</h2>
		<ul>
			<li><a href="#palheta">Técnica de Palheta</a></li>
			<li><a href="#pentagrama">Pentagrama Musical</a></li>
			<li><a href="#duracao">Duração das Notas</a></li>
			<li><a href="#braco">Braço do Violão</a></li>
			<li><a href="#alteracoes-braco">Alterações no Braço</a></li>
			<li><a href="#compasso">Compasso Musical</a></li>
			<li><a href="#tablatura">Tablatura</a></li>
			<li><a href="#pausas">Pausas Musicais</a></li>
		</ul>
	</nav>

	<!-- Conteúdo -->
	<main>
		<header>
			<h1>🎸 Tutorial 01 – Fundamentos do Violão</h1>
			<p>Aprenda os princípios básicos: postura, leitura e ritmo.</p>
		</header>

		<!-- Técnica de Palheta e Mãos -->
		<section class="card" id="palheta">
			<h2>Técnica de Palheta</h2>
			<p>Segure a palheta entre a polpa do polegar e o lado da primeira falange do indicador. O ângulo ideal é de 90° em relação às cordas.</p>
			<p>Evite rigidez excessiva, pois dificulta a execução rápida. Segurar frouxamente pode fazer a palheta escapar.</p>

			<h4>Técnica da Mão Direita e Esquerda</h4>
			<p>A coordenação entre as duas mãos é essencial. Pratique lentamente até adquirir precisão e ritmo.</p>

			<h4>Técnica da Mão Esquerda</h4>
			<p>Posicione os dedos próximos aos trastes e mantenha o polegar atrás do braço do violão para oferecer suporte sem tensão.</p>
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
				<img src="assets/img/violao/pentagrama.png" class="img-responsive" alt="Pentagrama com clave de sol">
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
				<img src="img/duracaonota.png" class="img-responsive" alt="Figuras musicais">
				<figcaption>Relação entre as figuras e suas durações.</figcaption>
			</figure>
		</section>

		<!-- Braço do Violão -->
		<section class="card" id="braco">
			<h2>Braço do Violão e Notas</h2>
			<p>O braço é composto por trastes (divisórias de metal). Cada casa equivale a ½ tom. As notas se repetem a cada 12 casas.</p>
			<figure>
				<img src="img/bracoviol.png" class="img-responsive" alt="Braço do violão com notas">
				<figcaption>Visualização das notas ao longo do braço.</figcaption>
			</figure>
		</section>

		<!-- Alterações no Braço -->
		<section class="card" id="alteracoes-braco">
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
		<section class="card" id="compasso">
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
				<img src="img/compasso.png" class="img-responsive" alt="Compasso 4/4 com clave de sol">
				<figcaption>Compasso 4/4 com clave de sol.</figcaption>
			</figure>
		</section>

		<!-- Tablatura -->
		<section class="card" id="tablatura">
			<h2>Tablatura</h2>
			<p>Seis linhas representam as cordas. Os números indicam as casas que devem ser pressionadas.</p>
			<figure>
				<img src="img/tablatura.png" class="img-responsive" alt="Tablatura de violão">
				<figcaption>Tablatura didática com números e cordas.</figcaption>
			</figure>
		</section>

		<!-- Pausas -->
		<section class="card" id="pausas">
			<h2>Pausas Musicais (Figuras de Silêncio)</h2>
			<p>Representam o tempo de silêncio. Têm a mesma duração que as figuras equivalentes.</p>
			<figure>
				<img src="img/pausasmusicais.png" class="img-responsive" alt="Pausas musicais">
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