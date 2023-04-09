

<html lang="pt-br">
<script src="js/scripts.js"></script>
<script type="text/javascript">
 <?php
 // header('Content-Type: text/html; charset=utf-8');
// // Inclui o arquivo class.phpmailer.php localizado na pasta phpmailer
// require_once("phpmailer/PHPMailerAutoload.php");
// // Inicia a classe PHPMailer
// $mail = new PHPMailer();
// // Define os dados do servidor e tipo de conexão
// // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
// $mail->IsSMTP(); // Define que a mensagem será SMTP
// $mail->Host = "smtp-mail.gmail.com"; // Endereço do servidor SMTP
// //$mail->SMTPAuth = true; // Usa autenticação SMTP? (opcional)
// //$mail->Username = 'seumail@dominio.net'; // Usuário do servidor SMTP
// //$mail->Password = 'senha'; // Senha do servidor SMTP
// // Define o remetente
// // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
// $mail->From = "lukrodrigues68@gmail.com"; // Seu e-mail
// $mail->FromName = "Luciano"; // Seu nome
// // Define os destinatário(s)
// // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
// $mail->AddAddress('lukrodrigues68@hotmail.com', 'Luciano Moraes');
// $mail->AddAddress('lukrodrigues68@hotmail.com');
// //$mail->AddCC('ciclano@site.net', 'Ciclano'); // Copia
// //$mail->AddBCC('fulano@dominio.com.br', 'Fulano da Silva'); // Cópia Oculta
// // Define os dados técnicos da Mensagem
// // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
// $mail->IsHTML(true); // Define que o e-mail será enviado como HTML
// //$mail->CharSet = 'iso-8859-1'; // Charset da mensagem (opcional)
// // Define a mensagem (Texto e Assunto)
// // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
// $mail->Subject  = "Mensagem Teste"; // Assunto da mensagem
// $mail->Body = "Este é o corpo da mensagem de teste, em <b>HTML</b>!  :)";
// $mail->AltBody = "Este é o corpo da mensagem de teste, em Texto Plano! \r\n :)";
// // Define os anexos (opcional)
// // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
// //$mail->AddAttachment("c:/temp/documento.pdf", "novo_nome.pdf");  // Insere um anexo
// // Envia o e-mail
// $enviado = $mail->Send();
// // Limpa os destinatários e os anexos
// $mail->ClearAllRecipients();
// $mail->ClearAttachments();
// // Exibe uma mensagem de resultado
// if ($enviado) {
  // echo "E-mail enviado com sucesso!";
// } else {
  // echo "Não foi possível enviar o e-mail.";
  // echo "<b>Informações do erro:</b> " . $mail->ErrorInfo;
// }

$nome		= $_POST["nome"];	// Pega o valor do campo Nome
$email		= $_POST["email"];	// Pega o valor do campo Email
$assunto	= $_POST["assunto"];	// Pega o valor do campo Telefone
$mensagem	= $_POST["mensagem"];	// Pega os valores do campo Mensagem

// Variável que junta os valores acima e monta o corpo do email

$Vai 		= "Nome: $nome\n\nE-mail: $email\n\nAssunto: $assunto\n\nMensagem: $mensagem\n";

require_once("phpmailer/PHPMailerAutoload.php");

$mail = new PHPMailer();

define('GUSER', 'lukrodrigues68@gmail.com');	// <-- Insira aqui o seu GMail
define('GPWD', 'vic23060509');		// <-- Insira aqui a senha do seu GMail


function smtpmailer($para, $de, $de_nome, $assunto, $corpo) { 
	global $error;
	$mail = new PHPMailer();
	$mail->IsSMTP();		// Ativar SMTP
	$mail->CharSet = "UTF-8";  //Erro de acentuação
	$mail->SMTPDebug = 2;		// Debugar: 1 = erros e mensagens, 2 = mensagens apenas
	$mail->SMTPAuth = true;		// Autenticação ativada
	$mail->SMTPSecure = 'tls';	// SSL REQUERIDO pelo GMail
	$mail->Host = 'smtp.gmail.com';	// SMTP utilizado
	$mail->Port = 587;  		// A porta 587 deverá estar aberta em seu servidor
	$mail->Username = GUSER;
	$mail->Password = GPWD;
	$mail->SetFrom($de, $de_nome);
	$mail->Subject = $assunto;
	$mail->Body = $corpo;
	$mail->AddAddress($para);
	
	// $mail->SMTPOptions = [
    // 'ssl' => array(
        // 'verify_peer' => false,
        // 'verify_peer_name' => false,
        // 'allow_self_signed' => true
    // )
// ];
	
	
	
	if(!$mail->Send()) {
		$error = 'Mail error: '.$mail->ErrorInfo; 
		echo "Mensagem não enviada";
		return false;
	} else {
		$error = 'Mensagem enviada!';
		echo "<script type=\"text/javascript\">
						alert(\"Usuario cadastrado com Sucesso.\");
					</script>";
		return true;
		
	}
}


// Insira abaixo o email que irá receber a mensagem, o email que irá enviar (o mesmo da variável GUSER), 
//o nome do email que envia a mensagem, o Assunto da mensagem e por último a variável com o corpo do email.

 if (smtpmailer('lukrodrigues68@gmail.com', 'lukrodrigues68@hotmail.com', 'Nome do Enviador', 'Assunto do Email', $Vai)) {

	Header("location:http://decords/contato.php"); // Redireciona para uma página de obrigado.

}
if (!empty($error)) echo $error;
?>

	
	