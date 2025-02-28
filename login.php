<!DOCTYPE html>
<?php
session_start();
?>

<html lang="pt-br">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Decords Música e Teoria</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Decords Música e Teoria">
	<meta name="author" content="Luciano Moraes Rodrigues">
	<link rel="icon" href="img/favicon.ico">
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/style.css" rel="stylesheet">
	<link href="css/signin.css" rel="stylesheet">
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
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="index.php"><img id="logo" src="img/foto22.jpg" width="100" height="40" alt="Logo Decords"></a>
			</div>
			<div id="navbar" class="navbar-collapse collapse">
				<ul class="nav navbar-nav">
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">Categorias <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href="historico.php">Histórico</a></li>
							<li class="divider"></li>
							<li><a href="professor.php">Professor</a></li>
							<li class="divider"></li>
							<li><a href="fotos.php">Fotos</a></li>
							<li class="divider"></li>
							<li><a href="videos.php">Vídeos</a></li>
						</ul>
					</li>
					<li><a href="contato.php">Contato/Agenda</a></li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">Exercícios <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href="login.php">Sou Aluno/Usuário</a></li>
							<li><a href="login_professor.php">Sou Professor</a></li>
						</ul>
					</li>
					<li><a href="comentarios.php">Comentários</a></li>
				</ul>
				<div class="nav navbar-nav navbar-right">
					<a href="https://www.facebook.com/decordsoficial/" target="_blank"><img src="img/48/Facebook.png" alt="Facebook"></a>
					<a href="https://www.youtube.com/channel/UCYlKeJvPE7jUXpZMNaVtG1A" target="_blank"><img src="img/48/Youtube.png" alt="Youtube"></a>
					<a href="https://plus.google.com/u/0/+AdemirHomrich2/posts" target="_blank"><img src="img/48/Google-Plus.png" alt="Google Plus"></a>
					<a href="http://ademirhomrichmusica.blogspot.com.br" target="_blank"><img src="img/48/Blogger.png" alt="Blogger"></a>
				</div>
			</div>
		</div>
	</nav>

	<div class="container">
		<form class="form-signin" method="POST" action="login_aluno.php">
			<h2 class="form-signin-heading text-center">Área do Aluno</h2>
			<label for="inputEmail" class="sr-only">Email</label>
			<input type="text" name="email" class="form-control" placeholder="Email" required autofocus>
			<br>
			<label for="inputPassword" class="sr-only">Senha</label>
			<input type="password" name="senha" class="form-control" placeholder="Senha" required>
			<br>
			<button class="btn btn-lg btn-primary btn-block" type="submit">Acessar</button>
			<br>
			<p class="text-center">
				<span style="color:red">*Caso você não tenha cadastro</span><br>
				<a href="cadastrar.php" class="btn btn-success btn-lg">Cadastrar</a>
			</p>
			<p class="text-center text-danger">
				<?php
				if (isset($_SESSION['loginErro'])) {
					echo $_SESSION['loginErro'];
					unset($_SESSION['loginErro']);
				}
				?>
			</p>
		</form>
	</div>

	<script src="js/ie10-viewport-bug-workaround.js"></script>
</body>

</html>