<?php
session_start();
session_unset();
session_destroy();
header("Location: login.php");

$_SESSION['AlunoId'];
$_SESSION['AlunoEmail'];
$_SESSION['AlunoSenha'];
$_SESSION['AlunoNome'];
$_SESSION['AlunonivelAcesso'];

header("Location: Login.php");
exit;
