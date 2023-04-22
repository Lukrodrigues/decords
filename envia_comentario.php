<?php
   header('Content-Type: text/html; charset=utf-8');
   include_once("conexao.php");
?>
 
 <?php
      session_start();
      if (isset($_SESSION['captcha']) && isset($_POST['captcha'])){
         if (isset($nome) && isset($cidade) && isset($mensagem)) {
            $data = date("Y-m-d");

            $nome = $_POST['nome'];
            $cidade = $_POST['cidade'];
            $mensagem = $_POST['mensagem'];
          

      
	$stmt = $conn->prepare("INSERT INTO comentarios_db (nome, cidade, mensagem, date)
	VALUES ('$nome','$cidade', '$mensagem', '$data')");
   $stmt->bind_param("ssss", $nome, $cidade, $mensagem, $data);
	$result = mysqli_query($conn,$insert) or die(mysqli_error($mensagem));
	
	if ($stmt->execute()) {
      $_SESSION['msg'] = "<h3 style='color:green;'>Mensagem cadastrada com sucesso, obrigado pelo comentario</h3>";
      header("Location: comentarios.php");
      exit;
  } else {
      $_SESSION['msg'] = "<h3 style='color:red;'>ERRO! Não foi possível cadastrar a mensagem.</h3>";
      header("Location: comentarios.php");
      exit;
  }
} else {
  $_SESSION['msg'] = "<h3 style='color:red;'>ERRO! Preencha todos os campos obrigatórios.</h3>";
  header("Location: comentarios.php");
  exit;
}
} else {
$_SESSION['msg'] = "<h3 style='color:red;'>ERRO! Caracteres anti-robô inválidos.</h3>";
header("Location: comentarios.php");
exit;
}

 ?>