<?php
header('Content-Type: text/html; charset=utf-8');
include_once("conexao.php");

$captcha = isset($_POST['g-recaptcha-response'])
   ? $_POST['g-recaptcha-response']
   : null;

if (!is_null($captcha)) {

   $res = file_get_contents(
      "https://google.com/recaptcha/api/siteverify?secret= 6Ldy9mIUAAAAAK-ZIndDtpq2cKb1qHnxB2D-LkjO&" .
         "response=" . $captcha . "&remoteip=" . $_SERVER['REMOTE_ADDR']
   );
   $res = json_decode($res);
   print_r($res);
   if ($res->sucess === true) {
      echo "Captcha validado!!!";
   } else {
      echo "Captcha não valido!!!";
   }
} else {
   echo 'Captcha Nulo!!!';
}

?>
 
 <?php
   session_start();


   if (isset($nome) && isset($cidade) && isset($mensagem)) {
      $data = date("Y-m-d");

      $nome = $_POST['nome'];
      $cidade = $_POST['cidade'];
      $mensagem = $_POST['mensagem'];



      $stmt = $conn->prepare("INSERT INTO comentarios(nome, cidade, mensagem, date)
	VALUES ('$nome','$cidade', '$mensagem', '$data')");
      $stmt->bind_param("ssss", $nome, $cidade, $mensagem, $data);
      $result = mysqli_query($conn, $insert) or die(mysqli_error($mensagem));

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

   ?>