<!DOCTYPE html>
<?php
session_start();
if (!isset($_SESSION['AlunoEmail']) and !isset($_SESSION['AlunoSenha'])) {
	echo "é necessario login";
	header("Location: index.php");
	exit;
}
?>
<html lang="pt-br">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Decords Musica e Teoria</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Decords Música e Teoria">
	<meta name="" content="Luciano Moraes Rodrigues">
	<link rel="icon" href="img/favicon-96x96.png">
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/bootstrap-theme.min.css" rel="stylesheet">
	<link href="css/theme.css" rel="stylesheet">

	<script src="js/jquery.min.js"></script>
	<script src="js/document.min.js"></script>
	<script src="js/bootstrap.min.js"></script>

</head>

<body role="document">
	</head>

	<body role="document">

		<!-- Fixed navbar -->
		<nav class="navbar navbar-inverse navbar-fixed-top">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="Index.php"><img id="logo" src="img/foto22.jpg" width="100" height="30"></a>
			</div>
			<div id="navbar" class="navbar-collapse collapse">
				<ul class="nav navbar-nav">
					<li class="active"><a href="Administrativo.php">Administrativo</a></li>
					<li class="active"><a href="Acompanhamento.php">Acompanhamento</a></li>
					<li class="active"><a href="tuto.php">Tutorial</a></li>
					<li class="active"><a href="lista_alunos.php">Lista Alunos</a></li>
					<li class="active"><a href="Administrativo.php">Voltar</a></li>
					<li class="active"><a href="Login.php">Sair</a></li>
					<div class="container theme showcase" role="main">
		</nav>

		<h2>Lista de Aluno</h2>
		<div class="panel panel-default">
			<div class="panel-heading">
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table">
						<thead>
							<tr>
								<th>Nome do Aluno:</th>
								<th>Nível:</th>
								<th>Data de acesso:</th>
								<th>Email:</th>
							</tr>
						</thead>

						<?php
						include_once("conexao.php");

						// Recebe o termo pesquisado, garantindo que não seja nulo
						$pesquisar = isset($_POST['pesquisar']) ? trim($_POST['pesquisar']) : '';

						// Primeira consulta com filtro pelo termo pesquisado
						$sql = $conn->prepare("
    SELECT a.nome, a.email, a.nivel, b.data_termino
    FROM alunos a
    JOIN alunos_exercicios b ON a.id = b.id_usuario
    WHERE a.nome LIKE ?
");
						$pesquisar = "%$pesquisar%";
						$sql->bind_param("s", $pesquisar);
						$sql->execute();
						$result = $sql->get_result();
						$rowCount = $result->num_rows;

						// Segunda consulta para exibir todos os exercícios ordenados por data de término
						$sql4 = $conn->prepare("
    SELECT a.nome, a.nivel, e.id_exercicios, e.data_termino
    FROM alunos_exercicios e
    JOIN alunos a ON a.id = e.id_usuario
    ORDER BY e.data_termino ASC
");
						$sql4->execute();
						$queryResult4 = $sql4->get_result();

						// Exibindo os resultados da primeira consulta
						if ($rowCount > 0) {
							echo "<h2>Resultados encontrados:</h2>";
							while ($row = $result->fetch_assoc()) {
								$nome = $row['nome'];
								$email = $row['email'];
								$nivel = $row['nivel'];
								$data = $row['data_termino'];

								// Determina o nível do aluno
								$resultado = ($nivel == 1) ? "Iniciante" : (($nivel == 2) ? "Intermediário" : "Avançado");

								echo "<p>Nome: $nome | Email: $email | Nível: $resultado | Data de Término: $data</p>";
							}
						} else {
							echo "<p>Nenhum resultado encontrado.</p>";
						}

						// Exibindo os resultados da segunda consulta
						echo "<h2>Exercícios ordenados por data de término:</h2>";
						while ($row4 = $queryResult4->fetch_assoc()) {
							$nome = $row4['nome'];
							$nivel = $row4['nivel'];
							$data = $row4['data_termino'];

							$resultado = ($nivel == 1) ? "Iniciante" : (($nivel == 2) ? "Intermediário" : "Avançado");

							echo "<p>Nome: $nome | Nível: $resultado | Data de Término: $data</p>";
						}


						echo '<tr>
                                             <td>' . $nome . '</td>
											 <td>' . $nivel . '</td>
											 <td>' . date('d/m/Y', strtotime($data)) . '</td>
                                             <td>' . $email . '</td>
											 
											 
											
                             </tr>';



						// Fecha as conexões
						$sql->close();
						$sql4->close();
						$conn->close();
						?>
				</div>
			</div>
		</div>
	</body>