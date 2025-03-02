<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once "conexao.php";

function sendResponse($status, $message, $httpCode = 200)
{
	http_response_code($httpCode);
	echo json_encode(['status' => $status, 'message' => $message]);
	exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
	sendResponse('error', 'Método não permitido.', 405);
}

$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

if (empty($email) || empty($senha)) {
	sendResponse('error', 'E-mail e senha são obrigatórios.', 400);
}

try {
	// Busca o usuário no banco de dados
	$stmt = $conn->prepare("SELECT id, nome, senha FROM alunos WHERE email = ?");
	if (!$stmt) {
		throw new Exception("Erro ao preparar consulta.");
	}

	$stmt->bind_param("s", $email);
	$stmt->execute();
	$stmt->store_result();

	if ($stmt->num_rows === 0) {
		$stmt->close();
		sendResponse('error', 'E-mail ou senha incorretos.', 401);
	}

	$stmt->bind_result($id, $nome, $senha_hash);
	$stmt->fetch();
	$stmt->close();

	// Verifica a senha
	if (password_verify($senha, $senha_hash)) {
		// Login bem-sucedido
		$_SESSION['AlunoId'] = $id;
		$_SESSION['AlunoNome'] = $nome;
		$_SESSION['AlunoEmail'] = $email;

		sendResponse('success', 'Login realizado com sucesso!');
	} else {
		sendResponse('error', 'E-mail ou senha incorretos.', 401);
	}
} catch (Exception $ex) {
	error_log("Erro no login: " . $ex->getMessage());
	sendResponse('error', 'Erro interno, tente novamente.', 500);
} finally {
	if (isset($stmt)) {
		$stmt->close();
	}
	if (isset($conn)) {
		$conn->close();
	}
}
