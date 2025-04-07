<?php
ob_start();
if(($_SESSION['AlunoId'] == "") || ($_SESSION['AlunoNome'] == "") 
	||($_SESSION['AunoEmail'] == "" ) || ($_SESSION['AlunoSenha'] == "")
	||($_SESSION['AlunonivelAcesso']=="")){
		
	//Mensagem Erro
	$_SESSION['LoginErro'] = 'Area Restrita somente Cadastrados';
	
	//Mandar Tela Login
	header("Location:Login.php");
	
}
?>
