<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['aluno_logado']) || $_SESSION['aluno_logado'] !== true) {
    echo json_encode(['error' => 'Não logado']);
    exit;
}

$nivelMax = 3;
$nivel = $_SESSION['aluno_nivel'] ?? 1;
$desempenho = floatval($_SESSION['aluno_desempenho'] ?? 0);
$novoDesempenho = isset($_POST['desempenho']) ? floatval($_POST['desempenho']) : null;

if ($novoDesempenho !== null) {
    $desempenho = $novoDesempenho;
    $_SESSION['aluno_desempenho'] = $desempenho;

    if ($desempenho >= 60 && $nivel < $nivelMax) {
        $nivel++;
        $desempenho = 0;
        $_SESSION['aluno_nivel'] = $nivel;
        $_SESSION['aluno_desempenho'] = 0;
        $avancou = true;
    } else {
        $avancou = false;
    }
} else {
    $avancou = false;
}

echo json_encode([
    'success' => true,
    'avancou' => $avancou,
    'nivel'   => $nivel,
    'desempenho' => $desempenho
]);
