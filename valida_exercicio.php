<?php
session_start();
header('Content-Type: text/html; charset=utf-8');

// Verificar se o usuário está autenticado
if (!isset($_SESSION['AlunoId'])) {
	echo json_encode(['status' => 'error', 'message' => 'Usuário não autenticado.']);
	exit;
}

// Conexão com o banco de dados
include_once("conexao.php");

// Validações dos dados recebidos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$idExercicio = filter_input(INPUT_POST, 'id_exercicio', FILTER_VALIDATE_INT);
	$resposta = filter_input(INPUT_POST, 'resposta', FILTER_SANITIZE_STRING);

	if (!$idExercicio || !$resposta) {
		echo json_encode([
			'status' => 'error',
			'message' => 'Dados inválidos fornecidos.',
			'debug' => $_POST // Para debug temporário
		]);
		exit;
	}

	// Identificação do usuário
	$idUsuario = intval($_SESSION['AlunoId']);

	// Verificar se o exercício existe
	$sqlExercicio = "SELECT resposta_correta FROM exercicios WHERE id = ?";
	$stmtExercicio = $conn->prepare($sqlExercicio);
	$stmtExercicio->bind_param("i", $idExercicio);
	$stmtExercicio->execute();
	$stmtExercicio->store_result();

	if ($stmtExercicio->num_rows === 0) {
		echo json_encode(['status' => 'error', 'message' => 'Exercício não encontrado.']);
		exit;
	}

	$stmtExercicio->bind_result($respostaCorreta);
	$stmtExercicio->fetch();
	$stmtExercicio->close();

	// Verificar se o usuário já realizou o exercício
	$sqlVerifica = "SELECT status FROM alunos_exercicios WHERE id_usuario = ? AND id_exercicios = ?";
	$stmtVerifica = $conn->prepare($sqlVerifica);
	$stmtVerifica->bind_param("ii", $idUsuario, $idExercicio);
	$stmtVerifica->execute();
	$stmtVerifica->store_result();

	if ($stmtVerifica->num_rows > 0) {
		$stmtVerifica->bind_result($status);
		$stmtVerifica->fetch();

		if ($status === 1) {
			echo json_encode(['status' => 'error', 'message' => 'Exercício já realizado.']);
			exit;
		}
	}
	$stmtVerifica->close();

	// Determinar o resultado (se a resposta fornecida é a correta)
	$resultado = strtolower(trim($resposta)) === strtolower(trim($respostaCorreta)) ? 1 : 2;
	$status = 1; // Marcado como concluído

	// Inserir ou atualizar o status do exercício
	if ($stmtVerifica->num_rows > 0) {
		$sqlAtualiza = "UPDATE alunos_exercicios SET resultado = ?, status = ? WHERE id_usuario = ? AND id_exercicios = ?";
		$stmtAtualiza = $conn->prepare($sqlAtualiza);
		$stmtAtualiza->bind_param("iiii", $resultado, $status, $idUsuario, $idExercicio);
		$stmtAtualiza->execute();
		$stmtAtualiza->close();
	} else {
		$sqlInsere = "INSERT INTO alunos_exercicios (id_usuario, id_exercicios, resultado, status) VALUES (?, ?, ?, ?)";
		$stmtInsere = $conn->prepare($sqlInsere);
		$stmtInsere->bind_param("iiii", $idUsuario, $idExercicio, $resultado, $status);
		$stmtInsere->execute();
		$stmtInsere->close();
	}

	// Resposta ao cliente
	$mensagem = $resultado === 1 ? 'Resposta correta!' : 'Resposta incorreta!';
	echo json_encode(['status' => 'success', 'message' => $mensagem]);
} else {
	// Caso o método HTTP não seja POST
	echo json_encode(['status' => 'error', 'message' => 'Método inválido.']);
	exit;
}

// Fechar conexão
$conn->close();
