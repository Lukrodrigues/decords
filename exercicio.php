<!DOCTYPE html>
<?php
header('Content-Type: text/html; charset=utf-8');
session_start();

// Verificar se o usuário está autenticado
if (!isset($_SESSION['AlunoId'])) {
	header("Location: index.php");
	exit;
}
?>
<html lang="pt-br">

<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Decords Musica e Teoria</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="icon" href="img/favicon-96x96.png">
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/bootstrap-theme.min.css" rel="stylesheet">
	<link href="css/theme.css" rel="stylesheet">
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>

	<!-- Support partitura -->
	<script src="js/partitura/vexflow-min.js"></script>
	<script src="js/partitura/underscore-min.js"></script>
	<script src="js/partitura/tabdiv-min.js"></script>
	<!-- Support partitura -->
	<style>
		#continua {
			display: none;
		}
	</style>
	<script type="text/javascript">
		// Função escolha para capturar a escolha do usuário
		function escolha() {
			var nnn = getRadioValor('res'); // Obtém o valor da opção selecionada
			document.getElementById('inf').value = nnn; // Atualiza o valor do campo oculto com a escolha
		}

		// Função para obter o valor do radio selecionado
		function getRadioValor(name) {
			var rads = document.getElementsByName(name); // Busca todos os radios com o nome "res"
			for (var i = 0; i < rads.length; i++) {
				if (rads[i].checked) {
					return rads[i].value; // Retorna o valor da opção selecionada
				}
			}
			return null; // Caso nenhuma opção seja selecionada, retorna null
		}

		let idExercicio = $('#exe').val();
		let resposta = $('#inf').val();


		if (!idExercicio || !resposta) {
			alert("Por favor, selecione uma resposta e tente novamente.")
			return false;
		}

		function finaliza() {
			console.log('Função finaliza chamada');
			$.ajax({
				type: "POST",
				url: "valida_exercicio.php",
				data: {
					id_exercicios: $('#exe').val(),
					resposta: $('#inf').val()
				},
				success: function(data) {
					console.log(data); // Exibe a resposta do servidor
				},
				error: function() {
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
				// Obter o ID do exercício
				$idExercicio = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
				$idAluno = $_SESSION['AlunoId'];

				if (!$idExercicio) {
					echo "<div class='alert alert-danger'>Exercício inválido.</div>";
					exit;
				}

				include_once("conexao.php");

				// Buscar o exercício
				$sql = "SELECT pergunta, tablatura, dica, a, b, c, d, resposta FROM exercicios WHERE id = ?";
				$stmt = $conn->prepare($sql);
				$stmt->bind_param("i", $idExercicio);
				$stmt->execute();
				$result = $stmt->get_result();

				if ($result->num_rows > 0) {
					$exercicio = $result->fetch_assoc();
					echo '
                        <div id="questao">
                            <div class="form-group">
                                <label>Enunciado:</label>
                                <textarea readonly class="form-control" rows="3">' . htmlspecialchars($exercicio['pergunta'], ENT_QUOTES, 'UTF-8') . '</textarea>
                                <div class="vex-tabdiv" width=500 scale=1.0 editor="false" editor_height=100>
                                    ' . htmlspecialchars($exercicio['tablatura'], ENT_QUOTES, 'UTF-8') . '
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Opções:</label>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="res" value="a" onclick="escolha()">a - ' . htmlspecialchars($exercicio['a'], ENT_QUOTES, 'UTF-8') . '
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="res" value="b" onclick="escolha()">b - ' . htmlspecialchars($exercicio['b'], ENT_QUOTES, 'UTF-8') . '
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="res" value="c" onclick="escolha()">c - ' . htmlspecialchars($exercicio['c'], ENT_QUOTES, 'UTF-8') . '
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="res" value="d" onclick="escolha()">d - ' . htmlspecialchars($exercicio['d'], ENT_QUOTES, 'UTF-8') . '
                                    </label>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button id="envio" type="button" class="btn btn-primary" onclick="finaliza()">Responder</button>
                                <button class="btn btn-warning">Dica: ' . htmlspecialchars($exercicio['dica'], ENT_QUOTES, 'UTF-8') . '</button>
                            </div>
                        </div>
                        <input type="hidden" id="inf" value="">
                        <input type="hidden" id="resp" value="' . htmlspecialchars($exercicio['resposta'], ENT_QUOTES, 'UTF-8') . '">
                        <input type="hidden" id="usr" value="' . htmlspecialchars($idAluno, ENT_QUOTES, 'UTF-8') . '">
                        <input type="hidden" id="exe" value="' . htmlspecialchars($idExercicio, ENT_QUOTES, 'UTF-8') . '">
                        <div id="resolucao"></div>
                    ';
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