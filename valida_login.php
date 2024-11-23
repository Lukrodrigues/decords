<?php
session_start();

// Captura e validação dos dados recebidos via POST
$emailt = isset($_POST['email']) ? trim($_POST['email']) : '';
$senhat = isset($_POST['senha']) ? trim($_POST['senha']) : '';

// Exibe os dados recebidos para debug (remova isso em produção)
echo $emailt . ' - ' . $senhat;

// Verifica se ambos os campos foram preenchidos
if (empty($emailt) || empty($senhat)) {
    $_SESSION['loginErro'] = "Preencha todos os campos.";
    header("Location: login.php");
    exit();
}

include_once("conexao.php");

// Prepara a consulta SQL com prepared statements para evitar SQL injection
$stmt = $conn->prepare("SELECT * FROM professor WHERE email = ? AND senha = ? LIMIT 1");
$stmt->bind_param("ss", $emailt, $senhat);
$stmt->execute();
$result = $stmt->get_result();
$resultado = $result->fetch_assoc();

// Verifica se o resultado foi encontrado
if ($resultado) {
    echo "Professor: " . $resultado['nome'];
    // Redirecionar para a página desejada ou realizar outras ações
} else {
    $_SESSION['loginErro'] = "Email ou senha inválido.";
    header("Location: login.php");
    exit();
}

{
	// //Define os valores atribuidos na sessao do aluno
	$_SESSION['AlunoId'] = $resultado['id'];
	$_SESSION['AlunoEmail'] = $resultado['email'];
	$_SESSION['AlunoSenha'] = $resultado['senha'];
	$_SESSION['AlunoNome'] = $resultado['nome'];
	$_SESSION['AlunonivelAcesso'] = $resultado['nivel_acesso_id'];
}
if ($_SESSION['AlunonivelAcesso'] == 1) {
	header("Location:administrativo.php");
} else {
	// $_SESSION['aluno'] = $resultado['nome'];
	header("Location: login_professor.php");
}

$stmt->close();
$conn->close();

?>