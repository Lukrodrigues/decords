<?php
session_start();
include_once("conexao.php");

if (isset($_POST['email']) && isset($_POST['senha'])) {
	$email = $_POST['email'];
	$senha = $_POST['senha'];

	// Consulta para validar login
	$sql = "SELECT * FROM professor WHERE email = '$email' AND senha = '$senha'";
	$result = mysqli_query($conn, $sql);

	if ($result && mysqli_num_rows($result) > 0) {
		$professor = mysqli_fetch_assoc($result); // Apenas um argumento
		$_SESSION['professor'] = $professor; // Salva os dados do usuário na sessão
		header("Location: acompanhamento.php"); // Redireciona para o dashboard
		exit;
	} else {
		$_SESSION['msg'] = "<p style='color:red;'>Email ou senha incorretos.</p>";
		header("Location: login.php"); // Redireciona para a tela de login
		exit;
	}
} else {
	$_SESSION['msg'] = "<p style='color:red;'>Preencha todos os campos.</p>";
	header("Location: login.php");
	exit;
} {
	// //Define os valores atribuidos na sessao do aluno
	$_SESSION['AlunoId'] = $resultado['id'];
	$_SESSION['AlunoEmail'] = $resultado['email'];
	$_SESSION['AlunoSenha'] = $resultado['senha'];
	$_SESSION['AlunoNome'] = $resultado['nome'];
	$_SESSION['AlunonivelAcesso'] = $resultado['nivel_acesso_id'];
}
if ($_SESSION['AlunonivelAcesso'] == 1) {
	header("Location:administrativo.php");
} else {
	// $_SESSION['aluno'] = $resultado['nome'];
	header("Location: login_professor.php");
}
