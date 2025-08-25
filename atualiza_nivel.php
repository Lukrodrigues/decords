<?php
session_start();
include_once("conexao.php");

// Obtém o nível atual
$nivelAtual = $_SESSION['aluno_nivel'] ?? 1;
$nivelMaximo = 3;

// Avança nível
if ($nivelAtual < $nivelMaximo) {
    $nivelAtual++;
    $_SESSION['aluno_nivel'] = $nivelAtual;
    $_SESSION['aluno_desempenho'] = 0;
}

// Menu atualizado
$menuItens = [
    1 => ['nome' => 'Iniciantes', 'link' => 'iniciantes.php'],
    2 => ['nome' => 'Intermediários', 'link' => 'intermediarios.php'],
    3 => ['nome' => 'Avançados', 'link' => 'avancados.php'],
];

function getMenuStatus($menuItens, $nivelAluno)
{
    $status = [];
    foreach ($menuItens as $nivel => $dados) {
        if ($nivel < $nivelAluno) $status[$nivel] = 'concluido';
        elseif ($nivel == $nivelAluno) $status[$nivel] = 'andamento';
        else $status[$nivel] = 'bloqueado';
    }
    return $status;
}

$menuStatus = getMenuStatus($menuItens, $nivelAtual);
$htmlMenu = '';
foreach ($menuItens as $nivel => $dados) {
    $classe = $menuStatus[$nivel] === 'concluido' ? 'menu-concluido' : ($menuStatus[$nivel] === 'andamento' ? 'menu-em-andamento' : 'menu-bloqueado');
    $status = $menuStatus[$nivel] === 'concluido' ? ' - Concluído ✅' : ($menuStatus[$nivel] === 'andamento' ? ' - Em andamento 🚀' : ' - Bloqueado 🔒');
    if ($menuStatus[$nivel] === 'bloqueado') {
        $htmlMenu .= "<li class='disabled'><span class='$classe'>{$dados['nome']}{$status}</span></li><li class='divider'></li>";
    } else {
        $htmlMenu .= "<li><a href='{$dados['link']}' class='$classe'>{$dados['nome']}{$status}</a></li><li class='divider'></li>";
    }
}

echo json_encode([
    'sucesso' => true,
    'nivelAtual' => $nivelAtual,
    'htmlMenu' => $htmlMenu
]);
