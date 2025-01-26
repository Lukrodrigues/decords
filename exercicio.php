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

// Consulta o exercício
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
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Responder Exercício</title>
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<script src="js/jquery.min.js"></script>
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

		.btn-dica {
			margin-top: 10px;
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
						<h6><strong>Dica: Caso dúvida clique abaixo</strong></h6>
						<a href="tutorial-01.php" class="btn btn-info btn-dica">Abrir Tutorial</a>
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
						feedback.removeClass('alert-success alert-danger');
						feedback.addClass(response.status === 'success' ? 'alert-success' : 'alert-danger');
						feedback.text(response.message).fadeIn();

						// Redireciona automaticamente para a página de iniciantes
						setTimeout(() => {
							window.location.href = 'iniciantes.php';
						}, 2000); // Tempo de 2 segundos antes do redirecionamento
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