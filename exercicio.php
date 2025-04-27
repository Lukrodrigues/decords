<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
session_start();

// Conexão com o banco de dados
$servername = "localhost";
$dbname     = "decords_bd";
$username   = "root";
$password   = "";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
	die("Erro na conexão: " . $conn->connect_error);
}

// Verificação de sessão
if (!isset($_SESSION['aluno_logado']) || !$_SESSION['aluno_logado']) {
	$redirect = urlencode("exercicio.php?id=" . ($_GET['id'] ?? ''));
	header("Location: login_aluno.php?redirect=$redirect");
	exit;
}

// Validação do ID do exercício
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
	header("Location: tutorial-01.php");
	exit;
}

// Processamento do POST via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	header('Content-Type: application/json');
	try {
		if (!isset($_POST['id_exercicios'], $_POST['resposta'])) {
			throw new Exception('Selecione uma resposta antes de enviar!');
		}
		// Busca a resposta correta do exercício
		$stmt = $conn->prepare("SELECT resposta FROM exercicios WHERE id = ?");
		if (!$stmt) {
			throw new Exception('Erro ao preparar consulta: ' . $conn->error);
		}
		$stmt->bind_param("i", $_POST['id_exercicios']);
		$stmt->execute();
		$result = $stmt->get_result();
		$exercicioDB = $result->fetch_assoc();
		$stmt->close();

		$respostaCorreta = $exercicioDB['resposta'];
		$respostaUsuario  = $_POST['resposta'];
		$acertou = ($respostaUsuario === $respostaCorreta) ? 1 : 0;

		// Salva no banco de dados
		$stmt = $conn->prepare(
			"INSERT INTO alunos_exercicios
                (id_usuario, id_exercicios, status, resultado)
             VALUES (?, ?, 1, ?)
             ON DUPLICATE KEY UPDATE status = 1, resultado = VALUES(resultado)"
		);
		if (!$stmt) {
			throw new Exception('Erro ao preparar insert: ' . $conn->error);
		}
		$stmt->bind_param("iii", $_SESSION['aluno_id'], $_POST['id_exercicios'], $acertou);
		$stmt->execute();
		$stmt->close();

		echo json_encode([
			'success'  => true,
			'acertou'  => (bool)$acertou,
			'message'  => $acertou ? "✓ Resposta Correta!" : "✗ Resposta Incorreta!",
			'redirect' => 'iniciantes.php'
		]);
		exit;
	} catch (Exception $e) {
		http_response_code(500);
		echo json_encode(['error' => $e->getMessage()]);
		exit;
	}
}

// Busca dados do exercício para exibição
$stmt = $conn->prepare("SELECT * FROM exercicios WHERE id = ?");
if (!$stmt) {
	die("Erro ao preparar consulta de exercício: " . $conn->error);
}
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$exercicio = $result->fetch_assoc();
$stmt->close();

// Sanitização dos dados
$pergunta = htmlspecialchars($exercicio['pergunta'] ?? '');
$opcoes = [
	'a' => htmlspecialchars($exercicio['a'] ?? ''),
	'b' => htmlspecialchars($exercicio['b'] ?? ''),
	'c' => htmlspecialchars($exercicio['c'] ?? ''),
	'd' => htmlspecialchars($exercicio['d'] ?? '')
];

// Exibe tablatura/partitura a partir do exercício 11
$showTablatura = ($id >= 11);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Responder Exercício</title>
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<?php if ($showTablatura): ?>
		<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
		<script src="js/partitura/vexflow-min.js"></script>
		<script src="js/partitura/underscore-min.js"></script>
		<script src="js/partitura/tabdiv-min.js"></script>
	<?php endif; ?>
	<style>
		body {
			background-color: #f8f9fa;
		}

		.card {
			margin-top: 30px;
			box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
			border: none;
		}

		.btn-primary {
			background-color: #007bff;
			border-color: #007bff;
		}

		.btn-dica {
			margin-top: 10px;
		}

		.form-check {
			margin-bottom: 10px;
		}

		.btn-submit {
			margin-top: 20px;
			padding: 10px 20px;
			font-size: 16px;
		}

		.tablatura-container {
			margin: 20px 0;
		}
	</style>
