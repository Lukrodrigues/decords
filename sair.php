<?php
session_start();
session_destroy();

	$_SESSION['AlunoId'],
	$_SESSION['AlunoEmail'],
	$_SESSION['AlunoSenha'],
	$_SESSION['AlunoNome'],
	$_SESSION['AlunonivelAcesso'],
	
	header("Location: Login.php");

?>