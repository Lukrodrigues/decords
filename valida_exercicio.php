<?php
header('Content-Type: text/html; charset=utf-8');
session_start();
include_once("conexao.php");


$escolha = $_POST['escolha'];
$resp = $_POST['resposta'];
$cod = $_POST['cod']; //aluno
$exe = $_POST['exe']; //exercicio
$nivell=$_SESSION['AlunoNivel']; // nivel aluno
$data=date("Y-m-d H:i:sa");
$linkk="exercicios.php";



 
//verifica se resposta esta correta
if($escolha==$resp){

//REGISTRA A ATIVIDADE COMO EFETUADA E BOTÃO CONTINUAR
$sql5 = "INSERT INTO alunos_exercicios (id_usuario,id_exercicios,data_termino,resultado,status) VALUES('$cod','$exe','$data','1','1')";
$queryResult5 = mysql_query($sql5) or die(mysql_error());

						//quantidade de exercicios feitos
						$sql4 = "SELECT count(*) FROM alunos_exercicios where id_usuario='$cod' and id_exercicios in (
						select id from exercicios where nivel='$nivell')";
						$queryResult4 = mysql_query($sql4) or die(mysql_error());
						$row4 = mysql_fetch_row($queryResult4);
						$qtd_feitos = $row4[0];
						
						
						//quantidade de exercicios feitos certos
						$sql4 = "SELECT count(*) FROM alunos_exercicios where resultado=1 and id_usuario='$cod' and id_exercicios in (
						select id from exercicios where nivel='$nivell')";
						$queryResult4 = mysql_query($sql4) or die(mysql_error());
						$row4 = mysql_fetch_row($queryResult4);
						$qtd_feitos_certos = $row4[0];
						
						//quantidade total de exercicios
						$sql4 = "select count(*) from exercicios where nivel='$nivell'";
						$queryResult4 = mysql_query($sql4) or die(mysql_error());
						$row4 = mysql_fetch_row($queryResult4);
						$qtd_total = $row4[0];
							
//verifica se fez todos os exercicios disponiveis 
if($qtd_total==$qtd_feitos){
//calculando porcentagem
$percent = (($qtd_feitos_certos / $qtd_total) * 100);

if($percent>=60){
	$sql6 = "update alunos set nivel=$nivell+1 where id='$cod'";
    $queryResult6 = mysql_query($sql6) or die(mysql_error());
	$_SESSION['AlunoNivel']=$nivell+1;
	$nivell=$_SESSION['AlunoNivel'];
if($nivell==1){$linkk = "iniciantes.php";}else if ($nivell==2){$linkk = "intermediarios.php";}else if ($nivell==3){$linkk = "avancados.php";}

	   
	   	   	echo'</br></br>
							<div id="correto" class="alert alert-success">
							<button type="button" class="close" data-dismiss="alert">×</button>
							<strong>Correto!</strong> Você concluiu o nivel com mais de 60% de Acertos!
							</div><br>
							 <a  type="button" class="btn btn-primary" href="'.$linkk.'" >Continua</a>';
	
}else {
	
	 					
	
	$sql6 = "delete from alunos_exercicios where id_usuario='$cod' and id_exercicios in (
						select id from exercicios where nivel='$nivell')";
    $queryResult6 = mysql_query($sql6) or die(mysql_error());
if($nivell==1){$linkk = "iniciantes.php";}else if ($nivell==2){$linkk = "intermediarios.php";}else if ($nivell==3){$linkk = "avancados.php";}

	   
	   	echo'</br></br>
							<div id="correto" class="alert alert-success">
							<button type="button" class="close" data-dismiss="alert">×</button>
							<strong>Correto!</strong> Você não concluiu o minimo de 60%, refazer o nivel!
							</div><br>
							 <a  type="button" class="btn btn-primary" href="'.$linkk.'" >Continua</a>';
	   
}



}else{
		if($nivell==1){$linkk = "iniciantes.php";}else if ($nivell==2){$linkk = "intermediarios.php";}else if ($nivell==3){$linkk = "avancados.php";}
	echo'</br></br>
							<div id="correto" class="alert alert-success">
							<button type="button" class="close" data-dismiss="alert">×</button>
							<strong>Correto!</strong> Muito bem!
							</div><br>
							 <a  type="button" class="btn btn-primary" href="'.$linkk.'" >Continua</a>';
}
	
}else{

//REGISTRA A ATIVIDADE COMO EFETUADA
$sql5 = "INSERT INTO alunos_exercicios (id_usuario,id_exercicios,data_termino,resultado,status) VALUES('$cod','$exe','$data','2','1')";
$queryResult5 = mysql_query($sql5) or die(mysql_error());

						//quantidade de exercicios feitos
						$sql4 = "SELECT count(*) FROM alunos_exercicios where id_usuario='$cod' and id_exercicios in (
						select id from exercicios where nivel='$nivell')";
						$queryResult4 = mysql_query($sql4) or die(mysql_error());
						$row4 = mysql_fetch_row($queryResult4);
						$qtd_feitos = $row4[0];
						
						
						//quantidade de exercicios feitos certos
						$sql4 = "SELECT count(*) FROM alunos_exercicios where resultado=1 and id_usuario='$cod' and id_exercicios in (
						select id from exercicios where nivel='$nivell')";
						$queryResult4 = mysql_query($sql4) or die(mysql_error());
						$row4 = mysql_fetch_row($queryResult4);
						$qtd_feitos_certos = $row4[0];
						
						//quantidade total de exercicios
						$sql4 = "select count(*) from exercicios where nivel='$nivell'";
						$queryResult4 = mysql_query($sql4) or die(mysql_error());
						$row4 = mysql_fetch_row($queryResult4);
						$qtd_total = $row4[0];

