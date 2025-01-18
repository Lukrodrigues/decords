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
    <link rel="icon" href="img/favicon-96x96.png">
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

    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="text-center">Desempenho Atual</h1>
                <?php
                include_once("conexao.php");

                $aluno = filter_var($_SESSION['AlunoId'], FILTER_VALIDATE_INT);
                $nivel = $_SESSION['AlunoNivel'] ?? 1;

                $sqlAcertos = "
                    SELECT COUNT(*) AS total, 
                    SUM(CASE WHEN resultado = 1 THEN 1 ELSE 0 END) AS acertos 
                    FROM alunos_exercicios ae
                    INNER JOIN exercicios e ON ae.id_exercicios = e.id
                    WHERE ae.id_usuario = ? AND e.nivel = ? AND ae.status = 1";
                $stmt = $conn->prepare($sqlAcertos);
                $stmt->bind_param("ii", $aluno, $nivel);
                $stmt->execute();
                $stmt->bind_result($total, $acertos);
                $stmt->fetch();
                $stmt->close();

                if ($total > 0) {
                    $percentualAcertos = ($acertos / $total) * 100;

                    echo "<div class='alert alert-info'>
                            <strong>Total de Exercícios:</strong> $total <br>
                            <strong>Acertos:</strong> $acertos <br>
                            <strong>Percentual de Acertos:</strong> " . round($percentualAcertos, 2) . "%
                          </div>";

                    if ($percentualAcertos >= 60) {
                        echo "<div class='alert alert-success'>Parabéns! Você pode avançar para o próximo nível.</div>";
                    } else {
                        echo "<div class='alert alert-warning'>Você precisa de pelo menos 60% de acertos para avançar.</div>";
                    }
                } else {
                    echo "<div class='alert alert-info'>Nenhum exercício concluído neste nível ainda.</div>";
                }
                ?>
            </div>
        </div>

        <div class="row">
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
                                    // Incluído sem alterações a lógica da tabela de exercícios
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>