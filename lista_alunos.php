<!DOCTYPE html>
<?php
session_start();
include_once("conexao.php"); // assume $conn como mysqli
if (!isset($_SESSION['AlunoEmail']) and !isset($_SESSION['AlunoSenha'])) {
	header("Location: index.php");
	exit;
}

// Função utilitária para traduzir nível numérico
function nivelNome($n)
{
	$map = [1 => "Iniciante", 2 => "Intermediário", 3 => "Avançado"];
	return isset($map[$n]) ? $map[$n] : "Não identificado";
}
?>
<html lang="pt-br">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Decords Música e Teoria - Lista de Alunos</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
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
			<a class="navbar-brand" href="Index.php"><img id="logo" src="img/foto22.jpg" width="100" height="30"></a>
		</div>
		<div id="navbar" class="navbar-collapse collapse">
			<ul class="nav navbar-nav">
				<li class="active"><a href="Administrativo.php">Administrativo</a></li>
				<li class="active"><a href="Acompanhamento.php">Acompanhamento</a></li>
				<li class="active"><a href="tuto.php">Tutorial</a></li>
				<li class="active"><a href="lista_alunos.php">Lista Alunos</a></li>
				<li class="active"><a href="Administrativo.php">Voltar</a></li>
				<li class="active"><a href="Logout.php">Sair</a></li>
			</ul>
		</div>
	</nav>

	<div class="container theme-showcase" role="main" style="margin-top:70px;">
		<h2>Lista de Alunos</h2>

		<!-- Painel Pesquisa -->
		<div class="panel panel-default">
			<div class="panel-heading">
				<form action="lista_alunos.php" method="post" class="form-inline" style="display:flex; gap:8px;">
					<input type="search" class="form-control" id="pesquisar" name="pesquisar" placeholder="Pesquisar por nome"
						required="required" maxlength="500" />
					<button class="btn btn-primary" type="submit">Pesquisar</button>
					<a class="btn btn-default" href="lista_alunos.php">Limpar</a>
				</form>
			</div>
			<div class="panel-body">
				<div class="table-responsive">
					<?php
					// Recebe termo pesquisado
					$pesquisar = isset($_POST['pesquisar']) ? trim($_POST['pesquisar']) : '';

					if ($pesquisar !== '') {
						// Busca alunos cujo nome combina com o termo (lista única por aluno)
						$sql = $conn->prepare("SELECT id, nome, email FROM alunos WHERE nome LIKE ? ORDER BY nome ASC");
						$like = "%" . $pesquisar . "%";
						$sql->bind_param("s", $like);
						$sql->execute();
						$res = $sql->get_result();

						if ($res && $res->num_rows > 0) {
							echo "<h3>Resultados encontrados:</h3>";
							// Para cada aluno encontrado, buscamos níveis concluídos e último exercício concluído
							$stmtNiveis = $conn->prepare("
                                SELECT DISTINCT ex.nivel
                                FROM alunos_exercicios ae
                                JOIN exercicios ex ON ae.id_exercicios = ex.id
                                WHERE ae.id_usuario = ? AND ae.resultado = 1
                                ORDER BY ex.nivel
                            ");

							$stmtUltimoConcl = $conn->prepare("
                                SELECT ae.id_exercicios, ae.data_termino, ex.nivel
                                FROM alunos_exercicios ae
                                JOIN exercicios ex ON ae.id_exercicios = ex.id
                                WHERE ae.id_usuario = ? AND ae.resultado = 1
                                ORDER BY ae.data_termino DESC
                                LIMIT 1
                            ");

							while ($al = $res->fetch_assoc()) {
								$idAluno = (int)$al['id'];
								$nome = htmlspecialchars($al['nome']);
								$email = htmlspecialchars($al['email']);

								// Níveis concluídos
								$niveisConcluidos = [];
								$stmtNiveis->bind_param("i", $idAluno);
								$stmtNiveis->execute();
								$rN = $stmtNiveis->get_result();
								while ($rowN = $rN->fetch_assoc()) {
									$niveisConcluidos[] = nivelNome((int)$rowN['nivel']);
								}
								$txtConcluidos = $niveisConcluidos ? implode(", ", $niveisConcluidos) : "Nenhum concluído";

								// Último exercício concluído (onde parou)
								$stmtUltimoConcl->bind_param("i", $idAluno);
								$stmtUltimoConcl->execute();
								$rU = $stmtUltimoConcl->get_result();
								if ($rU && $rU->num_rows > 0) {
									$u = $rU->fetch_assoc();
									$exId = $u['id_exercicios'];
									$exNivel = (int)$u['nivel'];
									$dataTerm = $u['data_termino'];
									$ondeParou = "Parou no Exercício " . htmlspecialchars($exId) . " do Nível " . nivelNome($exNivel);
									$ultAtividade = $dataTerm ? date('d/m/Y H:i', strtotime($dataTerm)) : '-';
								} else {
									$ondeParou = "Ainda não iniciou";
									$ultAtividade = "-";
								}

								// Exibe cartão (layout parecido com acompanhamento.php)
								echo "
                                <div class='panel panel-info'>
                                    <div class='panel-heading'><strong>{$nome}</strong></div>
                                    <div class='panel-body'>
                                        <p><b>Email:</b> {$email}</p>
                                        <p><b>Níveis concluídos:</b> {$txtConcluidos}</p>
                                        <p><b>Onde parou:</b> {$ondeParou}</p>
                                        <p><b>Última atividade (concluído):</b> {$ultAtividade}</p>
                                    </div>
                                </div>";
							}

							$stmtNiveis->close();
							$stmtUltimoConcl->close();
						} else {
							echo "<p>Nenhum resultado encontrado para '<strong>" . htmlspecialchars($pesquisar) . "</strong>'.</p>";
						}
						$sql->close();
					} else {
						echo "<p>Digite um nome e clique em Pesquisar para encontrar um aluno.</p>";
					}
					?>
				</div>
			</div>
		</div>

		<!-- Exercícios ordenados por data de término: mostrar apenas o último exercício por aluno -->
		<h3>Exercícios ordenados por data de término (último registro por aluno)</h3>
		<div class="row">
			<?php
			// Pegamos o último registro (por id) de alunos_exercicios para cada aluno
			$sqlUltimos = "
                SELECT a.nome, a.email, ae.id_exercicios, ae.data_termino, ae.resultado, ex.nivel
                FROM (
                    SELECT * FROM alunos_exercicios
                ) ae
                INNER JOIN (
                    SELECT id_usuario, MAX(id) as ultimo_id
                    FROM alunos_exercicios
                    GROUP BY id_usuario
                ) ult ON ae.id = ult.ultimo_id
                JOIN alunos a ON a.id = ae.id_usuario
                LEFT JOIN exercicios ex ON ae.id_exercicios = ex.id
                ORDER BY ae.data_termino DESC
            ";
			$qr = $conn->query($sqlUltimos);

			if ($qr && $qr->num_rows > 0) {
				while ($r = $qr->fetch_assoc()) {
					$nome = htmlspecialchars($r['nome']);
					$email = htmlspecialchars($r['email']);
					$exId = $r['id_exercicios'];
					$data = $r['data_termino'];
					$resultado = $r['resultado'];
					$nivelEx = isset($r['nivel']) ? nivelNome((int)$r['nivel']) : "Nível não definido";

					$status = ($resultado == 1) ? "✅ Concluído" : "❌ Não concluído";
					$dataFmt = $data ? date('d/m/Y H:i', strtotime($data)) : '-';

					echo "
                    <div class='col-sm-4'>
                        <div class='panel panel-default'>
                            <div class='panel-heading'><strong>{$nome}</strong></div>
                            <div class='panel-body'>
                                <p><b>Email:</b> {$email}</p>
                                <p><b>Exercício (último):</b> " . ($exId ? htmlspecialchars($exId) : '-') . "</p>
                                <p><b>Nível do exercício:</b> {$nivelEx}</p>
                                <p><b>Status:</b> {$status}</p>
                                <p><b>Data término:</b> {$dataFmt}</p>
                            </div>
                        </div>
                    </div>";
				}
			} else {
				echo "<p>Nenhum registro de exercício encontrado.</p>";
			}

			if ($qr) $qr->free();
			$conn->close();
			?>
		</div>
	</div>
</body>

</html>