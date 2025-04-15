<!DOCTYPE html>
<?php
//session_start();
header('Content-Type: text/html; charset=utf-8');
include_once("conexao.php");


	$conteudo = isset($_POST['txtEditor']) ? $_POST['txtEditor'] : '';
	
	
	
	
	try
    {
        //insere na BD                    
        $sql = "SELECT * FROM tutorial";
		 $result = mysql_query($sql) or die(mysql_error());
			
        //retorna 1 para no sucesso do ajax saber que foi com inserido sucesso
        echo "Com sucesso";
    } 
    catch (Exception $ex)
    {
        //retorna 0 para no sucesso do ajax saber que foi um erro
        echo "Sem sucesso";
    }
	
	mysql_query("SET NAMES 'utf-8'");
	mysql_query("SET character_set_connection=utf-8");
	mysql_query("SET character_set_clent=utf-8");
	mysql_query("SET character_set_results=utf-8");
	
	
?>

				