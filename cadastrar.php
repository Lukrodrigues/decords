<!DOCTYPE html>
<?php
session_start();
// Gerar token CSRF
if (empty($_SESSION['csrf_token'])) {
	$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<html lang="pt-br">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Decords Musica e Teoria</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Decords Música e Teoria">
	<meta name="author" content="Luciano Moraes Rodrigues">
	<link rel="icon" href="img/favicon-96x96.png">
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/bootstrap-theme.min.css" rel="stylesheet">
	<link href="css/theme.css" rel="stylesheet">
	<style>
		.form-group {
			margin-bottom: 15px;
		}

		.btn-success {
			margin-top: 10px;
		}
	</style>
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script type="text/javascript">
		function validar_cadastro() {
			var nome = $("#nome").val();
			var email = $("#email").val();
			var senha = $("#senha").val();
			var senha2 = $("#senha2").val();

			if (nome == "") {
				alert("Campo nome é obrigatório");
				$("#nome").focus();
				return false;
			}
			if (email == "") {
				alert("Campo email é obrigatório");
				$("#email").focus();
				return false;
			}
			if (senha == "") {
				alert("Campo senha é obrigatório");
				$("#senha").focus();
				return false;
			}
			if (senha2 != senha) {
				alert("As senhas não coincidem");
				$("#senha2").focus();
				return false;
			}
			cadastrar();
			return false; // Impede o envio tradicional do formulário
		}

		function cadastrar() {
			var dadosajax = {
				'nome': $("#nome").val(),
				'email': $("#email").val(),
				'senha': $("#senha").val(),
				'senha2': $("#senha2").val(),
				'csrf_token': "<?php echo $_SESSION['csrf_token']; ?>"
			};

			$.ajax({
				url: 'cad_novo_alunos.php',
				data: dadosajax,
				type: 'POST',
				cache: false,
				error: function() {
					alert('Erro: Inserir Registo!!');
				},
				success: function(result) {
					if (result === "Usuário cadastrado com sucesso.") {
						alert(result); // Exibe a mensagem de sucesso
						window.location.href = "http://localhost/decords/login.php"; // Redireciona para a página de login
					} else {
						alert(result); // Exibe a mensagem de erro retornada pelo servidor
					}
				}
			});
		}
	</script>
</head>

<body>
	<nav class="navbar navbar-inverse navbar-fixed-top">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="Index.php">Decords</a>
		</div>
		<div id="navbar" class="navbar-collapse collapse">
			<ul class="nav navbar-nav">
				<li class="active"><a href="login.php">sair</a></li>
			</ul>
		</div>
	</nav>
	<div class="container">
		<div class="page-header">
			<h1>Cadastrar Usuario</h1>
		</div>
		<div class="row">
			<form class="form-horizontal" method="POST" onsubmit="return validar_cadastro()">
				<div class="form-group">
					<label for="nome" class="col-sm-2 control-label">Nome*:</label>
					<div class="col-sm-6">
						<input type="text" class="form-control" id="nome" name="nome" placeholder="Nome Completo" required>
					</div>
				</div>
				<div class="form-group">
					<label for="inputEmail3" class="col-sm-2 control-label">Email*:</label>
					<div class="col-sm-6">
						<input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
					</div>
				</div>
				<div class="form-group">
					<label for="inputPassword3" class="col-sm-2 control-label">Senha*:</label>
					<div class="col-sm-6">
						<input type="password" class="form-control" id="senha" name="senha" placeholder="Senha" required>
					</div>
				</div>
				<div class="form-group">
					<label for="inputPassword3" class="col-sm-2 control-label">Confirme Senha*:</label>
					<div class="col-sm-6">
						<input type="password" class="form-control" id="senha2" name="senha2" placeholder="Confirme Senha" required>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-10">
						<button type="submit" class="btn btn-success">Cadastrar</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</body>

</html>