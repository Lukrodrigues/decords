<?php
session_start();
header('Content-Type: application/json');

$desempenho = $_POST['desempenho'] ?? 0;
$nivelAluno = $_SESSION['aluno_nivel'] ?? 1;

$menuItens = [
    1 => ['nome' => 'Iniciantes', 'link' => 'iniciantes.php'],
    2 => ['nome' => 'Intermediários', 'link' => 'intermediarios.php'],
    3 => ['nome' => 'Avançados', 'link' => 'avancados.php'],
];

// Avança de nível se desempenho >= 60
if ($desempenho >= 60 && $nivelAluno < max(array_keys($menuItens))) {
    $nivelAluno++;
    $_SESSION['aluno_nivel'] = $nivelAluno;
    $_SESSION['aluno_desempenho'] = 0;
}

// Atualiza status do menu
$menuStatus = [];
foreach ($menuItens as $nivel => $dados) {
    if ($nivel < $nivelAluno) $menuStatus[$nivel] = 'concluido';
    elseif ($nivel == $nivelAluno) $menuStatus[$nivel] = 'andamento';
    else $menuStatus[$nivel] = 'bloqueado';
}

echo json_encode(['status' => 'ok', 'menuStatus' => $menuStatus, 'nivelAluno' => $nivelAluno]);
