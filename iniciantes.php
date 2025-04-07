<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ativa a exibição de erros apenas em desenvolvimento
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

// Configuração de segurança de headers
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

// Configuração da conexão MySQLi
$servername = "localhost";
$dbname = "decords_bd";
$username = "root";
$password = "";

try {
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_errno) {
        throw new Exception("Falha na conexão: " . $conn->connect_error);
    }

    // Verificação de sessão consolidada
    if (!isset($_SESSION['aluno_logado']) || !$_SESSION['aluno_logado'] || !isset($_SESSION['aluno_id'])) {
        header("Location: login_aluno.php");
        exit;
    }

    // Dados do aluno
    $alunoId = (int)$_SESSION['aluno_id'];

    // Busca nível ATUALIZADO do banco
    $stmt = $conn->prepare("SELECT nivel FROM alunos WHERE id = ?");
    $stmt->bind_param('i', $alunoId);

    if (!$stmt->execute()) {
        throw new Exception("Erro ao buscar nível: " . $stmt->error);
    }

    $resultado = $stmt->get_result();
    $aluno = $resultado->fetch_assoc();
    $nivelAtual = (int)($aluno['nivel'] ?? 0);
    $stmt->close();

    // Consulta desempenho
    $sqlDesempenho = "SELECT 
        COUNT(*) AS total,
        SUM(CASE WHEN resultado = 1 THEN 1 ELSE 0 END) AS acertos
        FROM alunos_exercicios ae
        INNER JOIN exercicios e ON ae.id_exercicios = e.id
        WHERE ae.id_usuario = ? AND e.nivel = ?";

    $stmtDesempenho = $conn->prepare($sqlDesempenho);
    $stmtDesempenho->bind_param("ii", $alunoId, $nivelAtual);

    if (!$stmtDesempenho->execute()) {
        throw new Exception("Erro na consulta de desempenho: " . $stmtDesempenho->error);
    }

    $stmtDesempenho->bind_result($total, $acertos);
    $stmtDesempenho->fetch();
    $stmtDesempenho->close();

    // Lógica de reinício
    $percentualAcertos = 0;
    if ($total > 0) {
        $percentualAcertos = ($acertos / $total) * 100;

        if ($percentualAcertos < 60 && !isset($_GET['reiniciado'])) {
            $sqlReset = "UPDATE alunos_exercicios ae
                        INNER JOIN exercicios e ON ae.id_exercicios = e.id
                        SET ae.status = 0, ae.resultado = NULL
                        WHERE ae.id_usuario = ? AND e.nivel = ?";

            $stmtReset = $conn->prepare($sqlReset);
            $stmtReset->bind_param("ii", $alunoId, $nivelAtual);

            if (!$stmtReset->execute()) {
                throw new Exception("Erro ao reiniciar nível: " . $stmtReset->error);
            }

            $stmtReset->close();
            header("Location: iniciantes.php?reiniciado=1");
            exit;
        }
    }

    // Consulta exercícios
    $sqlExercicios = "SELECT 
        e.id,
        e.pergunta,
        IF(ae.status = 1, 'Sim', 'Não') AS concluido,
        CASE 
            WHEN ae.resultado = 1 THEN 'Acertou' 
            WHEN ae.resultado = 0 THEN 'Errou' 
            ELSE '--' 
        END AS resultado
        FROM exercicios e
        LEFT JOIN alunos_exercicios ae 
            ON e.id = ae.id_exercicios AND ae.id_usuario = ?
        WHERE e.nivel = ?";

    $stmt = $conn->prepare($sqlExercicios);
    $stmt->bind_param("ii", $alunoId, $nivelAtual);

    if (!$stmt->execute()) {
        throw new Exception("Erro na consulta de exercícios: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $exercicios = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} catch (Exception $e) {
    die("Erro crítico: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exercícios Iniciantes - DECORDS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --bs-success-bg-subtle: #d1e7dd;
            --bs-danger-bg-subtle: #f8d7da;
        }

        .progress-bar {
            transition: width 0.5s ease-in-out;
        }

        .table-hover tbody tr {
            transition: background-color 0.2s;
        }

        .badge-realizado {
            background-color: var(--bs-success);
            padding: 0.5em 1em;
        }
    </style>
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">DECORDS</a>
            <div class="navbar-text text-light">
                <?= htmlspecialchars($_SESSION['aluno_nome'] ?? 'Usuário') ?>
                <span class="badge bg-secondary">Nível <?= $nivelAtual ?></span>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Seção de Desempenho -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-speedometer2 me-2"></i>Desempenho</h5>
            </div>
            <div class="card-body">
                <?php if ($total > 0): ?>
                    <div class="progress mb-3" style="height: 25px;">
                        <div class="progress-bar bg-success"
                            role="progressbar"
                            style="width: <?= $percentualAcertos ?>%"
                            aria-valuenow="<?= $percentualAcertos ?>"
                            aria-valuemin="0"
                            aria-valuemax="100">
                            <?= number_format($percentualAcertos, 1) ?>%
                        </div>
                    </div>
                    <div class="d-flex gap-3 justify-content-center">
                        <span class="badge bg-success fs-6">Acertos: <?= $acertos ?></span>
                        <span class="badge bg-danger fs-6">Erros: <?= $total - $acertos ?></span>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle me-2"></i>Nenhum exercício concluído ainda
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Lista de Exercícios -->
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-journal-text me-2"></i>Exercícios do Nível</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr class="table-dark">
                                <th scope="col">#</th>
                                <th scope="col">Pergunta</th>
                                <th scope="col">Concluído</th>
                                <th scope="col">Resultado</th>
                                <th scope="col">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($exercicios)): ?>
                                <?php foreach ($exercicios as $index => $exercicio): ?>
                                    <tr class="<?= $exercicio['concluido'] === 'Sim' ? 'table-success' : '' ?>">
                                        <th scope="row"><?= $index + 1 ?></th>
                                        <td><?= htmlspecialchars($exercicio['pergunta']) ?></td>
                                        <td><?= $exercicio['concluido'] ?></td>
                                        <td>
                                            <?php if ($exercicio['resultado'] !== '--'): ?>
                                                <span class="badge <?= $exercicio['resultado'] === 'Acertou' ? 'bg-success' : 'bg-danger' ?>">
                                                    <?= $exercicio['resultado'] ?>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($exercicio['concluido'] === 'Não'): ?>
                                                <a href="exercicio.php?id=<?= $exercicio['id'] ?>"
                                                    class="btn btn-sm btn-primary">
                                                    Iniciar
                                                </a>
                                            <?php else: ?>
                                                <span class="badge badge-realizado">
                                                    <i class="bi bi-check2-circle me-1"></i>Realizado
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <div class="alert alert-warning mb-0">
                                            <i class="bi bi-exclamation-triangle me-2"></i>
                                            Nenhum exercício disponível para este nível!
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Fechar alertas automaticamente após 5 segundos
            setTimeout(() => {
                document.querySelectorAll('.alert').forEach(alert => {
                    new bootstrap.Alert(alert).close();
                });
            }, 5000);
        });
    </script>
</body>

</html>