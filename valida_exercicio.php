<?php
// inicial.php
session_start();
$_SESSION['acertos'] = $_SESSION['acertos'] ?? 0;
$_SESSION['total'] = $_SESSION['total'] ?? 0;

// Limpa qualquer saída anterior e define o cabeçalho como JSON
ob_clean();
header('Content-Type: application/json');

// Array de resposta padrão
$response = [];

try {
	// Verifica se o usuário está autenticado
	if (!isset($_SESSION['AlunoId'])) {
		$response = [
			'status' => 'error',
			'message' => 'Usuário não autenticado.'
		];
		echo json_encode($response);
		exit;
	}

	// Verifica o método HTTP
	if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
		$response = [
			'status' => 'error',
			'message' => 'Método inválido. Apenas POST é permitido.'
		];
		echo json_encode($response);
		exit;
	}

	// Inclui a conexão com o banco de dados
	include_once("conexao.php");

	// Validação dos dados recebidos
	$idExercicio = isset($_POST['id_exercicios']) ? intval($_POST['id_exercicios']) : null;
	$resposta = isset($_POST['resposta']) ? trim($_POST['resposta']) : null;

	if (!$idExercicio || !$resposta) {
		$response = [
			'status' => 'error',
			'message' => 'Dados inválidos fornecidos.',
			'debug' => [
				'id_exercicios' => $idExercicio,
				'resposta' => $resposta
			]
		];
		echo json_encode($response);
		exit;
	}

	// Conexão com o banco e lógica de validação
	$idUsuario = intval($_SESSION['AlunoId']);

	// Verifica se o exercício existe
	$sqlExercicio = "SELECT resposta FROM exercicios WHERE id = ?";
	$stmtExercicio = $conn->prepare($sqlExercicio);
	$stmtExercicio->bind_param("i", $idExercicio);
	$stmtExercicio->execute();
	$stmtExercicio->store_result();

	if ($stmtExercicio->num_rows === 0) {
		$response = [
			'status' => 'error',
			'message' => 'Exercício não encontrado.'
		];
		echo json_encode($response);
		exit;
	}

	$stmtExercicio->bind_result($respostaCorreta);
	$stmtExercicio->fetch();
	$stmtExercicio->close();

	// Determina o resultado
	$resultado = strtolower(trim($resposta)) === strtolower(trim($respostaCorreta)) ? 1 : 2;
	$mensagem = $resultado === 1 ? 'Resposta correta! Parabéns!' : 'Resposta incorreta. Tente novamente.';

	// Atualiza as sessões de desempenho
	$_SESSION['total']++;
	if ($resultado === 1) {
		$_SESSION['acertos']++;
	}

	// Atualiza ou insere o status do exercício
	$sqlVerifica = "SELECT id FROM alunos_exercicios WHERE id_usuario = ? AND id_exercicios = ?";
	$stmtVerifica = $conn->prepare($sqlVerifica);
	$stmtVerifica->bind_param("ii", $idUsuario, $idExercicio);
	$stmtVerifica->execute();
	$stmtVerifica->store_result();

	if ($stmtVerifica->num_rows > 0) {
		$sql = "UPDATE alunos_exercicios SET resultado = ?, status = 1 WHERE id_usuario = ? AND id_exercicios = ?";
	} else {
		$sql = "INSERT INTO alunos_exercicios (id_usuario, id_exercicios, resultado, status) VALUES (?, ?, ?, 1)";
	}
	$stmtVerifica->close();

	$stmt = $conn->prepare($sql);
	if ($stmt->bind_param("iii", $resultado, $idUsuario, $idExercicio)) {
		$stmt->execute();
		$stmt->close();
	} else {
		throw new Exception('Erro ao preparar consulta.');
	}

	// Resposta final
	$response = [
		'status' => 'success',
		'message' => $mensagem,
		'performance' => [
			'acertos' => $_SESSION['acertos'],
			'total' => $_SESSION['total'],
			'percentual' => round(($_SESSION['acertos'] / $_SESSION['total']) * 100, 2)
		]
	];
} catch (Exception $e) {
	$response = [
		'status' => 'error',
		'message' => 'Erro interno.',
		'debug' => $e->getMessage()
	];
}

// Envia a resposta JSON
echo json_encode($response);
exit;
