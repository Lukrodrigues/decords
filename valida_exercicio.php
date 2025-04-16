<?php

declare(strict_types=1);

session_start();
header('Content-Type: application/json');

// Configuração de erros
error_reporting(E_ALL);
ini_set('display_errors', '0');

try {
	// Autenticação
	if (empty($_SESSION['aluno_logado'])) {
		throw new Exception('Acesso não autorizado', 401);
	}

	// Validação do POST
	if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
		throw new Exception('Método não permitido', 405);
	}

	// Campos obrigatórios
	if (!isset($_POST['id_exercicios'], $_POST['resposta'])) {
		throw new Exception('Dados incompletos', 400);
	}

	require_once 'conexao.php';

	// Processamento da resposta
	$alunoId = (int)$_SESSION['aluno_id'];
	$exercicioId = (int)$_POST['id_exercicios'];
	$resposta = mb_strtolower(trim($_POST['resposta']), 'UTF-8');

	// Consulta exercício
	$stmt = $conn->prepare("SELECT resposta, nivel FROM exercicios WHERE id = ?");
	$stmt->bind_param("i", $exercicioId);
	$stmt->execute();
	$exercicio = $stmt->get_result()->fetch_assoc();

	if (!$exercicio) {
		throw new Exception('Exercício não encontrado', 404);
	}

	// Verifica resposta
	$respostaCorreta = mb_strtolower(trim($exercicio['resposta']), 'UTF-8');
	$acerto = ($resposta === $respostaCorreta) ? 1 : 0;

	// Registra resposta
	$conn->begin_transaction();
	$stmt = $conn->prepare(
		"INSERT INTO alunos_exercicios 
        (id_usuario, id_exercicios, resultado, status) 
        VALUES (?, ?, ?, 1)
        ON DUPLICATE KEY UPDATE resultado = VALUES(resultado), status = 1"
	);
	$stmt->bind_param("iii", $alunoId, $exercicioId, $acerto);
	$stmt->execute();

	// Verifica conclusão do nível
	$stmt = $conn->prepare(
		"SELECT COUNT(*) as total 
        FROM alunos_exercicios 
        WHERE id_usuario = ? AND status = 1"
	);
	$stmt->bind_param("i", $alunoId);
	$stmt->execute();
	$totalRespondidos = $stmt->get_result()->fetch_assoc()['total'];

	if ($totalRespondidos >= 10) {
		$stmt = $conn->prepare(
			"SELECT SUM(resultado) as acertos 
            FROM alunos_exercicios 
            WHERE id_usuario = ?"
		);
		$stmt->bind_param("i", $alunoId);
		$stmt->execute();
		$acertos = $stmt->get_result()->fetch_assoc()['acertos'];
		$percentual = ($acertos / 10) * 100;

		if ($percentual >= 60) {
			// Atualiza nível
			$stmt = $conn->prepare("UPDATE alunos SET nivel = nivel + 1 WHERE id = ?");
			$stmt->bind_param("i", $alunoId);
			$stmt->execute();

			$_SESSION['mensagem'] = "Parabéns! Você atingiu {$percentual}% e avançou para o nível intermediário!";
			$redirect = 'intermediarios.php';
		} else {
			// Limpa respostas
			$stmt = $conn->prepare(
				"DELETE FROM alunos_exercicios 
                WHERE id_usuario = ?"
			);
			$stmt->bind_param("i", $alunoId);
			$stmt->execute();

			$_SESSION['mensagem'] = "Você atingiu {$percentual}%. Vamos tentar novamente!";
			$redirect = 'iniciantes.php?reset=1';
		}
	} else {
		$redirect = 'iniciantes.php';
	}

	$conn->commit();
	echo json_encode(['success' => true, 'redirect' => $redirect]);
} catch (Exception $e) {
	http_response_code($e->getCode() ?: 500);
	echo json_encode([
		'success' => false,
		'error' => $e->getMessage()
	]);
} finally {
	if (isset($conn)) $conn->close();
	exit;
}
