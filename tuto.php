<!DOCTYPE html>
<?php
session_start();
include_once ("conexao.php");
if (!isset ($_SESSION['AlunoEmail'])and !isset ($_SESSION['AlunoSenha'])){
echo "é necessario login";
header ("Location: index.php");
exit;
}
?>
<html lang="pt-br">
	<head>
		<meta http-equiv="content-type" content="text/html;charset=utf-8" >
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
		<script src="js/tinymce_4.4.3/tinymce/js/tinymce/jquery.tinymce.min.js"></script>
		<script src="js/tinymce_4.4.3/tinymce/js/tinymce/tinymce.min.js"></script>
		<script src="js/langs/pt_BR.js"></script>
		<script type="text/javascript" src="js/get.data.js"></script>
		<!--<script src="js/nicedit/nicEdit.js"></script>
		<script src="http://js.nicedit.com/nicEdit-latest.js" type="text/javascript"></script>
		<script type="text/javascript">bkLib.onDomLoaded(nicEditors.allTextAreas);</script>
		<script src="js/ckeditor_4.5.11_standard/ckeditor/ckeditor.js"></script>
		<script src="js/ckeditor_4.5.11_standard/ckeditor/config.js"></script>
		<script src="js/ckeditor_4.5.11_standard/ckeditor/config.js"></script>
		<script src="js/ckeditor_4.5.11_standard/ckeditor/styles.js"></script>
		<script src="js/ckeditor_4.5.11_standard/ckeditor/build-config.js"></script>
		
		
		
		
		<!-- Support partitura -->
		  <script src="js/partitura/vexflow-min.js"></script>
		  <script src="js/partitura/underscore-min.js"></script>
		  <script src="js/partitura/jquery.js"></script>
		  <script src="js/partitura/tabdiv-min.js"></script>
		<!-- Support partitura -->
		
			<script type="text/javascript">	
 function cadastra_tutorial()
{

    //dados a enviar, vai buscar os valores dos campos que queremos enviar para a BD
    var dadosajax = {
       'editor1' : $("#editor1").val()

	   

    };
    pageurl = 'tuto_sql.php';
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
			window.location.reload();
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
<script>
tinymce.init({
		  selector: 'textarea',
		  height: 200,
		  theme: 'modern',
		  
		  plugins: [
			'advlist autolink lists link image charmap print preview hr anchor pagebreak',
			'searchreplace wordcount visualblocks visualchars code fullscreen',
			'insertdatetime media nonbreaking save table contextmenu directionality',
			'emoticons template paste textcolor colorpicker textpattern imagetools codesample'
		  ],
		  toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
		  toolbar2: 'print preview media | forecolor backcolor emoticons | codesample',
		  image_advtab: true,
		  templates: [
			{ title: 'Test template 1', content: 'Test 1' },
			{ title: 'Test template 2', content: 'Test 2' }
		  ],
		  content_css: [
			'//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
			'//www.tinymce.com/css/codepen.min.css'
		  ]
		 
		 });
		 window.onload = function(){
        document.getElementById('ok').onclick = function(){
                alert( tinyMCE.get('teste').getContent() );
        }
}
		 
</script>
			<script language="Javascript">
					function submitForm() {
					tinyMCE.triggerSave();
					document.forms[0].submit();
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
					<li class="active"><a href="login.php">Sair</a></li>
					<div class="container theme showcase" role="main">
			</nav>
				<div class="page-header">
					<h1>Criar Tutorial:</h1>
				</div>
				<div class="container">
				<div class="row">
				<div class="box-content">
				<div class="form-horizontal">
				
											<form method="post" action="tutorial_02.php" enctype="multipart/form-data">
													<textarea class="tinymce" id="textarea" name="editor1"></textarea>
											</form>
													<div class="form-actions"></br >
														<button type="submit" class="btn btn-primary" onclick="cadastra_tutorial()">salvar</button>
													</div>
				</div>
						
	</html>
</body>

								