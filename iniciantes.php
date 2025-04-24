<?php

declare(strict_types=1);
session_start();

// 1. Se solicitado reset, apaga todas as tentativas do nível atual
if (isset($_GET['reset']) && $_GET['reset'] === '1') {
    require_once 'conexao.php';
    $alunoId = (int) $_SESSION['aluno_id'];

    // Busca nível atual
    $stmtNivel = $conn->prepare("SELECT nivel FROM alunos WHERE id = ?");
    $stmtNivel->bind_param('i', $alunoId);
    $stmtNivel->execute();
    $nivel = (int) $stmtNivel->get_result()->fetch_assoc()['nivel'];
    $stmtNivel->close();

    // Deleta tentativas
    $stmt = $conn->prepare(
        "DELETE ae FROM alunos_exercicios ae
         JOIN exercicios e ON ae.id_exercicios = e.id
         WHERE ae.id_usuario = ? AND e.nivel = ?"
    );
    $stmt->bind_param('ii', $alunoId, $nivel);
    $stmt->execute();
    $stmt->close();

    $_SESSION['mensagem'] = "Infelizmente não atingiu o desempenho para próximo nível!! Tente novamente.";
    header('Location: iniciantes.php');
    exit;
}

// 2. Captura e limpa mensagem de flash
$mensagem = $_SESSION['mensagem'] ?? '';
unset($_SESSION['mensagem']);

// 3. Conexão e lógica original de busca de exercícios/desempenho
require_once 'conexao.php';
$alunoId = (int) $_SESSION['aluno_id'];

// Busca nível
$stmt      = $conn->prepare("SELECT nivel FROM alunos WHERE id = ?");
$stmt->bind_param('i', $alunoId);
$stmt->execute();
$nivelAtual  = (int) $stmt->get_result()->fetch_assoc()['nivel'];
$stmt->close();

// Total disponível e exibido (até 10)
$stmt      = $conn->prepare("SELECT COUNT(*) AS total_questions FROM exercicios WHERE nivel = ?");
$stmt->bind_param('i', $nivelAtual);
$stmt->execute();
$totalDB   = (int) $stmt->get_result()->fetch_assoc()['total_questions'];
$stmt->close();
$totalExib = min(10, $totalDB);

// Desempenho atual
$sqlPerf = "
    SELECT
      SUM(CASE WHEN ae.resultado = 1 THEN 1 ELSE 0 END) AS acertos,
      SUM(CASE WHEN ae.resultado = 0 THEN 1 ELSE 0 END) AS erros
    FROM alunos_exercicios ae
    JOIN exercicios e ON ae.id_exercicios = e.id
    WHERE ae.id_usuario = ? AND e.nivel = ?
";
$stmt = $conn->prepare($sqlPerf);
$stmt->bind_param('ii', $alunoId, $nivelAtual);
$stmt->execute();
$stmt->bind_result($acertos, $erros);
$stmt->fetch();
$stmt->close();

$naoResp   = $totalExib - ($acertos + $erros);
$percent   = $totalExib > 0 ? ($acertos / $totalExib) * 100 : 0;

// Lista de até 10 exercícios
$sqlEx = "
    SELECT
      e.id, e.pergunta,
      IF(MAX(ae.status)=1,'Sim','Não') AS concluido,
      CASE
        WHEN MAX(ae.status)=1 AND MAX(ae.resultado)=1 THEN 'Certo'
        WHEN MAX(ae.status)=1 AND MAX(ae.resultado)=0 THEN 'Errado'
        ELSE '--'
      END AS resultado
    FROM exercicios e
    LEFT JOIN alunos_exercicios ae
      ON e.id = ae.id_exercicios AND ae.id_usuario = ?
    WHERE e.nivel = ?
    GROUP BY e.id
    ORDER BY e.id ASC
    LIMIT 10
";
$stmt = $conn->prepare($sqlEx);
$stmt->bind_param('ii', $alunoId, $nivelAtual);
$stmt->execute();
$exercicios = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Exercícios Iniciantes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">DECORDS</a>
            <span class="navbar-text text-light">
                <?= htmlspecialchars($_SESSION['aluno_nome'] ?? 'Usuário') ?>
                <span class="badge bg-secondary">Nível <?= $nivelAtual ?></span>
            </span>
        </div>
    </nav>

    <div class="container">

        <?php if ($mensagem): ?>
            <div class="alert alert-info"><?= htmlspecialchars($mensagem) ?></div>
        <?php endif; ?>

        <!-- Desempenho -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Desempenho (<?= number_format($percent, 1) ?>%)
            </div>
            <div class="card-body">
                <div class="progress mb-3" style="height:25px;">
                    <div class="progress-bar" role="progressbar"
                        style="width:<?= $percent ?>%"
                        aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100">
                        <?= number_format($percent, 1) ?>%
                    </div>
                </div>
                <div class="d-flex gap-3">
                    <span class="badge bg-success">Acertos: <?= $acertos ?></span>
                    <span class="badge bg-danger">Erros: <?= $erros ?></span>
                    <span class="badge bg-secondary">Não resp.: <?= $naoResp ?></span>
                </div>
            </div>
        </div>

        <!-- Exercícios -->
        <div class="card">
            <div class="card-header bg-primary text-white">Exercícios</div>
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Pergunta</th>
                            <th>Concluído</th>
                            <th>Resultado</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($exercicios): foreach ($exercicios as $i => $e): ?>
                                <tr class="<?= $e['concluido'] === 'Sim' ? 'table-success' : '' ?>">
                                    <td><?= $i + 1 ?></td>
                                    <td><?= htmlspecialchars($e['pergunta']) ?></td>
                                    <td><?= $e['concluido'] ?></td>
                                    <td>
                                        <?php if ($e['resultado'] == 'Certo'): ?>
                                            <span class="badge bg-success">Certo</span>
                                        <?php elseif ($e['resultado'] == 'Errado'): ?>
                                            <span class="badge bg-danger">Errado</span>
                                        <?php else: ?>
                                            --
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($e['concluido'] === 'Não'): ?>
                                            <a href="exercicio.php?id=<?= $e['id'] ?>" class="btn btn-sm btn-primary">Iniciar</a>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">Concluído</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach;
                        else: ?>
                            <tr>
                                <td colspan="5" class="text-center">Nenhum exercício disponível</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>