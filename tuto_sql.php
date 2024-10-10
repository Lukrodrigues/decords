<?php
//session_start();
header('Content-Type: text/html; charset=utf-8');
include_once("conexao.php");

	   $conteudo = addslashes($_POST(['editor1']));

	try
	{
	
        //insere na BD                    
        $inserir = "INSERT INTO tutorial (editor1) VALUES ('$conteudo')";
		 $result = mysqli_query($inserir) or die(mysqli_error());
			
        //retorna 1 para no sucesso do ajax saber que foi com inserido sucesso
        echo "1";
    } 
    catch (Exception $ex)
    {
        //retorna 0 para no sucesso do ajax saber que foi um erro
        echo "0";
    }
	/*   
	mysqli_query("SET NAMES 'utf-8'");
	mysqli_query("SET character_set_connection=utf-8");
	mysqli_query("SET character_set_clent=utf-8");
	mysqli_query("SET character_set_results=utf-8");
		
	*/
	
?>