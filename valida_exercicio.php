<?php
session_start();
include_once("conexao.php");

header('Content-Type: application/json');

// Verifica se os dados necessários foram enviados
if (!isset($_POST['id_exercicios'], $_POST['resposta'])) {
	echo json_encode([
		'status' => 'error',
		'message' => 'Dados inválidos ou incompletos.'
	]);
	exit;
}

// Captura os dados
$idExercicio = intval($_POST['id_exercicios']);
$respostaAluno = $_POST['resposta'];
$alunoId = $_SESSION['AlunoId'] ?? null;
$nivelAtual = $_SESSION['AlunoNivel'] ?? 1;

// Verifica se o aluno está logado
if (!$alunoId) {
	echo json_encode([
		'status' => 'error',
		'message' => 'Você não está logado.'
	]);
	exit;
}

// Consulta a resposta correta
$sqlResposta = "SELECT resposta FROM exercicios WHERE id = ?";
$stmtResposta = $conn->prepare($sqlResposta);
$stmtResposta->bind_param("i", $idExercicio);
$stmtResposta->execute();
$result = $stmtResposta->get_result();
$respostaCorreta = $result->fetch_assoc()['resposta'];
$stmtResposta->close();

if (!$respostaCorreta) {
	echo json_encode([
		'status' => 'error',
		'message' => 'Exercício não encontrado.'
	]);
	exit;
}

// Verifica a resposta e registra no banco
$resultado = ($respostaAluno === $respostaCorreta) ? 1 : 2;
$sqlRegistro = "
    INSERT INTO alunos_exercicios (id_usuario, id_exercicios, resultado, status)
    VALUES (?, ?, ?, 1)
    ON DUPLICATE KEY UPDATE resultado = VALUES(resultado), status = 1";
$stmtRegistro = $conn->prepare($sqlRegistro);
$stmtRegistro->bind_param("iii", $alunoId, $idExercicio, $resultado);
$stmtRegistro->execute();
$stmtRegistro->close();

// Consulta a quantidade total de exercícios no nível atual
$sqlTotalExercicios = "
    SELECT COUNT(*) AS total
    FROM exercicios
    WHERE nivel = ?";
$stmtTotal = $conn->prepare($sqlTotalExercicios);
$stmtTotal->bind_param("i", $nivelAtual);
$stmtTotal->execute();
$stmtTotal->bind_result($totalExerciciosNivel);
$stmtTotal->fetch();
$stmtTotal->close();

// Calcula o desempenho do aluno no nível atual
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

// Verifica se o aluno respondeu todos os exercícios do nível
if ($totalRespondidos >= $totalExerciciosNivel) {
	if ($percentualAcertos >= 60) {
		// Aluno atingiu 60% ou mais de acertos e concluiu o nível
		$_SESSION['AlunoNivel'] = $nivelAtual + 1;

		echo json_encode([
			'status' => 'success',
			'message' => 'Parabéns! Você concluiu o nível atual com sucesso e avançará para o próximo nível!'
		]);
	} else {
		// Aluno respondeu todos os exercícios, mas não atingiu 60% de acertos
		echo json_encode([
			'status' => 'error',
			'message' => 'Você concluiu o nível atual, mas não atingiu a pontuação mínima de 60% para avançar. Tente novamente!'
		]);
	}
} else {
	// Aluno ainda não concluiu todos os exercícios do nível
	echo json_encode([
		'status' => 'success',
		'message' => 'Resposta registrada com sucesso! Continue respondendo os exercícios para concluir o nível.'
	]);
}