</head>

<body>
	<nav class="navbar navbar-inverse navbar-fixed-top">
		<div class="container">
			<a class="navbar-brand" href="index.php">Decords Música</a>
			<ul class="nav navbar-nav navbar-right">
				<li><a href="?logout=true">Sair</a></li>
			</ul>
		</div>
	</nav>
	<div class="container">
		<div class="card">
			<div class="card-body"><br>
				<h2 class="card-title text-center">Responder Exercício</h2>
				<hr>
				<form id="formExercicio" method="POST">
					<div class="form-group">
						<h4>Pergunta:</h4>
						<p class="lead"><strong><?= $pergunta ?></strong></p>
					</div>

					<?php if ($showTablatura): ?>
						<div class="tablatura-container">
							<div id="tablatura"></div>
						</div>
					<?php endif; ?>

					<div class="form-group">
						<h5>Escolha uma opção:</h5>
						<?php foreach ($opcoes as $letra => $descricao): ?>
							<div class="form-check">
								<input class="form-check-input" type="radio" name="resposta" id="opcao-<?= $letra ?>" value="<?= $letra ?>" required>
								<label class="form-check-label" for="opcao-<?= $letra ?>"><?= $descricao ?></label>
							</div>
						<?php endforeach; ?>
					</div>

					<div class="form-group">
						<h6><strong>Dica: Caso dúvida clique abaixo</strong></h6>
						<a href="tutorial-01.php" class="btn btn-info btn-dica">Abrir Tutorial</a>
					</div>

					<input type="hidden" name="id_exercicios" value="<?= $id ?>">
					<button type="submit" class="btn btn-primary btn-submit btn-block">Enviar Resposta</button>
				</form>

				<div id="feedback" class="alert alert-dismissible mt-3" style="display: none;"></div>
			</div>
		</div>
	</div>

	<?php if ($showTablatura): ?>
		<script>
			$(function() {
				var VF = Vex.Flow;
				var div = document.getElementById("tablatura");
				var renderer = new VF.Renderer(div, VF.Renderer.Backends.CANVAS);
				renderer.resize(600, 200);
				var context = renderer.getContext();
				var stave = new VF.Stave(10, 10, 500);
				stave.addClef("treble").addTimeSignature("4/4");
				stave.setContext(context).draw();

				// Exemplo de notas, ajuste conforme BD se necessário
				var notes = [
					new VF.StaveNote({
						clef: "treble",
						keys: ["c/4"],
						duration: "q"
					}),
					new VF.StaveNote({
						clef: "treble",
						keys: ["d/4"],
						duration: "q"
					}),
					new VF.StaveNote({
						clef: "treble",
						keys: ["e/4"],
						duration: "q"
					}),
					new VF.StaveNote({
						clef: "treble",
						keys: ["f/4"],
						duration: "q"
					})
				];
				VF.Formatter.FormatAndDraw(context, stave, notes);
				$("#tablatura").tabdiv({
					tab: true,
					sheet: false
				});
			});
		</script>
	<?php endif; ?>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script>
		$(document).ready(function() {
			$('#formExercicio').on('submit', function(e) {
				e.preventDefault();
				var feedback = $('#feedback');
				feedback.hide().removeClass('alert-success alert-danger');

				$.ajax({
					type: 'POST',
					url: window.location.href,
					data: $(this).serialize(),
					dataType: 'json'
				}).done(function(response) {
					var msg = response.message;
					var acertou = response.acertou;
					feedback.text(msg).addClass(acertou ? 'alert-success' : 'alert-danger').fadeIn();
					if (response.redirect) {
						setTimeout(function() {
							window.location.href = response.redirect;
						}, 1500);
					}
				}).fail(function(xhr) {
					var err = xhr.responseJSON?.error || 'Erro desconhecido';
					feedback.text('Erro: ' + err).addClass('alert-danger').fadeIn();
				});
			});
		});
	</script>
</body>

</html>