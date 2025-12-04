<?php
session_start();
require_once "conexao.php";
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Controle de sessão (30 minutos)
$tempoMaximo = 1800;
if (isset($_SESSION['ultimo_acesso'])) {
	if ((time() - $_SESSION['ultimo_acesso']) > $tempoMaximo) {
		session_unset();
		header("Location: login.php?msg=timeout");
		exit();
	}
}
$_SESSION['ultimo_acesso'] = time();

// Verifica login
if (!isset($_SESSION['aluno_id']) || empty($_SESSION['aluno_id'])) {
	header('Location: login.php');
	exit;
}

$alunoId = (int)$_SESSION['aluno_id'];
$nomeAluno = htmlspecialchars($_SESSION['aluno_nome'] ?? 'Aluno');

// --- CÁLCULO DESEMPENHO (corrigido: garante chave 'concluded') ---
$nivels = [1, 2, 3];
$levelData = [];

// ler status persistidos (se existir tabela alunos_niveis)
$nivelStatusPersist = [];
$stmt = $conn->prepare("SELECT nivel, status FROM alunos_niveis WHERE id_usuario = ?");
$stmt->bind_param('i', $alunoId);
$stmt->execute();
$res = $stmt->get_result();
while ($r = $res->fetch_assoc()) {
	$nivelStatusPersist[(int)$r['nivel']] = (int)$r['status'];
}
$stmt->close();

