<?php
session_start();
include_once("conexao.php");

$idAluno = $_SESSION['id_aluno'] ?? null;

if (!$idAluno) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Aluno não autenticado']);
    exit;
}

$sql = "SELECT nivel_atual FROM alunos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idAluno);
$stmt->execute();
$stmt->bind_result($nivelAtual);
$stmt->fetch();
$stmt->close();

echo json_encode(['status' => 'ok', 'nivel_atual' => $nivelAtual]);
