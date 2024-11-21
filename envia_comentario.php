<?php
header('Content-Type: text/html; charset=utf-8');

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
   if ($res->success === true) {
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
   include_once("conexao.php");

   if (isset($_POST['nome']) && isset($_POST['cidade']) && isset($_POST['mensagem'])) {
      $nome = $_POST['nome'];
      $cidade = $_POST['cidade'];
      $mensagem = $_POST['mensagem'];
      $data = date("Y-m-d");

      // Prepara a consulta SQL
      $stmt = $conn->prepare("INSERT INTO comentarios_db (nome, cidade, mensagem, data) VALUES (?, ?, ?,?)");

      if ($stmt === false) {
         die("Erro na preparação da consulta: " . $conn->error);
      }

      // Faz o bind dos parâmetros (s: string)
      $stmt->bind_param("ssss", $nome, $cidade, $mensagem, $data);

      // Executa a consulta
      if ($stmt->execute()) {
         $_SESSION['msg'] = "<h3 style='color:green;'>Mensagem cadastrada com sucesso, obrigado pelo comentário!</h3>";
         header("Location: comentarios.php");
         exit;
      } else {
         $_SESSION['msg'] = "<h3 style='color:red;'>Erro ao cadastrar mensagem: " . $stmt->error . "</h3>";
         header("Location: comentarios.php");
         exit;
      }

      // Fecha o statement
      $stmt->close();
   } else {
      $_SESSION['msg'] = "<h3 style='color:red;'>Preencha todos os campos antes de enviar!</h3>";
      header("Location: comentarios.php");
      exit;
   }
   ?>
