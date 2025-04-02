<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Conexão com o Banco de Dados
$servername = "localhost";
$dbname = "decords_bd";
$username = "root";
$password = "";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
	die("Falha na conexão: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$email = $_POST['email'] ?? '';
	$senha = $_POST['senha'] ?? '';

	if (empty($email) || empty($senha)) {
		$_SESSION['erro_login'] = "Preencha todos os campos!";
		header('Location: login_aluno.php');
		exit;
	}

	$sql = "SELECT id, nome, senha, nivel FROM alunos WHERE email = ?";
	$stmt = $conn->prepare($sql);

	if (!$stmt) {
		die("Erro na preparação da consulta: " . $conn->error);
	}

	$stmt->bind_param("s", $email);
	$stmt->execute();
	$result = $stmt->get_result();
	$aluno = $result->fetch_assoc();

	if ($aluno && password_verify($senha, $aluno['senha'])) {
		$_SESSION['aluno_logado'] = true;
		$_SESSION['aluno_id'] = $aluno['id'];
		$_SESSION['aluno_nome'] = $aluno['nome'];
		$_SESSION['aluno_nivel'] = $aluno['nivel'];

		// Redireciona para tutorial-01.php por padrão
		$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'tutorial-01.php';
		header("Location: $redirect");
		exit();
	} else {
		$_SESSION['erro_login'] = "Email ou senha incorretos!";
		header('Location: login_aluno.php');
		exit;
	}

	$stmt->close();
} else {
	$_SESSION['erro_login'] = "Requisição inválida.";
	header('Location: login_aluno.php');
	exit;
}

$conn->close();
?>



<!DOCTYPE html>
<html>

<head>
	<title>Login do Aluno</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>

<body>
	<div class="container mt-5">
		<div class="col-md-6 offset-md-3">
			<form class="form-signin" method="POST">
				<h2 class="text-center mb-4">Área do Aluno</h2>

				<?php if (isset($_SESSION['erro_login'])) : ?>
					<div class="alert alert-danger">
						<?php
						echo $_SESSION['erro_login'];
						unset($_SESSION['erro_login']); // Remove a mensagem após exibir
						?>
					</div>
				<?php endif; ?>

				<div class="form-group">
					<label for="inputEmail">Email</label>
					<input type="email" name="email" class="form-control" required autofocus>
				</div>

				<div class="form-group">
					<label for="inputPassword">Senha</label>
					<input type="password" name="senha" class="form-control" required>
				</div>

				<button class="btn btn-lg btn-primary btn-block" type="submit">Acessar</button>

				<div class="text-center mt-3">
					<a href="cadastrar.php" class="btn btn-link">Não tem conta? Cadastre-se</a>
				</div>
			</form>
		</div>
	</div>
</body>

</html>