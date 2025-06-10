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

$deveRedirecionar = false; // Variável para controle de redirecionamento

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

    /*--- Performance ---*/
    // Total de exercícios do nível
    $stmtTotal = $conn->prepare("SELECT COUNT(*) as total_questions FROM exercicios WHERE nivel = ?");
    $stmtTotal->bind_param("i", $nivelAtual);
    if (!$stmtTotal->execute()) {
        throw new Exception("Erro ao buscar total de questões: " . $stmtTotal->error);
    }
    $resultTotal = $stmtTotal->get_result();
    $rowTotal    = $resultTotal->fetch_assoc();
    $totalQuestionsDB = (int)$rowTotal['total_questions'];
    $stmtTotal->close();

    $totalQuestionsExibidas = ($totalQuestionsDB > 10) ? 10 : $totalQuestionsDB;

    // Consulta acertos e erros
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

    $percentualAcertos = $totalQuestionsExibidas > 0 ? ($acertos / $totalQuestionsExibidas) * 100 : 0;

    /*--- Redirecionamento Condicional com base no desempenho ---*/
    if ($totalQuestionsExibidas > 0 && ($acertos + $erros) === $totalQuestionsExibidas) {
        if ($percentualAcertos >= 60) {
            // Atualiza o nível do aluno no banco
            $novoNivel = 2;
            $stmtUpdate = $conn->prepare("UPDATE alunos SET nivel = ? WHERE id = ?");
            $stmtUpdate->bind_param('ii', $novoNivel, $alunoId);
            if (!$stmtUpdate->execute()) {
                throw new Exception("Erro ao atualizar nível: " . $stmtUpdate->error);
            }
            $stmtUpdate->close();

            // Define a mensagem e flag para redirecionamento
            $_SESSION['mensagem'] = "🎉 Parabéns! Concluiu o nível iniciante. Será direcionado para o próximo nível em 5 segundos.";
            $deveRedirecionar = true;
        } else {
            // Limpa respostas para permitir novo início
            $stmtReset = $conn->prepare("DELETE FROM alunos_exercicios WHERE id_usuario = ? AND id_exercicios IN (SELECT id FROM exercicios WHERE nivel = ?)");
            $stmtReset->bind_param("ii", $alunoId, $nivelAtual);
            $stmtReset->execute();
            $stmtReset->close();

            $_SESSION['mensagem'] = "😔 Infelizmente não atingiu 60% de aproveitamento, mas não desista! Tente novamente.";
            header("Location: iniciantes.php?reset=1");
            exit;
        }
    }

    /*--- Consulta dos Exercícios ---*/
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

        .badge-concluido {
            background-color: orange;
            color: #fff;
            padding: 0.5em 1em;
            font-size: 0.875rem;
            border-radius: 0.25rem;
        }

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

        .countdown {
            font-weight: bold;
            color: #0d6efd;
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
        <?php if (isset($_SESSION['mensagem'])): ?>
            <div class="alert alert-info">
                <?= htmlspecialchars($_SESSION['mensagem']) ?>
                <?php if ($deveRedirecionar): ?>
                    <div class="mt-2">Redirecionando em <span class="countdown">5</span> segundos...</div>
                <?php endif; ?>
            </div>
            <?php unset($_SESSION['mensagem']); ?>
        <?php endif; ?>

        <?php if (isset($_GET['reset']) && $_GET['reset'] == 1): ?>
            <div class="alert alert-warning">
                Reinício do Nível Iniciante - as perguntas foram reiniciadas, comece novamente!
            </div>
        <?php endif; ?>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Desempenho</h5>
            </div>
            <div class="card-body">
                <?php if ($totalQuestionsExibidas > 0): ?>
                    <div class="progress mb-3" style="height: 25px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: <?= $percentualAcertos ?>%;" aria-valuenow="<?= $percentualAcertos ?>" aria-valuemin="0" aria-valuemax="100">
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
                        Nenhum exercício disponível para este nível!
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Exercícios do Nível Iniciante</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr class="table-dark">
                                <th>#</th>
                                <th>Pergunta</th>
                                <th>Concluído</th>
                                <th>Resultado</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($exercicios)): ?>
                                <?php foreach ($exercicios as $index => $exercicio): ?>
                                    <tr class="<?= ($exercicio['concluido'] === 'Sim') ? 'table-success' : '' ?>">
                                        <th><?= $index + 1 ?></th>
                                        <td><?= htmlspecialchars($exercicio['pergunta']) ?></td>
                                        <td><?= $exercicio['concluido'] ?></td>
                                        <td>
                                            <?php if ($exercicio['resultado'] !== '--'): ?>
                                                <?php if ($exercicio['resultado'] === 'Certo'): ?>
                                                    <span class="badge-resultado-certo"><?= $exercicio['resultado'] ?></span>
                                                <?php elseif ($exercicio['resultado'] === 'Errado'): ?>
                                                    <span class="badge-resultado-errado"><?= $exercicio['resultado'] ?></span>
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
                                        Nenhum exercício disponível!
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                document.querySelectorAll('.alert').forEach(alert => {
                    new bootstrap.Alert(alert).close();
                });
            }, 5000);

            <?php if ($deveRedirecionar): ?>
                // Contagem regressiva para redirecionamento
                let seconds = 5;
                const countdownEl = document.querySelector('.countdown');
                const countdownInterval = setInterval(() => {
                    seconds--;
                    countdownEl.textContent = seconds;
                    if (seconds <= 0) {
                        clearInterval(countdownInterval);
                        window.location.href = 'intermediarios.php?novo_nivel=2';
                    }
                }, 1000);
            <?php endif; ?>
        });
    </script>
</body>

</html>