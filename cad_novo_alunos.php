<?php
header('Content-Type: text/html; charset=utf-8');
session_start();
include_once("conexao.php");

     $nome = $_POST['nome'];
	 $email = $_POST['email'];
     $senha = $_POST['senha'];
	 $senha2 = $_POST['senha2'];
	 $cadastro_data = date("Y-m-d");	
	 
	
	  try
	 	 {
        $sql = "INSERT INTO alunos (nome, email, senha, senha2, cadastro_data) 
		VALUES('$nome','$email','$senha', '$senha2','$cadastro_data')";
        $result = mysql_query($sql) or die(mysql_error());
       
			
        //retorna 1 para no sucesso do ajax saber que foi com inserido sucesso
        echo "<META HTTP-EQUIV=REFRESH CONTENT = '0;URL=http://localhost/Decords/Login.php'>
					<script type=\"text/javascript\">
						alert(\"Usuario cadastrado com Sucesso.\");
					</script>
				";	
    } 
    catch (Exception $ex)
    {
        //retorna 0 para no sucesso do ajax saber que foi um erro
        echo "0";
    }
	?>
	
	