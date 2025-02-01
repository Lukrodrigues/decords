<?php
session_start();
include_once("conexao.php");

header('Content-Type: application/json');

// Verifica se os dados foram enviados corretamente
if (!isset($_POST['id_exercicios'], $_POST['resposta'])) {
	echo json_encode([
		'status' => 'error',
		'resultado' => 'erro',
		'message' => 'Dados inválidos ou incompletos.'
	]);
	exit;
}

$idExercicio = intval($_POST['id_exercicios']);
$respostaAluno = trim($_POST['resposta']);
$alunoId = $_SESSION['AlunoId'] ?? null;
$nivelAtual = $_SESSION['AlunoNivel'] ?? 1;

if (!$alunoId) {
	echo json_encode([
		'status' => 'error',
		'resultado' => 'erro',
		'message' => 'Você não está logado.'
	]);
	exit;
}

// Busca a resposta correta no banco de dados
$sqlResposta = "SELECT resposta FROM exercicios WHERE id = ?";
$stmt = $conn->prepare($sqlResposta);
$stmt->bind_param("i", $idExercicio);
$stmt->execute();
$result = $stmt->get_result();
$respostaCorreta = $result->fetch_assoc()['resposta'];
$stmt->close();

if (!$respostaCorreta) {
	echo json_encode([
		'status' => 'error',
		'resultado' => 'erro',
		'message' => 'Exercício não encontrado.'
	]);
	exit;
}

// Verifica se a resposta do aluno está correta
$resultado = ($respostaAluno === $respostaCorreta) ? 1 : 2;

// Registra a resposta no banco de dados
$sqlRegistro = "
    INSERT INTO alunos_exercicios (id_usuario, id_exercicios, resultado, status)
    VALUES (?, ?, ?, 1)
    ON DUPLICATE KEY UPDATE resultado = VALUES(resultado), status = 1";
$stmtRegistro = $conn->prepare($sqlRegistro);
$stmtRegistro->bind_param("iii", $alunoId, $idExercicio, $resultado);
$stmtRegistro->execute();
$stmtRegistro->close();

// Consulta o total de exercícios do nível
$sqlTotalExercicios = "SELECT COUNT(*) AS total FROM exercicios WHERE nivel = ?";
$stmtTotal = $conn->prepare($sqlTotalExercicios);
$stmtTotal->bind_param("i", $nivelAtual);
$stmtTotal->execute();
$stmtTotal->bind_result($totalExerciciosNivel);
$stmtTotal->fetch();
$stmtTotal->close();

// Conta os acertos do aluno
$sqlDesempenho = "
    SELECT COUNT(*) AS total,
           SUM(CASE WHEN resultado = 1 THEN 1 ELSE 0 END) AS acertos
    FROM alunos_exercicios ae
    INNER JOIN exercicios e ON ae.id_exercicios = e.id
    WHERE ae.id_usuario = ? AND e.nivel = ? AND ae.status = 1";
$stmtDesempenho = $conn->prepare($sqlDesempenho);
$stmtDesempenho->bind_param("ii", $alunoId, $nivelAtual);
$stmtDesempenho->execute();
$stmtDesempenho->bind_result($totalRespondidos, $totalAcertos);
$stmtDesempenho->fetch();
$stmtDesempenho->close();

// Calcula o percentual de acertos
$percentualAcertos = ($totalRespondidos > 0) ? ($totalAcertos / $totalExerciciosNivel) * 100 : 0;

$paginaAtual = ($nivelAtual == 1) ? "iniciantes.php" : (($nivelAtual == 2) ? "intermediarios.php" : "avancados.php");
$proximaPagina = ($nivelAtual == 1) ? "intermediarios.php" : (($nivelAtual == 2) ? "avancados.php" : "parabens.php");

// Verifica se o aluno respondeu tudo
if ($totalRespondidos >= $totalExerciciosNivel) {
	if ($percentualAcertos >= 60) {
		$_SESSION['AlunoNivel'] = $nivelAtual + 1;
		echo json_encode([
			'status' => 'success',
			'resultado' => 'acerto',
			'message' => 'Parabéns! Você avançou para o próximo nível!',
			'redirect' => $proximaPagina
		]);
	} else {
		echo json_encode([
			'status' => 'error',
			'resultado' => 'erro',
			'message' => 'Você precisa de pelo menos 60% de acertos para avançar. Tente novamente!',
			'redirect' => $paginaAtual
		]);
	}
} else {
	echo json_encode([
		'status' => 'success',
		'resultado' => ($resultado === 1) ? 'acerto' : 'erro',
		'message' => ($resultado === 1) ? 'Resposta correta! Continue assim.' : 'Resposta errada. Tente novamente!',
		'redirect' => $paginaAtual
	]);
}
