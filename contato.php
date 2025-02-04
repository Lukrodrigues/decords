<?php
session_start();
?>
<html lang="pt-br">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Decords</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Decords Música e Teoria">
	<meta name="" content="Luciano Moraes Rodrigues">
	<link rel="icon" href="img/favicon-96x96.png">
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/style.css" rel="stylesheet">
	<link href="css/styles.css" rel="stylesheet">
	<link href="css/map.css" rel="stylesheet">

	<script src="js/jquery.min.js"></script>
	<script src="js/scripts.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/ie-emulation-modes-warning.js"></script>
	<script src="http://maps.googleapis.com/maps/api/js?sensor=false&extension=.js&output=embed"></script>
</head>

<body>
	<nav class="navbar navbar-inverse navbar" role="navigation">
		<div id="map-canvas"></div>
		<div class="container">
			<div class="row">
				<div class="navbar-header">
					<button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
						<span class="sr-only">Toggle navigation</span>
					</button>

					<a class="navbar-brand" href="index.php"><img id="logo" src="img/foto22.jpg" width="100" height="40"></a>
				</div>
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav">
						<li class="dropdown"><a class="dropdown-toggle" href="#" data-toggle="dropdown">Categorias<b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="historico.php">Historico</a></li>
								<li class="divider"></li>
								<li><a href="professor.php">Professor</a></li>
								<li class="divider"></li>
								<li><a href="fotos.php">Fotos</a></li>
								<li class="divider"></li>
								<li><a href="videos.php">Vídeos</a></li>
							</ul>
						</li>
						<li><a href="contato.php">Contato/Agenda</a></li>
						<li class="dropdown"><a class="dropdown-toggle" href="#" data-toggle="dropdown">Exercicios<b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="login.php">Sou Aluno/Usuario</a></li>
								<li><a href="login_professor.php">Sou Professor</a></li>
							</ul>
						</li>
						<li><a href="comentarios.php">Comentarios</a></li>
					</ul>
					<div class="nav navbar-form navbar-right">
						<a href="https://www.facebook.com/decordsoficial/" target="_blank"><img src="img/48/Facebook.png"></a>
						<a href="https://www.youtube.com/channel/UCYlKeJvPE7jUXpZMNaVtG1A" target="_blank"><img src="img/48/Youtube.png"></a>
						<a href="https://plus.google.com/u/0/+AdemirHomrich2/posts" target="_blank"><img src="img/48/Google-Plus.png"></a>
						<a href=" http://ademirhomrichmusica.blogspot.com.br" target="_blank"><img src="img/48/Blogger.png"></a>

					</div>
				</div>
			</div>
		</div>
		</div>
	</nav>


	<div id="container">
		<div class="one_half">
			<h2>Estamos Aqui</h2>
			<div class="col-md-12">
				<div class="map-responsive">
					<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3458.6626103729345!2d-51.22383738488834!3d-29.902820781934093!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x951964dff354bb83%3A0xda3119108c44ac52!2sEscola+Ministro+Rubem+Carlos+Ludwig!5e0!3m2!1spt-BR!2sbr!4v1465962532071"
						width="100" height="650" frameborder="10" style="border:10"></iframe>
				</div>
				<div class="page-header text-left">
					<h1>Contato-nos</h1>
				</div>
				<script type="text/javascript">
					function validar_contato() {
						var nome = contato.nome.value;
						var email = contato.email.value;
						var assunto = contato.assunto.value;
						var mensagem = contato.mensagem.value;

						if (nome == "") {
							alert("Campo nome é obrigatorio");
							contato.nome.focus();
							return false;
						}
						if (email == "") {
							alert("Campo email é obrigatorio");
							contato.email.focus();
							return false;
						}
						if (assunto == "") {
							alert("Campo assunto é obrigatorio");
							contato.assunto.focus();
							return false;
						}
						if (mensagem == "") {
							alert("Campo mensagem é obrigatorio");
							contato.mensagem.focus();
							return false;
						} else {
							cadastrar();
						}
					}

					function cadastrar() {

						//dados a enviar, vai buscar os valores dos campos que queremos enviar para a BD
						var dadosajax = {
							'nome': $("#nome").val(),
							'email': $("#email").val(),
							'assunto': $("#assunto").val(),
							'mensagem': $("#mensagem").val()


						};
						pageurl = 'salva_mensagem.php';
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

									alert('Obrigado pelo seu contato!!!!Mensagem enviado com sucesso!!');
									//location.href="#" class="btn-setting";
									//alert("Sua conta foi criada com sucesso! Agora você já pode acessar com seu E-MAIL e SENHA!");
									//location.href="index.php";
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


				<div class="col-lg-7 text-left">
					<form class="form-horizontal" action="envia_mensagem_email.php" name="contato" method="POST">
						<div class="form-group">
							<label for="nome" class="col-sm-2 control-label">Nome*:</label>
							<div class="col-sm-6">
								<input type="text" class="form-control" id="nome" name="nome" placeholder="Nome Completo" required>
								<br />
							</div>
						</div>
						<div class="form-group">
							<label for="inputEmail3" class="col-sm-2 control-label">Email*:</label>
							<div class="col-sm-6">
								<input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
								<br />
							</div>
						</div>
						<div class="form-group">
							<label for="inputPassword3" class="col-sm-2 control-label">Assunto*:</label>
							<div class="col-sm-6">
								<input type="Assunto" class="form-control" id="assunto" name="assunto" placeholder="Assunto" required>
								<br />
							</div>
						</div>
						<div class="form-group">
							<label for="inputPassword3" class="col-sm-2 control-label">Mensagem*:</label>
							<div class="col-sm-6">
								<textarea class="form-control" rows="6" id="mensagem" name="mensagem" required></textarea>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-10">
								<button type="submit" onclick="validar_contato()" value="enviar" class="btn btn-success">Enviar</button>
							</div>
						</div>
				</div>
				<div class="col-lg-5 text-left">
					<div style="background-color:black;color:white;padding:20px;">
						<h1>Agenda Apresentações:</h1>
						<h3>Decords - Camara de Vereadores dia 28/06 as 15:00hs</h3>
					</div>
				</div><br /><br /><br />
			</div>

</body>

</html>