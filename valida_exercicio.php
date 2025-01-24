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
$stmtDesempenho->bind_result($totalExercicios, $totalAcertos);
$stmtDesempenho->fetch();
$stmtDesempenho->close();

// Calcula percentual de acertos
$percentualAcertos = ($totalExercicios > 0) ? ($totalAcertos / $totalExercicios) * 100 : 0;

// Verifica se o aluno concluiu o nível
if ($totalExercicios > 0 && $percentualAcertos >= 60) {
	// Atualiza o nível do aluno para o próximo
	$_SESSION['AlunoNivel'] = $nivelAtual + 1;

	echo json_encode([
		'status' => 'success',
		'message' => 'Parabéns! Você atingiu a pontuação necessária e avançará para o próximo nível!',
		'redirect' => 'intermediarios.php' // Define o redirecionamento
	]);
} else if ($totalExercicios > 0) {
	// Mostra mensagem de desempenho insuficiente
	echo json_encode([
		'status' => 'error',
		'message' => 'Infelizmente, você não atingiu a pontuação necessária para avançar para o próximo nível.'
	]);
} else {
	echo json_encode([
		'status' => 'error',
		'message' => 'Nenhum exercício encontrado no nível atual.'
	]);
}
