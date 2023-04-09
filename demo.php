<!DOCTYPE HTML>
<?php
header('Content-Type: text/html; charset=utf-8');
include_once("conexao.php");

?>
 <html lang="pt-br">
	<head>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
		<script src="editor.js"></script>
		<script type="text/javascript">
			$(document).ready(function() {
				$("#txtEditor").Editor();
			});
			$(document).ready( function() {
				$("#EditorContainername ?>").Editor("setText", "value ?>");
				});

</script>
		</script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
		<link href="editor.css" type="text/css" rel="stylesheet"/>
		<title>LineControl | v1.1.0</title>
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
							  <a class="navbar-brand" href="Index.php"><img id="logo" src="img/foto22.jpg" width="100" height="30"></a>
							</div>
							<div id="navbar" class="navbar-collapse collapse">
									  <ul class="nav navbar-nav">
										<li class="active"><a href="Administrativo.php">Administrativo</a></li>
										<li class="active"><a href="Acompanhamento.php">Acompanhamento</a></li>
										<li class="active"><a href="demo.php">Tutorial</a></li>
										<li class="active"><a href="lista_alunos.php">Lista Alunos</a></li>
										<li class="active"><a href="Login.php">Sair</a></li>
										<div class="container theme showcase" role="main">
								</nav>
									<div class="page-header"><br />
										<h1>Criar Tutorial:</h1>
									</div>
									<?php
											session_start();
											header('Content-Type: text/html; charset=utf-8');
											include_once("conexao.php");
													
													
													if (isset($_POST['enviar'])){
														$conteudo = isset($_POST['txtEditor']);
														$conteudo1 = $_POST['txtEditor'];
													//insere na BD                    
													$inserir = "INSERT INTO tutorial (txtEditor) VALUES ('$conteudo1')";
													 $result = mysql_query($inserir) or die(mysql_error());
														
													if($inserir >= '1'){
														echo "tudo certo";
													}
												}   
									?>
	
	
								<div class="container">
									<form id="form" name="form" action="demo.php" method="post">
										<div class="col-lg-12 nopadding">
										<textarea id="txtEditor" name="txtEditor" rows="5" cols="40"></textarea>
										</div>
										<input type="submit"  id="enviar" name="enviar" value="Cadastrar">
									</form>
								</div>
							<?php	
								$sql = "SELECT * FROM tutorial";
						$queryResult = mysql_query($sql) ;
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
								
								
								
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/editor.js"></script>

	<script language="javascript" type="text/javascript">

	/*$(document).ready( function() {
	$("#txtEditor").Editor();
	$("input:submit").click(function(){
	$('#txtEditorContent').text($('#txtEditor').Editor("getText"));
	});

	}); */
</script>
				
</body>
</html>

	