foreach ($nivels as $nivel) {
	// total exercícios
	$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM exercicios WHERE nivel = ?");
	$stmt->bind_param('i', $nivel);
	$stmt->execute();
	$total = (int)($stmt->get_result()->fetch_assoc()['total'] ?? 0);
	$stmt->close();

	// tentativas e acertos
	$stmt = $conn->prepare("
        SELECT 
            COUNT(ae.id) AS attempted,
            SUM(CASE WHEN ae.resultado = 1 THEN 1 ELSE 0 END) AS correct
        FROM alunos_exercicios ae
        JOIN exercicios e ON e.id = ae.id_exercicios
        WHERE ae.id_usuario = ? AND e.nivel = ? AND ae.status = 1
    ");
	$stmt->bind_param('ii', $alunoId, $nivel);
	$stmt->execute();
	$row = $stmt->get_result()->fetch_assoc();
	$attempted = (int)($row['attempted'] ?? 0);
	$correct = (int)($row['correct'] ?? 0);
	$stmt->close();

	$percent = $attempted > 0 ? ($correct / $attempted) * 100.0 : 0.0;

	// Regra: concluído somente se tentou TODOS os exercícios e percent >= 60
	$concluded = false;
	if ($total > 0 && $attempted >= $total && $percent >= 60.0) {
		$concluded = true;
	}

	// status persistido tem prioridade (0=bloqueado,1=andamento,2=concluido)
	if (isset($nivelStatusPersist[$nivel])) {
		$status = $nivelStatusPersist[$nivel];
		// se persistido for 2 (concluido) mas a computação local diz que não concluiu,
		// mantenha concluído apenas se desejado (você pode forçar reconciliação aqui)
	} else {
		// inferir status: 2 concluido, 1 andamento, 0 bloqueado
		if ($concluded) $status = 2;
		elseif ($attempted > 0) $status = 1;
		else $status = 0;
	}

	// garantir consistência: se computed concluded é true, force status = 2
	if ($concluded) $status = 2;

	$levelData[$nivel] = [
		'total_exercises' => $total,
		'attempted'       => $attempted,
		'correct'         => $correct,
		'percent'         => $percent,
		'concluded'       => $concluded,    // <-- chave garantida
		'status'          => $status       // 0/1/2
	];
}


// --- Determina níveis desbloqueados (com base em status persistido/computado) ---
$highestUnlocked = 1;
for ($n = 1; $n <= 3; $n++) {
	// a condição para liberar o próximo é: status do nivel atual == 2 (concluido)
	if (isset($levelData[$n]) && $levelData[$n]['status'] === 2) {
		$highestUnlocked = $n + 1;
	} else {
		break;
	}
}
if ($highestUnlocked > 4) $highestUnlocked = 4;

// --- Se todos concluídos, redireciona ---
if ($highestUnlocked === 4) {
	// opcional: destroi sessão aqui e redirect
	session_destroy();
	header('Location: conclusao.php');
	exit();
}

// --- Monta menuStatus textual (usado para exibir no menu) ---
$menuItens = [
	1 => ['nome' => 'Iniciantes', 'link' => 'iniciantes.php'],
	2 => ['nome' => 'Intermediários', 'link' => 'intermediarios.php'],
	3 => ['nome' => 'Avançados', 'link' => 'avancados.php'],
];

$menuStatus = [];
foreach ($menuItens as $nivel => $dados) {
	$st = $levelData[$nivel]['status'] ?? 0;
	if ($st === 2) $menuStatus[$nivel] = 'concluido';
	elseif ($st === 1) $menuStatus[$nivel] = 'andamento';
	else {
		// desbloqueado lógico: se nivel < highestUnlocked e não concluido, manter andamento
		if ($nivel < $highestUnlocked) $menuStatus[$nivel] = 'concluido';
		elseif ($nivel == $highestUnlocked) $menuStatus[$nivel] = 'andamento';
		else $menuStatus[$nivel] = 'bloqueado';
	}
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="UTF-8">
	<title>Tutorial - Decords Música e Teoria</title>
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>

	<style>
		body {
			margin-top: 60px;
			background-color: #f5f5f5;
			font-family: "Segoe UI", Arial, sans-serif;
			scroll-behavior: smooth;
		}

		.navbar {
			height: 50px;
			border-radius: 0;
			font-size: 15px;
		}

		.navbar a {
			color: #fff !important;
		}

		.menu-concluido {
			color: green !important;
		}

		.menu-em-andamento {
			color: orange !important;
			font-weight: bold;
		}

		.menu-bloqueado {
			color: #ccc !important;
			cursor: not-allowed;
		}

		.menu-bloqueado a {
			pointer-events: none;
		}

		.tutorial-header {
			background: linear-gradient(135deg, #2980b9, #6dd5fa);
			color: #fff;
			padding: 40px 20px;
			border-radius: 0 0 25px 25px;
			text-align: center;
			box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
			margin-bottom: 30px;
		}

		.tutorial-title {
			font-size: 2.2rem;
			margin-bottom: 25px;
		}

		.tutorial-status {
			font-size: 1.2rem;
			max-width: 700px;
			margin: 0 auto;
		}

		.tutorial-status p {
			margin: 10px 0;
		}

		.label {
			font-weight: bold;
			color: #ffeaa7;
		}

		.center-info {
			display: flex;
			flex-direction: column;
			align-items: center;
			text-align: center;
			justify-content: center;
		}
	</style>
</head>

<body>
	<!-- NAVBAR SUPERIOR ESTILIZADA -->
	<nav class="nav-top">
		<div class="nav-container">

			<!-- LOGO + LINKS ESQUERDA -->
			<div class="nav-left">
				<a class="brand" href="index.php">
					<img src="img/foto22.jpg" width="100" height="30" alt="Logo">
				</a>

				<a href="tutorial-01.php" class="nav-link"><b>Tutoria-01</b></a>
				<a href="tutorial-01.php" class="nav-link"><b>Tutoria-02</b></a>
			</div>

			<!-- MENU DIREITO (PHP inalterado) -->
			<ul class="nav-right">
				<li class="dropdown">
					<a href="#" class="dropdown-toggle">Exercícios <b class="caret"></b></a>

					<ul class="dropdown-menu">
						<?php foreach ($menuItens as $nivel => $dados):
							$classe = $menuStatus[$nivel] == 'concluido' ? 'menu-concluido' : ($menuStatus[$nivel] == 'andamento' ? 'menu-em-andamento' : 'menu-bloqueado');
							$statusTxt = [
								'concluido' => ' - Concluído ✅',
								'andamento' => ' - Em andamento 🚀',
								'bloqueado'  => ' - Bloqueado 🔒'
							][$menuStatus[$nivel]];
						?>
							<?php if ($menuStatus[$nivel] == 'bloqueado' || $menuStatus[$nivel] == 'concluido'): ?>
								<li class="disabled"><span class="<?= $classe ?>"><?= $dados['nome'] . $statusTxt ?></span></li>
							<?php else: ?>
								<li><a href="<?= $dados['link'] ?>" class="<?= $classe ?>"><?= $dados['nome'] . $statusTxt ?></a></li>
							<?php endif; ?>
							<li class="divider"></li>
						<?php endforeach; ?>
					</ul>
				</li>

				<li><a href="logout.php" class="logout-btn">Sair</a></li>
			</ul>

		</div>
	</nav>

	<!-- CSS MODERNO AJUSTADO -->
	<style>
		/* Navbar */
		.nav-top {
			width: 100%;
			background: #1f1f1f;
			color: #fff;
			padding: 10px 0;
			border-bottom: 2px solid #444;
			position: fixed;
			top: 0;
			z-index: 9999;
			font-family: Arial, Helvetica, sans-serif;
		}

		.nav-container {
			width: 95%;
			max-width: 1300px;
			margin: auto;
			display: flex;
			align-items: center;
			justify-content: space-between;
		}

		/* LOGO + LINKS */
		.nav-left {
			display: flex;
			align-items: center;
			gap: 20px;
		}

		/* LINKS */
		.nav-link {
			color: #ddd;
			text-decoration: none;
			font-size: 15px;
			padding: 6px 10px;
			transition: 0.3s;
		}

		.nav-link:hover {
			color: #fff;
			background: #333;
			border-radius: 5px;
		}

		/* MENUS DIREITA */
		.nav-right {
			list-style: none;
			display: flex;

			/* MENOS ESPAÇAMENTO PARA TRAZER O DROPDOWN PARA A ESQUERDA */
			gap: 10px;
			margin-right: 40px;
		}

		.nav-right li {
			position: relative;
		}

		/* DROPDOWN */
		.dropdown-menu {
			display: none;
			position: absolute;
			background: #2a2a2a;
			min-width: 260px;

			/* MOVE UM POUCO PARA A ESQUERDA PARA NÃO ESCAPAR DA TELA */
			left: -80px;
			right: auto;

			border-radius: 6px;
			box-shadow: 0px 4px 10px #00000050;
			padding: 10px 0;
		}

		/* mostrar dropdown */
		.dropdown:hover .dropdown-menu {
			display: block;
		}

		/* ITENS */
		.dropdown-menu li {
			padding: 10px 18px;

			/* MAIS ESPAÇAMENTO VISUAL */
			margin-bottom: 4px;
		}

		.dropdown-menu li:hover {
			background: #3c3c3c;
		}

		/* Linha divisória */
		.divider {
			height: 1px;
			width: 100%;
			background: #444;
			margin: 5px 0;
		}

		/* STATUS */
		.menu-concluido {
			color: #00ff7f;
			font-weight: bold;
		}

		.menu-em-andamento {
			color: #ffa500;
			font-weight: bold;

			/* DESTAQUE: MAIS ABAIXO E ESPAÇADO */
			display: block;
			margin-top: 6px;
			padding-top: 4px;
			border-top: 1px dashed #555;
		}

		.menu-bloqueado {
			color: #ff4d4d;
			font-weight: bold;
		}

		/* DESABILITADO */
		.disabled {
			cursor: not-allowed;
			opacity: 0.5;
		}

		/* SAIR */
		.logout-btn {
			background: #c0392b;
			padding: 6px 12px;
			border-radius: 5px;
			color: #fff;
			transition: 0.3s;
		}

		.logout-btn:hover {
			background: #e74c3c;
		}
	</style>

	<!-- BLOCO COMPLETO: Cabeçalho + Status dos Níveis (substitua o bloco antigo por este) -->
	<div class="center-info-block">

		<h1 class="welcome-user">
			👋 Bem-vindo(a) ao curso, <span class="user-name"><?= $nomeAluno ?></span>!
		</h1>

		<p class="current-level">
			<span class="label">Nível atual em andamento:</span>
			<?php
			if ($highestUnlocked === 4) {
				echo "<span class='level-title green-text'>Todos os níveis concluídos</span>";
			} else {
				echo "<span class='level-title blue-text'>" . $menuItens[$highestUnlocked]['nome'] . "</span>";
			}
			?>
		</p>

		<div class="tutorial-status">
			<?php foreach ($nivels as $n): ?>
				<div class="level-card">
					<p>
						<strong class="level-title"><?= $menuItens[$n]['nome'] ?></strong><br>

						<span class="level-info">
							Tentativas: <strong><?= $levelData[$n]['attempted'] ?></strong> /
							<?= $levelData[$n]['total_exercises'] ?> &nbsp;|&nbsp;

							Acertos: <strong><?= $levelData[$n]['correct'] ?></strong> &nbsp;|&nbsp;

							Percentual:
							<strong class="<?= $levelData[$n]['percent'] >= 60 ? 'green-text' : 'red-text' ?>">
								<?= number_format($levelData[$n]['percent'], 1) ?>%
							</strong>

							<?php if ($levelData[$n]['concluded']): ?>
								<span class="level-finished green-badge">Concluído (bloqueado)</span>
							<?php endif; ?>
						</span>
					</p>
				</div>
			<?php endforeach; ?>
		</div>

	</div>


	<!-- ESTILOS (cole dentro do seu CSS ou entre <style> no cabeçalho da página) -->
	<style>
		/* espaçamento superior para evitar sobreposição com navbar fixa */
		/* Container central */
		.center-info-block {
			width: 95%;
			max-width: 900px;
			margin: 30px auto;
			padding: 20px;
			font-size: 18px;
		}

		/* Título do usuário */
		.welcome-user {
			font-size: 28px;
			font-weight: 700;
			text-align: center;
			color: #333;
			margin-bottom: 15px;
		}

		/* Nome do usuário com destaque */
		.user-name {
			color: #007BFF;
			font-weight: bold;
		}

		/* Nível atual */
		.current-level {
			text-align: center;
			font-size: 20px;
			margin-bottom: 25px;
			color: #0d7f5b0f;
		}

		.label {
			font-weight: 600;
		}

		.level-title {
			font-size: 20px;
			font-weight: 700;
		}

		/* Cartões dos níveis */
		.tutorial-status {
			display: flex;
			flex-direction: column;
			gap: 15px;
		}

		.level-card {
			background: #f8f8f8;
			border: 1px solid #e2e2e2;
			padding: 15px 20px;
			border-radius: 12px;
			transition: 0.3s;
		}

		.level-card:hover {
			background: #f0f0f0;
			box-shadow: 0px 3px 8px rgba(0, 0, 0, 0.1);
		}

		/* Info de cada nível */
		.level-info {
			font-size: 17px;
			color: #555;
		}

		/* Cores */
		.green-text {
			color: #11a511;
		}

		.red-text {
			color: #e63946;
		}

		.blue-text {
			color: #007BFF;
		}

		/* Badge */
		.green-badge {
			display: inline-block;
			background: #dcffe4;
			color: #0f8f3c;
			padding: 3px 8px;
			font-size: 14px;
			margin-left: 8px;
			border-radius: 8px;
			font-weight: bold;
		}

		/* ⚡ Responsividade */
		@media (max-width: 768px) {
			.welcome-user {
				font-size: 24px;
			}

			.current-level {
				font-size: 18px;
			}

			.level-card {
				padding: 12px 15px;
			}

			.level-info {
				font-size: 16px;
			}
		}

		@media (max-width: 480px) {
			.welcome-user {
				font-size: 22px;
			}

			.user-name {
				font-size: 22px;
			}

			.center-info-block {
				padding: 10px;
			}
		}
	</style>


	<script>
		$(document).ready(function() {
			document.addEventListener('visibilitychange', function() {
				if (!document.hidden) location.reload();
			});

			window.addEventListener('focus', function() {
				location.reload();
			});
		});
	</script>
	<script>
		(function() {
			// função utilitária para POST fetch
			async function postStatus(nivel, status) {
				try {
					const form = new FormData();
					form.append('nivel', nivel);
					form.append('status', status);
					// send synchronous-like using navigator.sendBeacon if available for unload
					if (navigator.sendBeacon) {
						// create blob
						const data = new URLSearchParams();
						data.append('nivel', nivel);
						data.append('status', status);
						navigator.sendBeacon('save_level_status.php', data);
						return;
					}
					// fallback
					await fetch('save_level_status.php', {
						method: 'POST',
						body: form,
						credentials: 'same-origin'
					});
				} catch (e) {
					// não bloquear a saída do usuário por erro de rede
					console.warn('Erro salvando status:', e);
				}
			}

			// chamada para quando o usuário tenta sair da página
			window.addEventListener('beforeunload', function(e) {
				try {
					// valores JS precisam ser calculados a partir do estado do exercício.
					// suponha que você tenha variáveis globais: CURRENT_LEVEL, completedCount, totalCount, percent
					// se não tiver, calcule aqui usando DOM/variáveis do app
					const nivel = window.CURRENT_LEVEL || 1;
					const completed = window.LEVEL_COMPLETED_EXERCISES || 0; // substitua com sua variável
					const total = window.LEVEL_TOTAL_EXERCISES || 0; // substitua
					const percent = window.LEVEL_PERCENT || 0; // substitua

					let status = 1; // andamento
					if (total > 0 && completed === total && percent >= 60) {
						status = 2; // concluido
					} else {
						status = 1; // andamento
					}

					// prefer sendBeacon para garantir envio no unload
					if (navigator.sendBeacon) {
						const params = new URLSearchParams();
						params.append('nivel', nivel);
						params.append('status', status);
						navigator.sendBeacon('save_level_status.php', params);
					} else {
						// tentativa via sync fetch (não garantido)
						navigator.sendBeacon('save_level_status.php', new URLSearchParams({
							nivel,
							status
						}));
					}
				} catch (err) {
					console.warn(err);
				}
				// Não impedir que o usuário saia — apenas salvar em background
			}, {
				capture: true,
				passive: true
			});

			// Hook para botões "Sair" ou "Voltar" no UI — chama explicitamente:
			window.saveLevelStatusNow = function(nivel, completed, total, percent) {
				let status = 1;
				if (total > 0 && completed === total && percent >= 60) status = 2;
				// post async (no await)
				postStatus(nivel, status);
			};

		})();
	</script>

	<button id="btnSair" class="btn btn-danger">Sair</button>

	<script>
		document.getElementById('btnSair').addEventListener('click', function(e) {
			e.preventDefault();
			const nivel = window.CURRENT_LEVEL || 1;
			const completed = window.LEVEL_COMPLETED_EXERCISES || 0;
			const total = window.LEVEL_TOTAL_EXERCISES || 0;
			const percent = window.LEVEL_PERCENT || 0;

			// Save status and then redirect
			fetch('save_level_status.php', {
				method: 'POST',
				body: new URLSearchParams({
					nivel,
					status: (total > 0 && completed === total && percent >= 60) ? 2 : 1
				}),
				credentials: 'same-origin'
			}).finally(() => {
				// redirect after attempt to save (no await to avoid delay)
				window.location.href = 'index.php';
			});
		});
	</script>


</body>

</html>
< !DOCTYPE html>
	<html lang="pt-BR">

	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Tutorial 01 - Violão</title>
		<style>
			body {
				margin: 0;
				font-family: Arial, sans-serif;
				background-color: #f5f5f5;
			}

			/* ===== MENU LATERAL FIXO ===== */
			.sidebar {
				position: fixed;
				top: 0;
				left: 0;
				width: 250px;
				height: 100%;
				background-color: #222;
				color: white;
				overflow-y: auto;
				padding: 20px;
				box-sizing: border-box;
			}

			.sidebar h2 {
				text-align: center;
				font-size: 20px;
				margin-bottom: 15px;
				border-bottom: 1px solid #555;
				padding-bottom: 10px;
			}

			.sidebar a {
				display: block;
				color: #ddd;
				text-decoration: none;
				padding: 8px 12px;
				border-radius: 4px;
				margin: 4px 0;
				transition: background 0.3s;
			}

			.sidebar a:hover {
				background-color: #444;
			}

			/* ===== CONTEÚDO PRINCIPAL ===== */
			.main-content {
				margin-left: 270px;
				/* Espaço para o menu lateral */
				padding: 20px 40px;
				box-sizing: border-box;
			}

			.tutorial-header {
				background-color: #fff;
				border-radius: 8px;
				padding: 20px;
				margin-bottom: 30px;
				box-shadow: 0 2px 16px rgba(0, 0, 0, 0.1);
			}

			.tutorial-title {
				margin: 0 0 10px;
				font-size: 26px;
				color: #333;
			}

			.tutorial-status {
				display: flex;
				justify-content: space-between;
				font-size: 15px;
			}

			.tutorial-status .label {
				font-weight: bold;
				color: #555;
			}

			.card {
				background-color: #fff;
				border-radius: 8px;
				padding: 20px;
				margin-bottom: 30px;
				box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
			}

			.card img {
				max-width: 100%;
				height: auto;
				display: block;
				margin: 10px auto;
				border-radius: 6px;
			}

			.card h2,
			.card h4 {
				color: #222;
			}

			@media (max-width: 768px) {
				.sidebar {
					position: relative;
					width: 100%;
					height: auto;
				}

				.main-content {
					margin-left: 0;
					padding: 15px;
				}
			}
		</style>
	</head>

	<body>

		<!-- ===== MENU LATERAL ===== -->
		<nav class="sidebar">
			<br><br>
			<h2>📘 Módulos</h2>
			<a href="#palheta">Técnica de Palheta</a>
			<a href="#pentagrama">Pentagrama Musical</a>
			<a href="#duracao">Duração das Notas</a>
			<a href="#braco">Braco e Notas</a>
			<a href="#alteracoes">Alterações no Braço</a>
			<a href="#compassos">Compassos Musicais</a>
			<a href="#tablatura">Tablatura</a>
			<a href="#pausas">Pausas Musicais</a>
		</nav>

		<!-- ===== CONTEÚDO PRINCIPAL ===== -->
		<div class="main-content">
			<!-- Técnica de Palheta e Mãos -->
			<section class="card" id="palheta"><br><br>
				<h2>🎸 Técnica de Palheta</h2>
				<p>Segure a palheta entre a polpa do polegar e o lado da primeira falange do indicador. O ângulo ideal é de 90° em relação às cordas.</p>
				<p>Evite rigidez excessiva, pois dificulta a execução rápida. Segurar frouxamente pode fazer a palheta escapar.</p>
				<p>As principais técnicas incluem a palhetada alternada (baixo-cima-baixo-cima), a palhetada econômica (que otimiza o movimento para aumentar a velocidade,
					especialmente com três notas por corda) e a palhetada direcional (usada para se mover entre as cordas de forma mais eficiente). </p>
				<h4>Técnica da Mão Direita e Esquerda</h4>
				<p>A coordenação entre as duas mãos é essencial. Pratique lentamente até adquirir precisão e ritmo.</p>
				<p>Mão Direita
					A mão direita é usada para tocar (pode ser com os dedos, unha ou palheta) as cordas, produzindo o som. Os dedos são identificados por letras, provenientes da nomenclatura em espanhol (o método é amplamente difundido com essa origem), sendo:
					P: Dedo Polegar (Pulgar)
					I: Dedo Indicador (Índice)
					M: Dedo Médio (Médio)
					A: Dedo Anular (Anular)
					O dedo mínimo (E, de Extremo) da mão direita raramente é usado no violão clássico e não está incluído na nomenclatura principal.</p>
				<h4>Técnica da Mão Esquerda</h4>
				<p>Posicione os dedos próximos aos trastes e mantenha o polegar atrás do braço do violão para oferecer suporte sem tensão.</p>
				<p>Mão Esquerda
					A mão esquerda é utilizada para pressionar as cordas no braço do violão, alterando a nota que será tocada. Os dedos são identificados por números, sendo:
					1: Dedo Indicador
					2: Dedo Médio
					3: Dedo Anular
					4: Dedo Mínimo (ou mindinho)
					O polegar da mão esquerda geralmente fica na parte de trás do braço do violão para dar apoio, mas não é usado para pressionar as cordas diretamente neste sistema de notação.
				</p>
				<figure>
					<img src="img/maosdeviolao.png" class="img-responsive" alt="Maos Violao" width="400">
					<figcaption>Mostra a posiçao de dedos no violão.</figcaption>
				</figure>
			</section>

			<!-- Pentagrama -->
			<section class="card" id="pentagrama"><br>
				<h2>🎼 Pentagrama Musical</h2>
				<p>O pentagrama é formado por 5 linhas e 4 espaços. As notas são posicionadas conforme sua altura. A leitura inicia na <b>clave de sol</b>.</p>
				<ul>
					<li>Linhas: E (mi), G (sol), B (si), D (ré), F (fá)</li>
					<li>Espaços: F (fá), A (lá), C (dó), E (mi)</li>
				</ul>
				<figure>
					<img src="img/pentagramaDiag.png" class="img-responsive" alt="Pentagrama com clave de sol" width="400">
					<figcaption>Pentagrama com clave de sol e notas de referência.</figcaption>
				</figure>
			</section>

			<!-- Duração das Notas -->
			<section class="card" id="duracao"><br>
				<h2>⏱️ Duração das Notas</h2>
				<p>Cada figura musical representa uma duração específica no compasso. Exemplo:</p>
				<ul>
					<li>Semibreve → 4 tempos</li>
					<li>Mínima → 2 tempos</li>
					<li>Semínima → 1 tempo</li>
					<li>Colcheia → ½ tempo</li>
				</ul>
				<figure>
					<img src="img/duracaonota.png" class="img-responsive" alt="Figuras musicais" width="300">
					<figcaption>Relação entre as figuras e suas durações.</figcaption>
				</figure>
			</section>

			<!-- Braço do Violão -->
			<section class="card" id="braco"><br>
				<h2>🎶Braço do Violão e Notas</h2>
				<p>O braço é composto por trastes (divisórias de metal). Cada casa equivale a ½ tom. As notas se repetem a cada 12 casas.</p>
				<figure>
					<img src="img/bracoviol.png" class="img-responsive" alt="Braço do violão com notas" width="400">
				</figure>
				<figure>
					<img src="img/notasViol.png" class="img-responsive" alt="Braço do violão com notas" width="500">
					<figcaption>Visualização das notas ao longo do braço.</figcaption>
				</figure>
			</section>

			<!-- Alterações no Braço -->
			<section class="card" id="alteracoes"><br>
				<h2>♯ Entendendo Alterações de Notas no Braço do Violão</h2>
				<p>A cada casa percorrida soma-se ½ tom:</p>
				<ul>
					<li>Casa 1 → Casa 2 = ½ tom</li>
					<li>Corda solta → Casa 1 = ½ tom</li>
					<li>Casa 1 → Casa 3 = 1 tom</li>
				</ul>
				<h4>Alterações</h4>
				<p>(b) <b>Bemol</b> – diminui ½ tom | (#) <b>Sustenido</b> – aumenta ½ tom.</p>
				<p>Para alterar notas no braço do violão, use sustenidos (\(\#\)) para subir meio tom
					(uma casa para a frente, em direção ao corpo do instrumento) e bemóis (\(b\)) para descer
					meio tom (uma casa para trás, em direção às tarraxas). Mover-se entre duas casas no
					braço do violão representa uma alteração de meio tom, o que corresponde a uma casa no
					instrumento. Por exemplo, um Sol na terceira corda se torna Sol sustenido na
					quarta casa e Sol bemol na segunda casa.</p>
				<p>Essas relações ajudam na afinação e na construção de escalas.</p>
				<h3>Alterando uma nota para sustenido (#)</h3>
				<p><span class="badge">O que é</span><b>Um sustenido (<code class="k">#</code>) aumenta a nota em meio tom.</b></p>
				<h3>Como fazer</h3>
				<p>Move-se para a casa imediatamente à frente no braço do violão, em direção ao corpo do instrumento (mais perto das casas numeradas maiores).</p>
				<p><b>Exemplo:</b> Se a nota é <em>Lá</em> (na segunda casa da sexta corda), o <em>Lá sustenido</em> estará na terceira casa.</p>
				<h3>Alterando uma nota para bemol (b)</h3>
				<p><span class="badge">O que é</span><b>Um bemol (<code class="k">b</code>) diminui a nota em meio tom.</b></p>
				<p><b>Exemplo:</b> Se a nota é <em>Lá</em> (na segunda casa da sexta corda), o <em>Lá sustenido</em> estará na terceira casa.</p>
				<h3>Como fazer</h3>
				<p>Move-se para a casa imediatamente atrás no braço do violão, em direção às tarraxas (casas numeradas menores).</p>
				<p><b>Exemplo:</b> Se a nota é <em>Si</em> (na segunda casa da quinta corda), o <em>Si bemol</em> estará na primeira casa.</p>
				<figure>
					<img src="img/alterSustBem.png" class="img-responsive" alt="Alteração de Notas" width="600">
					<figcaption>Alterações de Notas Sustenido e Bemol</figcaption>
				</figure>
			</section>


			<!-- Compasso Musical -->
			<section class="card" id="compassos"><br>
				<h2>🎵 Compasso Musical</h2>
				<p>Um compasso musical é a divisão de uma partitura em grupos de tempos regulares,
					organizando o ritmo e a pulsação da música. Ele é representado por barras verticais que
					separam os compassos e são definidos por uma fórmula de compasso, que indica quantas batidas
					há e qual tipo de nota vale uma batida. </p>
				<p>O compasso organiza o tempo da música. No compasso 4/4, cada compasso possui 4 tempos.</p>
				<h4>📖Tipos de Compassos</h4>
				<ul>
					<li><b>Simples:</b> tempos divisíveis por 2</li>
					<li><b>Composto:</b> tempos divisíveis por 3</li>
					<li><b>Ternário:</b> 3 tempos</li>
					<li><b>Quaternário:</b> 4 tempos</li>
				</ul>
				<div>
					<h4>Fórmula de compasso</h4>
					<p>A fórmula (como <code class="k">4/4</code> ou <code class="k">3/4</code>) informa o número de batidas por compasso e qual nota vale a batida.</p>
					<ul>
						<li><b>4/4</b> — Quatro batidas por compasso; a semínima vale 1 tempo. Padrão de acentuação: <em>forte — fraco — médio — fraco</em>.</li>
						<li><b>3/4</b> — Três batidas; a primeira é forte (comum em valsa).</li>
						<li><b>2/4</b> — Duas batidas; sensação de movimento rápido ou marcial.</li>
					</ul>
					<p>Existem também compassos <em>compostos</em> (ex.: 6/8) e <em>mistos</em>, que agrupam tempos de forma diferente — cada tipo tem sua própria forma de subdivisão e acentuação.</p>
					</main>
					<h4>Para que serve</h4>
					<ul>
						<li><b>Organização:</b> Agrupa os tempos em blocos regulares, facilitando estudo e execução.</li>
						<li><b>Estrutura:</b> Define a pulsação e o ritmo da música, dando uma estrutura clara à peça.</li>
						<li><b>Orientação:</b> Ajuda a identificar padrões rítmicos e repetições musicais.</li>
					</ul>
					<div style="margin-top:12px;font-size:1.78rem;color:var(--muted)">
						<p><b>Nota:</b> Ao escrever partituras, use barras de compasso claramente posicionadas e indique a fórmula no início da pauta para orientar executantes.</p>
					</div>
				</div>
				<h3>Pausas Musicais em Compassos Ternários e Quaternários</h3>
				<p>
					As pausas musicais em compassos <b>ternários</b> e <b>quaternários</b> seguem as regras de duração de seus respectivos compassos.
					O compasso ternário (<code>3/4</code> ou <code>3/8</code>) tem três tempos e o quaternário (<code>4/4</code> ou <code>4/8</code>) tem quatro tempos,
					e as pausas (como a semibreve, mínima, semínima, etc.) preenchem esses tempos de acordo com o compasso.
				</p>
				<p>
					Por exemplo, em um compasso <code>4/4</code>, a pausa da semibreve preenche os quatro tempos, enquanto em um compasso <code>3/4</code>,
					a mesma pausa preencheria todo o compasso se o tempo da semibreve fosse a unidade de compasso. No entanto, isso é menos comum,
					pois cada compasso ternário normalmente usa três semínimas.
				</p>
				<h3>Compassos Ternários</h3>
				<ul>
					<li><b>Divisão:</b> 3 tempos por compasso.</li>
					<li><b>Estrutura rítmica:</b> Primeiro tempo forte, seguido de dois tempos fracos.</li>
					<li><b>Exemplos de compassos:</b> <code>3/4</code>, <code>3/2</code>, <code>3/8</code>.</li>
				</ul>
				<h3>Compassos Quaternários</h3>
				<ul>
					<li><b>Divisão:</b> 4 tempos por compasso.</li>
					<li><b>Estrutura rítmica:</b> O primeiro tempo é forte, o segundo é fraco e o terceiro é meio forte ou fraco.</li>
					<li><b>Exemplos de compassos:</b> <code>4/4</code>, <code>4/2</code>, <code>4/8</code>.</li>
				</ul>
				<h3>Considerações Adicionais</h3>
				<ul>
					<li>
						<b>Compassos compostos:</b> São aqueles em que a unidade de tempo é uma nota com ponto.
						O número superior do compasso pode ser <code>6</code> (binário composto), <code>9</code> (ternário composto) ou <code>12</code> (quaternário composto).
					</li>
					<li>
						<b>Unidade de tempo:</b> A duração de cada tempo é definida pelo número inferior da fração.
						Em <code>3/4</code>, a semínima vale um tempo; em <code>3/8</code>, a colcheia vale um tempo.
					</li>
				</ul>

				<div class="note">
					💡 <b>Dica:</b> Visualizar os compassos e pausas em um pentagrama ajuda a compreender melhor como o silêncio e o som se distribuem no tempo.
				</div>
				</main>
	</body>
	<figure>
		<img src="img/compasso.png" class="img-responsive" alt="Compasso 4/4 com clave de sol" width="400">
		<figcaption>Compasso 4/4 com clave de sol.</figcaption>
	</figure>
	</section>
	<!-- Tablatura -->
	<section class="card" id="tablatura"><br>
		<h2>📖 Tablatura</h2>
		<p>
			A tablatura é um sistema de notação musical simplificado para violão e outros instrumentos de cordas,
			indicando onde e em qual corda posicionar os dedos. Ela utiliza seis linhas horizontais que
			representam as cordas do instrumento, com números para indicar as casas a serem pressionadas.
		</p>

		<h3>Estrutura da tablatura</h3>
		<dl>
			<dt>Linha de baixo</dt>
			<dd>6ª corda (Mi — a mais grossa).</dd>

			<dt>Os números</dt>
			<dd>Indicam a casa que você deve pressionar na corda correspondente.</dd>

			<dt>0 (zero)</dt>
			<dd>Tocar a corda solta, sem pressionar nenhuma casa.</dd>

			<dt>Números alinhados verticalmente</dt>
			<dd>Indica que as notas devem ser tocadas ao mesmo tempo, formando um acorde.</dd>

			<dt>Números em sequência</dt>
			<dd>Tocar as notas uma após a outra, como em um solo ou dedilhado.</dd>
		</dl>

		<h3>Como ler as notas</h3>
		<p>
			Para traduzir a tablatura para as notas musicais, você precisa saber as notas das cordas soltas e como elas mudam a cada casa:
		</p>

		<ol>
			<li>
				<b>Cordas soltas (número 0 na tablatura)</b>
				<ul>
					<li><code>e</code> (1ª corda) → Nota Mi</li>
					<li><code>B</code> (2ª corda) → Nota Si</li>
					<li><code>G</code> (3ª corda) → Nota Sol</li>
					<li><code>D</code> (4ª corda) → Nota Ré</li>
					<li><code>A</code> (5ª corda) → Nota Lá</li>
					<li><code>E</code> (6ª corda) → Nota Mi</li>
				</ul>
			</li>

			<li>
				<b>A progressão das notas</b>
				<p class="muted">
					Na música ocidental, existem 12 notas (dó, dó#, ré, ré#, mi, fá, fá#, sol, sol#, lá, lá# e si)
					que se repetem. No violão, cada casa que você avança corresponde à próxima nota nesta sequência.
				</p>
			</li>
		</ol>

		<div class="note">
			<b>Exemplo:</b> se a 6ª corda (E) solta é Mi, a 1ª casa é Fá, a 2ª casa é Fá#, a 3ª casa é Sol, e assim por diante.
		</div>

		<div class="tablatura-bloco">
			<h3>Exemplo de tablatura</h3>
			<p class="muted">Formato simples comum em tutoriais:</p>

			<pre class="tablatura">e|-----0-----| ← 1ª corda (mi)
B|---1---1---| ← 2ª corda (si)
G|-0-------0-| ← 3ª corda (sol)
D|-----------| ← 4ª corda (ré)
A|-----------| ← 5ª corda (lá)
E|-----------| ← 6ª corda (mi)</pre>

			<p>
				Neste exemplo, você tocaria a 3ª corda solta (G) e depois a 1ª corda solta (e), etc.
				Números empilhados verticalmente (por exemplo, <code>0</code> em várias linhas na mesma coluna)
				significam acordes.
			</p>

			<h4>Dicas práticas</h4>
			<ul>
				<li>Leia sempre da esquerda para a direita.</li>
				<li>Marque com o dedo as casas mais usadas para facilitar a posição.</li>
				<li>Se não souber uma tablatura, toque devagar e aumente a velocidade gradualmente.</li>
			</ul>

			<h3>Representação de um acorde</h3>
			<p>
				Números: Indicam qual casa você deve pressionar em cada corda. Por exemplo, um acorde pode ser representado por uma coluna de números.
			</p>
			<p><b>Zero (0)</b>: Significa que você deve tocar a corda solta (sem pressionar nenhuma casa).</p>
			<p>
				<b>Aparência</b>: Em uma tablatura simples (texto), um acorde é formado por uma coluna de números que se estendem pelas seis linhas,
				indicando a posição dos dedos em cada corda para formar o acorde.
			</p>

			<h3>Exemplo de acorde na tablatura (texto)</h3>
			<p>Um acorde de Mi menor pode ser representado da seguinte forma:</p>

			<pre class="tablatura">e|--0--|
B|--0--|
G|--0--|
D|--2--|
A|--2--|
E|--0--|</pre>

			<p>Neste exemplo:</p>
			<ul>
				<li>A primeira e a última corda (Mi agudo e Mi grave) são tocadas soltas (0).</li>
				<li>A terceira e a quarta cordas são pressionadas na segunda casa (2).</li>
			</ul>

			<h3>Como os acordes podem ser tocados</h3>
			<ul>
				<li><b>Simultaneamente</b>: Números alinhados verticalmente indicam que todas as notas devem ser tocadas ao mesmo tempo, como uma batida.</li>
				<li><b>Arpejado</b>: As notas do acorde podem ser tocadas sequencialmente, uma de cada vez.</li>
			</ul>

			<h3>Diagramas de acordes</h3>
			<p>Além da tablatura, os acordes também são representados por diagramas, que mostram de forma visual o braço do violão:</p>
			<ul>
				<li><b>Bolinhas pretas</b>: indicam onde colocar os dedos.</li>
				<li><b>"X"</b>: cordas que não devem ser tocadas.</li>
				<li><b>"O" ou bolinha branca</b>: cordas que devem ser tocadas soltas.</li>
			</ul>
		</div>
		<figure>
			<figcaption>Tablatura didática com números e cordas.</figcaption>
		</figure>
	</section>

	<!-- Pausas -->
	<section class="card" id="pausas"><br>
		<h2>🤫 Pausas Musicais (Figuras de Silêncio)</h2>
		<p>Representam o tempo de silêncio. Têm a mesma duração que as figuras equivalentes.</p>
		<p>A pausa de semínima é igual à duração de uma semínima. A pausa de colcheia é igual à duração da colcheia,
			a pausa de semicolcheia é igual à duração da semicolcheia, a pausa de trigésima segunda é igual à duração da trigésima segunda nota
			e a pausa de sexagésima quarta é igual à duração da sexagésima quarta nota.</p>
		<h3>Exemplo de pausas Quaternário</h3>
		<p><b>Compasso <code>4/4</code>:</b> Uma pausa de semibreve pode preencher todo o compasso.</p>
		<p><b>Compasso <code>4/8</code>:</b> Uma pausa de mínima preenche dois tempos, e uma pausa de semínima preenche um tempo.</p>
		<h3>Exemplo de pausas Ternario</h3>
		<p><b>Compasso <code>3/4</code>:</b> Uma pausa de semibreve não cabe em um único compasso, pois a unidade de tempo é a semínima.</p>
		<p>Você pode usar uma pausa de mínima e uma de semínima, ou uma pausa de semínima e duas de colcheia, etc.</p>
		<p><b>Compasso <code>3/8</code>:</b> Pode conter uma pausa de mínima e uma de colcheia, ou três pausas de colcheia.</p>
		<figure>
			<img src="img/pausasmusicais.png" class="img-responsive" alt="Pausas musicais" width="400">
			<figcaption>Pausas e suas durações correspondentes.</figcaption>
		</figure>
	</section>

	<footer>
		<p>© 2025 – Projeto Educacional de Violão | Luciano Rodrigues</p>
	</footer>
	</main>

	</body>

	</html>

	</html>