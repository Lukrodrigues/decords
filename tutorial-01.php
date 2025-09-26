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
<html lang="pt-br">

<head>
	<meta charset="UTF-8">
	<title>Tutorial de Violão - Iniciantes</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
	<style>
		body {
			background: #f9f9f9;
			font-family: "Segoe UI", sans-serif;
			line-height: 1.7;
		}

		.tutorial-container {
			max-width: 950px;
			margin: 40px auto;
		}

		.card {
			background: #fff;
			border-radius: 6px;
			padding: 20px;
			margin-bottom: 30px;
			box-shadow: 0 3px 8px rgba(0, 0, 0, 0.08);
		}

		h1,
		h2,
		h3 {
			margin-top: 10px;
			font-weight: 600;
		}

		figure {
			text-align: center;
			margin: 20px 0;
		}

		figure img {
			max-width: 100%;
			border-radius: 6px;
		}

		figcaption {
			font-size: 0.9em;
			color: #777;
			margin-top: 5px;
		}
	</style>
</head>

<body>
	<div class="tutorial-container">

		<!-- Introdução -->
		<div class="card">
			<h1 class="text-center">Tutorial de Violão para Iniciantes</h1>
			<p>
				Aprender violão é uma jornada divertida e enriquecedora.
				Neste tutorial, você conhecerá as principais partes do instrumento,
				os acordes básicos, técnicas de afinação e leitura de tablaturas.
				O conteúdo está dividido em seções claras, com explicações em texto e imagens ilustrativas.
			</p>
		</div>

		<!-- Corpo do Violão -->
		<section class="card">
			<h2>Corpo do Violão</h2>
			<figure>
				<img class="img-responsive-center" src="img/violao.png" alt="Partes do corpo do violão" width="540" height="340" class="img-responsive center-block">
				<figcaption>Principais partes do corpo de um violão.</figcaption>
			</figure>
			<p>
				O corpo do violão é a parte responsável por amplificar o som.
				Nele encontramos a <b>boca</b>, o <b>cavalete</b> e o <b>rastilho</b>.
				Esses elementos trabalham juntos para sustentar as cordas e projetar o som.
			</p>
			<p>
				A <b>boca</b> é a abertura central, por onde o som vibra e se espalha.
				O <b>cavalete</b> segura as cordas em sua extremidade inferior,
				enquanto o <b>rastilho</b> mantém a altura adequada das cordas,
				garantindo precisão na afinação.
			</p>
		</section>

		<!-- Braço do Violão -->
		<section class="card">
			<h2>Braço do Violão</h2>
			<center>
				<img class="img-responsive-center" src="img/braco.png" alt="Braco do violão" width="540" height="340">
			</center>
			<figure>
				<figcaption>Braço, casas e trastes do violão.</figcaption>
			</figure>
			<p>
				O braço do violão é a parte longa que conecta o corpo à cabeça.
				É nele que o músico pressiona as cordas para formar acordes e notas.
			</p>
			<p>
				O braço é dividido por <b>trastes</b>, pequenas divisórias metálicas
				que criam as chamadas <b>casas</b>.
				Cada casa representa um semitom e permite ao violonista tocar diferentes notas.
			</p>
		</section>

		<!-- Acordes Básicos -->
		<section class="card">
			<h2>Acordes Básicos</h2>
			<figure>
				<img class="img-responsive-center" src="img/la-violao.png" alt="Acorde do violão" width="440" height="240" class="img-responsive center-block">
				<figcaption>Diagrama do acorde de Lá (A).</figcaption>
			</figure>
			<p>
				Os acordes são a base da maioria das músicas.
				Entre os primeiros acordes que todo iniciante deve aprender estão:
				<b>A (Lá)</b>, <b>C (Dó)</b> e <b>G (Sol)</b>.
			</p>
			<p>
				Para tocar o acorde <b>A</b>, posicione os dedos 1, 2 e 3
				nas segundas casas das cordas 2, 3 e 4.
				Toque as demais cordas soltas para formar o acorde completo.
			</p>
		</section>

		<!-- Tablatura -->
		<section class="card">
			<h2>Tablatura</h2>
			<figure>
				<img class="img-responsive-center" src="img/tablatura.png" alt="tablatura do violão" width="440" height="240" class="img-responsive center-block">
				<figcaption>Exemplo simples de tablatura.</figcaption>
			</figure>
			<p>
				A tablatura é uma forma prática de ler música para violão.
				Ela utiliza seis linhas que representam as cordas do instrumento.
				Os números indicam em qual casa a corda deve ser pressionada.
			</p>
			<p>
				Por exemplo, se na segunda linha aparecer o número "3",
				significa que você deve pressionar a terceira casa da segunda corda.
			</p>
		</section>

		<!-- Afinação -->
		<section class="card">
			<h2>Afinação</h2>
			<figure>
				<figcaption>Afinação usando afinador eletrônico.</figcaption>
			</figure>
			<p>
				Afinar o violão corretamente é essencial para que ele produza um som agradável.
				A afinação padrão das cordas, de cima para baixo, é:
				<b>E (Mi)</b>, <b>A (Lá)</b>, <b>D (Ré)</b>, <b>G (Sol)</b>, <b>B (Si)</b>, <b>E (Mi)</b>.
			</p>
			<p>
				Você pode usar um afinador eletrônico, um aplicativo de celular
				ou até mesmo afinar de ouvido com base em outra referência musical.
			</p>
		</section>

	</div>
</body>

</html>