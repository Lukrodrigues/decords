<?php
// save_level_status.php
session_start();
require_once 'conexao.php';
header('Content-Type: application/json; charset=utf-8');

// Expecting POST: nivel (int), status (0/1/2)
if (!isset($_SESSION['aluno_id'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Não logado']);
    exit;
}

$alunoId = (int)$_SESSION['aluno_id'];
$nivel = isset($_POST['nivel']) ? (int)$_POST['nivel'] : null;
$status = isset($_POST['status']) ? (int)$_POST['status'] : null;

if ($nivel === null || !in_array($status, [0, 1, 2], true)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Parâmetros inválidos']);
    exit;
}

// Upsert: insere ou atualiza
$stmt = $conn->prepare("
    INSERT INTO alunos_niveis (id_usuario, nivel, status)
    VALUES (?, ?, ?)
    ON DUPLICATE KEY UPDATE status = VALUES(status), atualizado_em = CURRENT_TIMESTAMP
");
$stmt->bind_param('iii', $alunoId, $nivel, $status);

if ($stmt->execute()) {
    echo json_encode(['ok' => true, 'nivel' => $nivel, 'status' => $status]);
} else {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $stmt->error]);
}
$stmt->close();
