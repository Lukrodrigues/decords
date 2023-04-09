<!DOCTYPE html>
<?php
 session_start(); 
?>
<html lang="pt-br">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>Decords Musica e Teoria</title>
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
		
	 <script type="text/javascript">
		function validar_cadastro(){
				var nome = cadastro.nome.value;
				var email = cadastro.email.value;
				var senha = cadastro.senha.value;
				var senha2 = cadastro.senha2.value;
				
				if(nome == ""){
					alert("Campo nome é obrigatorio");
					cadastro.nome.focus();
					return false;
				}if(email == ""){
					alert("Campo email é obrigatorio");
					cadastro.email.focus();
					return false;
				}if(senha == ""){
					alert("Campo senha é obrigatorio");
					cadastro.senha.focus();
					return false;
				}if(senha2 != senha){
					alert("Campo senha2 é obrigatorio e tem que ser igual a senha");
					cadastro.senha2.focus();
					return false;
				}else{
				cadastrar();
				}
			}
function cadastrar()
{

    //dados a enviar, vai buscar os valores dos campos que queremos enviar para a BD
    var dadosajax = {
        'nome' : $("#nome").val(),
        'email' : $("#email").val(),
		'senha' :  $("#senha").val(),
		'senha2' :  $("#senha2").val()
		
		

	
    };
    pageurl = 'cad_novo_alunos.php';
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
        error: function(){
            alert('Erro: Inserir Registo!!');
        },
        //retorna o resultado da pagina para onde enviamos os dados
        success: function(result)
        { 
            //se foi inserido com sucesso
            if($.trim(result) == '1')
            {
			
			 alert('Registo criado com sucesso!!');
			//location.href="#" class="btn-setting";
			//alert("Sua conta foi criada com sucesso! Agora você já pode acessar com seu E-MAIL e SENHA!");
			//location.href="index.php";
            }
            //se foi um erro
            else
            {
			    //erro de banco de dados ao tentar inserir
                alert("E-mail já cadastrado!");
            }

        }
    });
}
</script>
  <body >
    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="Index.php">Decords</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="login.php">sair</a></li>
			<div class="container theme showcase" >
			</nav>
				<div class="page-header">
					<h1>Cadastrar Usuario</h1>
				</div>
				<div class="row">
				<form class="form-horizontal" method="POST" action="cad_novo_alunos.php" >
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
							<label for="inputPassword3" class="col-sm-2 control-label">Senha*:</label>
							<div class="col-sm-6">
							  <input type="password" class="form-control" id="senha" name="senha" placeholder="Senha"required>
							  <br />
							</div>
					  </div>
					  <div class="form-group">
							<label for="inputPassword3" class="col-sm-2 control-label">Confirme Senha*:</label>
							<div class="col-sm-6">
							  <input type="password" class="form-control" id="senha2" name="senha2" placeholder="Senha2"required>
							  <br />
							</div>
					  </div>
					  <div class="form-group">
						<div class="col-sm-offset-2 col-sm-10">
						  <button type="submit" onclick="validar_cadastro()" value="cadastro" class="btn btn-success">Cadastrar</button>
						</div>
					  </div> 
				</div>
			</div>
		</div>
	</body>
</html>