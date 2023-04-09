<!DOCTYPE html>
<?php
header('Content-Type: text/html; charset=utf-8');
session_start();
?>
<html lang="pt-br">
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" >
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>Decords Musica e Teoria</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">  
		<meta name="description" content="Decords Música e Teoria">
	    <meta name="" content="Luciano Moraes Rodrigues">
	    <link rel="icon" href="img/favicon-96x96.png"> 
	    <link href="css/bootstrap.min.css" rel="stylesheet">
		<link href="css/bootstrap-theme.min.css" rel="stylesheet"> 
		<link href="css/theme.css" rel="stylesheet">
		<script src="js/document.min.js"></script>
		<script src="js/jquery.min.js"></script>
	   <script src="js/bootstrap.min.js"></script>
	
		<!-- Support partitura -->
  <script src="js/partitura/vexflow-min.js"></script>
  <script src="js/partitura/underscore-min.js"></script>
  <script src="js/partitura/jquery.js"></script>
  <script src="js/partitura/tabdiv-min.js"></script>
		<!-- Support partitura -->

<style>
#continua{display:none;}
</style>
		
	<script type="text/javascript">	
function escolha(){
  var nnn = getRadioValor('res');
  document.getElementById('inf').value = nnn; 
 //alert("ola"); 
 }
 function getRadioValor(name){
  var rads = document.getElementsByName(name);
  
  for(var i = 0; i < rads.length; i++){
   if(rads[i].checked){
    return rads[i].value;
   }
  
  }
  
  return null;
 }
 function finaliza() {
  $.ajax({
    type: "POST",
    url: "valida_exercicio.php",
    data: {
      escolha: $('#inf').val(),
	  resposta: $('#resp').val(),
	  cod: $('#usr').val(),
	  exe: $('#exe').val()
    },
    success: function(data) {
      $('#resolucao').html(data);
	 termina();
    }
  });
}
function termina()
{
$('#questao').hide();
//$('#continua').show();
}

			
</script>	

	</head>
		<body role="document">
		</head>

  <body role="document">

    <!-- Fixed navbar -->
    <nav class="navbar navbar-inverse navbar-fixed-top">
		<div class="row">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php">Decords</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="iniciantes.php">voltar</a></li>
			<div class="container theme showcase" role="main">
			</nav>
				<div class="page-header">
					<h1>Exercício:</h1>
				</div>
				<nav class="navbar navbar-default navbar" role="navigation">
				<div class="container-fluid">
				<div class="row-fluid">
				<div class="box-content">
				<div class="form-horizontal">
				
	<body>			
				<?php
				//conclusao do exercicio
				 echo '<div id="resolucao"></div>';	
				
//id do exercicio	
header('Content-Type: text/html; charset=utf-8');			
$cod= $_GET['id'];
$codd=$_SESSION['AlunoId'];
include_once("conexao.php");

						$sql = "SELECT * FROM exercicios WHERE id='$cod'";
						$queryResult = mysql_query($sql) or die(mysql_error());
						while ($row = mysql_fetch_array($queryResult)) {
						$pergunta = $row['pergunta'];
	     				$tablatura = $row['tablatura'];
						$dica = $row['dica'];
						$a = $row['a'];
						$b = $row['b'];
						$c = $row['c'];
						$d = $row['d'];
						$resp = $row['resposta'];
							echo'<div id="questao">
							<div class="form-group" >
                                            <label>Enunciado</label>
                                            <textarea class="form-control" rows="3">'.$pergunta.'</textarea>
											<div class="vex-tabdiv" width=500 scale=1.0 editor="false" editor_height=100>
											'.$tablatura.'
											</div> 
                                        </div>
									      <div class="form-group">
                                            <label>Opções</label>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="res" id="a" value="a" onclick="escolha()">a - '.$a.'
                                                </label>
                                            </div>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="res" id="b" value="b" onclick="escolha()">b - '.$b.'
                                                </label>
                                            </div>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="res" id="c" value="c" onclick="escolha()">c - '.$c.'
                                                </label>
                                            </div>
											<div class="radio">
                                                <label>
                                                    <input type="radio" name="res" id="d" value="d" onclick="escolha()">d - '.$d.'
                                                </label>
                                            </div>
											<div class="form-actions"></br>
											<button id="envio" type="submit" class="btn btn-primary" onclick="finaliza()">Responder</button>
											<button class="btn btn-danger">Dica:'.$dica.'</button>
										
											</div>
									  </div>
									</div>  
									  <input TYPE="hidden" name="inf" id="inf" value="nulo">
							          <input TYPE="hidden" name="resp" id="resp" value="'.$resp.'">
									  <input TYPE="hidden" name="usr" id="usr" value="'.$codd.'">
									  <input TYPE="hidden" name="exe" id="exe" value="'.$cod.'">
									  
									  ';
									  mysql_query("SET NAMES 'utf-8'");
									  mysql_query("SET character_set_connection=utf-8");
									  mysql_query("SET character_set_clent=utf-8");
									  mysql_query("SET character_set_results=utf-8");
						}
						
							?>
					
			</div>
		</nav>
	</body>
</html>						