//verifica se fez todos os exercicios disponiveis 

if($qtd_total==$qtd_feitos){
//calculando porcentagem
$percent = (($qtd_feitos_certos / $qtd_total) * 100);

if($percent>=60){
	$sql6 = "update alunos set nivel=$nivell+1 where id='$cod'";
    $queryResult6 = mysql_query($sql6) or die(mysql_error());
	$_SESSION['AlunoNivel']=$nivell+1;
	$nivell=$_SESSION['AlunoNivel'];
if($nivell==1){$linkk = "iniciantes.php";}else if ($nivell==2){$linkk = "intermediarios.php";}else if ($nivell==3){$linkk = "avancados.php";}
	   
	   echo'</br></br>
							<div id="correto" class="alert alert-danger">
							<button type="button" class="close" data-dismiss="alert">×</button>
							<strong>A resposta esta incorreta e com mais de 60% de Acertos!! A resposta correta é '.$resp.'</strong>
							</div><br>
							<a type="button" class="btn btn-primary" href="'.$linkk.'" >Continua</a>';
	   
	
}else {
	
	$sql6 = "delete from alunos_exercicios where id_usuario='$cod' and id_exercicios in (
						select id from exercicios where nivel='$nivell')";
    $queryResult6 = mysql_query($sql6) or die(mysql_error());
	

if($nivell==1){$linkk = "iniciantes.php";}else if ($nivell==2){$linkk = "intermediarios.php";}else if ($nivell==3){$linkk = "avancados.php";}

echo'</br></br>
							<div id="correto" class="alert alert-danger">
							<button type="button" class="close" data-dismiss="alert">×</button>
							<strong>A resposta esta incorreta e você não concluiu o minimo de 60%, refazer o nivel!! A resposta correta é '.$resp.'</strong>
							</div><br>
							<a type="button" class="btn btn-primary" href="'.$linkk.'" >Continua</a>';

}


} else{						
						
if($nivell==1){$linkk = "iniciantes.php";}else if ($nivell==2){$linkk = "intermediarios.php";}else if ($nivell==3){$linkk = "avancados.php";}
echo'</br></br>
							<div id="correto" class="alert alert-danger">
							<button type="button" class="close" data-dismiss="alert">×</button>
							<strong>A resposta esta incorreta!</strong><br>
							</div><br>
							<div id="correto" class="alert alert-success"
							<strong>"A resposta CORRETA e:" '.$resp.'</strong><br>
							</div><br>
							<a type="button" class="btn btn-primary" href="'.$linkk.'" >Continua</a>';
							
 }
 
}

						


	

 
?>