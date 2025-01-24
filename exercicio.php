<?php
session_start();
if (!isset($_SESSION['AlunoEmail']) || !isset($_SESSION['AlunoSenha'])) {
	header("Location: index.php");
	exit;
}

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
	header("Location: iniciantes.php");
	exit;
}

include_once("conexao.php");

$idExercicio = intval($_GET['id']);

// Consulta o exercício e suas opções diretamente da tabela `exercicios`
$sqlExercicio = "
    SELECT pergunta, resposta, a, b, c, d, dica, nivel 
    FROM exercicios 
    WHERE id = ?";
$stmtExercicio = $conn->prepare($sqlExercicio);
$stmtExercicio->bind_param("i", $idExercicio);
$stmtExercicio->execute();
$result = $stmtExercicio->get_result();

$exercicio = $result->fetch_assoc();
$stmtExercicio->close();

if (!$exercicio) {
	header("Location: iniciantes.php");
	exit;
}

$pergunta = $exercicio['pergunta'];
$dica = $exercicio['dica'];
$nivel = $exercicio['nivel'];
$opcoes = [
	'a' => $exercicio['a'],
	'b' => $exercicio['b'],
	'c' => $exercicio['c'],
	'd' => $exercicio['d']
];
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Responder Exercício</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Decords Música e Teoria">
	<link rel="icon" href="img/favicon-96x96.png">
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/style.css" rel="stylesheet">
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<style>
		body {
			background-color: #f8f9fa;
		}

		.card {
			margin-top: 30px;
			border: none;
			box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
		}

		.btn-primary {
			background-color: #007bff;
			border-color: #007bff;
		}

		.btn-primary:hover {
			background-color: #0056b3;
			border-color: #004085;
		}

		#feedback {
			margin-top: 20px;
		}

		.form-check-label {
			font-size: 1.2em;
			margin-left: 10px;
		}
	</style>
</head>

<body>
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
		<div class="container">
			<a class="navbar-brand" href="index.php">Decords Música</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarNav">
				<ul class="navbar-nav ml-auto">
					<li class="nav-item">
						<a class="nav-link" href="Login.php">Sair</a>
					</li>
				</ul>
			</div>
		</div>
	</nav>

	<div class="container">
		<div class="card">
			<div class="card-body">
				<h1 class="card-title text-center">Responder Exercício</h1>
				<hr>

				<form id="formExercicio" method="POST">
					<div class="form-group">
						<h4 class="card-text">Pergunta:</h4>
						<p class="lead"><strong><?php echo htmlspecialchars($pergunta, ENT_QUOTES, 'UTF-8'); ?></strong></p>
					</div>

					<div class="form-group">
						<h5 class="card-text">Escolha uma opção:</h5>
						<div id="opcoes">
							<?php foreach ($opcoes as $letra => $descricao) : ?>
								<div class="form-check">
									<input
										class="form-check-input"
										type="radio"
										name="resposta"
										id="opcao-<?php echo $letra; ?>"
										value="<?php echo $letra; ?>"
										required>
									<label class="form-check-label" for="opcao-<?php echo $letra; ?>">
										<?php echo htmlspecialchars($descricao, ENT_QUOTES, 'UTF-8'); ?>
									</label>
								</div>
							<?php endforeach; ?>
						</div>
					</div>

					<div class="form-group">
						<h6><strong>Dica:</strong></h6>
						<p class="text-muted"><?php echo htmlspecialchars($dica, ENT_QUOTES, 'UTF-8'); ?></p>
					</div>

					<input type="hidden" id="id_exercicios" name="id_exercicios" value="<?php echo $idExercicio; ?>">
					<button type="submit" class="btn btn-primary btn-lg btn-block">Enviar Resposta</button>
				</form>

				<div id="feedback" class="alert alert-dismissible" style="display: none;"></div>
			</div>
		</div>
	</div>

	<script>
		$(document).ready(function() {
			$('#formExercicio').on('submit', function(e) {
				e.preventDefault();

				$.ajax({
					url: 'valida_exercicio.php',
					type: 'POST',
					data: $(this).serialize(),
					dataType: 'json',
					success: function(response) {
						const feedback = $('#feedback');
						feedback.removeClass('alert-success alert-danger alert-warning');
						feedback.addClass(response.status === 'success' ? 'alert-success' : 'alert-danger');
						feedback.text(response.message).fadeIn();

						setTimeout(() => {
							window.location.href = 'iniciantes.php';
						}, 3000);
					},
					error: function() {
						alert('Ocorreu um erro inesperado. Por favor, tente novamente.');
					}
				});
			});
		});
	</script>
</body>

</html>