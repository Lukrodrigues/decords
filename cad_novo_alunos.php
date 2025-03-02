<?php
header('Content-Type: text/html; charset=utf-8');
session_start();

// Verifica o token CSRF
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
  die("Erro de segurança: Token CSRF inválido.");
}

include_once("conexao.php");

// Recebe os dados do formulário
$nome = $_POST['nome'];
$email = $_POST['email'];
$senha = $_POST['senha'];
$senha2 = $_POST['senha2'];
$cadastro_data = date("Y-m-d");

// Validações básicas
if (empty($nome) || empty($email) || empty($senha) || empty($senha2)) {
  die("Todos os campos são obrigatórios.");
}

if ($senha !== $senha2) {
  die("As senhas não coincidem.");
}

// Verifica se o e-mail já está cadastrado
$query = "SELECT id FROM alunos WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
  die("E-mail já cadastrado.");
}

// Hash da senha
$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

// Insere o novo usuário no banco de dados
try {
  $sql = "INSERT INTO alunos (nome, email, senha, cadastro_data) VALUES (?, ?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ssss", $nome, $email, $senhaHash, $cadastro_data);

  if ($stmt->execute()) {
    // Retorna uma mensagem de sucesso
    echo "Usuário cadastrado com sucesso.";
  } else {
    // Retorna uma mensagem de erro
    echo "Erro ao cadastrar usuário.";
  }
} catch (Exception $ex) {
  // Retorna uma mensagem de erro
  echo "Erro ao cadastrar usuário.";
}

// Fecha a conexão com o banco de dados
$stmt->close();
$conn->close();
