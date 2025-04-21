<?php
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Configuração do banco
$servername = "localhost";
$dbname = "decords_bd";
$username = "root";
$password = "";

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_errno) {
        throw new Exception("Falha na conexão: " . $conn->connect_error);
    }

    // Verificação de autenticação
    if (empty($_SESSION['aluno_logado']) || empty($_SESSION['aluno_id'])) {
        header("Location: login_aluno.php");
        exit;
    }
    $alunoId = (int)$_SESSION['aluno_id'];

    // Reset completo se necessário
    if (isset($_GET['reset'])) {
        $stmt = $conn->prepare("
            DELETE ae FROM alunos_exercicios ae
            INNER JOIN exercicios e ON ae.id_exercicios = e.id
            WHERE ae.id_usuario = ? AND e.nivel = (
                SELECT nivel FROM alunos WHERE id = ?
            )
        ");
        $stmt->bind_param("ii", $alunoId, $alunoId);
        $stmt->execute();
        header("Location: iniciantes.php"); // Limpa parâmetros
        exit;
    }

    // Mensagens do sistema
    $mensagem = $_SESSION['mensagem'] ?? '';
    unset($_SESSION['mensagem']);

    // Nível atual
    $stmt = $conn->prepare("SELECT nivel FROM alunos WHERE id = ?");
    $stmt->bind_param("i", $alunoId);
    $stmt->execute();
    $nivelAtual = (int)$stmt->get_result()->fetch_assoc()['nivel'];

    // Cálculo do progresso
    $stmt = $conn->prepare("
        SELECT 
            COUNT(CASE WHEN resultado = 1 THEN 1 END) as acertos,
            COUNT(CASE WHEN resultado = 0 THEN 1 END) as erros
        FROM alunos_exercicios ae
        INNER JOIN exercicios e ON ae.id_exercicios = e.id
        WHERE ae.id_usuario = ? AND e.nivel = ?
    ");
    $stmt->bind_param("ii", $alunoId, $nivelAtual);
    $stmt->execute();
    $progresso = $stmt->get_result()->fetch_assoc();
    $acertos = (int)($progresso['acertos'] ?? 0);
    $erros = (int)($progresso['erros'] ?? 0);
    $totalExercicios = 10;
    $percentual = ($acertos / $totalExercicios) * 100;

    // Lista de exercícios com status
    $stmt = $conn->prepare("
        SELECT 
            e.id,
            e.pergunta,
            MAX(ae.resultado) as resultado,
            MAX(ae.status) as status
        FROM exercicios e
        LEFT JOIN alunos_exercicios ae 
            ON e.id = ae.id_exercicios AND ae.id_usuario = ?
        WHERE e.nivel = ?
        GROUP BY e.id
        ORDER BY e.id
        LIMIT 10
    ");
    $stmt->bind_param("ii", $alunoId, $nivelAtual);
    $stmt->execute();
    $exercicios = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    die("Erro crítico: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nível Iniciante</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .progresso-baixo {
            background-color: #f8d7da;
        }

        /* Vermelho para <60% */
        .acertou {
            background-color: #d4edda;
        }

        /* Verde para acertos */
        .errou {
            background-color: #f8d7da;
        }

        /* Vermelho para erros */
        .badge-acerto {
            background-color: #28a745 !important;
        }

        .badge-erro {
            background-color: #dc3545 !important;
        }
    </style>
</head>

<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">DECORDS</a>
            <div class="text-light">
                <?= htmlspecialchars($_SESSION['aluno_nome'] ?? 'Usuário') ?>
                <span class="badge bg-secondary">Nível <?= $nivelAtual ?></span>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php if ($mensagem): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= htmlspecialchars($mensagem) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Seção de Progresso -->
        <div class="card mb-4 <?= $percentual < 60 ? 'progresso-baixo' : '' ?>">
            <div class="card-header bg-primary text-white">
                Progresso: <?= number_format($percentual, 1) ?>%
            </div>
            <div class="card-body">
                <div class="progress mb-3" style="height: 25px;">
                    <div class="progress-bar"
                        role="progressbar"
                        style="width: <?= $percentual ?>%; background-color: <?= $percentual >= 60 ? '#28a745' : '#dc3545' ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <span class="badge badge-acerto">Acertos: <?= $acertos ?></span>
                    </div>
                    <div class="col-md-6">
                        <span class="badge badge-erro">Erros: <?= $erros ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Exercícios -->
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                Exercícios
            </div>
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Pergunta</th>
                            <th>Status</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($exercicios as $idx => $ex): ?>
                            <?php
                            $statusClass = '';
                            $statusText = 'Não iniciado';
                            if ($ex['status'] == 1) {
                                $statusClass = $ex['resultado'] ? 'acertou' : 'errou';
                                $statusText = $ex['resultado'] ? 'Acertou' : 'Errou';
                            }
                            ?>
                            <tr class="<?= $statusClass ?>">
                                <td><?= $idx + 1 ?></td>
                                <td><?= htmlspecialchars($ex['pergunta']) ?></td>
                                <td>
                                    <span class="badge <?= $ex['resultado'] ? 'badge-acerto' : 'badge-erro' ?>">
                                        <?= $statusText ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($ex['status'] != 1): ?>
                                        <a href="exercicio.php?id=<?= $ex['id'] ?>"
                                            class="btn btn-sm <?= $ex['resultado'] === null ? 'btn-primary' : 'btn-danger' ?>">
                                            <?= $ex['resultado'] === null ? 'Iniciar' : 'Tentar Novamente' ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Concluído</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>