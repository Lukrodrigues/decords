<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once "conexao.php"; // Garante que a conexão está inclusa corretamente

// Função para enviar respostas JSON
function sendResponse($status, $message, $httpCode = 200)
{
  http_response_code($httpCode);
  echo json_encode(['status' => $status, 'message' => $message]);
  exit;
}

// Verifica se os dados foram enviados via POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  sendResponse('error', 'Método não permitido.', 405);
}

// Captura e sanitiza os inputs
$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';
$senha2 = $_POST['senha2'] ?? '';

// Validações básicas
if (empty($nome) || empty($email) || empty($senha) || empty($senha2)) {
  sendResponse('error', 'Todos os campos são obrigatórios.', 400);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  sendResponse('error', 'E-mail inválido.', 400);
}

if ($senha !== $senha2) {
  sendResponse('error', 'As senhas não coincidem.', 400);
}

if (strlen($senha) < 6) {
  sendResponse('error', 'A senha deve ter pelo menos 6 caracteres.', 400);
}

try {
  // Hash seguro da senha
  $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
  $cadastro_data = date("Y-m-d");

  // Verifica se o e-mail já está cadastrado
  $stmt = $conn->prepare("SELECT id FROM alunos WHERE email = ?");
  if (!$stmt) {
    throw new Exception("Erro ao preparar consulta.");
  }

  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows > 0) {
    $stmt->close();
    sendResponse('error', 'Este e-mail já está cadastrado.', 400);
  }
  $stmt->close();

  // Insere o novo aluno no banco de dados
  $stmt = $conn->prepare("INSERT INTO alunos (nome, email, senha, cadastro_data) VALUES (?, ?, ?, ?)");
  if (!$stmt) {
    throw new Exception("Erro ao preparar inserção.");
  }

  $stmt->bind_param("ssss", $nome, $email, $senha_hash, $cadastro_data);

  if ($stmt->execute()) {
    sendResponse('success', 'Usuário cadastrado com sucesso!');
  } else {
    throw new Exception("Erro ao cadastrar usuário.");
  }
} catch (Exception $ex) {
  error_log("Erro no cadastro: " . $ex->getMessage()); // Log do erro para o servidor
  sendResponse('error', 'Erro interno, tente novamente.', 500);
} finally {
  if (isset($stmt)) {
    $stmt->close();
  }
  if (isset($conn)) {
    $conn->close();
  }
}
