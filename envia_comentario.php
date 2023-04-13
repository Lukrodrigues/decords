<?php
   header('Content-Type: text/html; charset=utf-8');
   include_once("conexao.php");
?>
 
 <?php
      session_start();
      if (isset($_SESSION['captcha']) && isset($_POST['captcha'])){
         if (isset($nome) && isset($cidade) && isset($mensagem)) {
            $data = date("Y-m-d");
      
	$insert = "INSERT INTO comentarios_db (nome, cidade, mensagem, date)
	VALUES ('$nome','$cidade', '$mensagem', '$data')";
	$result = mysqli_query($conn,$insert) or die(mysqli_error($mensagem));
	
	$_SESSION['msg'] = "<h3 style='color:green;'>Mensagem cadastrada com sucesso, obrigado pelo comentario</h3>";
	   header("Location: comentarios.php");
   } else {
	   $_SESSION['msg'] = "<h3 style='color:red;'>ERRO! Caracteres anti-robô inválidos.</h3>";
	   header("Location: comentarios.php");
   }
}

      

   $conn = mysqli_connect("localhost",'root',"") or die("Erro na conexao");
    mysqli_select_db($conn,"decords") or die ("base não encontrada");
    mysqli_query($conn,"SET NAMES 'utf8'");
    mysqli_query($conn,'SET character_set_connection=utf8');
    mysqli_query($conn,'SET character_set_client=utf8');
    mysqli_query($conn,'SET character_set_results=utf8');
    ini_set('default_charset','UTF-8');

   
 /* echo "<META HTTP-EQUIV=REFRESH CONTENT = '0;URL=http://localhost:3000/comentarios.php'>
					<script type=\"text/javascript\">
						alert(\"Obrigado pela seu comentario.\");
					</script>
				";	
 */

 header("Location: http://localhost:3000/comentarios.php");
 exit;
 echo "<script type='text/javascript'>alert('Obrigado pelo seu comentário.');</script>";

 ?>