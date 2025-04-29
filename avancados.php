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
$alunoId    = (int) $_SESSION['aluno_id'];
$nivelAtual = 3; // avançado

// Flash de sucesso (apenas via novo_nivel=2)
$flashMsg = '';
if (isset($_GET['novo_nivel']) && $_GET['novo_nivel'] == 2 && !empty($_SESSION['mensagem'])) {
	$flashMsg = $_SESSION['mensagem'];
	unset($_SESSION['mensagem']);
}

// Mensagem de reset
$resetMsg = '';
if (isset($_GET['reset']) && $_GET['reset'] == 1) {
	$resetMsg = '😔 Você não atingiu 60% de aproveitamento. Progresso reiniciado!';
}

try {
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

	// Se completou 10 exercícios, decide transição ou reset
	if (($acertos + $erros) === $totalExibidas && $totalExibidas > 0) {
		if ($percentual >= 60) {
			// Prepara flash e recarrega nesta página
			$_SESSION['mensagem'] = "🎉 Parabéns! Você concluiu o nível avançado.";
			header("Location: avancados.php?novo_nivel=2");
			exit;
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
		}

		.badge-certo {
			background: green;
			color: #fff;
		}

		.badge-errado {
			background: red;
			color: #fff;
		}

		.alert {
			position: relative;
		}
	</style>
	<!-- jQuery antes de VexFlow/TabDiv -->
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="js/partitura/vexflow-min.js"></script>
	<script src="js/partitura/underscore-min.js"></script>
	<script src="js/partitura/tabdiv-min.js"></script>
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

		<!-- Flash sucesso (só via novo_nivel=2) -->
		<?php if ($flashMsg): ?>
			<div id="flash-success" class="alert alert-success alert-dismissible fade show">
				<?= htmlspecialchars($flashMsg) ?>
			</div>
		<?php endif; ?>

		<!-- Mensagem de reset -->
		<?php if ($resetMsg): ?>
			<div class="alert alert-warning"><?= htmlspecialchars($resetMsg) ?></div>
		<?php endif; ?>

		<!-- Partitura e Tablatura -->
		<div class="card mb-4">
			<div class="card-header bg-primary text-white">Partitura e Tablatura</div>
			<div class="card-body">
				<div id="tablatura"></div>
			</div>
		</div>

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
										? '<span class="badge-certo">Certo</span>'
										: ($ex['resultado'] === 'Errado'
											? '<span class="badge-errado">Errado</span>'
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

	<!-- Fecha e limpa query string após 2s -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
	<script>
		setTimeout(() => {
			const el = document.getElementById('flash-success');
			if (el) {
				bootstrap.Alert.getOrCreateInstance(el).close();
				const url = new URL(window.location);
				url.searchParams.delete('novo_nivel');
				url.searchParams.delete('reset');
				window.history.replaceState({}, '', url);
			}
		}, 2000);

		// Inicializa VexFlow & TabDiv
		$(function() {
			var VF = Vex.Flow;
			var renderer = new VF.Renderer(
				document.getElementById("tablatura"),
				VF.Renderer.Backends.CANVAS
			);
			renderer.resize(600, 200);
			var ctx = renderer.getContext();
			var stave = new VF.Stave(10, 10, 500);
			stave.addClef("treble").addTimeSignature("4/4");
			stave.setContext(ctx).draw();
			var notes = [
				new VF.StaveNote({
					clef: "treble",
					keys: ["c/4"],
					duration: "q"
				}),
				new VF.StaveNote({
					clef: "treble",
					keys: ["d/4"],
					duration: "q"
				}),
				new VF.StaveNote({
					clef: "treble",
					keys: ["e/4"],
					duration: "q"
				}),
				new VF.StaveNote({
					clef: "treble",
					keys: ["f/4"],
					duration: "q"
				})
			];
			VF.Formatter.FormatAndDraw(ctx, stave, notes);
			$("#tablatura").tabdiv({
				sheet: false,
				tab: true
			});
		});
	</script>
</body>

</html>