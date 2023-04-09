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
			<li class="active"><a href="Administrativo.php">Administrativo</a></li>
			<li class="active"><a href="Acompanhamento.php">Acompanhamento</a></li>
			<li class="active"><a href="tuto.php">Tutorial</a></li>
			<li class="active"><a href="lista_alunos.php">Lista Alunos</a></li>
            <li class="active"><a href="Administrativo.php">Voltar</a></li>
			<li class="active"><a href="Login.php">Sair</a></li>
			<div class="container theme showcase" role="main"> 
	</nav>
			
			<h2>Lista de Aluno</h2>
			<div class="panel panel-default">
                        <div class="panel-heading">
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
											<th>Email:</th>
											
                                        </tr>
										
								   </thead>
									   
		<?php
		include_once("conexao.php");
		?>
		<?php
		         $pesquisar = isset($_POST['pesquisar']) ? $_POST['pesquisar'] : '';
				 $sql = mysql_query ("SELECT a.nome, a.email, a.nivel, b.data_termino FROM alunos a, alunos_exercicios b WHERE a.nome LIKE '%$pesquisar%' and a.id=b.id_usuario");
				 $row = mysql_num_rows($sql);
				 
				 $sql4 = "Select a.nome, a.nivel, e.id_exercicios, e.data_termino from alunos_exercicios e
								join alunos a
								on a.id = e.id_usuario
								order by e.data_termino asc";
						$queryResult4 = mysql_query($sql4) or die(mysql_error());
						while ($row4 = mysql_fetch_array($queryResult4)) { 
						$nome = $row4['nome'];
						$nivell = $row4['nivel'];
						$data = $row4['data_termino'];
				 
				  if($nivell==1){$resultado="Iniciante";} else if($nivell==2) {$resultado="Intermediario";} else if($nivell==3) {$resultado="Avançado";}
				 
				 if($row > 0){
					 
	
					 
					 while($row = mysql_fetch_array($sql)){
						$nome = $row['nome']; 
						$emaill = $row['email'];
						$data = $row['data_termino'];
						$nivell = $row['nivel'];
						
						echo'<tr>
                                             <td>'.$nome.'</td>
											 <td>'.$nivell.'</td>
											 <td>'.date('d/m/Y', strtotime($data)).'</td>
                                             <td>'.$emaill.'</td>
											 
											 
											
                                         </tr>';
						
					 }
				  }

			}					   
		?>		   
						</div>	
					</div>
				</div>
		</body>