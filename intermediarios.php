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

// Obtém as perguntas do nível atual
$sql = "SELECT id, pergunta FROM exercicios WHERE nivel = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $nivelAtual);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($idExercicio, $pergunta);

// Cabeçalho HTML
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <nav style="background-color: black; padding: 10px;">
        <ul style="list-style: none; display: flex; margin: 0; padding: 0;">
            <li style="margin-right: 15px;">
                <a href="iniciantes.php" style="color: white; text-decoration: none;"
                    onmouseover="this.style.color='black'"
                    onmouseout="this.style.color='white'">Iniciantes</a>
            </li>
            <li style="margin-right: 15px;">
                <a href="intermediarios.php" style="color: white; text-decoration: none;"
                    onmouseover="this.style.color='black'"
                    onmouseout="this.style.color='white'">Intermediários</a>
            </li>
            <li style="margin-right: 15px;">
                <a href="avancados.php" style="color: white; text-decoration: none;"
                    onmouseover="this.style.color='black'"
                    onmouseout="this.style.color='white'">Avançados</a>
            </li>
            <li>
                <a href="logout.php" style="color: white; text-decoration: none;"
                    onmouseover="this.style.color='black'"
                    onmouseout="this.style.color='white'">Sair</a>
            </li>
        </ul>
    </nav>

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
                    // Consulta o status da resposta do aluno
                    $sqlStatus = "SELECT resultado, status FROM alunos_exercicios WHERE id_usuario = ? AND id_exercicios = ?";
                    $stmtStatus = $conn->prepare($sqlStatus);
                    $stmtStatus->bind_param("ii", $aluno, $idExercicio);
                    $stmtStatus->execute();
                    $stmtStatus->store_result();
                    $stmtStatus->bind_result($resultado, $status);
                    $stmtStatus->fetch();

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
                    $stmtStatus->close(); // Fecha o statement do status
                }
                ?>
            </tbody>
        </table>
    </div>
</body>

</html>
<?php
$stmt->close(); // Fecha o statement das perguntas
$conn->close(); // Fecha a conexão com o banco de dados
?>