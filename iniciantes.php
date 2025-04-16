<?php
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configurações de segurança e cache
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

// Dados de conexão com o banco
$servername = "localhost";
$dbname     = "decords_bd";
$username   = "root";
$password   = "";

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_errno) {
        throw new Exception("Falha na conexão: " . $conn->connect_error);
    }

    // Verifica sessão do aluno
    if (!isset($_SESSION['aluno_logado']) || !$_SESSION['aluno_logado'] || !isset($_SESSION['aluno_id'])) {
        header("Location: login_aluno.php");
        exit;
    }
    $alunoId = (int) $_SESSION['aluno_id'];

    // Busca o nível atualizado do aluno
    $stmt = $conn->prepare("SELECT nivel FROM alunos WHERE id = ?");
    $stmt->bind_param('i', $alunoId);
    if (!$stmt->execute()) {
        throw new Exception("Erro ao buscar nível: " . $stmt->error);
    }
    $resultado = $stmt->get_result();
    $aluno     = $resultado->fetch_assoc();
    $nivelAtual = (int)($aluno['nivel'] ?? 0);
    $stmt->close();

    // Reset de nível se necessário
    if (isset($_GET['reset'])) {
        $stmtReset = $conn->prepare(
            "DELETE FROM alunos_exercicios 
             WHERE id_usuario = ? 
               AND id_exercicios IN (
                   SELECT id FROM exercicios WHERE nivel = ?
               )"
        );
        $stmtReset->bind_param("ii", $alunoId, $nivelAtual);
        $stmtReset->execute();
        $stmtReset->close();
        $_SESSION['mensagem'] = "Nível reiniciado. Boa sorte!";
        header("Location: iniciantes.php");
        exit;
    }

    /*--- Performance ---*/
    // Total de exercícios do nível (pode ser mais que 10, mas a página exibe 10)
    $stmtTotal = $conn->prepare("SELECT COUNT(*) as total_questions FROM exercicios WHERE nivel = ?");
    $stmtTotal->bind_param("i", $nivelAtual);
    if (!$stmtTotal->execute()) {
        throw new Exception("Erro ao buscar total de questões: " . $stmtTotal->error);
    }
    $resultTotal = $stmtTotal->get_result();
    $rowTotal    = $resultTotal->fetch_assoc();
    $totalQuestionsDB = (int)$rowTotal['total_questions'];
    $stmtTotal->close();

    // Para efeito de exibição, usamos somente os 10 exercícios da página
    $totalQuestionsExibidas = ($totalQuestionsDB > 10) ? 10 : $totalQuestionsDB;

    // Consulta a soma de acertos e erros para o nível
    $sqlPerformance = "SELECT 
            SUM(CASE WHEN ae.resultado = 1 THEN 1 ELSE 0 END) AS acertos,
            SUM(CASE WHEN ae.resultado = 0 THEN 1 ELSE 0 END) AS erros
        FROM alunos_exercicios ae
        INNER JOIN exercicios e ON ae.id_exercicios = e.id
        WHERE ae.id_usuario = ? AND e.nivel = ?";
    $stmtPerf = $conn->prepare($sqlPerformance);
    $stmtPerf->bind_param("ii", $alunoId, $nivelAtual);
    if (!$stmtPerf->execute()) {
        throw new Exception("Erro na consulta de desempenho: " . $stmtPerf->error);
    }
    $stmtPerf->bind_result($acertos, $erros);
    $stmtPerf->fetch();
    $stmtPerf->close();
    $acertos = (int)$acertos;
    $erros   = (int)$erros;
    $naoRespondidos = $totalQuestionsExibidas - ($acertos + $erros);

    // Percentual de acertos com base em 10 exercícios exibidos
    $percentualAcertos = $totalQuestionsExibidas > 0 ? ($acertos / $totalQuestionsExibidas) * 100 : 0;

    /*--- Consulta dos Exercícios ---*/
    // Consulta os exercícios com LEFT JOIN para obter informações do status e resultado
    $sqlExercicios = "SELECT 
            e.id,
            e.pergunta,
            IF(MAX(ae.status)=1, 'Sim', 'Não') AS concluido,
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
        LIMIT 10";
    $stmtExercicios = $conn->prepare($sqlExercicios);
    $stmtExercicios->bind_param("ii", $alunoId, $nivelAtual);
    if (!$stmtExercicios->execute()) {
        throw new Exception("Erro na consulta de exercícios: " . $stmtExercicios->error);
    }
    $resultExercicios = $stmtExercicios->get_result();
    $exercicios = $resultExercicios->fetch_all(MYSQLI_ASSOC);
    $stmtExercicios->close();
} catch (Exception $e) {
    die("Erro crítico: " . $e->getMessage());
}

