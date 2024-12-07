<?php
header('Content-Type: text/html; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
if (!isset($_SESSION['AlunoId'])) {
	die("Acesso negado!");
}

include_once("conexao.php");


$aluno = intval($_SESSION['AlunoId']);
$exe = intval($_POST['exe']);
$escolha = $_POST['escolha'];
$resposta = $_POST['resposta'];

// Determinar resultado
$resultado = ($escolha === $resposta) ? 1 : 2;
$status = 1;

// Atualizar exercício
$sql = "UPDATE alunos_exercicios 
        SET resultado = ?, status = ?, data_termino = NOW() 
        WHERE id_usuario = ? AND id_exercicios = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
	die("Erro na preparação da consulta: " . $conn->error);
}

$stmt->bind_param("iiii", $resultado, $status, $aluno, $exe);

if (!$stmt->execute()) {
	die("Erro na execução da consulta: " . $stmt->error);
} else {
	echo "Exercício atualizado com sucesso!";
}
if ($resultado === 1) {
	echo "Acertou";
} else {
	echo "Errou";
}

$stmt->close();
$conn->close();
