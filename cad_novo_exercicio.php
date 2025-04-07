<?php
//session_start();
header('Content-Type: text/html; charset=utf-8');
include_once("conexao.php");

    $campo1 = $_REQUEST['pergunta'];
	$campo3 = $_REQUEST['nivel'];
	$campo5 = $_REQUEST['tab'];
	$campo11 = $_REQUEST['dica'];
	$campo6 = $_REQUEST['a'];
   	$campo7 = $_REQUEST['b'];
	$campo8 = $_REQUEST['c'];
	$campo9 = $_REQUEST['d'];
	$campo10 = $_REQUEST['resp'];
	//$campo4 = date("Y-m-d");
	
	try
    {
        //insere na BD                    
        $sql = "INSERT INTO exercicios (pergunta, resposta, tablatura, nivel, dica,a,b,c,d) 
		 VALUES('$campo1','$campo10','$campo5','$campo3','$campo11','$campo6','$campo7','$campo8','$campo9')";
		 $result = mysql_query($sql) or die(mysql_error());
			
        //retorna 1 para no sucesso do ajax saber que foi com inserido sucesso
        echo "1";
    } 
    catch (Exception $ex)
    {
        //retorna 0 para no sucesso do ajax saber que foi um erro
        echo "0";
    }
	
	mysql_query("SET NAMES 'utf-8'");
	mysql_query("SET character_set_connection=utf-8");
	mysql_query("SET character_set_clent=utf-8");
	mysql_query("SET character_set_results=utf-8");
	
	
?>