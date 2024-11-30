<!DOCTYPE html>
<?php
header('Content-Type: text/html; charset=utf-8');
session_start();
if (!isset($_SESSION['AlunoEmail']) and !isset($_SESSION['AlunoSenha'])) {
	echo "é necessario login";
	header("Location: index.php");
	exit;
}


?>
<html lang="pt-br">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Decords Musica e Teoria</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Decords Música e Teoria">
	<meta name="" content="Luciano Moraes Rodrigues">
	<link rel="icon" href="img/favicon.ico">
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/style.css" rel="stylesheet">
	<link href="css/signin.css" rel="stylesheet">
	<link href="css/tabdiv.css" media="screen" rel="Stylesheet" type="text/css" />
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/ie-emulation-modes-warning.js"></script>

	<!-- Support partitura -->
	<script src="js/partitura/vexflow-min.js"></script>
	<script src="js/partitura/underscore-min.js"></script>
	<script src="js/partitura/jquery.js"></script>
	<script src="js/partitura/tabdiv-min.js"></script>
	<!-- Support partitura -->
</head>

<body>
	<nav class="navbar navbar-inverse navbar-fixed-top">
		<div class="container">
			<div class="row">
				<div class="navbar-header">
					<button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
						<span class="sr-only">Toggle navigation</span>
					</button>

					<a class="navbar-brand" href="index.php"><img id="logo" src="img/foto22.jpg" width="100" height="30"></a>
				</div>
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav">
						<li class="dropdown"><a class="dropdown-toggle" href="#" data-toggle="dropdown">Tutorial <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="tutorial-01.php">Tutorial-01</a></li>
								<li class="divider"></li>
								<li><a href="tutorial_02.php">Tutorial-02</a></li>
								<li class="divider"></li>
							</ul>
						<li class="dropdown"><a class="dropdown-toggle" href="#" data-toggle="dropdown">Exercicios <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="iniciantes.php">Iniciantes</a></li>
								<li class="divider"></li>
								<li><a href="intermediarios.php">Intermediarios</a></li>
								<li class="divider"></li>
								<li><a href="avancados.php">Avancados</a></li>
								<li class="divider"></li>
							</ul>
						<li class="active"><a href="Login.php">Sair</a></li>
						</li>
					</ul>
				</div>
			</div>
	</nav>

	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table-responsive">
						<thead>
							<tr>
								<th>Num/</th>
								<th>Pergunta/</th>
								<th>Concluído/</th>
								<th>Resultado/</th>
								<th>Ações/</th>
							</tr>
						</thead>
						<tbody>
							<?php
							header('Content-Type: text/html; charset=utf-8');
							if (session_status() === PHP_SESSION_NONE) {
								session_start();
							}
							$aluno = $_SESSION['AlunoId']; // ID do aluno
							include_once("conexao.php"); // Certifique-se de que sua conexão usa MySQLi

							// Verificar se a resposta foi enviada
							if (isset($_POST['escolha']) && isset($_POST['resposta']) && isset($_POST['exe'])) {
								$escolha = $_POST['escolha'];
								$resposta = $_POST['resposta'];
								$exe = $_POST['exe']; // Exercício atual

								// Verificar se a resposta está correta
								$resultado = ($escolha == $resposta) ? 1 : 2; // 1 = acertou, 2 = errou
								$status = 1; // Marcar como concluído

								// Preparar consulta para atualizar o status e resultado do exercício
								$sqlUpdate = "UPDATE alunos_exercicios 
                  SET resultado = ?, status = ?, data_termino = NOW() 
                  WHERE id_usuario = ? AND id_exercicios = ?";
								if ($stmtUpdate = $conn->prepare($sqlUpdate)) {
									$stmtUpdate->bind_param("iiii", $resultado, $status, $aluno, $exe);
									$stmtUpdate->execute();
									$stmtUpdate->close();
								} else {
									echo "Erro ao preparar a consulta: " . $conn->error;
								}
							}

							// Consultar os exercícios do nível atual
							$numeracao = 1;
							$sql = "SELECT id, pergunta FROM exercicios WHERE nivel = 1"; // Ajuste para o nível do aluno
							if ($queryResult = $conn->query($sql)) {
								while ($row = $queryResult->fetch_assoc()) {
									$ident = $row['id'];
									$pergunta = $row['pergunta'];
									$botao = "Fazer";
									$botaocor = "btn btn-warning";
									$link = "exercicio.php?id=" . $ident;

									// Consultar o status e resultado do exercício do aluno
									$sql4 = "SELECT resultado, status FROM alunos_exercicios WHERE id_usuario = ? AND id_exercicios = ?";
									if ($stmt4 = $conn->prepare($sql4)) {
										$stmt4->bind_param("ii", $aluno, $ident);
										$stmt4->execute();
										$stmt4->bind_result($resultado, $status);
										$stmt4->fetch();
										$stmt4->close();
									}

									// Definir status e resultado
									if ($status == 0) {
										$status = "Não";
									} else {
										$status = "Sim";
										$link = "#";
										$botao = "Concluído";
										$botaocor = "btn btn-success";
									}

									if ($resultado == 1) {
										$resultado = "Acertou";
									} else if ($resultado == 2) {
										$resultado = "Errou";
									} else {
										$resultado = "--";
									}

									// Exibir os exercícios
									echo '<tr>
                <td>' . $numeracao . '</td>
                <td>' . $pergunta . '</td>
                <td>' . $status . '</td>
                <td>' . $resultado . '</td>
                <td><a href="' . $link . '"><button type="button" class="' . $botaocor . '">' . $botao . '</button></a></td>
              </tr>';

									$numeracao++;
								}
							} else {
								echo "Erro na consulta de exercícios: " . $conn->error;
							}

							// Verificar se todos os exercícios foram concluídos para o próximo nível
							$nivelAtual = 1; // Atribuindo o nível atual à variável (pode ser dinâmico, dependendo do contexto)
							$stmtCheckNivel = $conn->prepare("SELECT COUNT(*) FROM alunos_exercicios ae 
                                  INNER JOIN exercicios e ON ae.id_exercicios = e.id
                                  WHERE ae.id_usuario = ? AND e.nivel = ? AND ae.status = 1");

							$stmtCheckNivel->bind_param("ii", $aluno, $nivelAtual); // Agora, passando $nivelAtual
							$stmtCheckNivel->execute();
							$stmtCheckNivel->bind_result($concluidos);
							$stmtCheckNivel->fetch();
							$stmtCheckNivel->close();


							$nivelAtual = $_SESSION['AlunoNivel']; // O nível pode variar, dependendo da sessão do aluno

							// Prepara a consulta para contar o total de exercícios no nível atual
							$stmtTotalExercicios = $conn->prepare("SELECT COUNT(*) FROM exercicios WHERE nivel = ?");
							$stmtTotalExercicios->bind_param("i", $nivelAtual); // Usando o nível atual da sessão

							// Executa a consulta
							$stmtTotalExercicios->execute();

							// Associa o resultado à variável
							$stmtTotalExercicios->bind_result($totalExercicios);

							// Recupera o valor
							$stmtTotalExercicios->fetch();

							// Fecha a consulta
							//$stmtTotalExercicios->close();

							if ($concluidos === $totalExercicios) {
								$_SESSION['AlunoNivel'] = 2; // Mudar para o próximo nível
								echo "<div class='alert alert-success'>Parabéns! Você concluiu o nível 1. Você foi promovido ao próximo nível!</div>";
							}
							?>

				</div>
</body>