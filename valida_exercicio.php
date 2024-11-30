<?php
// Verifica se a sessão não foi iniciada
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

header('Content-Type: text/html; charset=utf-8');
include_once("conexao.php");

// Valida os parâmetros enviados
if (!isset($_POST['escolha'], $_POST['resposta'], $_POST['cod'], $_POST['exe'])) {
	echo "Erro: Parâmetros insuficientes.";
	exit;
}

header('Content-Type: text/html; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

$escolha = $_POST['escolha'];
$resp = $_POST['resposta'];
$cod = $_POST['cod']; // ID do aluno
$exe = $_POST['exe']; // ID do exercício
$nivell = $_SESSION['AlunoNivel']; // Nível do aluno
$data = date("Y-m-d H:i:sa");
$linkk = "Exercicios.php";

// Conexão com o banco de dados usando MySQLi
include_once("conexao.php");

// Verifica se a resposta está correta
if ($escolha == $resp) {

	// Definindo a consulta com placeholders (?):
	$sql5 = "INSERT INTO alunos_exercicios (id_usuario, id_exercicios, data_termino, resultado, status) 
VALUES (?, ?, ?, ?, ?)";

	// Prepara a consulta:
	if ($stmt5 = $conn->prepare($sql5)) {
		// Definindo os tipos dos parâmetros: i = inteiro, s = string
		$stmt5->bind_param("iisss", $cod, $exe, $data, $resultado, $status);

		// Definindo as variáveis
		$resultado = 1;  // Resposta correta
		$status = 1;     // Status de exercício concluído

		// Executa a consulta
		$stmt5->execute();

		// Verifica se a execução foi bem-sucedida
		if ($stmt5->affected_rows > 0) {
			echo "Atividade registrada com sucesso!";
		} else {
			echo "Erro ao registrar atividade.";
		}

		// Fecha o statement
		$stmt5->close();
	} else {
		die("Erro ao preparar a consulta: " . $conn->error);
	}

	// Verifica se todos os exercícios do nível atual foram concluídos
	$sql_check_concluidos = "SELECT COUNT(*) FROM alunos_exercicios 
                             WHERE id_usuario = ? AND status = 1 
                             AND id_exercicios IN (SELECT id FROM exercicios WHERE nivel = ?)";
	if ($stmt_check_concluidos = $conn->prepare($sql_check_concluidos)) {
		$stmt_check_concluidos->bind_param("ii", $cod, $nivell);
		$stmt_check_concluidos->execute();
		$stmt_check_concluidos->bind_result($concluidos);
		$stmt_check_concluidos->fetch();
		$stmt_check_concluidos->close();
	} else {
		die("Erro ao preparar a consulta: " . $conn->error);
	}

	// Conta o total de exercícios do nível atual
	$sql_total_exercicios = "SELECT COUNT(*) FROM exercicios WHERE nivel = ?";
	if ($stmt_total = $conn->prepare($sql_total_exercicios)) {
		$stmt_total->bind_param("i", $nivell);
		$stmt_total->execute();
		$stmt_total->bind_result($total_exercicios);
		$stmt_total->fetch();
		$stmt_total->close();
	} else {
		die("Erro ao preparar a consulta: " . $conn->error);
	}

	// Se todos os exercícios do nível foram concluídos, avança para o próximo nível
	if ($concluidos == $total_exercicios) {
		$_SESSION['AlunoNivel'] = $nivell + 1;  // Avança o nível do aluno
		echo "<div class='alert alert-success'>Parabéns! Você concluiu o nível $nivell. Você foi promovido ao próximo nível!</div>";
	} else {
		echo "<div class='alert alert-info'>Você concluiu este exercício! Continue para o próximo.</div>";
	}
} else {
	echo "<div class='alert alert-danger'>Resposta incorreta! A resposta correta é: <strong>$resp</strong></div>";
}

// Fecha a conexão com o banco de dados

try {
	// [Seu código para a execução das consultas vai aqui]

	// Fecha os statements se foram inicializados
	if (isset($stmt)) $stmt->close();
	if (isset($stmtCheck)) $stmtCheck->close();
	if (isset($stmtUpdate)) $stmtUpdate->close();
} catch (Exception $e) {
	echo "<div class='alert alert-danger'>Erro: " . $e->getMessage() . "</div>";
}

$conn->close();
