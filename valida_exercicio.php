<?php
session_start();
ini_set('display_errors', 0); // Não mostrar erros diretamente na saída
error_reporting(0); // Desativar relatórios de erro
ini_set('log_errors', 1);
ini_set('error_log', 'valida_exercicio_error.log');


header('Content-Type: application/json');
if (headers_sent()) {
	echo json_encode(['status' => 'error', 'message' => 'Headers já enviados.']);
	exit;
}

// Verificar método HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	echo json_encode(['status' => 'error', 'message' => 'Método inválido. Apenas POST é permitido.']);
	exit;
}

// Verificar autenticação
if (!isset($_SESSION['AlunoId'])) {
	echo json_encode(['status' => 'error', 'message' => 'Usuário não autenticado.']);
	exit;
}

// Conexão com o banco de dados
include_once("conexao.php");


// Validação dos dados recebidos

$idExercicio = isset($_POST['id_exercicios']) ? intval($_POST['id_exercicios']) : null;
$resposta = isset($_POST['resposta']) ? trim($_POST['resposta']) : null;

if (!$idExercicio || !$resposta) {
	echo json_encode([
		"status" => "error",
		"message" => "Dados inválidos fornecidos.",
		"debug" => [
			"id_exercicios" => $idExercicio,
			"resposta" => $resposta
		]
	]);
	exit;
}
try {
	// Seu código existente aqui...
	echo json_encode(['status' => 'success', 'message' => 'Resposta processada.']);
} catch (Exception $e) {
	echo json_encode([
		'status' => 'error',
		'message' => 'Erro interno.',
		'debug' => $e->getMessage()
	]);
}

exit;


// Identificação do usuário
$idUsuario = intval($_SESSION['AlunoId']);

// Verificar se o exercício existe
$sqlExercicio = "SELECT resposta FROM exercicios WHERE id = ?";
$stmtExercicio = $conn->prepare($sqlExercicio);
$stmtExercicio->bind_param("i", $idExercicio);
$stmtExercicio->execute();
$stmtExercicio->store_result();

if ($stmtExercicio->num_rows === 0) {
	echo json_encode(['status' => 'error', 'message' => 'Exercício não encontrado.']);
	exit;
}

$stmtExercicio->bind_result($resposta);
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

// Determinar o resultado
$resultado = strtolower(trim($resposta)) === strtolower(trim($resposta)) ? 1 : 2;
$status = 1; // Marcado como concluído

// Inserir ou atualizar o status do exercício
$sql = $stmtVerifica->num_rows > 0
	? "UPDATE alunos_exercicios SET resultado = ?, status = ? WHERE id_usuario = ? AND id_exercicios = ?"
	: "INSERT INTO alunos_exercicios (id_usuario, id_exercicios, resultado, status) VALUES (?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $resultado, $status, $idUsuario, $idExercicio);
$stmt->execute();
$stmt->close();

// Responder ao cliente
$mensagem = $resultado === 1 ? 'Resposta correta! Parabéns!' : 'Resposta incorreta. Tente novamente.';
echo json_encode(['status' => 'success', 'message' => $mensagem]);

// Fechar conexão
$conn->close();
