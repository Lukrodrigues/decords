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
				<li class="active"><a href="login.php">Sair</a></li>
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
					<form action="lista_alunos.php" method="post" class="form-inline">
						<input type="search" id="pesquisar" name="pesquisar" class="form-control"
							placeholder="Pesquisar aluno" required maxlength="500" />
						<button type="submit" class="btn btn-primary">Pesquisar</button>
					</form>
				</div>
				<div class="panel-body">
					<?php
					$pesquisar = isset($_POST['pesquisar']) ? trim($_POST['pesquisar']) : '';
					if ($pesquisar !== '') {
						$stmt = $conn->prepare("
                            SELECT a.nome, a.email, a.nivel, b.data_termino 
                            FROM alunos a 
                            JOIN alunos_exercicios b ON a.id = b.id_usuario 
                            WHERE a.nome LIKE ?
                        ");
						$pesquisarLike = "%$pesquisar%";
						$stmt->bind_param("s", $pesquisarLike);
						$stmt->execute();
						$result = $stmt->get_result();
						$rowCount = $result->num_rows;

						if ($rowCount > 0) {
							echo "<h3>Resultados da pesquisa:</h3>";
							echo '<table class="table table-striped table-condensed">';
							echo '<thead><tr><th>Nome</th><th>Email</th><th>Nível</th><th>Data de Acesso</th></tr></thead><tbody>';
							while ($row = $result->fetch_assoc()) {
								echo "<tr>
                                        <td>{$row['nome']}</td>
                                        <td>{$row['email']}</td>
                                        <td>{$row['nivel']}</td>
                                        <td>{$row['data_termino']}</td>
                                    </tr>";
							}
							echo '</tbody></table>';
						} else {
							echo "<p>Nenhum resultado encontrado para '$pesquisar'.</p>";
						}
						$stmt->close();
					}
					?>
				</div>
			</div>
		</div>

		<!-- Exercícios Recentes (Tabela Compacta) -->
		<?php
		// --- PAGINAÇÃO ---
		$limite = 10; // número de linhas por página
		$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
		$inicio = ($pagina - 1) * $limite;

		// --- CONSULTA COM PAGINAÇÃO ---
		$sql4 = "
            SELECT a.nome, a.nivel, a.email, e.id_exercicios, e.data_termino, e.resultado, e.id_usuario 
            FROM alunos a 
            JOIN alunos_exercicios e ON a.id = e.id_usuario 
            ORDER BY e.data_termino DESC
            LIMIT $inicio, $limite
        ";
		$queryResult4 = $conn->query($sql4);

		echo "<h2>Exercícios Recentes:</h2>";
		echo '<table class="table table-bordered table-condensed table-hover">';
		echo '<thead>
                <tr>
                    <th>Nome</th>
                    <th>Nível</th>
                    <th>Email</th>
                    <th>Data Término</th>
                    <th>Exercício Atual</th>
                    <th>Acertos</th>
                </tr>
              </thead><tbody>';

		while ($row4 = $queryResult4->fetch_assoc()) {
			$nome = $row4['nome'];
			$nivel = $row4['nivel'];
			$exercicios = $row4['id_exercicios'];
			$id_usuario = $row4['id_usuario'];
			$email = $row4['email'];
			$data = $row4['data_termino'];

			$resultados = ($nivel == 1) ? "Iniciante" : (($nivel == 2) ? "Intermediário" : "Avançado");

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

			echo "<tr>
                    <td>$nome</td>
                    <td>$resultados</td>
                    <td>$email</td>
                    <td>" . date('d/m/Y', strtotime($data)) . "</td>
                    <td>$exercicios</td>
                    <td>$acertos</td>
                 </tr>";
		}
		echo '</tbody></table>';

		// --- PAGINAÇÃO ---
		$resultTotal = $conn->query("SELECT COUNT(*) as total FROM alunos_exercicios")->fetch_assoc();
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