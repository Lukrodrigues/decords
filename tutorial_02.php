<!DOCTYPE html>
<?php
session_start();
if (!isset ($_SESSION['AlunoEmail'])and !isset ($_SESSION['AlunoSenha'])){
echo "é necessario login";
header ("Location: index.php");
exit;
}
?>
<html lang="pt-br">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Decords</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Bootstrap --> 
	<link href="css/bootstrap.min.css" rel="stylesheet"> 
	<link href="css/style.css" rel="stylesheet">   
	<script src="js/jquery.min.js"></script>
	 <script src="js/bootstrap.min.js"></script>
</head>
<body>
	<nav class="navbar navbar-inverse navbar" role="navigation">
		<div class="container">
		<div class="row">
			<div class="navbar-header">
				<button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1"> 
					<span class="sr-only">Toggle navigation</span> 
				</button> 

				<a class="navbar-brand" href="Index.php"><img id="logo" src="img/foto22.jpg" width="100" height="30"></a>
			</div>
			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				<ul class="nav navbar-nav">
					<li class="dropdown"><a class="dropdown-toggle" href="#" data-toggle="dropdown">Tutorial <b class="caret"></b></a>
					<ul class="dropdown-menu">
					<li><a href="tutorial-01.php">Tutorial-01</a></li>
					<li class="divider"></li>
					<li><a href="tutorial_02.php">Tutorial-02</a></li>
					<li class="divider"></li>
				</ul>
					<li class="dropdown"><a class="dropdown-toggle" href="#" data-toggle="dropdown">Exercicios <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href="Iniciantes.php">Iniciantes</a></li>
							<li class="divider"></li>
							<li><a href="Intermediarios.php">Intermediarios</a></li>
							<li class="divider"></li>
							<li><a href="Avancados.php">Avancados</a></li>
							<li class="divider"></li>
						</ul>
						<li class="active"><a href="Login.php">Sair</a></li>
					</li>
				</ul>
			</div>
		</div>
	</div>
</nav>
	<body>
	<script>
	function bigImg(x) {
    x.style.height = "400px";
    x.style.width = "600px";
}

function normalImg(x) {
    x.style.height = "140px";
    x.style.width = "440px";
}
</script>
<body>

<h1 style="text-align:center">Introdução Violão:</h1>
<?php
// $cod= $_GET['id'];
//$codd=$_SESSION['AlunoId'];
include_once("conexao.php");

						$sql = "SELECT * FROM tutorial";
						$queryResult = mysql_query($sql) or die(mysql_error());
						while ($row = mysql_fetch_array($queryResult)) {
	     				// $conteudo = $row['editor1'];
						?>
						<?php
						echo html_entity_decode ($row['txtEditor']);?>
						<?php
						
						// echo'<div id="tutorial">					
											// <textarea class="form-control" rows="3">'.$conteudo.'</textarea>';	
						}
?>
</body>
