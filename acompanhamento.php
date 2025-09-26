<!DOCTYPE html>
<?php
session_start();
include_once("conexao.php");
if (!isset($_SESSION['AlunoEmail']) and !isset($_SESSION['AlunoSenha'])) {
	echo "É necessário login";
	header("Location: index.php");
	exit;
}
?>
<html lang="pt-br">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Decords Música e Teoria</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Decords Música e Teoria">
	<meta name="" content="Luciano Moraes Rodrigues">
	<link rel="icon" href="img/favicon-96x96.png">
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/bootstrap-theme.min.css" rel="stylesheet">
	<link href="css/theme.css" rel="stylesheet">

	<script src="js/jquery.min.js"></script>
	<script src="js/document.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
</head>

<body role="document">

	<!-- Fixed navbar -->
	<nav class="navbar navbar-inverse navbar-fixed-top">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
				aria-expanded="false" aria-controls="navbar">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="index.php">
				<img id="logo" src="img/foto22.jpg" width="100" height="30">
			</a>
		</div>
		<div id="navbar" class="navbar-collapse collapse">
			<ul class="nav navbar-nav">
				<li class="active"><a href="administrativo.php">Administrativo</a></li>
				<li class="active"><a href="acompanhamento.php">Acompanhamento</a></li>
				<li class="active"><a href="tuto.php">Tutorial</a></li>
				<li class="active"><a href="lista_alunos.php">Lista Alunos</a></li>
				<li class="active"><a href="administrativo.php">Voltar</a></li>
				<li class="active"><a href="logout.php">Sair</a></li>
			</ul>
		</div>
	</nav>

	<div class="container theme-showcase" role="main" style="margin-top:70px;">
		<div class="page-header">
			<h1>Acompanhamento das Atividades:</h1>
		</div>

		<!-- Contagem por nível -->
		<div class="container-fluid">
			<form class="form-horizontal" role="form">
				<div class="form-group">
					<ul class="resultados"></ul>
					<?php
					$aux = 1;
					while ($aux <= 3) {
						$stmt = $conn->prepare("SELECT COUNT(*) FROM exercicios WHERE nivel = ?");
						$stmt->bind_param("i", $aux);
						$stmt->execute();
						$stmt->bind_result($result);
						$stmt->fetch();
						$stmt->close();

						switch ($aux) {
							case 1:
								echo '<label class="control-label col-sm-2">Iniciante: ' . $result . '</label>';
								break;
							case 2:
								echo '<label class="control-label col-sm-2">Intermediário: ' . $result . '</label>';
								break;
							case 3:
								echo '<label class="control-label col-sm-2">Avançado: ' . $result . '</label>';
								break;
						}
						$aux++;
					}
					?>
				</div>
			</form>
		</div>

		<!-- Pesquisa -->
		<div class="col-lg-12">
			<h2>Acompanhamento do Aluno</h2>
			<div class="panel panel-default">
				<div class="panel-heading">
					<form action="lista_alunos.php" method="post">
						<input type="search" id="pesquisar" name="pesquisar" placeholder="Pesquisar"
							required="required" maxlength="500" />
						<input type="submit" value="Pesquisar" />
					</form>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<?php
						$pesquisar = isset($_POST['pesquisar']) ? trim($_POST['pesquisar']) : '';
						if ($pesquisar !== '') {

							// Busca todos os exercícios do aluno pesquisado
							$sql = "
                        SELECT a.id as aluno_id, a.nome, a.email,
                               ae.id_exercicios, ae.resultado, ae.data_termino,
                               e.nivel
                        FROM alunos a
                        LEFT JOIN alunos_exercicios ae ON a.id = ae.id_usuario
                        LEFT JOIN exercicios e ON ae.id_exercicios = e.id
                        WHERE a.nome LIKE ?
                        ORDER BY ae.data_termino DESC
                    ";
							$stmt = $conn->prepare($sql);
							$pesquisarLike = "%$pesquisar%";
							$stmt->bind_param("s", $pesquisarLike);
							$stmt->execute();
							$result = $stmt->get_result();

							if ($result->num_rows > 0) {
								echo "<h3>Resultados encontrados:</h3>";

								$alunos = [];
								while ($row = $result->fetch_assoc()) {
									$idAluno = $row['aluno_id'];

									if (!isset($alunos[$idAluno])) {
										$alunos[$idAluno] = [
											'nome' => $row['nome'],
											'email' => $row['email'],
											'concluidos' => [],
											'ultimo_exercicio' => null,
											'ultimo_nivel' => null,
											'data_termino' => null
										];
									}

									// Marca níveis concluídos (apenas se realmente finalizado)
									if ($row['resultado'] == 1 && $row['nivel']) {
										$alunos[$idAluno]['concluidos'][$row['nivel']] = true;

										// Atualiza último exercício concluído
										if (!$alunos[$idAluno]['ultimo_exercicio']) {
											$alunos[$idAluno]['ultimo_exercicio'] = $row['id_exercicios'];
											$alunos[$idAluno]['ultimo_nivel'] = $row['nivel'];
											$alunos[$idAluno]['data_termino'] = $row['data_termino'];
										}
									}
								}

								// Exibe resultados formatados
								foreach ($alunos as $aluno) {
									$mapaNiveis = [1 => "Iniciante", 2 => "Intermediário", 3 => "Avançado"];

									$concluidos = [];
									foreach ($aluno['concluidos'] as $n => $val) {
										$concluidos[] = $mapaNiveis[$n];
									}
									$txtConcluidos = $concluidos ? implode(", ", $concluidos) : "Nenhum concluído";

									$ondeParou = $aluno['ultimo_exercicio']
										? "Parou no Exercício {$aluno['ultimo_exercicio']} do Nível " . $mapaNiveis[$aluno['ultimo_nivel']]
										: "Ainda não iniciou";

									echo "
                            <div class='panel panel-info'>
                                <div class='panel-heading'><strong>{$aluno['nome']}</strong></div>
                                <div class='panel-body'>
                                    <p><b>Email:</b> {$aluno['email']}</p>
                                    <p><b>Níveis concluídos:</b> {$txtConcluidos}</p>
                                    <p><b>Onde parou:</b> {$ondeParou}</p>
                                    <p><b>Última atividade:</b> " . ($aluno['data_termino'] ? date('d/m/Y H:i', strtotime($aluno['data_termino'])) : '-') . "</p>
                                </div>
                            </div>";
								}
							} else {
								echo "<p>Nenhum resultado encontrado para '$pesquisar'.</p>";
							}
							$stmt->close();
						}
						?>
					</div>
				</div>
			</div>
		</div>



		<!-- Exercícios Recentes -->
		<?php
		// --- PAGINAÇÃO ---
		$limite = 6;
		$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
		$inicio = ($pagina - 1) * $limite;

		// --- CONSULTA: último exercício de cada aluno ---
		$sql4 = "
            SELECT a.nome, a.email, e.id_exercicios, e.data_termino, e.resultado, e.id_usuario, x.nivel as nivel_exercicio
            FROM alunos a
            LEFT JOIN (
                SELECT ae.*
                FROM alunos_exercicios ae
                INNER JOIN (
                    SELECT id_usuario, MAX(id) as ultimo_id
                    FROM alunos_exercicios
                    GROUP BY id_usuario
                ) ult
                ON ae.id = ult.ultimo_id
            ) e ON a.id = e.id_usuario
            LEFT JOIN exercicios x ON e.id_exercicios = x.id
            ORDER BY e.data_termino DESC
            LIMIT $inicio, $limite
        ";
		$queryResult4 = $conn->query($sql4);

		echo "<h2>Exercícios Recentes:</h2>";
		echo '<div class="row">';

		$contador = 0;
		while ($row4 = $queryResult4->fetch_assoc()) {
			$nome = $row4['nome'];
			$exercicios = $row4['id_exercicios'];
			$id_usuario = $row4['id_usuario'];
			$email = $row4['email'];
			$data = $row4['data_termino'];
			$resultado = $row4['resultado'];
			$nivelExercicio = $row4['nivel_exercicio'];

			// Traduz o nível do exercício
			if ($nivelExercicio == 1) {
				$nivelNome = "Iniciante";
			} elseif ($nivelExercicio == 2) {
				$nivelNome = "Intermediário";
			} elseif ($nivelExercicio == 3) {
				$nivelNome = "Avançado";
			} else {
				$nivelNome = "Não iniciado";
			}

			// Conta acertos
			$acertos = 0;
			if ($id_usuario) {
				$stmtAcertos = $conn->prepare("
                    SELECT COUNT(*) 
                    FROM alunos_exercicios 
                    WHERE id_usuario = ? AND resultado = 1
                ");
				$stmtAcertos->bind_param("i", $id_usuario);
				$stmtAcertos->execute();
				$stmtAcertos->bind_result($acertos);
				$stmtAcertos->fetch();
				$stmtAcertos->close();
			}

			// Status
			$status = ($resultado == 1) ? "✅ Concluído" : (($resultado === null) ? "-" : "❌ Não concluído");

			// Onde parou
			if ($exercicios) {
				$ondeParou = "Parou no Exercício " . $exercicios . " do Nível " . $nivelNome;
			} else {
				$ondeParou = "Ainda não iniciou os exercícios";
			}

			echo '
            <div class="col-sm-4">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <strong>' . $nome . '</strong>
                    </div>
                    <div class="panel-body">
                        <p><b>Email:</b> ' . $email . '</p>
                        <p><b>Data Término:</b> ' . ($data ? date('d/m/Y H:i', strtotime($data)) : '-') . '</p>
                        <p><b>Status:</b> ' . $status . '</p>
                        <p><b>Onde Parou:</b> ' . $ondeParou . '</p>
                        <p><b>Total de Acertos:</b> ' . $acertos . '</p>
                    </div>
                </div>
            </div>
            ';

			$contador++;
			if ($contador % 3 == 0) {
				echo '</div><div class="row">';
			}
		}
		echo '</div>';

		// --- PAGINAÇÃO ---
		$resultTotal = $conn->query("SELECT COUNT(*) as total FROM alunos")->fetch_assoc();
		$totalPaginas = ceil($resultTotal['total'] / $limite);

		if ($totalPaginas > 1) {
			echo '<nav><ul class="pagination">';
			for ($i = 1; $i <= $totalPaginas; $i++) {
				$active = ($i == $pagina) ? 'class="active"' : '';
				echo "<li $active><a href='?pagina=$i'>$i</a></li>";
			}
			echo '</ul></nav>';
		}

		$conn->close();
		?>
	</div>
</body>

</html>