<!DOCTYPE html>
<?php
session_start();

// Verifica se o usuário já está logado
if (isset($_SESSION['AlunoId'])) {
	header("Location: index.php"); // Redireciona para a página inicial
	exit;
}
?>

<html lang="pt-br">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Decords Música e Teoria</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Decords Música e Teoria">
	<meta name="author" content="Luciano Moraes Rodrigues">
	<link rel="icon" href="img/favicon-96x96.png">
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/bootstrap-theme.min.css" rel="stylesheet">
	<link href="css/theme.css" rel="stylesheet">
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<style>
		.form-container {
			max-width: 600px;
			margin: 0 auto;
			padding: 20px;
			background-color: #f9f9f9;
			border: 1px solid #ddd;
			border-radius: 5px;
			box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
		}

		.form-container h1 {
			text-align: center;
			margin-bottom: 20px;
		}

		.form-group label {
			font-weight: bold;
		}

		.btn-cadastrar {
			width: 100%;
			margin-top: 20px;
		}

		.alert {
			margin-top: 20px;
		}
	</style>
	<script>
		$(document).ready(function() {
			$("#cadastro").submit(function(event) {
				event.preventDefault(); // Evita o envio tradicional do formulário

				let nome = $("#nome").val().trim();
				let email = $("#email").val().trim();
				let senha = $("#senha").val().trim();
				let senha2 = $("#senha2").val().trim();

				// Validações
				if (nome === "") {
					alert("O campo nome é obrigatório.");
					$("#nome").focus();
					return;
				}
				if (email === "") {
					alert("O campo e-mail é obrigatório.");
					$("#email").focus();
					return;
				}
				if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
					alert("Por favor, insira um e-mail válido.");
					$("#email").focus();
					return;
				}
				if (senha === "") {
					alert("O campo senha é obrigatório.");
					$("#senha").focus();
					return;
				}
				if (senha.length < 6) {
					alert("A senha deve ter pelo menos 6 caracteres.");
					$("#senha").focus();
					return;
				}
				if (senha2 !== senha) {
					alert("As senhas não coincidem.");
					$("#senha2").focus();
					return;
				}

				// Envia os dados via AJAX
				$.ajax({
					url: "cad_novo_alunos.php",
					type: "POST",
					data: {
						nome: nome,
						email: email,
						senha: senha,
						senha2: senha2
					},
					dataType: "json", // Garante que a resposta seja tratada como JSON
					success: function(response) {
						if (response.status === "success") {
							alert(response.message);
							window.location.href = "login.php"; // Redireciona ao sucesso
						} else {
							alert(response.message); // Exibe a mensagem de erro
						}
					},
					error: function() {
						alert("Erro ao processar o cadastro. Tente novamente.");
					}
				});
			});
		});
	</script>
</head>

<body>
	<nav class="navbar navbar-inverse navbar-fixed-top">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
					aria-expanded="false" aria-controls="navbar">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="index.php">Decords</a>
			</div>
			<div id="navbar" class="navbar-collapse collapse">
				<ul class="nav navbar-nav">
					<li><a href="login.php">Sair</a></li>
				</ul>
			</div>
		</div>
	</nav>

	<div class="container">
		<div class="form-container">
			<h1>Cadastrar Usuário</h1>
			<form id="cadastro" class="form-horizontal">
				<div class="form-group">
					<label for="nome" class="col-sm-2 control-label">Nome*:</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" id="nome" name="nome" placeholder="Nome Completo" required>
					</div>
				</div>
				<div class="form-group">
					<label for="email" class="col-sm-2 control-label">E-mail*:</label>
					<div class="col-sm-10">
						<input type="email" class="form-control" id="email" name="email" placeholder="E-mail" required>
					</div>
				</div>
				<div class="form-group">
					<label for="senha" class="col-sm-2 control-label">Senha*:</label>
					<div class="col-sm-10">
						<input type="password" class="form-control" id="senha" name="senha" placeholder="Senha" required>
					</div>
				</div>
				<div class="form-group">
					<label for="senha2" class="col-sm-2 control-label">Confirme Senha*:</label>
					<div class="col-sm-10">
						<input type="password" class="form-control" id="senha2" name="senha2" placeholder="Confirme Senha" required>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-10">
						<button type="submit" class="btn btn-success btn-cadastrar">Cadastrar</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</body>

</html>