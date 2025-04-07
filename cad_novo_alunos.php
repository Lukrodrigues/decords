<?php

declare(strict_types=1);

header('Content-Type: application/json');
session_start();

try {
  // Garante que a sessão está ativa
  if (session_status() !== PHP_SESSION_ACTIVE) {
    throw new RuntimeException("Falha ao iniciar a sessão", 500);
  }

  // Verificação do método HTTP
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    throw new RuntimeException("Método inválido", 405);
  }

  // Leitura dos dados enviados
  $input = json_decode(file_get_contents('php://input'), true, 512, JSON_THROW_ON_ERROR);

  // Debug: Verificar se o token está vindo corretamente
  if (!isset($_SESSION['csrf_token'], $input['csrf_token'])) {
    throw new RuntimeException("Token de segurança não enviado", 403);
  }

  // Comparação segura do token CSRF
  if (!hash_equals($_SESSION['csrf_token'], $input['csrf_token'])) {
    throw new RuntimeException("Token de segurança inválido", 403);
  }

  // Validação dos campos obrigatórios
  $required = ['nome', 'email', 'senha', 'senha2'];
  foreach ($required as $field) {
    if (empty($input[$field])) {
      throw new RuntimeException("Campo {$field} é obrigatório", 400);
    }
  }

  if ($input['senha'] !== $input['senha2']) {
    throw new RuntimeException("As senhas não coincidem", 400);
  }

  // Conexão com o banco de dados
  require 'conexao.php';

  // Verificar se o email já está cadastrado
  $stmt = $conn->prepare("SELECT id FROM alunos WHERE email = ? LIMIT 1");
  $stmt->bind_param('s', $input['email']);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    throw new RuntimeException("Email já cadastrado", 409);
  }

  // Criar hash seguro da senha
  $senhaHash = password_hash($input['senha'], PASSWORD_ARGON2ID);

  // Iniciar transação para inserir os dados com segurança
  $conn->begin_transaction();

  try {
    $stmt = $conn->prepare("INSERT INTO alunos (nome, email, senha) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $input['nome'], $input['email'], $senhaHash);

    if (!$stmt->execute()) {
      throw new RuntimeException("Erro ao inserir no banco de dados", 500);
    }

    $conn->commit();

    // Resposta de sucesso com redirecionamento
    echo json_encode([
      'success' => true,
      'message' => 'Aluno cadastrado com sucesso!',
      'redirect' => 'login.php'
    ]);
    exit;
  } catch (Exception $e) {
    $conn->rollback();
    throw new RuntimeException("Erro na transação: " . $e->getMessage(), 500);
  }
} catch (RuntimeException $e) {
  http_response_code($e->getCode() ?: 500);
  echo json_encode([
    'success' => false,
    'message' => $e->getMessage()
  ]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode([
    'success' => false,
    'message' => 'Erro interno do servidor'
  ]);
}
