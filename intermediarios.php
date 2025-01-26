<?php
session_start();
header('Content-Type: text/html; charset=utf-8');

if (!isset($_SESSION['AlunoEmail']) || !isset($_SESSION['AlunoSenha'])) {
    echo "É necessário login.";
    header("Location: index.php");
    exit;
}

include_once("conexao.php");

$aluno = $_SESSION['AlunoId'];
$nivelAtual = 2; // Nível dos exercícios da página
$numeracao = 1;

// Verifica as perguntas do nível atual
$sql = "SELECT id, pergunta FROM exercicios WHERE nivel = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $nivelAtual);
$stmt->execute();
$stmt->bind_result($idExercicio, $pergunta);

// Cabeçalho HTML
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Intermediários</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <h1>Bem-vindo ao nível intermediário!</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Pergunta</th>
                    <th>Status</th>
                    <th>Resultado</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($stmt->fetch()) {
                    // Verifica o status da resposta do aluno
                    $sqlStatus = "SELECT resultado, status FROM alunos_exercicios WHERE id_usuario = ? AND id_exercicios = ?";
                    $stmtStatus = $conn->prepare($sqlStatus);
                    $stmtStatus->bind_param("ii", $aluno, $idExercicio);
                    $stmtStatus->execute();
                    $stmtStatus->bind_result($resultado, $status);
                    $stmtStatus->fetch();
                    $stmtStatus->close();

                    $resultadoTexto = $resultado == 1 ? "Acertou" : ($resultado == 2 ? "Errou" : "--");
                    $statusTexto = $status == 1 ? "Sim" : "Não";
                    $botaoTexto = $status == 1 ? "Concluído" : "Fazer";
                    $botaoCor = $status == 1 ? "btn-success" : "btn-warning";
                    $linkAcao = $status == 1 ? "#" : "exercicio.php?id=" . $idExercicio;

                    echo "<tr>
                    <td>{$numeracao}</td>
                    <td>{$pergunta}</td>
                    <td>{$statusTexto}</td>
                    <td><span class='text-" . ($resultado == 1 ? "success" : "danger") . "'>{$resultadoTexto}</span></td>
                    <td><a href='{$linkAcao}' class='btn {$botaoCor}'>{$botaoTexto}</a></td>
                  </tr>";
                    $numeracao++;
                }
                ?>
            </tbody>
        </table>
    </div>
</body>

</html>
<?php
$stmt->close();
?>