<!DOCTYPE html>
<?php
session_start();
if (!isset($_SESSION['AlunoEmail']) || !isset($_SESSION['AlunoSenha'])) {
    header("Location: index.php");
    exit;
}
?>
<html lang="pt-br">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Decords Música e Teoria</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Decords Música e Teoria">
    <meta name="" content="Luciano Moraes Rodrigues">
    <link rel="icon" href="img/favicon.ico">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/signin.css" rel="stylesheet">
    <link href="css/tabdiv.css" media="screen" rel="Stylesheet" type="text/css" />
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/ie-emulation-modes-warning.js"></script>

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
            <div class="row">
                <div class="navbar-header">
                    <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                        <span class="sr-only">Toggle navigation</span>
                    </button>
                    <a class="navbar-brand" href="index.php"><img id="logo" src="img/foto22.jpg" width="100" height="30"></a>
                </div>
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        <li class="dropdown">
                            <a class="dropdown-toggle" href="#" data-toggle="dropdown">Tutorial <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="tutorial-01.php">Tutorial-01</a></li>
                                <li class="divider"></li>
                                <li><a href="tutorial_02.php">Tutorial-02</a></li>
                                <li class="divider"></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a class="dropdown-toggle" href="#" data-toggle="dropdown">Exercícios <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="iniciantes.php">Iniciantes</a></li>
                                <li class="divider"></li>
                                <li><a href="intermediarios.php">Intermediários</a></li>
                                <li class="divider"></li>
                                <li><a href="avancados.php">Avançados</a></li>
                                <li class="divider"></li>
                            </ul>
                        </li>
                        <li class="active"><a href="Login.php">Sair</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <?php
                if (isset($_SESSION['MensagemSucesso'])) {
                    echo "<div class='alert alert-success'>{$_SESSION['MensagemSucesso']}</div>";
                    unset($_SESSION['MensagemSucesso']);
                }
                ?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table-responsive">
                        <thead>
                            <tr>
                                <th>Num/</th>
                                <th>Pergunta/</th>
                                <th>Concluído/</th>
                                <th>Resultado/</th>
                                <th>Ações/</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include_once("conexao.php");

                            $aluno = filter_var($_SESSION['AlunoId'], FILTER_VALIDATE_INT);
                            $nivel = $_SESSION['AlunoNivel'] ?? 1;
                            $numeracao = 1;

                            $sql = "SELECT id, pergunta FROM exercicios WHERE nivel = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("i", $nivel);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            while ($row = $result->fetch_assoc()) {
                                $ident = $row['id'];
                                $pergunta = htmlspecialchars($row['pergunta'], ENT_QUOTES, 'UTF-8');

                                $sql4 = "SELECT resultado, status FROM alunos_exercicios WHERE id_usuario = ? AND id_exercicios = ?";
                                $stmt2 = $conn->prepare($sql4);
                                $stmt2->bind_param("ii", $aluno, $ident);
                                $stmt2->execute();
                                $stmt2->bind_result($resultado, $status);
                                $stmt2->fetch();
                                $stmt2->close();

                                $botao = "Fazer";
                                $botaocor = "btn btn-warning";
                                $link = "exercicio.php?id=" . $ident;

                                if ($status === 1) {
                                    $botao = $resultado == 1 ? "Acertou" : "Errou";
                                    $botaocor = $resultado == 1 ? "btn btn-success" : "btn btn-danger";
                                    $link = "#";
                                }

                                $resultadoTexto = $resultado == 1 ? "Acertou" : ($resultado == 2 ? "Errou" : "--");

                                echo "<tr>
                                        <td>{$numeracao}</td>
                                        <td>{$pergunta}</td>
                                        <td>" . ($status === 1 ? "Sim" : "Não") . "</td>
                                        <td>{$resultadoTexto}</td>
                                        <td><a href='{$link}'><button type='button' class='{$botaocor}'>{$botao}</button></a></td>
                                    </tr>";

                                $numeracao++;
                            }

                            $sqlAcertos = "
                                SELECT COUNT(*) AS total, 
                                SUM(CASE WHEN resultado = 1 THEN 1 ELSE 0 END) AS acertos 
                                FROM alunos_exercicios ae
                                INNER JOIN exercicios e ON ae.id_exercicios = e.id
                                WHERE ae.id_usuario = ? AND e.nivel = ? AND ae.status = 1";
                            $stmt3 = $conn->prepare($sqlAcertos);
                            $stmt3->bind_param("ii", $aluno, $nivel);
                            $stmt3->execute();
                            $stmt3->bind_result($total, $acertos);
                            $stmt3->fetch();
                            $stmt3->close();

                            if ($total > 0) {
                                $percentualAcertos = ($acertos / $total) * 100;

                                if ($percentualAcertos >= 60) {
                                    $_SESSION['AlunoNivel'] = $nivel + 1;
                                    $_SESSION['MensagemSucesso'] = "Parabéns! Você alcançou $percentualAcertos% de acertos e foi promovido.";
                                } else {
                                    echo "<div class='alert alert-warning'>Você precisa de pelo menos 60% de acertos para avançar.</div>";
                                }
                            }

                            $conn->close();
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
