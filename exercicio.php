<<<<<<< HEAD
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
session_start();

// Conexão com o banco de dados
$servername = "localhost";
$dbname     = "decords_bd";
$username   = "root";
$password   = "";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
	die("Erro na conexão: " . $conn->connect_error);
}

// Verificação de sessão
if (!isset($_SESSION['aluno_logado'])) {
	$redirect = urlencode("exercicio.php?id=" . $_GET['id']);
	header("Location: login_aluno.php?redirect=$redirect");
	exit;
}

// Validação do ID do exercício
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0 || !ctype_digit($_GET['id'] ?? '')) {
	header("Location: tutorial-01.php");
	exit;
}

// Processamento do POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	header('Content-Type: application/json');
	try {
		if (!isset($_POST['id_exercicios'], $_POST['resposta'])) {
			throw new Exception('Selecione uma resposta antes de enviar!');
		}
		// Busca a resposta correta do exercício
		$stmt = $conn->prepare("SELECT resposta FROM exercicios WHERE id = ?");
		$stmt->bind_param("i", $_POST['id_exercicios']);
		$stmt->execute();
		$result = $stmt->get_result();
		$exercicio = $result->fetch_assoc();

		// Verifica se a resposta está correta
		$respostaCorreta = $exercicio['resposta'];
		$respostaUsuario  = $_POST['resposta'];
		$acertou = ($respostaUsuario === $respostaCorreta) ? 1 : 0;

		// Salva no banco de dados (a lógica de bloqueio será processada no valida_exercicio.php)
		$stmt = $conn->prepare("INSERT INTO alunos_exercicios 
            (id_usuario, id_exercicios, status, resultado) 
            VALUES (?, ?, 1, ?)
            ON DUPLICATE KEY UPDATE 
                status = 1, 
                resultado = VALUES(resultado)");
		$stmt->bind_param("iii", $_SESSION['aluno_id'], $_POST['id_exercicios'], $acertou);
		$stmt->execute();

		echo json_encode([
			"success" => true,
			"redirect" => "iniciantes.php",
			"acertou" => $acertou,
			"message" => $acertou ? "✓ Resposta Correta!" : "✗ Resposta Incorreta!"
		]);
		exit;
	} catch (Exception $e) {
		http_response_code(500);
		echo json_encode(['error' => $e->getMessage()]);
		exit;
	}
}

// Busca dados do exercício
$stmt = $conn->prepare("SELECT * FROM exercicios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$exercicio = $result->fetch_assoc();

// Sanitização dos dados
$pergunta = htmlspecialchars($exercicio['pergunta'] ?? '', ENT_QUOTES, 'UTF-8');
$tablatura = $exercicio['tablatura'] ?? ''; // Mantém HTML original para exibição correta
$opcoes = [
	'a' => htmlspecialchars($exercicio['a'] ?? '', ENT_QUOTES, 'UTF-8'),
	'b' => htmlspecialchars($exercicio['b'] ?? '', ENT_QUOTES, 'UTF-8'),
	'c' => htmlspecialchars($exercicio['c'] ?? '', ENT_QUOTES, 'UTF-8'),
	'd' => htmlspecialchars($exercicio['d'] ?? '', ENT_QUOTES, 'UTF-8')
];
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Responder Exercício</title>
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<script src="js/partitura/vexflow-min.js"></script>
	<script src="js/partitura/underscore-min.js"></script>
	<script src="js/partitura/jquery.js"></script>
	<script src="js/partitura/tabdiv-min.js"></script>
	<style>
		body {
			background-color: #f8f9fa;
		}

		.card {
			margin-top: 30px;
			border: none;
			box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
		}

		.btn-primary {
			background-color: #007bff;
			border-color: #007bff;
		}

		.btn-primary:hover {
			background-color: #0056b3;
			border-color: #004085;
		}

		.btn-dica {
			margin-top: 10px;
		}

		.form-check {
			margin-bottom: 10px;
		}

		.btn-submit {
			margin-top: 20px;
			padding: 10px 20px;
			font-size: 16px;
		}

		.tablatura-container {
			margin-top: 20px;
			margin-bottom: 20px;
		}
	</style>
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
	<div class="container">
		<div class="card">
			<div class="card-body"><br>
				<h2 class="card-title text-center">Responder Exercício</h2>
				<hr>
				<form id="formExercicio" method="POST">
					<div class="form-group">
						<h4>Pergunta:</h4>
						<p class="lead"><strong><?= $pergunta ?></strong></p>
					</div>
					<?php if (!empty($tablatura)) : ?>
						<div class="tablatura-container">
							<?= $tablatura ?>
						</div>
					<?php endif; ?>
					<div class="form-group">
						<h5>Escolha uma opção:</h5>
						<div id="opcoes">
							<?php foreach ($opcoes as $letra => $descricao) : ?>
								<div class="form-check">
									<input class="form-check-input" type="radio" name="resposta" id="opcao-<?= $letra ?>" value="<?= $letra ?>" required>
									<label class="form-check-label" for="opcao-<?= $letra ?>"><?= $descricao ?></label>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
					<div class="form-group">
						<h6><strong>Dica: Caso dúvida clique abaixo</strong></h6>
						<a href="tutorial-01.php" class="btn btn-info btn-dica">Abrir Tutorial</a>
					</div>
					<input type="hidden" name="id_exercicios" value="<?= $id ?>">
					<button type="submit" class="btn btn-primary btn-submit btn-block">Enviar Resposta</button>
				</form>
				<div id="feedback" class="alert alert-dismissible mt-3" style="display: none;"></div>
			</div>
		</div>
	</div>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script>
		$(document).ready(function() {
			$('#formExercicio').on('submit', function(e) {
				e.preventDefault();
				$.ajax({
					type: 'POST',
					url: window.location.href,
					data: $(this).serialize(),
					dataType: 'json',
					success: function(response) {
						const feedback = $('#feedback');
						const message = response.message || 'Resposta processada!';
						const redirect = response.redirect || '';
						const acertou = response.acertou || false;
						feedback.html(message)
							.removeClass('alert-success alert-danger')
							.addClass(acertou ? 'alert-success' : 'alert-danger')
							.fadeIn();
						if (redirect) {
							setTimeout(() => {
								window.location.href = redirect;
							}, 1500);
						}
					},
					error: function(xhr) {
						$('#feedback').html('Erro: ' + (xhr.responseJSON?.error || 'Erro desconhecido'))
							.addClass('alert-danger')
							.fadeIn();
					}
				});
			});
		});
	</script>
</body>

=======
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
session_start();

// Conexão com o banco de dados
$servername = "localhost";
$dbname = "decords_bd";
$username = "root";
$password = "";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
	die("Erro na conexão: " . $conn->connect_error);
}

