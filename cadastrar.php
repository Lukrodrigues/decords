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
	<link href="css/bootstrap.min.css" rel="stylesheet">
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
		document.addEventListener("DOMContentLoaded", function() {
			const form = document.getElementById("cadastroForm");
			form.addEventListener("submit", async function(e) {
				e.preventDefault();
				const button = form.querySelector('button[type="submit"]');
				const alertContainer = document.getElementById("alertContainer");
				button.disabled = true;
				button.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Cadastrando...';
				try {
					const formData = {
						nome: form.nome.value.trim(),
						email: form.email.value.trim(),
						senha: form.senha.value,
						senha2: form.senha2.value,
						csrf_token: form.csrf_token.value
					};
					// Validação dos campos obrigatórios
					if (!formData.nome || !formData.email || !formData.senha) {
						throw new Error("Preencha todos os campos obrigatórios!");
					}
					if (formData.senha !== formData.senha2) {
						throw new Error("As senhas não coincidem!");
					}
					// Requisição para o backend com inclusão dos cookies (sessão)
					const response = await fetch("cad_novo_alunos.php", {
						method: "POST",
						headers: {
							"Content-Type": "application/json"
						},
						credentials: "include",
						body: JSON.stringify(formData)
					});
					if (!response.ok) {
						throw new Error("Erro HTTP: " + response.status);
					}
					const data = await response.json();
					console.log("Resposta do servidor:", data);
					if (!data.success) {
						throw new Error(data.message || "Erro no cadastro");
					}
					// Exibe a mensagem de sucesso e aguarda 3 segundos antes de redirecionar
					alertContainer.innerHTML = `
						<div class="alert alert-success alert-dismissible fade show">
							✅ ${data.message} Redirecionando...
						</div>
					`;
					setTimeout(() => {
						window.location.href = data.redirect || "login.php";
					}, 3000);
				} catch (error) {
					alertContainer.innerHTML = `
						<div class="alert alert-danger alert-dismissible fade show">
							❌ ${error.message}
						</div>
					`;
					console.error("Erro:", error);
				} finally {
					button.disabled = false;
					button.innerHTML = "Cadastrar";
				}
			});
		});
	</script>
</body>

</html>