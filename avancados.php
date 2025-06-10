<?php
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Segurança de cache
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

// Conexão com banco
include_once 'conexao.php';

// Verifica sessão
if (!isset($_SESSION['aluno_logado']) || !$_SESSION['aluno_logado'] || !isset($_SESSION['aluno_id'])) {
	header('Location: login_aluno.php');
	exit;
}
$alunoId = (int) $_SESSION['aluno_id'];
$deveRedirecionar = false; // Variável para controle de redirecionamento

try {
	// Busca o nível atual do aluno
	$stmtNivel = $conn->prepare("SELECT nivel FROM alunos WHERE id = ?");
	$stmtNivel->bind_param('i', $alunoId);
	$stmtNivel->execute();
	$resultadoNivel = $stmtNivel->get_result();
	$aluno = $resultadoNivel->fetch_assoc();
	$nivelAtual = (int)($aluno['nivel'] ?? 3); // Default para avançado
	$stmtNivel->close();

	// Total de exercícios no nível
	$stmtTotal = $conn->prepare("SELECT COUNT(*) AS total_questions FROM exercicios WHERE nivel = ?");
	$stmtTotal->bind_param('i', $nivelAtual);
	$stmtTotal->execute();
	$totalQuestions = (int)$stmtTotal->get_result()->fetch_assoc()['total_questions'];
	$stmtTotal->close();
	$totalExibidas  = min($totalQuestions, 10);

	// Soma de acertos e erros
	$sqlPerf = "
        SELECT
            SUM(CASE WHEN ae.resultado = 1 THEN 1 ELSE 0 END) AS acertos,
            SUM(CASE WHEN ae.resultado = 0 THEN 1 ELSE 0 END) AS erros
        FROM alunos_exercicios ae
        JOIN exercicios e ON ae.id_exercicios = e.id
        WHERE ae.id_usuario = ? AND e.nivel = ?";
	$stmtPerf = $conn->prepare($sqlPerf);
	$stmtPerf->bind_param('ii', $alunoId, $nivelAtual);
	$stmtPerf->execute();
	$stmtPerf->bind_result($acertos, $erros);
	$stmtPerf->fetch();
	$stmtPerf->close();
	$acertos        = (int)$acertos;
	$erros          = (int)$erros;
	$naoRespondidos = $totalExibidas - ($acertos + $erros);
	$percentual     = $totalExibidas > 0
		? ($acertos / $totalExibidas) * 100
		: 0;

	// Se completou todos os exercícios
	if (($acertos + $erros) === $totalExibidas && $totalExibidas > 0) {
		if ($percentual >= 60) {
			// Define a mensagem e flag para redirecionamento
			$_SESSION['mensagem'] = "🎉 Parabéns! Concluiu todos os níveis e tornou-se um músico. Será direcionado para o login em 5 segundos.";
			$deveRedirecionar = true;
		} else {
			// Limpa progresso e recarrega com reset
			$stmtReset = $conn->prepare("
                DELETE ae FROM alunos_exercicios ae
                JOIN exercicios e ON ae.id_exercicios = e.id
                WHERE ae.id_usuario = ? AND e.nivel = ?");
			$stmtReset->bind_param('ii', $alunoId, $nivelAtual);
			$stmtReset->execute();
			$stmtReset->close();

			header("Location: avancados.php?reset=1");
			exit;
		}
	}

	// Busca os 10 exercícios do nível avançado
	$sqlExe = "
        SELECT
          e.id,
          e.pergunta,
          IF(MAX(ae.status)=1,'Sim','Não') AS concluido,
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
        ORDER BY e.id
        LIMIT 10";
	$stmtExe = $conn->prepare($sqlExe);
	$stmtExe->bind_param('ii', $alunoId, $nivelAtual);
	$stmtExe->execute();
	$exercicios = $stmtExe->get_result()->fetch_all(MYSQLI_ASSOC);
	$stmtExe->close();
} catch (Exception $e) {
	die("Erro: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Exercícios Avançados - DECORDS</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
	<style>
		.badge-concluido {
			background: orange;
			color: #fff;
			padding: 0.5em 1em;
			font-size: 0.875rem;
			border-radius: 0.25rem;
		}

		.badge-resultado-certo {
			background: green;
			color: #fff;
			padding: 0.5em 1em;
			font-size: 0.875rem;
			border-radius: 0.25rem;
		}

		.badge-resultado-errado {
			background: red;
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
			<span class="navbar-text text-light">
				<?= htmlspecialchars($_SESSION['aluno_nome'] ?? 'Usuário') ?>
				<span class="badge bg-secondary">Nível <?= $nivelAtual ?></span>
			</span>
		</div>
	</nav>
	<div class="container">

		<!-- Mensagem de sucesso -->
		<?php if (isset($_SESSION['mensagem'])): ?>
			<div class="alert alert-info">
				<?= htmlspecialchars($_SESSION['mensagem']) ?>
				<?php if ($deveRedirecionar): ?>
					<div class="mt-2">Redirecionando em <span class="countdown">5</span> segundos...</div>
				<?php endif; ?>
			</div>
			<?php unset($_SESSION['mensagem']); ?>
		<?php endif; ?>

		<!-- Mensagem de reset -->
		<?php if (isset($_GET['reset']) && $_GET['reset'] == 1): ?>
			<div class="alert alert-warning">
				😔 Você não atingiu 60% de aproveitamento. Progresso reiniciado!
			</div>
		<?php endif; ?>

		<!-- Desempenho -->
		<div class="card shadow-sm mb-4">
			<div class="card-header bg-primary text-white">Desempenho</div>
			<div class="card-body">
				<?php if ($totalExibidas > 0): ?>
					<div class="progress mb-3" style="height:25px;">
						<div class="progress-bar bg-success"
							role="progressbar"
							style="width: <?= $percentual ?>%;"
							aria-valuenow="<?= $percentual ?>"
							aria-valuemin="0"
							aria-valuemax="100">
							<?= number_format($percentual, 1) ?>%
						</div>
					</div>
					<div class="d-flex gap-3 justify-content-center">
						<span class="badge bg-success">Acertos: <?= $acertos ?></span>
						<span class="badge bg-danger">Erros: <?= $erros ?></span>
						<span class="badge bg-secondary">Não respondidos: <?= $naoRespondidos ?></span>
					</div>
				<?php else: ?>
					<div class="alert alert-info">Nenhum exercício disponível.</div>
				<?php endif; ?>
			</div>
		</div>

		<!-- Tabela de Exercícios -->
		<div class="card shadow-sm">
			<div class="card-header bg-primary text-white">Exercícios do Nível Avançado</div>
			<div class="card-body table-responsive">
				<table class="table table-hover align-middle">
					<thead class="table-dark">
						<tr>
							<th>#</th>
							<th>Pergunta</th>
							<th>Concluído</th>
							<th>Resultado</th>
							<th>Ação</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($exercicios as $i => $ex): ?>
							<tr class="<?= $ex['concluido'] === 'Sim' ? 'table-success' : '' ?>">
								<td><?= $i + 1 ?></td>
								<td><?= htmlspecialchars($ex['pergunta']) ?></td>
								<td><?= $ex['concluido'] ?></td>
								<td>
									<?= $ex['resultado'] === 'Certo'
										? '<span class="badge-resultado-certo">Certo</span>'
										: ($ex['resultado'] === 'Errado'
											? '<span class="badge-resultado-errado">Errado</span>'
											: '--') ?>
								</td>
								<td>
									<?= $ex['concluido'] === 'Não'
										? '<a href="exercicio.php?id=' . $ex['id'] . '" class="btn btn-sm btn-primary">Iniciar</a>'
										: '<span class="badge-concluido">Concluído</span>' ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
	<script>
		document.addEventListener('DOMContentLoaded', () => {
			<?php if ($deveRedirecionar): ?>
				// Contagem regressiva para redirecionamento
				let seconds = 5;
				const countdownEl = document.querySelector('.countdown');
				const countdownInterval = setInterval(() => {
					seconds--;
					countdownEl.textContent = seconds;
					if (seconds <= 0) {
						clearInterval(countdownInterval);

						// Encerrar sessão e redirecionar para login
						fetch('logout.php')
							.then(() => {
								window.location.href = 'login.php';
							})
							.catch(error => {
								console.error('Erro ao fazer logout:', error);
								window.location.href = 'login.php';
							});
					}
				}, 1000);
			<?php endif; ?>
		});
	</script>
</body>

</html>