// Verificação da sessão
if (!isset($_SESSION['aluno_logado'])) {
	$redirect = urlencode("exercicio.php?id=" . $_GET['id']);
	header("Location: login_aluno.php?redirect=$redirect");
	exit;
}

// Validação do ID do exercício
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0 || !ctype_digit($_GET['id'] ?? '')) {
	header("Location: tutorial-01.php");
	exit;
}

// Processamento do POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	header('Content-Type: application/json');
	try {
		if (!isset($_POST['id_exercicios'], $_POST['resposta'])) {
			throw new Exception('Selecione uma resposta antes de enviar!');
		}

		// Busca a resposta correta do banco
		$stmt = $conn->prepare("SELECT resposta FROM exercicios WHERE id = ?");
		$stmt->bind_param("i", $_POST['id_exercicios']);
		$stmt->execute();
		$result = $stmt->get_result();
		$exercicio = $result->fetch_assoc();
		/*
		$respostaCorreta = $exercicio['resposta'];
		$respostaUsuario = $_POST['resposta'];
		$acertou = ($respostaUsuario === $respostaCorreta) ? 1 : 0;
		*/
		// Salva no banco de dados
		$stmt = $conn->prepare("INSERT INTO alunos_exercicios 
            (id_usuario, id_exercicios, status, resultado) 
            VALUES (?, ?, 1, ?)
            ON DUPLICATE KEY UPDATE 
                status = 1, 
                resultado = VALUES(resultado)");

		$stmt->bind_param("iii", $_SESSION['aluno_id'], $_POST['id_exercicios'], $acertou);
		$stmt->execute();

		echo json_encode([
			"success" => true,
			"redirect" => $acertou ? "iniciantes.php" : "",
			"acertou" => $acertou,
			"message" => $acertou ? "✓ Resposta Correta!" : "✗ Resposta Incorreta!"
		]);
		exit;
	} catch (Exception $e) {
		http_response_code(500);
		echo json_encode(['error' => $e->getMessage()]);
		exit;
	}
}

