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

<!DOCTYPE html>
<html lang="pt-BR">

<!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Tutorial de Violão</title>
	<style>
		body {
			font-family: Arial, sans-serif;
			margin: 20px;
			line-height: 1.6;
			background: #fafafa;
		}

		h1,
		h2 {
			color: #333;
		}

		section {
			margin-bottom: 40px;
			padding: 20px;
			background: #fff;
			border-radius: 8px;
			box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
		}

		figure {
			text-align: center;
			margin: 20px 0;
		}

		img {
			max-width: 100%;
			height: auto;
			border: 1px solid #ddd;
			border-radius: 6px;
			background: #fff;
		}

		figcaption {
			font-size: 0.9em;
			color: #555;
			margin-top: 8px;
		}
	</style>
</head>

<body>

	<h1>Tutorial de Violão</h1>

	<!-- Módulo 1 -->
	<section>
		<h2>1. Corpo do Violão</h2>
		<p>Conheça as partes principais do corpo do violão: corpo, boca, cavalete e rastilho.</p>
		<figure>
			<img src="img/violao.png" alt="Desenho instrucional do corpo do violão com labels" width="500" class="img-responsive center-block" />
			<figcaption>Ilustração instrucional mostrando corpo, boca, cavalete e rastilho.</figcaption>
		</figure>
	</section>

	<!-- Módulo 2 -->
	<section>
		<h2>2. Braço do Violão</h2>
		<p>No braço ficam as casas, trastes, pestana e as tarraxas.</p>
		<figure>
			<img src="img/bracoviol.png" alt="Diagrama didático do braço do violão com labels" width="500" class="img-responsive center-block" />
			<figcaption>Diagrama mostrando mão, tarraxas, trastes, casas e pestana.</figcaption>
		</figure>
	</section>

	<!-- Módulo 3 -->
	<section>
		<h2>3. Acordes Básicos</h2>
		<p>Os acordes são a base para tocar músicas. Vamos ver os principais acordes iniciais.</p>
		<figure>
			<img src="imagens/acordes-basicos.png" alt="Diagramas didáticos de acordes básicos C, G, D, Em, Am" />
			<figcaption>Diagramas de acordes básicos: C, G, D, Em, Am.</figcaption>
		</figure>
	</section>

	<!-- Módulo 4 -->
	<section>
		<h2>4. Afinação</h2>
		<p>O violão possui afinação padrão: E - A - D - G - B - E.</p>
		<figure>
			<img src="imagens/afinacao.png" alt="Diagrama educativo da afinação padrão do violão" />
			<figcaption>Afinação padrão das cordas: E - A - D - G - B - E.</figcaption>
		</figure>
	</section>

	<!-- Módulo 5 -->
	<section>
		<h2>5. Tablaturas</h2>
		<p>Tablaturas são representações gráficas que indicam onde tocar as notas no braço do violão.</p>
		<figure>
			<img src="imagens/tablatura.png" alt="Representação didática de tablatura de violão" />
			<figcaption>Exemplo de tablatura com seis linhas e números indicando casas.</figcaption>
		</figure>
	</section>

	<!-- Módulo 6 -->
	<section>
		<h2>6. Partituras</h2>
		<p>A partitura é a forma tradicional de leitura musical, utilizando o pentagrama.</p>
		<figure>
			<img src="imagens/partitura.png" alt="Ilustração simples de partitura com notas básicas" />
			<figcaption>Exemplo de partitura em pentagrama com notas simples.</figcaption>
		</figure>
	</section>

	<!-- Módulo 7 -->
	<section>
		<h2>7. Compassos</h2>
		<p>O compasso organiza a música em tempos. Exemplos: 4/4 e 3/4.</p>
		<figure>
			<img src="imagens/compasso.png" alt="Diagrama instrucional de compassos 4/4 e 3/4" />
			<figcaption>Exemplo de compassos 4/4 e 3/4 em partitura.</figcaption>
		</figure>
	</section>

	<!-- Módulo 8 -->
	<section>
		<h2>8. Pausas Musicais</h2>
		<p>As pausas indicam momentos de silêncio na música.</p>
		<figure>
			<img src="imagens/pausas.png" alt="Ilustração instrucional mostrando pausas musicais" />
			<figcaption>Exemplos: pausa inteira, meia, semínima, colcheia.</figcaption>
		</figure>
	</section>

	<!-- Módulo 9 -->
	<section>
		<h2>9. Duração das Notas</h2>
		<p>Cada nota tem uma duração específica: inteira, meia, semínima, colcheia, semicolcheia.</p>
		<figure>
			<img src="imagens/duracao-notas.png" alt="Diagrama educativo de notas musicais com diferentes durações" />
			<figcaption>Notas com diferentes durações representadas em símbolos.</figcaption>
		</figure>
	</section>

</body>

</html>

</html>


</html>