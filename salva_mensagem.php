<?php
	//session_start();
	include_once('conexao.php');
		
	$nome = $_POST['nome'];
	$email = $_POST['email'];
	$assunto = $_POST['assunto'];
	$mensagem = $_POST['mensagem'];
	$created = date("Y-m-d");
	
	
		try
    {
        //insere na BD
        $sql = "INSERT INTO mensagem_contato (nome, email, assunto, mensagem, created) 
		 VALUES('$nome','$email','$assunto','$mensagem', '$created')";
		 $result = mysql_query($sql) or die(mysql_error());
			
        //retorna 1 para no sucesso do ajax saber que foi com inserido sucesso
        echo "1";
    } 
    catch (Exception $ex)
    {
        //retorna 0 para no sucesso do ajax saber que foi um erro
        echo "0";
    }
	
?>