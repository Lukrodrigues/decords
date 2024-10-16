<?php
session_start();
$emailt = $_POST['email'];
$senhat = $_POST['senha'];
echo $emailt.' - '.$senhat;
//include_once("conexao.php");
$result = mysqli_query($conn,"SELECT * FROM professor WHERE email='$emailt' AND senha='$senhat' LIMIT 1");
$resultado = mysqli_fetch_assoc($result, $conn);
echo "professor: " .$resultado['nome'];
if (empty($resultado)){
	// msg Errro
	$_SESSION['loginErro'] = "Email ou senha invalido";

	// manda para tela de login
	header("Location: login.php");

 }else{

// //Define os valores atribuidos na sessao do aluno
	$_SESSION['AlunoId'] = $resultado['id'];
	$_SESSION['AlunoEmail'] = $resultado['email'];
	$_SESSION['AlunoSenha'] = $resultado['senha'];
	$_SESSION['AlunoNome'] = $resultado['nome'];
	$_SESSION['AlunonivelAcesso'] = $resultado['nivel_acesso_id'];
	
}
	if($_SESSION['AlunonivelAcesso'] == 1){
		header("Location:administrativo.php");
	 }else{
    // $_SESSION['aluno'] = $resultado['nome'];
	header("Location: login_professor.php");
 }
?>