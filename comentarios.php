<?php
session_start();

//include_once("conexao.php");

?>

<!DOCTYPE html>

<html lang="pt-br">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Decords</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Decords Música e Teoria">
	<meta name="" content="Luciano Moraes Rodrigues">
	<link rel="icon" href="img/favicon.ico">
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<!--<link href="css/styles.css" rel="stylesheet">-->
	<link href="css/map.css" rel="stylesheet">
	<script src="js/jquery.min.js"></script>
	<!--<script src="js/scripts.js"></script>-->
	<script src="js/bootstrap.min.js"></script>
	<script src="js/ie-emulation-modes-warning.js"></script>
	<script src="http://maps.googleapis.com/maps/api/js?sensor=false&extension=.js&output=embed"></script>
	<script src="https://google.com/recaptcha/api.js"></script>
</head>

<script>
	function validarPost() {
		if (grecaptcha.getResponse() != "") return true;

		alert('Selecione a caixa de "não sou um robô"');
		return false;
	}
</script>

<body>
	<nav class="navbar navbar-inverse navbar" role="navigation">
		<div id="map-canvas"></div>
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
						<li class="dropdown"><a class="dropdown-toggle" href="#" data-toggle="dropdown">Categorias<b class="caret"></b></a>
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
		</div>
		</div>
	</nav>

	<div id="container">
		<div class="one_half">
			<h1>Sua opinião é importante para nós, comente sobre o site, exercícios e projeto:</h1></br> </br>
		</div>
	</div>

	<div class="col-lg-7 text-left">
		<form class="form-horizontal" action="envia_comentario.php" name="comentarios" method="post" onsubmit="return validarPost()">
			<div class="form-group">
				<label for="nome" class="col-sm-2 control-label">Nome*:</label>
				<div class="col-sm-6">
					<input type="text" class="form-control" id="nome" name="nome" placeholder="Nome Completo" required>
				</div>
			</div>
			<div class="form-group">
				<label for="nome" class="col-sm-2 control-label">Cidade*:</label>
				<div class="col-sm-6">
					<input type="text" class="form-control" id="cidade" name="cidade" placeholder="Cidade" required>
					<br />
				</div>
			</div>
			<div class="form-group">
				<label for="inputPassword3" class="col-sm-2 control-label">Mensagem*:</label>
				<div class="col-sm-6">
					<textarea class="form-control" rows="6" id="mensagem" name="mensagem" required></textarea>
				</div>
			</div>
			<div class="col-sm-offset-2 col-sm-10">
				<div class="g-recaptcha" data-sitekey="6Ldy9mIUAAAAANVnYWvVABDsId8Pnw_1kn4Hm85A"></div>
				<button type="submit" value="enviar comentarios" class="btn btn-success">Enviar</button>
			</div>
	</div>
	</form>
	<hr />

	<?php
	include_once("conexao.php");
	$sql = "SELECT * FROM comentarios_db ORDER BY data DESC";
	$result = mysqli_query($conn, $sql);

	if ($result) {
		$row_count = mysqli_num_rows($result);

		if ($row_count > 0) {
			while ($row = mysqli_fetch_assoc($result)) {
				echo "<strong>Nome:</strong> " . htmlspecialchars($row['nome']) . "<br>";
				echo "<strong>Cidade:</strong> " . htmlspecialchars($row['cidade']) . "<br>";
				echo "<strong>Mensagem:</strong><br>" . nl2br(htmlspecialchars($row['mensagem'])) . "<br>";
				echo "<strong>Data:</strong> " . date('Y/m/d/', strtotime($row['data'])) . "<hr>";
			}
		} else {
			echo "Nenhum comentário encontrado.";
		}
	} else {
		echo "Erro na consulta: " . mysqli_error($conn);
	}
	?>

</body>