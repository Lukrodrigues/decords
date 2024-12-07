<!DOCTYPE html>
<?php
session_start();
include_once("conexao.php");
?>
<html lang="pt-br">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Decords Musica e Teoria</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Decords Música e Teoria">
	<meta name="" content="Luciano Moraes Rodrigues">
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
			<div class="row">
				<div class="navbar-header">
					<button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
						<span class="sr-only">Toggle navigation</span>
					</button>

					<a class="navbar-brand" href="index.php"><img id="logo" src="img/foto22.jpg" width="100" height="40"></a>
				</div>
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav">
						<li class="dropdown"><a class="dropdown-toggle" href="#" data-toggle="dropdown">Categorias <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="historico.php">Historico</a></li>
								<li class="divider"></li>
								<li><a href="professor.php">Professor</a></li>
								<li class="divider"></li>
								<li><a href="fotos.php">Fotos</a></li>
								<li class="divider"></li>
								<li><a href="videos.php">Vídeos</a></li>
							</ul>
						</li>
						<li><a href="contato.php">Contato/Agenda</a></li>
						<li class="dropdown"><a class="dropdown-toggle" href="#" data-toggle="dropdown">Exercicios<b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="login.php">Sou Aluno</a></li>
								<li><a href="login.php">Sou Professor</a></li>
							</ul>
						</li>
						<li><a href="comentarios.php">Comentarios</a></li>

					</ul>
					<div class="nav navbar-form navbar-right">
						<a href="https://www.facebook.com/decordsoficial/" target="_blank"><img src="img/48/Facebook.png"></a>
						<a href="https://www.youtube.com/channel/UCYlKeJvPE7jUXpZMNaVtG1A" target="_blank"><img src="img/48/Youtube.png"></a>
						<a href="https://plus.google.com/u/0/+AdemirHomrich2/posts" target="_blank"><img src="img/48/Google-Plus.png"></a>
						<a href=" http://ademirhomrichmusica.blogspot.com.br" target="_blank"><img src="img/48/Blogger.png"></a>

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
		<form class="form-signin" method="POST" action="valida_login.php">
			<h2 class="form-signin-heading text-center">Área do Professor</h2>
			<label for="inputEmail" class="sr-only">Email</label>

			<input type="text" name="email" class="form-control" placeholder="Email" required autofocus><br />
			<label for="inputPassword" class="sr-only">Senha</label>
			<input type="password" name="senha" class="form-control" placeholder="Senha" required><br />
			<!--<div class="checkbox">
					  <label>
						<input type="checkbox" value="remember-me"> Lembra-me
					  </label>
					</div>-->
			<button class="btn btn-lg btn-primary btn-block" type="submit">Acessar</button><br /><br /><br />

			<p class="text-center text-danger">
				<?php
				if (isset($_SESSION['loginErro'])) {
					echo $_SESSION['loginErro'];
					unset($_SESSION['loginErro']);
					# code...
				}
				?>
			</p>


			<!-- </div>/container -->
			<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
			<!--<script src="js/ie10-viewport-bug-workaround.js"></script>
															</body>-->

			<!-- <div class="vex-tabdiv" width=500 scale=1.0 editor="false" editor_height=100>
							tabstave notation=false tablature=true
							notes 4-5-6/3 ## | 5-4-2/3 2/2
						 </div> -->

</body>

</html>