<!DOCTYPE html>
<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['aluno_logado']) || $_SESSION['aluno_logado'] !== true) {
    header("Location: login_aluno.php"); // Ou ajuste para login_aluno.php
    exit;
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

include_once("conexao.php");

$aluno = filter_var($_SESSION['AlunoId'], FILTER_VALIDATE_INT);
$nivel = 2; // Nível intermediário

// Verifica se foi redirecionado do nível anterior (iniciantes)
$prevMsg = "";
if (isset($_GET['novo_nivel']) && $_GET['novo_nivel'] == 1) {
    // Consulta desempenho no nível anterior (nivel=1)
    $sqlPrev = "SELECT COUNT(*) AS total, 
                       SUM(CASE WHEN resultado = 1 THEN 1 ELSE 0 END) AS acertos 
                FROM alunos_exercicios ae 
                INNER JOIN exercicios e ON ae.id_exercicios = e.id 
                WHERE ae.id_usuario = ? AND e.nivel = 1";
    $stmtPrev = $conn->prepare($sqlPrev);
    $stmtPrev->bind_param("i", $aluno);
    $stmtPrev->execute();
    $stmtPrev->bind_result($prevTotal, $prevAcertos);
    $stmtPrev->fetch();
    $stmtPrev->close();

    if ($prevTotal > 0) {
        $prevPercentual = ($prevAcertos / $prevTotal) * 100;
        $prevMsg = "<div class='alert alert-success'>
                        Parabéns! Você atingiu " . round($prevPercentual, 2) . "% de acertos no nível iniciante.
                    </div>";
    }
}

// Consulta desempenho no nível atual (intermediário)
$sqlDesempenho = "SELECT COUNT(*) AS total, 
                         SUM(CASE WHEN resultado = 1 THEN 1 ELSE 0 END) AS acertos 
                  FROM alunos_exercicios ae 
                  INNER JOIN exercicios e ON ae.id_exercicios = e.id 
                  WHERE ae.id_usuario = ? AND e.nivel = ? AND ae.status = 1";
$stmtDesempenho = $conn->prepare($sqlDesempenho);
$stmtDesempenho->bind_param("ii", $aluno, $nivel);
$stmtDesempenho->execute();
$stmtDesempenho->bind_result($total, $acertos);
$stmtDesempenho->fetch();
$stmtDesempenho->close();

$desempenhoMsg = "";
if ($total > 0) {
    $percentualAcertos = ($acertos / $total) * 100;

    // Redireciona se atingir 60% e adiciona parâmetro para o próximo nível
    if ($total === 10 && $percentualAcertos >= 60) {
        header("Location: avancados.php?novo_nivel=2"); // Parâmetro corrigido
        exit;
    }

    // Mensagem de desempenho atual
    $desempenhoMsg = "<div class='alert alert-info'>
                        <strong>Total de Exercícios:</strong> $total<br>
                        <strong>Acertos:</strong> $acertos<br>
                        <strong>Percentual de Acertos:</strong> " . round($percentualAcertos, 2) . "%
                      </div>";

    if ($total === 10 && $percentualAcertos < 60) {
        $desempenhoMsg .= "<div class='alert alert-warning'>
                            Você precisa de 60% de acertos para avançar. Progresso reiniciado!
                           </div>";
        // Reinicia os exercícios
        $sqlReset = "DELETE FROM alunos_exercicios 
                     WHERE id_usuario = ? 
                     AND id_exercicios IN (SELECT id FROM exercicios WHERE nivel = ?)";
        $stmtReset = $conn->prepare($sqlReset);
        $stmtReset->bind_param("ii", $aluno, $nivel);
        $stmtReset->execute();
        $stmtReset->close();
    }
} else {
    $desempenhoMsg = "<div class='alert alert-warning'>
                        Nenhum exercício concluído neste nível ainda.
                      </div>";
}

// Combina mensagens do nível anterior e atual
$desempenhoMsg = $prevMsg . $desempenhoMsg;
?>
<html lang="pt-br">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Decords Música e Teoria - Intermediários</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Decords Música e Teoria">
    <link rel="icon" href="img/favicon-96x96.png">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/signin.css" rel="stylesheet">
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <!-- Support partitura -->
    <script src="js/partitura/vexflow-min.js"></script>
    <script src="js/partitura/underscore-min.js"></script>
    <script src="js/partitura/jquery.js"></script>
    <script src="js/partitura/tabdiv-min.js"></script>
    <!-- Support partitura -->
</head>

<body>
    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">Decords Música</a>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="login.php">Sair</a></li>
            </ul>
        </div>
    </nav>

    <div class="container" style="margin-top: 80px;">
        <h1 class="text-center">Exercícios Intermediários</h1>
        <hr>

        <!-- Mensagens de desempenho -->
        <?= $desempenhoMsg; ?>

        <h2>Exercícios Disponíveis</h2>
        <div class="table-responsive">
            <table class="table table-bordered">
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
                    <?php
                    $sqlExercicios = "
                        SELECT e.id, e.pergunta, ae.resultado, ae.status
                        FROM exercicios e
                        LEFT JOIN alunos_exercicios ae ON e.id = ae.id_exercicios AND ae.id_usuario = ?
                        WHERE e.nivel = ?";
                    $stmtExercicios = $conn->prepare($sqlExercicios);
                    $stmtExercicios->bind_param("ii", $aluno, $nivel);
                    $stmtExercicios->execute();
                    $resultExercicios = $stmtExercicios->get_result();

                    $contador = 1;
                    while ($exercicio = $resultExercicios->fetch_assoc()) {
                        $idExercicio = $exercicio['id'];
                        $pergunta = htmlspecialchars($exercicio['pergunta'], ENT_QUOTES, 'UTF-8');
                        $status = $exercicio['status'];
                        $resultado = $exercicio['resultado'];

                        $statusTexto = $status === 1 ? "Sim" : "Não";
                        $resultadoTexto = $resultado === 1 ? "Acertou" : ($resultado === 2 ? "Errou" : "--");
                        $acaoTexto = $status === 1 ? ($resultado === 1 ? "Acertou" : "Errou") : "Fazer";
                        $acaoCor = $status === 1 ? ($resultado === 1 ? "btn-success" : "btn-danger") : "btn-warning";
                        $acaoLink = $status === 1 ? "#" : "exercicio.php?id=$idExercicio";

                        echo "<tr>
                                <td>{$contador}</td>
                                <td>{$pergunta}</td>
                                <td>{$statusTexto}</td>
                                <td>{$resultadoTexto}</td>
                                <td><a href='{$acaoLink}' class='btn {$acaoCor}'>{$acaoTexto}</a></td>
                              </tr>";

                        $contador++;
                    }

                    $stmtExercicios->close();
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>