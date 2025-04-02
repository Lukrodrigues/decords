<?php
session_start();
header('Content-Type: application/json');
// Habilita relatório de erros rigoroso
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

// Controle de buffer de saída
if (ob_get_level()) ob_end_clean();
ob_start();

try {
	// Verificação de autenticação
	if (!isset($_SESSION['aluno_logado']) || $_SESSION['aluno_logado'] !== true) {
		throw new Exception('Acesso não autorizado', 401);
	}

	// Validação do método HTTP
	if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
		throw new Exception('Método não permitido', 405);
	}

	// Verificação dos campos obrigatórios
	if (!isset($_POST['id_exercicios'], $_POST['resposta'])) {
		throw new Exception('Dados incompletos', 400);
	}

	// Conexão com banco de dados
	require_once 'conexao.php';

	// Sanitização dos dados
	$alunoId = (int)$_SESSION['aluno_id'];
	$exercicioId = (int)$_POST['id_exercicios'];
	$resposta = mb_strtolower(trim($_POST['resposta']), 'UTF-8');

	// Consulta ao exercício
	$stmtExercicio = $conn->prepare("SELECT resposta, nivel FROM exercicios WHERE id = ? LIMIT 1");
	$stmtExercicio->bind_param("i", $exercicioId);

	if (!$stmtExercicio->execute()) {
		throw new Exception('Erro na consulta do exercício', 500);
	}

	$resultExercicio = $stmtExercicio->get_result();

	if ($resultExercicio->num_rows === 0) {
		throw new Exception('Exercício não encontrado', 404);
	}

	$dadosExercicio = $resultExercicio->fetch_assoc();

	// Libera recursos imediatamente após o uso
	$resultExercicio->free();
	$stmtExercicio->close();

	// Validação de nível
	if ($dadosExercicio['nivel'] != $_SESSION['aluno_nivel']) {
		throw new Exception('Nível incompatível', 403);
	}

	// Comparação de respostas
	$respostaCorreta = mb_strtolower(trim($dadosExercicio['resposta']), 'UTF-8');
	$acerto = ($resposta === $respostaCorreta) ? 1 : 0;

	// Transação de banco de dados
	$conn->begin_transaction();
	$stmtUpdate = null;

	try {
		// Query de atualização
		$sql = "INSERT INTO alunos_exercicios 
                (id_usuario, id_exercicios, resultado, status) 
                VALUES (?, ?, ?, 1)
                ON DUPLICATE KEY UPDATE 
                resultado = VALUES(resultado),
                status = 1";

		$stmtUpdate = $conn->prepare($sql);
		$stmtUpdate->bind_param("iii", $alunoId, $exercicioId, $acerto);

		if (!$stmtUpdate->execute()) {
			throw new Exception('Falha ao registrar resposta', 500);
		}

		$conn->commit();

		// Resposta de sucesso
		echo json_encode([
			'success' => true,
			'redirect' => "iniciantes.php?exercicio_id=$exercicioId&status=" . ($acerto ? 'acerto' : 'erro')
		]);
	} catch (Exception $e) {
		$conn->rollback();
		throw $e;
	} finally {
		// Fecha statement de atualização
		if ($stmtUpdate !== null) $stmtUpdate->close();
	}
} catch (Exception $e) {
	// Log de erro detalhado
	error_log(date('[Y-m-d H:i:s]') . " ERRO: " . $e->getMessage() . "\n", 3, "erros.log");

	// Resposta de erro padronizada
	http_response_code($e->getCode() ?: 500);
	echo json_encode([
		'success' => false,
		'error' => $e->getMessage(),
		'code' => $e->getCode()
	]);
} finally {
	// Fecha conexão se existir
	if (isset($conn) && $conn instanceof mysqli) {
		$conn->close();
	}

	// Limpeza final do buffer
	ob_end_flush();
	exit;
}
