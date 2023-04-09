<?php
   header('Content-Type: text/html; charset=utf-8');
   include_once("conexao.php");
?>
 
 <?php
 $nome= $_POST['nome'];
 $cidade= $_POST['cidade'];
 $mensagem= $_POST['mensagem'];
 $data= date("Y-m-d");
 
 
 $insert = "INSERT INTO comentarios_db (nome, cidade, mensagem, data)
 VALUE ('$nome','$cidade', '$mensagem', '$data')";
 $result = mysql_query($insert) or die(mysql_error());

 
 echo "<META HTTP-EQUIV=REFRESH CONTENT = '0;URL=http://www.decords.com.br/comentarios.php'>
					<script type=\"text/javascript\">
						alert(\"Obrigado pela seu comentario.\");
					</script>
				";	
 
 
 
 
 
 ?>