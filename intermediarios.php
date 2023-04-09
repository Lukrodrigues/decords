<!DOCTYPE html>
<?php
header('Content-Type: text/html; charset=utf-8');
 	session_start();
	if (!isset ($_SESSION['AlunoEmail'])and !isset ($_SESSION['AlunoSenha'])){
	echo "é necessario login";
	header ("Location: index.php");
	exit;
	}
?>
<html lang="pt-br">
				<head>
							<meta http-equiv="Content-Type" content="text/html; charset=utf-8/>
							<meta http-equiv="X-UA-Compatible" content="IE=edge">
							<title>Decords Musica e Teoria</title>
							<meta name="viewport" content="width=device-width, initial-scale=1.0">  
							<meta name="description" content="Decords Música e Teoria">
							<meta name="" content="Luciano Moraes Rodrigues">
							<link rel="icon" href="img/favicon.ico"> 
							<link href="css/bootstrap.min.css" rel="stylesheet">
							<link href="css/style.css" rel="stylesheet">
							<link href="css/signin.css" rel="stylesheet">
							<link href="css/tabdiv.css" media="screen" rel="Stylesheet" type="text/css" />
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

						<a class="navbar-brand" href="index.php"><img id="logo" src="img/foto22.jpg" width="100" height="30"></a>
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
							<li><a href="iniciantes.php">Iniciantes</a></li>
							<li class="divider"></li>
							<li><a href="intermediarios.php">Intermediarios</a></li>
							<li class="divider"></li>
							<li><a href="avancados.php">Avancados</a></li>
							<li class="divider"></li>
						</ul>
						<li class="active"><a href="login.php">Sair</a></li>
							</li>
						</ul>
					</div>
				</div>
			</nav>

	
	
	
	                 <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table-responsive">
                                    <thead>
                                        <tr>
                                            <th>numeração</th>
                                            <th>Pergunta</th>
                                            <th>Concluído</th>
                                            <th>Resultado</th>
											<th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>

									
<?php
header('Content-Type: text/html; charset=utf-8');
$aluno=$_SESSION['AlunoId'];
$nivell=$_SESSION['AlunoNivel'];
if($nivell>=2){
include_once("conexao.php");
$numeracao=1;
						$sql = "SELECT id, pergunta FROM exercicios WHERE nivel=2";
						$queryResult = mysql_query($sql) or die(mysql_error());
						while ($row = mysql_fetch_array($queryResult)) {
						$ident = $row['id'];
						$pergunta = $row['pergunta'];
						$botao="Fazer";
						$botaocor="btn btn-warning"; 
				        $link="exercicio.php?id=".$ident;
						
						$sql4 = "SELECT resultado, status FROM alunos_exercicios where id_usuario='$aluno' and id_exercicios='$ident'";
						$queryResult4 = mysql_query($sql4) or die(mysql_error());
						$row4 = mysql_fetch_row($queryResult4);
						$resultado = $row4[0];
						$status = $row4[1];
						
						if($status==0){	$status="Não";} else{$status="Sim"; $link="#"; $botao="Concluído"; $botaocor="btn btn-success"; }
						
						if($resultado==1){$resultado="Acertou";} else if($resultado==2) {$resultado="Errou";} else if($resultado==0) {$resultado="--";}
						
                                      echo'<tr>
                                            <td>'.$numeracao.'</td>
                                            <td>'.$pergunta.'</td>
                                            <td>'.$status.'</td>
                                            <td>'.$resultado.'</td>
											<td><a href="'.$link.'"><button type="button" class="'.$botaocor.'">'.$botao.'</button>
											</td>	
                                        </tr>';
										
										
						$numeracao++;
						}

}else{
	
	if($nivell==1){$msg = "iniciante"; $linkk = "iniciantes.php";}else if ($nivell==2){$msg = "intermediario"; $linkk = "intermediarios.php";}
	
echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=".$linkk."'>
	   <script type=\"text/javascript\">
	   alert(\"Você não terminou o nível ".$msg." para avançar.\") 
	   </script>";
}
									
									  mysql_query("SET NAMES 'utf-8'");
									  mysql_query("SET character_set_connection=utf-8");
									  mysql_query("SET character_set_clent=utf-8");
									  mysql_query("SET character_set_results=utf-8");
						
?>

									
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.table-responsive -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
	</body>
	