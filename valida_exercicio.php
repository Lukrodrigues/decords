<?php

declare(strict_types=1);

session_start();
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', '0');

try {
	// Verificação rigorosa de autenticação
	if (!isset($_SESSION['aluno_logado'], $_SESSION['aluno_id'])) {
		throw new Exception('Acesso não autorizado', 401);
	}

	// Validação do método POST
	if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
		throw new Exception('Método não permitido', 405);
	}

	// Validação dos dados recebidos
	if (!isset($_POST['id_exercicios'], $_POST['resposta'])) {
		throw new Exception('Dados incompletos', 400);
	}

	require_once 'conexao.php';

	// Sanitização dos inputs
	$alunoId = (int)$_SESSION['aluno_id'];
	$exercicioId = (int)$_POST['id_exercicios'];
	$resposta = mb_strtolower(trim($_POST['resposta']), 'UTF-8');

	// Busca dados do exercício
	$stmt = $conn->prepare("SELECT resposta, nivel FROM exercicios WHERE id = ?");
	$stmt->bind_param("i", $exercicioId);
	$stmt->execute();
	$exercicio = $stmt->get_result()->fetch_assoc();

	if (!$exercicio) throw new Exception('Exercício inválido', 404);

	// Processamento da resposta
	$nivelAtual = $exercicio['nivel'];
	$respostaCorreta = mb_strtolower(trim($exercicio['resposta']), 'UTF-8');
	$acerto = ($resposta === $respostaCorreta) ? 1 : 0;

	$conn->begin_transaction();

	try {
		// Registra tentativa
		$stmt = $conn->prepare("
            INSERT INTO alunos_exercicios (id_usuario, id_exercicios, resultado, status)
            VALUES (?, ?, ?, 1)
            ON DUPLICATE KEY UPDATE 
                resultado = VALUES(resultado), 
                status = 1,
                data_attempt = NOW()
        ");
		$stmt->bind_param("iii", $alunoId, $exercicioId, $acerto);
		$stmt->execute();

		// Verifica conclusão do nível
		$stmt = $conn->prepare("
            SELECT COUNT(*) as total_respondidos
            FROM alunos_exercicios ae
            INNER JOIN exercicios e ON ae.id_exercicios = e.id
            WHERE ae.id_usuario = ? AND e.nivel = ?
        ");
		$stmt->bind_param("is", $alunoId, $nivelAtual);
		$stmt->execute();
		$totalRespondidos = (int)$stmt->get_result()->fetch_assoc()['total_respondidos'];

		$redirect = null;

		// Lógica de progresso
		if ($totalRespondidos >= 10) {
			$stmt = $conn->prepare("
                SELECT COUNT(*) as acertos
                FROM alunos_exercicios ae
                INNER JOIN exercicios e ON ae.id_exercicios = e.id
                WHERE ae.id_usuario = ? AND e.nivel = ? AND ae.resultado = 1
            ");
			$stmt->bind_param("is", $alunoId, $nivelAtual);
			$stmt->execute();
			$acertos = (int)$stmt->get_result()->fetch_assoc()['acertos'];
			$percentual = ($acertos / 10) * 100;

			if ($percentual < 60) {
				// Reset completo com verificação de nível
				$stmt = $conn->prepare("
                    DELETE ae FROM alunos_exercicios ae
                    INNER JOIN exercicios e ON ae.id_exercicios = e.id
                    WHERE ae.id_usuario = ? AND e.nivel = ?
                ");
				$stmt->bind_param("is", $alunoId, $nivelAtual);
				$stmt->execute();

				$_SESSION['mensagem'] = "Infelizmente não atingiu o desempenho para próximo nível! Tente novamente. ({$percentual}% de acertos)";
				$_SESSION['mensagem_tipo'] = "error";
				$redirect = 'iniciantes.php?reset=' . time(); // Força reload
			} else {
				// Lógica de aprovação
				$_SESSION['mensagem'] = "Parabéns! Concluiu com desempenho acima da média! Inicie o próximo nível. ({$percentual}% de acertos)";
				$_SESSION['mensagem_tipo'] = "success";
				$redirect = 'intermediarios.php';
			}
		}

		$conn->commit();

		echo json_encode([
			'success'  => true,
			'acertou'  => (bool)$acerto,
			'message'  => $acerto ? "✓ Resposta Correta!" : "✗ Resposta Incorreta!",
			'redirect' => $redirect ?? null
		]);
	} catch (Exception $e) {
		$conn->rollback();
		throw $e;
	}
} catch (Exception $e) {
	http_response_code($e->getCode() ?: 500);
	echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} finally {
	if (isset($conn) && $conn instanceof mysqli) {
		$conn->close();
	}
	exit;
}