// Código para exibir o exercício
$stmt = $conn->prepare("SELECT * FROM exercicios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$exercicio = $result->fetch_assoc();

// Sanitização
$pergunta = htmlspecialchars($exercicio['pergunta']);
$tablatura = htmlspecialchars($exercicio['tablatura']);
$opcoes = [
	'a' => htmlspecialchars($exercicio['a']),
	'b' => htmlspecialchars($exercicio['b']),
	'c' => htmlspecialchars($exercicio['c']),
	'd' => htmlspecialchars($exercicio['d'])
];
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Responder Exercício</title>
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<script src="js/partitura/vexflow-min.js"></script>
	<script src="js/partitura/underscore-min.js"></script>
	<script src="js/partitura/tabdiv-min.js"></script>

	<style>
		body {
			background-color: #f8f9fa;
		}

		.card {
			margin-top: 30px;
			border: none;
			box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
		}

		.btn-primary {
			background-color: #007bff;
			border-color: #007bff;
		}

		.btn-primary:hover {
			background-color: #0056b3;
			border-color: #004085;
		}

		.btn-dica {
			margin-top: 10px;
		}

		.form-check {
			margin-bottom: 10px;
		}

		.btn-submit {
			margin-top: 20px;
			padding: 10px 20px;
			font-size: 16px;
		}

		.tablatura-container {
			margin-top: 20px;
			margin-bottom: 20px;
		}
	</style>
	<nav class="navbar navbar-inverse navbar-fixed-top">
		<div class="container">
			<a class="navbar-brand" href="index.php">Decords Música</a>
			<ul class="nav navbar-nav navbar-right">
				<li><a href="?logout=true">Sair</a></li>
			</ul>
		</div>
	</nav>
</head>

<body>
	<div class="container">
		<div class="card">
			<div class="card-body"></br>
				<h2 class="card-title text-center">Responder Exercício</h2>
				<hr>
				<form id="formExercicio" method="POST">
					<div class="form-group">
						<h4 class="card-text">Pergunta:</h4>
						<p class="lead"><strong><?php echo htmlspecialchars($pergunta, ENT_QUOTES, 'UTF-8'); ?></strong></p>
					</div>
					<?php if (!empty($tablatura)) : ?>
						<div class="tablatura-container">
							<?php if (!empty($tablatura)): ?>
								<?= $tablatura ?> <!-- Corrigido a exibição da tablatura -->
							<?php endif; ?>
						</div>
					<?php endif; ?>
					<div class="form-group">
						<h5 class="card-text">Escolha uma opção:</h5>
						<div id="opcoes">
							<?php foreach ($opcoes as $letra => $descricao) : ?>
								<div class="form-check">
									<input class="form-check-input" type="radio" name="resposta" id="opcao-<?php echo $letra; ?>" value="<?php echo $letra; ?>" required>
									<label class="form-check-label" for="opcao-<?php echo $letra; ?>">
										<?php echo htmlspecialchars($descricao, ENT_QUOTES, 'UTF-8'); ?>
									</label>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
					<div class="form-group">
						<h6><strong>Dica: Caso dúvida clique abaixo</strong></h6>
						<a href="tutorial-01.php" class="btn btn-info btn-dica">Abrir Tutorial</a>
					</div>

					<input type="hidden" id="id_exercicios" name="id_exercicios" value="<?php echo $idExercicio; ?>">
					<button type="submit" class="btn btn-primary btn-submit btn-block">Enviar Resposta</button>
				</form>

				<div id="feedback" class="alert alert-dismissible" style="display: none;"></div>
			</div>
		</div>
	</div>
	<?php if (isset($_GET['status'])): ?>
		<div class="alert alert-<?= $_GET['status'] === 'acerto' ? 'success' : 'danger' ?> mt-4">
			<?= $_GET['status'] === 'acerto'
				? '✅ Resposta Correta!'
				: '❌ Resposta Incorreta!'
			?>
		</div>
		<div id="mensagem" style="display: none; color: red; padding: 20px;"></div>
	<?php endif; ?>
	<!-- Scripts de suporte -->
	<script src="js/partitura/vexflow-min.js"></script>
	<script src="js/partitura/underscore-min.js"></script>
	<script src="js/partitura/tabdiv-min.js"></script>

	<script>
		$(document).ready(function() {
			$('#formExercicio').on('submit', function(e) {
				e.preventDefault();

				$.ajax({
					type: 'POST',
					url: window.location.href,
					data: $(this).serialize(),
					dataType: 'json',
					success: function(response) {
						const feedback = $('#feedback');
						feedback.html(response.message)
							.removeClass('alert-success alert-danger')
							.addClass(response.acertou ? 'alert-success' : 'alert-danger')
							.fadeIn();

						if (response.acertou && response.redirect) {
							setTimeout(() => {
								window.location.href = response.redirect;
							}, 1500);
						}
					},
					error: function(xhr) {
						$('#feedback').html('Erro: ' + xhr.statusText).fadeIn();
					}
				});
			});
		});
	</script>
</body>

>>>>>>> b810700b2cad143c6703e26a7df3184cf73cebad
</html>