// Exibir mensagem apenas uma vez
$mensagem = $_SESSION['mensagem'] ?? '';
unset($_SESSION['mensagem']);
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

        /* Badge para botão "Concluído" (ação) */
        .badge-concluido {
            background-color: orange;
            color: #fff;
            padding: 0.5em 1em;
            font-size: 0.875rem;
            border-radius: 0.25rem;
        }

        /* Badges para resultado */
        .badge-resultado-certo {
            background-color: green;
            color: #fff;
            padding: 0.5em 1em;
            font-size: 0.875rem;
            border-radius: 0.25rem;
        }

        .badge-resultado-errado {
            background-color: red;
            color: #fff;
            padding: 0.5em 1em;
            font-size: 0.875rem;
            border-radius: 0.25rem;
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
        <!-- Exibição de Mensagens: se houver mensagem armazenada na sessão, exibe e remove -->
        <?php
        if (isset($_SESSION['mensagem'])) {
            echo "<div class='alert alert-info'>" . htmlspecialchars($_SESSION['mensagem']) . "</div>";
            unset($_SESSION['mensagem']);
        }
        ?>
        <!-- Seção de Desempenho -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-speedometer2 me-2"></i>Desempenho</h5>
            </div>
            <div class="card-body">
                <?php if ($totalQuestionsExibidas > 0): ?>
                    <div class="progress mb-3" style="height: 25px;">
                        <div class="progress-bar bg-success"
                            role="progressbar"
                            style="width: <?= $percentualAcertos ?>%;"
                            aria-valuenow="<?= $percentualAcertos ?>"
                            aria-valuemin="0"
                            aria-valuemax="100">
                            <?= number_format($percentualAcertos, 1) ?>%
                        </div>
                    </div>
                    <div class="d-flex gap-3 justify-content-center">
                        <span class="badge bg-success fs-6">Acertos: <?= $acertos ?></span>
                        <span class="badge bg-danger fs-6">Erros: <?= $erros ?></span>
                        <span class="badge bg-secondary fs-6">Não respondidos: <?= $naoRespondidos ?></span>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle me-2"></i>Nenhum exercício disponível para este nível!
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <!-- Lista de Exercícios (limitada aos 10) -->
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-journal-text me-2"></i>Exercícios do Nível Iniciante</h5>
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
                                    <tr class="<?= ($exercicio['concluido'] === 'Sim') ? 'table-success' : '' ?>">
                                        <th scope="row"><?= $index + 1 ?></th>
                                        <td><?= htmlspecialchars($exercicio['pergunta']) ?></td>
                                        <td><?= $exercicio['concluido'] ?></td>
                                        <td>
                                            <?php if ($exercicio['resultado'] !== '--'): ?>
                                                <?php if ($exercicio['resultado'] === 'Certo'): ?>
                                                    <span class="badge-resultado-certo"><?= $exercicio['resultado'] ?></span>
                                                <?php elseif ($exercicio['resultado'] === 'Errado'): ?>
                                                    <span class="badge-resultado-errado"><?= $exercicio['resultado'] ?></span>
                                                <?php else: ?>
                                                    <?= $exercicio['resultado'] ?>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <?= $exercicio['resultado'] ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($exercicio['concluido'] === 'Não'): ?>
                                                <a href="exercicio.php?id=<?= $exercicio['id'] ?>" class="btn btn-sm btn-primary">
                                                    Iniciar
                                                </a>
                                            <?php else: ?>
                                                <span class="badge-concluido">Concluído</span>
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
                    <?php if (!empty($mensagem)): ?>
                        <div class="container">
                            <div class="alert alert-info alert-dismissible fade show mt-3">
                                <?= htmlspecialchars($mensagem) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Scripts do Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                document.querySelectorAll('.alert').forEach(alert => {
                    new bootstrap.Alert(alert).close();
                });
            }, 5000);
        });
    </script>
</body>

</html>