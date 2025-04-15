<!DOCTYPE html>
<?php
session_start();
include_once("conexao.php");
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
	<script src="js/jquery.min.js"></script>

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
					<li class="active"><a href="administrativo.php">Administrativo</a></li>
					<li class="active"><a href="acompanhamento.php">Acompanhamento</a></li>
					<li class="active"><a href="tuto.php">Tutorial</a></li>
					<li class="active"><a href="lista_alunos.php">Lista Alunos</a></li>
					<li class="active"><a href="administrativo.php">Voltar</a></li>
					<li class="active"><a href="login.php">Sair</a></li>
					<div class="container theme showcase" role="main">
		</nav>
		<div class="page-header">
			<h1>Acompanhamento das Atividades:</h1>
		</div>

		<div class="container-fluid">
			<form class="form-horizontal" role="form">
				<div class="form-group">
					<ul class="resultados">
					</ul>
					<?php
					include_once("conexao.php");

					$aux = 1; // Inicializa a variável de controle

					while ($aux <= 3) {
						// Prepara a consulta SQL de forma segura
						$stmt = $conn->prepare("SELECT COUNT(*) FROM exercicios WHERE nivel = ?");
						$stmt->bind_param("i", $aux); // Vincula o parâmetro `$aux` como inteiro
						$stmt->execute();
						$stmt->bind_result($result); // Obtém o resultado da consulta
						$stmt->fetch();
						$stmt->close(); // Fecha o statement

						// Exibe o resultado
						switch ($aux) {
							case 1:
								echo '<label class="control-label col-sm-2">Iniciante: ' . $result . '</label>';
								break;
							case 2:
								echo '<label class="control-label col-sm-2">Intermediário: ' . $result . '</label>';
								break;
							case 3:
								echo '<label class="control-label col-sm-2">Avançado: ' . $result . '</label>';
								break;
						}

						$aux++; // Incrementa o controle do loop
					}

					// Fecha a conexão com o banco de dados
					//$conn->close();
					?>


				</div>
			</form>
		</div>
		<div class="col-lg-12">
			<h2>Acompanhamento do Aluno</h2>

			<div class="panel panel-default">
				<div class="panel-heading">
					<form action="lista_alunos.php" method="post">
						<input type="search" id="pesquisar" name="pesquisar" placeholder="Pesquisar" required="required" maxlength="500" />
						<input type="submit" value="Pesquisar" />
					</form>
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
									<th>Exercicio Atual:</th>
									<th>Certos:</th>
									<th>Email:</th>
								</tr>

							</thead>
							<tbody>
								<?php
								include_once("conexao.php");

								// Obtém o termo pesquisado de forma segura
								$pesquisar = isset($_POST['pesquisar']) ? trim($_POST['pesquisar']) : '';

								// Consulta para buscar alunos com base no termo pesquisado
								$stmt = $conn->prepare("
    SELECT a.nome, a.email, a.nivel, b.data_termino 
    FROM alunos a 
    JOIN alunos_exercicios b ON a.id = b.id_usuario 
    WHERE a.nome LIKE ?
");
								$pesquisarLike = "%$pesquisar%";
								$stmt->bind_param("s", $pesquisarLike);
								$stmt->execute();
								$result = $stmt->get_result();
								$rowCount = $result->num_rows;

								// Exibindo o número de resultados
								if ($rowCount > 0) {
									echo "<h2>Resultados da pesquisa:</h2>";
									while ($row = $result->fetch_assoc()) {
										echo "<p>Nome: {$row['nome']} | Email: {$row['email']} | Nível: {$row['nivel']} | Data Término: {$row['data_termino']}</p>";
									}
								} else {
									echo "<p>Nenhum resultado encontrado para '$pesquisar'.</p>";
								}
								$stmt->close();

								// Consulta para listar todos os exercícios ordenados por data de término
								$sql4 = "
    SELECT a.nome, a.nivel, a.email, e.id_exercicios, e.data_termino, e.resultado, e.id_usuario 
    FROM alunos a 
    JOIN alunos_exercicios e ON a.id = e.id_usuario 
    ORDER BY e.data_termino DESC
";
								$queryResult4 = $conn->query($sql4);

								echo "<h2>Exercícios Recentes:</h2>";
								while ($row4 = $queryResult4->fetch_assoc()) {
									$nome = $row4['nome'];
									$nivel = $row4['nivel'];
									$exercicios = $row4['id_exercicios'];
									$id_usuario = $row4['id_usuario'];
									$email = $row4['email'];
									$data = $row4['data_termino'];

									// Determina o nível do aluno
									$resultados = ($nivel == 1) ? "Iniciante" : (($nivel == 2) ? "Intermediário" : "Avançado");

									// Consulta para contar os acertos do aluno
									$stmtAcertos = $conn->prepare("
        SELECT COUNT(*) 
        FROM alunos_exercicios 
        WHERE id_usuario = ? AND resultado = 1
    ");
									$stmtAcertos->bind_param("i", $id_usuario);
									$stmtAcertos->execute();
									$stmtAcertos->bind_result($acertos);
									$stmtAcertos->fetch();
									$stmtAcertos->close();

									// Exibe os dados do aluno
									echo "<p>Nome: $nome | Nível: $resultados | Email: $email | Data Término: $data | Acertos: $acertos</p>";
								}

								// Fecha a conexão




								echo	'<tr>
									<td>' . $nome . '</td>
									<td>' . $resultados . '</td>
									<td>' . date('d/m/Y', strtotime($data)) . '</td>
									<td>' . $exercicios . '</td>
									<td>' . $acertos . '</td>
									<td>' . $email . '</td>

								

								</tr>';
								$conn->close();
								?>

					</div>
				</div>
			</div>
	</body>