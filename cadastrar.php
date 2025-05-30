<?php
// Iniciar sessão antes de qualquer saída
session_start();

// Gerar token CSRF seguro
if (!isset($_SESSION['csrf_token'])) {
	$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Cadastro - Decords</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!-- Bootstrap CSS -->
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<!-- jQuery CDN -->
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<style>
		.form-container {
			margin: 4rem auto;
			max-width: 500px;
			padding: 2rem;
			border-radius: 10px;
			box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
			background: #fff;
		}

		.navbar-fixed-top+.container {
			padding-top: 70px;
		}
	</style>
</head>

<body>
	<nav class="navbar navbar-inverse navbar-fixed-top">
		<div class="container">
			<div class="navbar-header">
				<a class="navbar-brand" href="index.php">Decords</a>
			</div>
			<ul class="nav navbar-nav navbar-right">
				<li>
					<a href="login.php" style="margin-right: 15px;">
						<span class="glyphicon glyphicon-log-in"></span> Login
					</a>
				</li>
			</ul>
		</div>
	</nav>
	<div class="container">
		<div class="form-container">
			<h2 class="mb-4 text-center">Cadastro de Usuário</h2>
			<div id="alertContainer"></div>
			<form id="cadastroForm">
				<!-- CSRF Token -->
				<input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
				<div class="mb-3">
					<label class="form-label">Nome Completo *</label>
					<input type="text" name="nome" class="form-control" required>
				</div>
				<div class="mb-3">
					<label class="form-label">E-mail *</label>
					<input type="email" name="email" class="form-control" required>
				</div>
				<div class="mb-3">
					<label class="form-label">Senha *</label>
					<input type="password" name="senha" class="form-control" required>
				</div>
				<div class="mb-4">
					<label class="form-label">Confirme a Senha *</label>
					<input type="password" name="senha2" class="form-control" required>
				</div>
				<button type="submit" class="btn btn-primary w-100">Cadastrar</button>
			</form>
		</div>
	</div>
	<script>
		$(document).ready(function() {
			$('#cadastroForm').submit(function(e) {
				e.preventDefault();
				var form = $(this);
				var button = form.find('button[type="submit"]');
				var alertContainer = $('#alertContainer');

				button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Cadastrando...');

				var formData = {
					nome: form.find('input[name="nome"]').val().trim(),
					email: form.find('input[name="email"]').val().trim(),
					senha: form.find('input[name="senha"]').val(),
					senha2: form.find('input[name="senha2"]').val(),
					csrf_token: form.find('input[name="csrf_token"]').val()
				};

				// Valida campos obrigatórios
				if (!formData.nome || !formData.email || !formData.senha) {
					alertContainer.html('<div class="alert alert-danger">Preencha todos os campos obrigatórios!</div>');
					button.prop('disabled', false).html('Cadastrar');
					return;
				}
				if (formData.senha !== formData.senha2) {
					alertContainer.html('<div class="alert alert-danger">As senhas não coincidem!</div>');
					button.prop('disabled', false).html('Cadastrar');
					return;
				}

				$.ajax({
					url: 'cad_novo_alunos.php',
					type: 'POST',
					contentType: 'application/json',
					data: JSON.stringify(formData),
					dataType: 'json',
					xhrFields: {
						withCredentials: true
					},
					success: function(data) {
						if (data.success) {
							alertContainer.html('<div class="alert alert-success">✅ ' +
								(data.message || "Aluno cadastrado com sucesso!") + ' Redirecionando...</div>');
							// Redireciona para login.php após 2 segundos
							setTimeout(function() {
								window.location.href = data.redirect || 'login.php';
							}, 2000);
						} else {
							alertContainer.html('<div class="alert alert-danger">❌ ' + data.message + '</div>');
						}
					},
					error: function(xhr, status, error) {
						alertContainer.html('<div class="alert alert-danger">❌ Erro: ' + error + '</div>');
					},
					complete: function() {
						button.prop('disabled', false).html('Cadastrar');
					}
				});
			});
		});
	</script>
</body>

</html>