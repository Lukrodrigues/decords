<!DOCTYPE html>
<?php
header('Content-Type: text/html; charset=utf-8');
session_start();
?>
<html lang="pt-br">

<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Decords Musica e Teoria</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Decords Música e Teoria">
	<meta name="" content="Luciano Moraes Rodrigues">
	<link rel="icon" href="img/favicon-96x96.png">
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/bootstrap-theme.min.css" rel="stylesheet">
	<link href="css/theme.css" rel="stylesheet">
	<script src="js/document.min.js"></script>
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>

	<!-- Support partitura -->
	<script src="js/partitura/vexflow-min.js"></script>
	<script src="js/partitura/underscore-min.js"></script>
	<script src="js/partitura/jquery.js"></script>
	<script src="js/partitura/tabdiv-min.js"></script>
	<!-- Support partitura -->

	<style>
		#continua {
			display: none;
		}
	</style>

	<script type="text/javascript">
		function escolha() {
			var nnn = getRadioValor('res');
			document.getElementById('inf').value = nnn;
			//alert("ola"); 
		}

		function getRadioValor(name) {
			var rads = document.getElementsByName(name);

			for (var i = 0; i < rads.length; i++) {
				if (rads[i].checked) {
					return rads[i].value;
				}

			}

			return null;
		}

		function finaliza() {
			$.ajax({
				type: "POST",
				url: "valida_exercicio.php",
				data: {
					escolha: $('#inf').val(),
					resposta: $('#resp').val(),
					cod: $('#usr').val(),
					exe: $('#exe').val()
				},
				success: function(data) {
					$('#resolucao').html(data);
					termina();
				}
			});
		}

		function termina() {
			$('#questao').hide();
			//$('#continua').show();
		}
	</script>

</head>

<body role="document">
	</head>

	<body role="document">

		<!-- Fixed navbar -->
		<nav class="navbar navbar-inverse navbar-fixed-top">
			<div class="row">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="index.php">Decords</a>
				</div>
				<div id="navbar" class="navbar-collapse collapse">
					<ul class="nav navbar-nav">
						<li class="active"><a href="iniciantes.php">voltar</a></li>
						<div class="container theme showcase" role="main">
		</nav>
		<div class="page-header">
			<h1>Exercício:</h1>
		</div>
		<nav class="navbar navbar-default navbar" role="navigation">
			<div class="container-fluid">
				<div class="row-fluid">
					<div class="box-content">
						<div class="form-horizontal">

							<body>
								<?php
								// Conclusão do exercício
								echo '<div id="resolucao"></div>';

								// Configurações iniciais
								header('Content-Type: text/html; charset=utf-8');
								if (session_status() === PHP_SESSION_NONE) {
									session_start();
								}
								$cod = isset($_GET['id']) ? intval($_GET['id']) : 0; // Sanitiza o ID do exercício
								$codd = isset($_SESSION['AlunoId']) ? intval($_SESSION['AlunoId']) : 0;

								// Conexão com o banco de dados
								include_once("conexao.php");

								// Verifica se a conexão foi estabelecida corretamente
								if ($conn->connect_error) {
									die("Falha na conexão: " . $conn->connect_error);
								}

								// Define o conjunto de caracteres para UTF-8
								$conn->set_charset("utf8");

								// Consulta SQL
								$sql = "SELECT * FROM exercicios WHERE id = ?";
								$stmt = $conn->prepare($sql);
								$stmt->bind_param("i", $cod);
								$stmt->execute();
								$result = $stmt->get_result();

								if ($result->num_rows > 0) {
									while ($row = $result->fetch_assoc()) {
										$pergunta = $row['pergunta'];
										$tablatura = $row['tablatura'];
										$dica = $row['dica'];
										$a = $row['a'];
										$b = $row['b'];
										$c = $row['c'];
										$d = $row['d'];
										$resp = $row['resposta'];

										echo '
        <div id="questao">
            <div class="form-group">
                <label>Enunciado</label>
                <textarea readonly class="form-control" rows="3">' . htmlspecialchars($pergunta, ENT_QUOTES, 'UTF-8') . '</textarea>
                <div class="vex-tabdiv" width=500 scale=1.0 editor="false" editor_height=100>
                    ' . htmlspecialchars($tablatura, ENT_QUOTES, 'UTF-8') . '
                </div> 
            </div>
            <div class="form-group">
                <label>Opções</label>
                <div class="radio">
                    <label>
                        <input type="radio" name="res" id="a" value="a" onclick="escolha()">a - ' . htmlspecialchars($a, ENT_QUOTES, 'UTF-8') . '
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input type="radio" name="res" id="b" value="b" onclick="escolha()">b - ' . htmlspecialchars($b, ENT_QUOTES, 'UTF-8') . '
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input type="radio" name="res" id="c" value="c" onclick="escolha()">c - ' . htmlspecialchars($c, ENT_QUOTES, 'UTF-8') . '
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input type="radio" name="res" id="d" value="d" onclick="escolha()">d - ' . htmlspecialchars($d, ENT_QUOTES, 'UTF-8') . '
                    </label>
                </div>
                <div class="form-actions"></br>
                    <button id="envio" type="submit" class="btn btn-primary" onclick="finaliza()">Responder</button>
                    <button class="btn btn-danger">Dica: ' . htmlspecialchars($dica, ENT_QUOTES, 'UTF-8') . '</button>
                </div>
            </div>
        </div>
        <input type="hidden" name="inf" id="inf" value="nulo">
        <input type="hidden" name="resp" id="resp" value="' . htmlspecialchars($resp, ENT_QUOTES, 'UTF-8') . '">
        <input type="hidden" name="usr" id="usr" value="' . htmlspecialchars($codd, ENT_QUOTES, 'UTF-8') . '">
        <input type="hidden" name="exe" id="exe" value="' . htmlspecialchars($cod, ENT_QUOTES, 'UTF-8') . '">
        ';
									}
								} else {
									echo "Nenhum exercício encontrado.";
								}

								// Fecha a conexão
								$stmt->close();
								$conn->close();
								?>

						</div>
		</nav>
	</body>

</html>