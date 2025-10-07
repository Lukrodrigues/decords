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
	<title>Tutorial de Violão - Introdução</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
	<style>
		body {
			background-color: #f9f9f9;
			font-family: "Segoe UI", sans-serif;
			line-height: 1.7;
			color: #333;
			padding-bottom: 50px;
		}

		h1,
		h2,
		h3 {
			color: #222;
			font-weight: bold;
		}

		.card {
			background: #fff;
			border-radius: 10px;
			box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
			margin-bottom: 30px;
			padding: 20px;
		}

		figure {
			margin: 15px 0;
			text-align: center;
		}

		figcaption {
			font-size: 0.9em;
			color: #666;
		}

		.text-muted {
			font-size: 0.85em;
		}
	</style>
</head>

<body>
	<div class="container">
		<h1 class="text-center">Tutorial de Violão para Iniciantes</h1>
		<p class="lead text-center">
			Nesta introdução, você aprenderá sobre as partes do violão, técnicas de afinação, acordes básicos,
			leitura de tablaturas e compassos musicais. Este guia foi criado para facilitar seu primeiro contato com o instrumento.
		</p>

		<!-- MÓDULO 1: Corpo do Violão -->
		<section class="card">
			<h2>Corpo do Violão</h2>
			<figure>
				<img src="img/bracoviol.png" alt="Diagrama didático do braço do violão com labels" width="500" class="img-responsive center-block" />

				<figcaption>Partes principais do violão acústico.</figcaption>
			</figure>
			<p>O corpo do violão é a parte responsável pela propagação do som. Nele estão localizados a <b>boca</b>, o <b>rastilho</b> e o <b>cavalete</b>.
				Em violões elétricos, também podemos encontrar a saída de som e controles de volume e tonalidade.</p>
			<ul>
				<li><b>Boca:</b> Abertura central por onde o som se propaga.</li>
				<li><b>Rastilho:</b> Suporte onde se prendem as cordas, ajustando sua altura.</li>
				<li><b>Cavalete:</b> Mantém o rastilho fixo, garantindo a estabilidade.</li>
			</ul>
		</section>

		<!-- MÓDULO 2: Braço do Violão -->
		<section class="card">
			<h2>Braço do Violão</h2>
			<figure>
				<img src="img/bracoviol.png" alt="Diagrama didático do braço do violão com labels" width="500" class="img-responsive center-block" />

				<figcaption>Componentes principais do braço do violão.</figcaption>
			</figure>
			<p>O braço do violão é onde as notas e acordes são formados. Ele é composto por várias partes importantes:</p>
			<ul>
				<li><b>Mão:</b> Extremidade superior do braço, onde ficam as tarraxas.</li>
				<li><b>Tarraxas:</b> Utilizadas para afinar as cordas.</li>
				<li><b>Trastes:</b> Pequenas divisórias metálicas que determinam as casas.</li>
				<li><b>Casas:</b> Espaços entre os trastes, onde pressionamos as cordas para formar notas.</li>
				<li><b>Pestana:</b> Peça que apoia as cordas e define a separação entre o braço e a mão.</li>
			</ul>
		</section>

		<!-- MÓDULO 3: Acordes Básicos -->
		<section class="card">
			<h2>Acordes Básicos</h2>
			<p>Os acordes são a base de qualquer música. Eles representam combinações de notas que formam harmonia.</p>

			<h3>Acorde de Lá (A)</h3>
			<figure>
				<img src="img/la-violao.png" alt="Diagramas didáticos de acordes básicos C, G, D, Em, Am" width="500" class="img-responsive center-block" />
			</figure>

			<h3>Acorde de Si (B)</h3>
			<p>O acorde de Si maior exige o uso da pestana (dedo indicador pressionando várias cordas simultaneamente).</p>
			<figure>
				<img src="img/la-violao.png" alt="Diagramas didáticos de acordes básicos C, G, D, Em, Am" width="500" class="img-responsive center-block" />
			</figure>
		</section>

		<!-- MÓDULO 4: Técnicas de Afinação -->
		<section class="card">
			<h2>Técnicas de Afinação</h2>
			<p>A afinação é essencial para garantir um som agradável. Cada corda possui uma nota específica,
				e deve ser ajustada com o auxílio das tarraxas.</p>
			<ul>
				<li>Use um <b>afinador eletrônico</b> ou aplicativo de celular.</li>
				<li>A corda <b>5ª</b> (Lá - A) é a referência para afinar as demais.</li>
				<li>O ajuste é feito girando as tarraxas até o som coincidir com a nota desejada.</li>
			</ul>
			<figure>
				<img src="img/afinacao.png" alt="Diagrama educativo da afinação padrão do violão" width="500" class="img-responsive center-block" />
				<figcaption>Afinação padrão das cordas: E - A - D - G - B - E.</figcaption>
			</figure>
		</section>

		<!-- MÓDULO 5: Como Ler Tablatura -->
		<section class="card">
			<h2>Como Ler Tablatura</h2>
			<p>As tablaturas são uma forma simples e visual de representar as notas a serem tocadas no violão.</p>
			<p>Cada uma das seis linhas representa uma corda, e os números indicam as casas onde as cordas devem ser pressionadas.</p>
			<figure>
				<img src="img/tablatura2.png" alt="Representação didática de tablatura de violão" width="500" class="img-responsive center-block" />
				<figcaption>Representação didática de uma tablatura.</figcaption>
			</figure>
		</section>

		<!-- MÓDULO 6: Compassos Musicais -->
		<section class="card">
			<h2>Compassos Musicais</h2>
			<p>O compasso é a divisão da música em intervalos de tempo iguais. Ele ajuda a organizar o ritmo.</p>

			<h3>Compasso Simples</h3>
			<p>Possui tempos que podem ser divididos em duas partes iguais.</p>
			<figure>
				<img src="img/compasso.png" alt="Diagrama instrucional de compassos 4/4 e 3/4" width="500" class="img-responsive center-block" />
			</figure>

			<h3>Compasso Composto</h3>
			<p>Os tempos podem ser divididos em três partes iguais, geralmente usado em ritmos ternários.</p>
			<figure>
				<img src="assets/img/violao/compasso-composto.png" class="img-responsive center-block" alt="Compasso composto">
			</figure>

			<h3>Compasso 4/4</h3>
			<p>O compasso mais comum na música. Possui quatro tempos: forte, fraco, médio e fraco.</p>
			<figure>
				<img src="assets/img/violao/compasso-quaternario.png" class="img-responsive center-block" alt="Compasso 4/4">
			</figure>
		</section>

		<!-- MÓDULO 7: Pausas Musicais -->
		<section class="card">
			<h2>Pausas Musicais (Figuras de Silêncio)</h2>
			<p>As pausas são momentos de silêncio dentro da música. Elas possuem duração proporcional às notas correspondentes.</p>
			<figure>
				<img src="img/pausasmusicais.png" alt="Ilustração instrucional mostrando pausas musicais" width="500" class="img-responsive center-block" />
				<figcaption>Exemplos: pausa inteira, meia, semínima, colcheia.</figcaption>
			</figure>
		</section>

		<footer class="text-center text-muted">
			<hr>
			<p>Tutorial desenvolvido para iniciantes | Projeto: Aprendendo Violão</p>
		</footer>
	</div>
</body>

</html>