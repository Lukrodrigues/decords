<!DOCTYPE html>
<?php
header('Content-Type: text/html; charset=utf-8');
session_start();

// Verificar se o usuário está autenticado
if (!isset($_SESSION['AlunoId'])) {
	header("Location: index.php");
	echo json_encode(['status' => 'error', 'message' => 'Usuário não autenticado.']);
	exit;
}
?>
<html lang="pt-br">

<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Decords Musica e Teoria</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="img/favicon-96x96.png" rel="icon">
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/bootstrap-theme.min.css" rel="stylesheet">
	<link href="css/theme.css" rel="stylesheet">
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<style>
		#continua {
			display: none;
		}

		.certo {
			background-color: green;
			color: white;
		}

		.errado {
			background-color: red;
			color: white;
		}
	</style>
	<script type="text/javascript">
		function escolha() {
			var valorSelecionado = getRadioValor('res');
			document.getElementById('inf').value = valorSelecionado;
		}

		function getRadioValor(name) {
			var radios = document.getElementsByName(name);
			for (var i = 0; i < radios.length; i++) {
				if (radios[i].checked) {
					return radios[i].value;
				}
			}
			return "";
		}

		function finaliza() {
			var idExercicio = document.getElementById('exe').value;
			var resposta = document.getElementById('inf').value;

			if (!resposta) {
				alert("Por favor, selecione uma resposta antes de enviar.");
				return;
			}

			$.ajax({
				type: "POST",
				url: "valida_exercicio.php",
				data: {
					id_exercicios: idExercicio,
					resposta: resposta
				},
				dataType: "json",
				success: function(response) {
					var respostaElement = document.getElementById('resposta');
					if (!respostaElement) {
						respostaElement = document.createElement('div');
						respostaElement.id = 'resposta';
						document.body.appendChild(respostaElement);
					}

					if (response.status === "success") {
						respostaElement.innerHTML = "Resposta enviada com sucesso!";
						respostaElement.classList.add(response.resultado === "certo" ? "certo" : "errado");
						setTimeout(() => {
							window.location.href = "exercicio.php?id=" + response.proximo_id;
						}, 2000);
					} else {
						respostaElement.innerHTML = "Erro: " + response.message;
					}
				},
				error: function(xhr, status, error) {
					alert("Erro ao enviar a resposta. Tente novamente.");
				}
			});
		}
	</script>
</head>

<body>
	<nav class="navbar navbar-inverse navbar-fixed-top">
		<div class="container">
			<a class="navbar-brand" href="iniciantes.php">Voltar</a>
		</div>
	</nav>

	<div class="container theme-showcase" style="margin-top: 70px;" role="main">
		<div class="page-header">
			<h1>Exercício:</h1>
		</div>
		<div class="box-content">
			<div class="form-horizontal">
				<?php
				$idExercicio = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
				$idAluno = $_SESSION['AlunoId'];

				if (!$idExercicio) {
					echo "<div class='alert alert-danger'>Exercício inválido.</div>";
					exit;
				}

				include_once("conexao.php");

				$sql = "SELECT id, pergunta, tablatura, dica, a, b, c, d, resposta FROM exercicios WHERE id = ?";
				$stmt = $conn->prepare($sql);
				$stmt->bind_param("i", $idExercicio);
				$stmt->execute();
				$result = $stmt->get_result();

				if ($result->num_rows > 0) {
					$exercicio = $result->fetch_assoc();
				?>
					<div id="pergunta">
						<div class="form-group">
							<label>Enunciado:</label>
							<textarea readonly class="form-control" rows="3"><?php echo htmlspecialchars($exercicio['pergunta'], ENT_QUOTES, 'UTF-8'); ?></textarea>
						</div>
						<div class="form-group">
							<label>Opções:</label>
							<?php foreach (['a', 'b', 'c', 'd'] as $opcao): ?>
								<div class="radio">
									<label>
										<input type="radio" name="res" value="<?php echo $opcao; ?>" onclick="escolha()">
										<?php echo $opcao . ' - ' . htmlspecialchars($exercicio[$opcao], ENT_QUOTES, 'UTF-8'); ?>
									</label>
								</div>
							<?php endforeach; ?>
						</div>
						<div class="form-actions">
							<button id="envio" type="button" class="btn btn-primary" onclick="finaliza()">Responder</button>
							<button class="btn btn-warning">Dica: <?php echo htmlspecialchars($exercicio['dica'], ENT_QUOTES, 'UTF-8'); ?></button>
						</div>
					</div>
					<input type="hidden" id="inf" value="">
					<input type="hidden" id="exe" value="<?php echo htmlspecialchars($idExercicio, ENT_QUOTES, 'UTF-8'); ?>">
					<div id="resposta"></div>
				<?php
				} else {
					echo "<div class='alert alert-danger'>Exercício não encontrado.</div>";
				}

				$stmt->close();
				$conn->close();
				?>
			</div>
		</div>
	</div>
</body>

</html>