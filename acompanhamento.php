<!DOCTYPE html>
<?php
session_start();
include_once ("conexao.php");
if (!isset ($_SESSION['AlunoEmail'])and !isset ($_SESSION['AlunoSenha'])){
echo "é necessario login";
header ("Location: index.php");
exit;
}
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
		<link href="css/bootstrap-theme.min.css" rel="stylesheet"> 
		<link href="css/theme.css" rel="stylesheet">
		
		<script src="js/jquery.min.js"></script>
		<script src="js/document.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/jquery.min.js"></script>
		
		
		
		<!-- Support partitura -->
		<script src="js/partitura/vexflow-min.js"></script>
		<script src="js/partitura/underscore-min.js"></script>
		<script src="js/partitura/jquery.js"></script>
		<script src="js/partitura/tabdiv-min.js"></script>
		<!-- Support partitura -->
		
	</head>
		<body role="document">
		</head>

  <body role="document">

    <!-- Fixed navbar -->
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
			<li class="active"><a href="administrativo.php">Administrativo</a></li>
			<li class="active"><a href="acompanhamento.php">Acompanhamento</a></li>
			<li class="active"><a href="tuto.php">Tutorial</a></li>
			<li class="active"><a href="lista_alunos.php">Lista Alunos</a></li>
            <li class="active"><a href="administrativo.php">Voltar</a></li>
			<li class="active"><a href="login.php">Sair</a></li>
			<div class="container theme showcase" role="main"> 
			</nav>
				<div class="page-header">
					<h1>Acompanhamento das Atividades:</h1>
				</div>
				
							<div class="container-fluid">
							  <form class="form-horizontal" role="form">
								<div class="form-group">
								<ul class="resultados">
								</ul>
<?php
include_once("conexao.php");
$aux=1;			
					while($aux<=3){
                        $sql4 = "SELECT count(*) from exercicios where nivel=$aux";
						$queryResult4 = mysql_query($sql4) or die(mysql_error());
						$row4 = mysql_fetch_row($queryResult4);
						$result = $row4[0];
                        if($aux==1){echo' <label class="control-label col-sm-2" for="email">iniciante:  '.$result.'</label>';}
						if($aux==2){echo'<label class="control-label col-sm-2" for="email">intermediario:  '.$result.'</label>';}
						if($aux==3){echo'<label class="control-label col-sm-2" for="email">avançado: '.$result.'</label>';}
					$aux++;	
					}
?>
								</div>
								 </form>
							</div>
							<div class="col-lg-12">
							<h2>Acompanhamento do Aluno</h2>
							
                    <div class="panel panel-default">
                        <div class="panel-heading">
						<form action="lista_alunos.php" method="post">
								<input type="search" id="pesquisar" name="pesquisar" placeholder="Pesquisar" required="required" maxlength="500" />
								<input type="submit" value="Pesquisar"/>
							</form>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
											<th>Nome do Aluno:</th>
                                            <th>Nível:</th>
											<th>Data de acesso:</th>
											<th>Exercicio Atual:</th>
											<th>Certos:</th>
											<th>Email:</th>
                                        </tr>
										
								   </thead>
								   <tbody>
	<?php
							$pesquisar = isset($_POST['pesquisar']) ? $_POST['pesquisar'] : '';
							$sql = mysql_query ("SELECT a.nome, a.email, a.nivel, b.data_termino FROM alunos a, alunos_exercicios b WHERE a.nome LIKE '%$pesquisar%' and a.id=b.id_usuario");
							$row = mysql_num_rows($sql);

					
						$sql4 = "Select a.nome, a.nivel,a.email, e.id_exercicios, e.data_termino, e.resultado, e.id_usuario from alunos a
								join alunos_exercicios e
								on a.id = e.id_usuario
								order by  e.data_termino desc ";
						$queryResult4 = mysql_query($sql4) or die(mysql_error());
						while ($row4 = mysql_fetch_array($queryResult4)) { 
						$nome = $row4['nome'];
						$nivell = $row4['nivel'];
						$exercicios = $row4['id_exercicios'];
						$id_usuario = $row4['id_usuario'];
						//$acertos = $row4['resultado'];
						$email = $row4['email'];
						$data = $row4['data_termino'];
						
						// $resultados = $row4['resultados'];						
                        if($nivell==1){$resultados="Iniciante";} else if($nivell==2) {$resultados="Intermediario";} else if($nivell==3) {$resultados="Avançado";}
						
						// else if($acertos==2) {$resultado="Errou";} else if($acertos==0) {$resultado="--";}
						
						
				
						$sql41 = "SELECT count(*) from alunos_exercicios where id_usuario=$id_usuario and resultado=1";
						$queryResult41 = mysql_query($sql41) or die(mysql_error());
						$row41 = mysql_fetch_row($queryResult41);
						$acertos = $row41[0];
						
				
                                      echo'<tr>
                                            <td>'.$nome.'</td>
                                            <td>'.$resultados.'</td>
											<td>'.date('d/m/Y', strtotime($data)).'</td>
											<td>'.$exercicios.'</td>
											<td>'.$acertos.'</td>
											<td>'.$email.'</td>
											
											
											
                                        </tr>';
						}		

                                    
?>							   
		
								   
							</div>
						</div>
					</div>
			</body>
 