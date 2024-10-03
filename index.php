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
	<link rel="icon" href="img/favicon.ico">
	<link href="css/style.css" rel="stylesheet">
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/carousel.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<!--<link rel="stylesheet" type="text/css" href="engine1/style.css" />-->
	<script type="text/javascript" src="js/jquery.js"></script>
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/ie-emulation-modes-warning.js"></script>
	<style type="text/css">
		p {
			text-indent: 50px;
			text-align: justify;
		}
	</style>

</head>

<body>
	<nav class="navbar navbar-inverse navbar" role="navigation">
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
								<li><a href="login.php">Sou Aluno/Usuario</a></li>
								<li><a href="login_professor.php">Sou Professor</a></li>
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
	</nav>



	<body style="margin:auto">
		<!-- Start WOWSlider.com BODY section -->
		<!-- End WOWSlider.com BODY section -->
		<div class="col-lg-12">
			<div id="wowslider-container1">
				<div class="ws_images">
					<ul>
						<li><img src="img/foto32.jpg" alt="foto32" title="" id="wows1_0" /></li>
						<li><img src="img/foto36.jpg" alt="foto36" title="" id="wows1_1" /></li>
						<li><img src="img/foto40.jpg" alt="foto40" title="" id="wows1_2" /></li>
					</ul>
				</div>
				<div class="ws_bullets">
					<div>
						<a href="#" title=""><span><img src="foto32.jpg" alt="foto32" />1</span></a>
						<a href="#" title=""><span><img src="foto36.jpg" alt="foto36" />2</span></a>
						<a href="#" title=""><span><img src="foto40.jpg" alt="foto40" />3</span></a>
					</div>
				</div>
				<div class="ws_script" style="position:absolute;left:-99%">jquery carousel</a></div>
				<div class="ws_shadow"></div>
			</div>
		</div>
		<script type="text/javascript" src="js/wowslider.js"></script>
		<script type="text/javascript" src="js/script.js"></script>


		<div class="container inicial">
			<div class="row">
				<div class="col-lg-4 text-left">
					<img class="img-responsive img-circle" src="img/foto14.jpg" width="140" height="140">
					<h2>Historico</h2>
					<p>O projeto tem como público-alvo os estudantes da Escola Ministro Rubem Carlos Ludwig (Bairro Mathias Velho, Canoas. RS). Desenvolve o Ensino Musical através do Violão, objetivando contribuir para inserção da música na escola com melhor qualidade, desenvolvimento técnico de violonistas de vários estilos musicais, bem como interação e habilidades sociais.
					</p>
					<p><a class="btn btn-success" href="historico.php" role="button">Ver Detalhes &raquo;</a></p>
				</div><!-- /.col-lg-4 -->
				<div class="col-lg-4 text-center">
					<img class="img-responsive img-circle" src="img/foto24.jpg" alt="Generic placeholder image" width="140" height="140">
					<h2>Professor</h2>
					<p>O Projeto é conduzido pelo Professor José A. Homrich que é Licenciado em Música pela UFRGS e pós graduando em Educação Inclusiva. Músico, professor de música, educador, instrumentista, cantor, compositor, arranjador, poeta e produtor musical. Nascido em Sobradinho, RS teve seus primeiros contatos com música muito cedo, aos 6 anos, em uma igreja católica, na qual cantava e por causa das inúmeras demontrações de “talento musical” (musicalidade) ganhou seu primeiro violão, nesta mesma época.</p>
					<p><a class="btn btn-success" href="professor.php" role="button">Ver Detalhes &raquo;</a></p>
					<p class="pull-center"><a href="#"><b>Back to top</b></a></p>
					</br></br></br></br>
				</div><!-- /.col-lg-4 -->
				<div class="col-lg-4 text-right">
					<img class="img-responsive img-circle align=" center"" src="img/foto22.jpg" alt="Generic placeholder image" width="140" height="140">
					<h2>Exercicios</h2>
					<p>Para utilizar para entendimento do tutorial-01 e conhecer um pouco de teoria musical para violao</p>
					<p><a class="btn btn-success" href="login.php" role="button">Ver Detalhes &raquo;</a></p>
				</div><!-- /.col-lg-4 -->
			</div>
		</div>

		<footer>
			<h2>
				Mapa do site:
			</h2>
			<h4>Categorias<href=""</h4>
					<p>&copy; 2016 Luciano Moraes Rodrigues. &middot;</a></p>
		</footer>


	</body>

</html>