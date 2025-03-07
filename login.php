<!DOCTYPE html>
<?php
session_start();

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
			<div class="row">
				<div class="navbar-header">
					<button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
						<span class="sr-only">Toggle navigation</span>
					</button>

					<a class="navbar-brand" href="Index.php">Decords</a>
				</div>
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav">
						<li class="active"><a href="Index.php">Home</a></li>
						<li class="dropdown"><a class="dropdown-toggle" href="#" data-toggle="dropdown">Categorias <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="Historico.php">Historico</a></li>
								<li class="divider"></li>
								<li><a href="Professor.php">Professor</a></li>
								<li class="divider"></li>
								<li><a href="Fotos.php">Fotos</a></li>
								<li class="divider"></li>
								<li><a href="Videos.php">Vídeos</a></li>
							</ul>
						</li>
						<li><a href="Contato.php">Contato/Agenda</a></li>
						<li class="dropdown"><a class="dropdown-toggle" href="#" data-toggle="dropdown">Login<b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="Login.php">Sou Aluno/Usuario</a></li>
								<li><a href="Login_professor.php">Sou Professor</a></li>
							</ul>
						</li>
						<li><a href="comentarios.php">Comentarios</a></li>
					</ul>
					<div class="nav navbar-form navbar-right">
						<a href="https://www.facebook.com/decordsoficial/" target="_blank"><img src="img/48/facebook.png"></a>
						<a href="https://www.youtube.com/channel/UCYlKeJvPE7jUXpZMNaVtG1A" target="_blank"><img src="img/48/youtube.png"></a>
						<a href="https://plus.google.com/u/0/+AdemirHomrich2/posts" target="_blank"><img src="img/48/google-plus.png"></a>
						<a href=" http://ademirhomrichmusica.blogspot.com.br" target="_blank"><img src="img/48/blogger.png"></a>

					</div>
				</div>
			</div>
		</div>
	</nav></br>

</body>

<body>


	<?php
	unset(
		$_SESSION['AlunoId'],
		$_SESSION['AlunoEmail'],
		$_SESSION['AlunoSenha'],
		$_SESSION['AlunoNome'],
		$_SESSION['AlunonivelAcesso']
	);
	?>

	<div class="container">
		<form class="form-signin" method="POST" action="login_aluno.php">
			<h2 class="form-signin-heading text-center">Área do Aluno</h2>

			<label for="inputEmail" class="sr-only">Email</label>

			<input type="text" name="email" class="form-control" placeholder="Email" required autofocus><br />
			<label for="inputPassword" class="sr-only">Senha</label>
			<input type="password" name="senha" class="form-control" placeholder="Senha" required>
			<div class="checkbox">
				<label>
					<input type="checkbox" value="remember-me"> Lembra-me
				</label>
			</div>
			<button class="btn btn-lg btn-primary btn-block" type="submit">Acessar</button><br />
			<form class="btn btn-primary btn-lg text-center" method="POST" action="cadastrar.php">
				<p>
					<span style="color:red">
						<label>*Caso você não tenha cadastro</label><br />
					</span>
					<a href="cadastrar.php" button type="button" class="btn btn-primary btn-lg text-center">Cadastrar</a>
				</p>
			</form>

			<p class="text-center text-danger">
				<?php
				if (isset($_SESSION['loginErro'])) {
					echo $_SESSION['loginErro'];
					unset($_SESSION['loginErro']);
					# code...
				}
				?>
			</p>


	</div>

	<script src="js/ie10-viewport-bug-workaround.js"></script>

</body>

</html>