<!DOCTYPE html>
<?php
 	session_start();
?>
<html lang="pt-br">
				<head>
							<meta charset="UTF-8">
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

						<a class="navbar-brand" href="Index.php"><img id="logo" src="img/favicon.ico"></a>
					</div>
					<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
						<ul class="nav navbar-nav">
							<li class="dropdown"><a class="dropdown-toggle" href="#" data-toggle="dropdown">Intermediarios <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="Iniciantes.php">Iniciantes</a></li>
									<li class="divider"></li>
									<li><a href="Intermediarios.php">Intermediarios</a></li>
									<li class="divider"></li>
									<li><a href="Avancados.php">Avancados</a></li>
									<li class="divider"></li>
								</ul>
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
				        $link="Exercicio.php?id=".$ident;
						
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
echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=Iniciantes.php'>
	   <script type=\"text/javascript\">
	   alert(\"Você não terminou o nível iniciante para avançar.\") 
	   </script>";
}
						
?>

									
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.table-responsive -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
	</body>
	