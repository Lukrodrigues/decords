<!DOCTYPE html>
<?php
session_start();
if (!isset($_SESSION['AlunoEmail']) and !isset($_SESSION['AlunoSenha'])) {
	echo "é necessario login";
	header("Location: index.php");
	exit;
}
?>
<html lang="pt-br">

<head>
	<meta http-equiv="content-type" content="text/html;charset=utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Decords Musica e Teoria</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Decords Música e Teoria">
	<meta name="" content="Luciano Moraes Rodrigues">
	<link rel="icon" href="img/favicon.ico">
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/bootstrap-theme.min.css" rel="stylesheet">
	<link href="css/theme.css" rel="stylesheet">

	<script src="js/jquery.min.js"></script>
	<script src="js/document.min.js"></script>
	<script src="js/bootstrap.min.js"></script>

	<!-- Support partitura -->
	<script src="js/partitura/vexflow-min.js"></script>
	<script src="js/partitura/underscore-min.js"></script>
	<script src="js/partitura/jquery.js"></script>
	<script src="js/partitura/tabdiv-min.js"></script>
	<!-- Support partitura -->

	<script type="text/javascript">
		function cadastra_exercicio() {

			//dados a enviar, vai buscar os valores dos campos que queremos enviar para a BD
			var dadosajax = {
				'pergunta': $("#pergunta").val(),
				'nivel': $("#nivel").val(),
				'tab': $("#tab").val(),
				'dica': $("#dica").val(),
				'a': $("#a").val(),
				'b': $("#b").val(),
				'c': $("#c").val(),
				'd': $("#d").val(),
				'resp': $("#resp").val()




			};
			pageurl = 'cad_novo_exercicio.php';
			//para consultar mais opcoes possiveis numa chamada ajax
			//http://api.jquery.com/jQuery.ajax/
			$.ajax({

				//url da pagina
				url: pageurl,
				//parametros a passar
				data: dadosajax,
				//tipo: POST ou GET
				type: 'POST',
				//cache
				cache: false,
				//se ocorrer um erro na chamada ajax, retorna este alerta
				//possiveis erros: pagina nao existe, erro de codigo na pagina, falha de comunicacao/internet, utilizar botoes dentro de form, etc etc etc
				error: function() {
					alert('Erro: Inserir Registo!!');
				},
				//retorna o resultado da pagina para onde enviamos os dados
				success: function(result) {
					//se foi inserido com sucesso
					if ($.trim(result) == '1') {

						alert('Registo criado com sucesso!!');
						window.location.reload();
					}
					//se foi um erro
					else {
						//erro de banco de dados ao tentar inserir
						alert("E-mail já cadastrado!");
					}

				}
			});


		}
	</script>


</head>

<body role="document">
	</head>

	<body role="document">

		<!-- Fixed navbar -->
		<nav class="navbar navbar-inverse navbar-fixed-top">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="Index.php"><img id="logo" src="img/foto22.jpg" width="100" height="30"></a>
			</div>
			<div id="navbar" class="navbar-collapse collapse">
				<ul class="nav navbar-nav">
					<li class="active"><a href="administrativo.php">Administrativo</a></li>
					<li class="active"><a href="acompanhamento.php">Acompanhamento</a></li>
					<li class="active"><a href="tuto.php">Tutorial</a></li>
					<li class="active"><a href="lista_alunos.php">Lista Alunos</a></li>
					<li class="active"><a href="logout.php">Sair</a></li>
					<div class="container theme showcase" role="main">
		</nav>
		<div class="page-header">
			<h1>Criar Novos Exercicios:</h1>
		</div>
		<nav class="navbar navbar-default navbar" role="navigation">
			<div class="container">
				<div class="row">
					<div class="box-content">
						<div class="form-horizontal">
							<div class="form-group" id="formular">
								<label>Enunciado</label>
								<textarea class="form-control" id="pergunta" name="pergunta" rows="4"></textarea></br>
								<label>Tablatura</label>
								<a href="http://www.vexflow.com/vextab/tutorial.html" title="Tutorial Vexflow" target="_blank">(*Tutorial Vexflow Tablaturas/Partituras)
								</a></br>
								<textarea class="form-control" id="tab" name="tab" rows="4"></textarea></br>
								<label>Dica</label>
								<textarea class="form-control" id="dica" name="dica" rows="4"></textarea></br>
							</div>
						</div>
						<div class="form-group">
							<label>Opções</label>
							<div class="radio">
								<label>
									a - <input type="text" name="a" id="a" value="">
								</label>
							</div>
							<div class="radio">
								<label>
									b - <input type="text" name="b" id="b" value="">
								</label>
							</div>
							<div class="radio">
								<label>
									c - <input type="text" name="c" id="c" value="">
								</label>
							</div>
							<div class="radio">
								<label>
									d - <input type="text" name="d" id="d" value="">
								</label>
							</div></br>
							<div class="form-group">
								<label for="inputPassword3" class="col-sm-2 control-label">Nivel a ser cadastrado:</label>
								<div class="col-sm-2">
									<select class="form-control" id="nivel" name="nivel">
										<option value="1">1-iniciante</option>
										<option value="2">2-intermediario</option>
										<option value="3">3-avançado</option>
									</select>
								</div>
							</div></br>
							<div class="form-actions"></br>
								<div class="form-group">
									<label for="inputPassword3" class="col-sm-2 control-label">Resposta a ser cadastrado:</label>
									<div class="col-sm-2">
										<select class="form-control" id="resp" name="resp">
											<option>a</option>
											<option>b</option>
											<option>c</option>
											<option>d</option>
										</select>
										<div class="form-actions"></br>
											<button id="envio" type="submit" class="btn btn-primary" onclick="cadastra_exercicio()">Enviar</button>
										</div>
									</div>
								</div>
							</div>
		</nav>

</html>