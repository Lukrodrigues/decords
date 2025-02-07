<!DOCTYPE html>
<?php
session_start();
if (!isset($_SESSION['AlunoEmail']) || !isset($_SESSION['AlunoSenha'])) {
	header("Location: index.php");
	exit;
}
/*
// Redirecionar se não concluiu o intermediarios  
if ($_SESSION['nivel_concluido'] !== 'intermediarios') {
	header("Location: " . ($_SESSION['nivel_concluido'] === 'iniciantes' ? 'intermediarios.php' : 'iniciantes.php'));
	exit;
}
*/
include_once("conexao.php");

$aluno = filter_var($_SESSION['AlunoId'], FILTER_VALIDATE_INT);
$nivel = 3; // Nível avançado


// Consultar desempenho geral no nível atual
$sqlDesempenho = "
    SELECT COUNT(*) AS total,
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

if ($total > 0) {
	$percentualAcertos = ($acertos / $total) * 100;

	if ($total === 10 && $percentualAcertos >= 60) {
		echo "<script>alert('Parabéns! Você concluiu todos os níveis e está apto para iniciar a tocar violão.'); window.location.href='login.php';</script>";
		exit;
	}

	$desempenhoMsg = "<div class='alert alert-info'>
                        <strong>Total de Exercícios:</strong> $total<br>
                        <strong>Acertos:</strong> $acertos<br>
                        <strong>Percentual de Acertos:</strong> " . round($percentualAcertos, 2) . "%
                      </div>";

	if ($total === 10 && $percentualAcertos < 60) {
		$desempenhoMsg .= "<div class='alert alert-warning'>Você precisa de pelo menos 60% de acertos para avançar. Progresso reiniciado!</div>";
		$sqlReset = "DELETE FROM alunos_exercicios WHERE id_usuario = ? AND id_exercicios IN (SELECT id FROM exercicios WHERE nivel = ?)";
		$stmtReset = $conn->prepare($sqlReset);
		$stmtReset->bind_param("ii", $aluno, $nivel);
		$stmtReset->execute();
		$stmtReset->close();
	}
} else {
	$desempenhoMsg = "<div class='alert alert-warning'>Nenhum exercício concluído neste nível ainda.</div>";
}
?>
<html lang="pt-br">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Decords Música e Teoria - Avançado</title>
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/style.css" rel="stylesheet">
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
</head>

<body>
	<nav class="navbar navbar-inverse navbar-fixed-top">
		<div class="container">
			<a class="navbar-brand" href="index.php">Decords Música</a>
			<ul class="nav navbar-nav navbar-right">
				<li><a href="?logout=true">Sair</a></li>
			</ul>
		</div>
	</nav>
	<div class="container" style="margin-top: 80px;">
		<h1 class="text-center">Exercícios Avançados</h1>
		<hr>
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