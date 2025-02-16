<?php
session_start();
include_once("conexao.php");

header('Content-Type: application/json');

// Verifica se os dados necessários foram enviados
if (!isset($_POST['id_exercicios'], $_POST['resposta'])) {
	echo json_encode([
		'status' => 'error',
		'resultado' => 'erro',
		'message' => 'Dados inválidos ou incompletos.'
	]);
	exit;
}

// Captura os dados
$idExercicio = intval($_POST['id_exercicios']);
$respostaAluno = trim($_POST['resposta']);
$alunoId = $_SESSION['AlunoId'] ?? null;
$nivelAtual = $_SESSION['AlunoNivel'] ?? 1;

// Verifica se o aluno está logado
if (!$alunoId) {
	echo json_encode([
		'status' => 'error',
		'resultado' => 'erro',
		'message' => 'Você não está logado.'
	]);
	exit;
}

// Consulta a resposta correta
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

// Verifica se a resposta está correta (comparação exata; ajuste se necessário para case-insensitive)
$resultado = (strcasecmp($respostaAluno, $respostaCorreta) === 0) ? 1 : 2;

// Registra ou atualiza a resposta do aluno no banco
$sqlRegistro = "
    INSERT INTO alunos_exercicios (id_usuario, id_exercicios, resultado, status)
    VALUES (?, ?, ?, 1)
    ON DUPLICATE KEY UPDATE resultado = VALUES(resultado), status = 1";
$stmtRegistro = $conn->prepare($sqlRegistro);
$stmtRegistro->bind_param("iii", $alunoId, $idExercicio, $resultado);
$stmtRegistro->execute();
$stmtRegistro->close();

// Consulta o total de exercícios do nível atual
$sqlTotalExercicios = "SELECT COUNT(*) AS total FROM exercicios WHERE nivel = ?";
$stmtTotal = $conn->prepare($sqlTotalExercicios);
$stmtTotal->bind_param("i", $nivelAtual);
$stmtTotal->execute();
$stmtTotal->bind_result($totalExerciciosNivel);
$stmtTotal->fetch();
$stmtTotal->close();

// Consulta o desempenho do aluno no nível atual
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

// Define as páginas para redirecionamento
$paginaAtual = ($nivelAtual == 1) ? "iniciantes.php" : (($nivelAtual == 2) ? "intermediarios.php" : "avancados.php");
$proximaPagina = ($nivelAtual == 1) ? "intermediarios.php" : (($nivelAtual == 2) ? "avancados.php" : "login.php");

// Se o aluno respondeu todos os exercícios do nível:
if ($totalRespondidos >= $totalExerciciosNivel) {
	if ($percentualAcertos >= 60) {
		if ($nivelAtual == 3) {
			// Nível avançado concluído com sucesso: redireciona para login.php
			echo json_encode([
				'status' => 'success',
				'resultado' => 'acerto',
				'message' => 'Parabéns! Você concluiu todos os níveis e está apto a iniciar a tocar violão.',
				'redirect' => 'login.php'
			]);
		} else {
			// Avança para o próximo nível (para iniciantes e intermediários)
			$_SESSION['AlunoNivel'] = $nivelAtual + 1;
			echo json_encode([
				'status' => 'success',
				'resultado' => 'acerto',
				'message' => 'Parabéns! Você concluiu o nível atual com sucesso e avançará para o próximo nível!',
				'redirect' => $proximaPagina
			]);
		}
	} else {
		// Se não atingir 60%, exibe mensagem e permanece no mesmo nível (pode ser implementado reset se necessário)
		echo json_encode([
			'status' => 'error',
			'resultado' => 'erro',
			'message' => 'Você concluiu o nível, mas não atingiu a pontuação mínima de 60% para avançar. Tente novamente!',
			'redirect' => $paginaAtual
		]);
	}
} else {
	// Se ainda não concluiu todos os exercícios, retorna mensagem informativa
	if ($resultado === 1) {
		echo json_encode([
			'status' => 'success',
			'resultado' => 'acerto',
			'message' => 'Resposta correta! Parabéns continue os exercicios seguintes.',
			'redirect' => $paginaAtual
		]);
	} else {
		echo json_encode([
			'status' => 'danger',
			'resultado' => 'erro',
			'message' => 'Resposta incorreta. Não desista, tente novamente!',
			'redirect' => $paginaAtual
		]);
	}
}
