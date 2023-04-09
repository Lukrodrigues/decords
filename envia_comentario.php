<?php
   header('Content-Type: text/html; charset=utf-8');
   include_once("conexao.php");
?>
 
 <?php
 $nome= $_POST['nome'];
 $cidade= $_POST['cidade'];
 $mensagem= $_POST['mensagem'];
 $data= date("Y-m-d");
 
 
 if($_SESSION['captcha'] == $_POST['captcha']){
	$insert = "INSERT INTO comentarios_db (nome, cidade, mensagem, data)
	VALUE ('$nome','$cidade', '$mensagem', '$data')";
	$result = mysqli_query($conectar,$insert) or die(mysqli_error($conectar));
	
	$_SESSION['msg'] = "<h3 style='color:green;'>Mensagem cadastrada com sucesso, obrigado pelo comentario</h3>";
	   header("Location: comentarios.php");
   }else{
	   $_SESSION['msg'] = "<h3 style='color:red;'>ERRO! Caracteres anti-robô inválidos.</h3>";
	   header("Location: comentarios.php");
   }

   $conectar = mysqli_connect("localhost",'root',"") or die("Erro na conexao");
    mysqli_select_db($conectar,"decords") or die ("base não encontrada");
    mysqli_query($conectar,"SET NAMES 'utf8'");
    mysqli_query($conectar,'SET character_set_connection=utf8');
    mysqli_query($conectar,'SET character_set_client=utf8');
    mysqli_query($conectar,'SET character_set_results=utf8');
    ini_set('default_charset','UTF-8');

   
  echo "<META HTTP-EQUIV=REFRESH CONTENT = '0;URL=http://localhost:3000/comentarios.php'>
					<script type=\"text/javascript\">
						alert(\"Obrigado pela seu comentario.\");
					</script>
				";	
 

 
 
 
 ?>