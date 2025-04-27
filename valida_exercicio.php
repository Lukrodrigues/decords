<?php
session_start();
header('Content-Type: application/json');

// Configuração de erros e logs
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
$logFile = __DIR__ . '/php_errors.log';
ini_set('error_log', $logFile);

// Controle do buffer de saída
if (ob_get_level()) {
	ob_end_clean();
}
ob_start();

try {
	// Verificação de autenticação
	if (empty($_SESSION['aluno_logado']) || $_SESSION['aluno_logado'] !== true) {
		throw new Exception('Acesso não autorizado', 401);
	}

	// Verificação do método HTTP
	if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
		throw new Exception('Método não permitido', 405);
	}

	// Verificação dos campos obrigatórios
	if (!isset($_POST['id_exercicios'], $_POST['resposta'])) {
		throw new Exception('Dados incompletos', 400);
	}

	// Conexão com o banco de dados
	require_once 'conexao.php';

	// Sanitização dos dados
	$alunoId = (int) $_SESSION['aluno_id'];
	if (!isset($_SESSION['aluno_nivel'])) {
		throw new Exception('Dados de sessão incompletos', 400);
	}
	$exercicioId = (int) $_POST['id_exercicios'];
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
	$resultExercicio->free();
	$stmtExercicio->close();

	// Verificação de nível
	if ($dadosExercicio['nivel'] != $_SESSION['aluno_nivel']) {
		throw new Exception('Nível incompatível', 403);
	}

	// Verifica se o exercício já foi respondido
	$stmtCheck = $conn->prepare("SELECT status FROM alunos_exercicios WHERE id_usuario = ? AND id_exercicios = ?");
	$stmtCheck->bind_param("ii", $alunoId, $exercicioId);
	if (!$stmtCheck->execute()) {
		throw new Exception("Erro na verificação do exercício", 500);
	}
	$resultCheck = $stmtCheck->get_result();
	if ($resultCheck->num_rows > 0) {
		$registro = $resultCheck->fetch_assoc();
		if ($registro['status'] == 1) {
			throw new Exception("Exercício já respondido", 409);
		}
	}
	$stmtCheck->close();

	// Comparação de resposta
	$respostaCorreta = mb_strtolower(trim($dadosExercicio['resposta']), 'UTF-8');
	$acerto = ($resposta === $respostaCorreta) ? 1 : 0;
	$resultado_text = $acerto ? "Certo" : "Errado";

	// Inicia transação para inserir ou atualizar registro
	$conn->begin_transaction();
	$stmtUpdate = null;
	try {
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
	} catch (Exception $e) {
		$conn->rollback();
		throw $e;
	} finally {
		if ($stmtUpdate !== null) {
			$stmtUpdate->close();
		}
	}

	// Verifica se o usuário concluiu todos os exercícios do nível
	$nivelAtual = $_SESSION['aluno_nivel'];
	$stmtTotal = $conn->prepare("SELECT COUNT(*) as total FROM exercicios WHERE nivel = ?");
	$stmtTotal->bind_param("s", $nivelAtual);
	$stmtTotal->execute();
	$resultTotal = $stmtTotal->get_result();
	$totalQuestoes = $resultTotal->fetch_assoc()['total'];
	$stmtTotal->close();

	$stmtAcertos = $conn->prepare("SELECT COUNT(*) as acertos FROM alunos_exercicios ae
        JOIN exercicios e ON ae.id_exercicios = e.id
        WHERE ae.id_usuario = ? AND e.nivel = ? AND ae.resultado = 1");
	$stmtAcertos->bind_param("is", $alunoId, $nivelAtual);
	$stmtAcertos->execute();
	$resultAcertos = $stmtAcertos->get_result();
	$totalAcertos = $resultAcertos->fetch_assoc()['acertos'];
	$stmtAcertos->close();

	$stmtRespondidos = $conn->prepare("SELECT COUNT(*) as respondidos FROM alunos_exercicios ae
        JOIN exercicios e ON ae.id_exercicios = e.id
        WHERE ae.id_usuario = ? AND e.nivel = ?");
	$stmtRespondidos->bind_param("is", $alunoId, $nivelAtual);
	$stmtRespondidos->execute();
	$resultRespondidos = $stmtRespondidos->get_result();
	$totalRespondidos = $resultRespondidos->fetch_assoc()['respondidos'];
	$stmtRespondidos->close();

	if ($totalRespondidos >= $totalQuestoes) {
		$percentualAcerto = ($totalQuestoes > 0) ? ($totalAcertos / $totalQuestoes) * 100 : 0;

		if ($percentualAcerto >= 60) {
			$_SESSION['mensagem'] = "Parabéns! Você será direcionado para o nível intermediário.";
			$_SESSION['aluno_nivel'] = 'intermediario';
			echo json_encode([
				'success'    => true,
				'redirect'   => "intermediarios.php",
				'concluido'  => 'Sim',
				'resultado'  => $resultado_text,
				'acertou'    => $acerto
			]);
		} else {
			$_SESSION['mensagem'] = "Você não atingiu 60% de acertos. Por favor, refaça os exercícios.";
			echo json_encode([
				'success'    => true,
				'redirect'   => "basicos.php",
				'concluido'  => 'Não',
				'resultado'  => $resultado_text,
				'acertou'    => $acerto
			]);
		}
	} else {
		// Caso ainda não tenha terminado todos
		echo json_encode([
			'success'    => true,
			'redirect'   => '',
			'concluido'  => 'Ainda não',
			'resultado'  => $resultado_text,
			'acertou'    => $acerto
		]);
	}
} catch (Exception $e) {
	http_response_code($e->getCode() ?: 400);
	echo json_encode([
		'success' => false,
		'message' => $e->getMessage()
	]);
} finally {
	if (ob_get_length()) {
		ob_end_flush();
	}
}
