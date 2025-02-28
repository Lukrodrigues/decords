<?php
session_start();
require_once "conexao.php"; // Garante que a conexão está inclusa corretamente

// Verifica se a requisição é POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
	http_response_code(405);
	exit("Método não permitido.");
}

// Captura e sanitiza os dados de entrada
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$senha = $_POST['senha'] ?? '';

// Verifica se os campos não estão vazios
if (empty($email) || empty($senha)) {
	$_SESSION['loginErro'] = "Preencha todos os campos.";
	header("Location: login.php");
	exit();
}

// Prepara a consulta ao banco de dados
$stmt = $conn->prepare("SELECT id, nome, email, senha, nivel FROM alunos WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$aluno = $result->fetch_assoc();
$stmt->close();

// Verifica se encontrou o usuário e se a senha está correta
if (!$aluno || !password_verify($senha, $aluno['senha'])) {
	$_SESSION['loginErro'] = "E-mail ou senha inválidos.";
	header("Location: login.php");
	exit();
}

// Garante uma nova sessão segura
session_regenerate_id(true);

// Armazena os dados do aluno na sessão
$_SESSION['AlunoId'] = $aluno['id'];
$_SESSION['AlunoEmail'] = $aluno['email'];
$_SESSION['AlunoNome'] = $aluno['nome'];
$_SESSION['AlunoNivel'] = $aluno['nivel'];

// Redireciona para a página inicial
header("Location: tutorial-01.php");
exit();
