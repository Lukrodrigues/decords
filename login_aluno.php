<?php
session_start();

// Verifica se os dados foram enviados via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// Sanitização dos dados de entrada
	$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
	$senha = $_POST['senha']; // Não sanitizar senhas (serão verificadas via hash)

	// Inclui o arquivo de conexão
	include_once("conexao.php");

	// Prepara a consulta SQL com prepared statements
	$sql = "SELECT id, email, senha, nome, nivel FROM alunos WHERE email = ? LIMIT 1";
	$stmt = $conn->prepare($sql);

	if ($stmt) {
		// Vincula os parâmetros e executa
		$stmt->bind_param("s", $email);
		$stmt->execute();
		$result = $stmt->get_result();

		// Verifica se encontrou um registro
		if ($result->num_rows === 1) {
			$resultado = $result->fetch_assoc();

			// Verifica a senha usando hash (supondo que a senha está armazenada com password_hash)
			if (password_verify($senha, $resultado['senha'])) {
				// Define as variáveis de sessão
				$_SESSION['AlunoId'] = $resultado['id'];
				$_SESSION['AlunoEmail'] = $resultado['email'];
				$_SESSION['AlunoNome'] = $resultado['nome'];
				$_SESSION['AlunoNivel'] = $resultado['nivel'];

				// Redireciona para a página de tutorial
				header("Location: tutorial-01.php");
				exit();
			} else {
				$_SESSION['loginErro'] = "Senha incorreta!";
				header("Location: Login.php");
				exit();
			}
		} else {
			$_SESSION['loginErro'] = "Usuário não encontrado!";
			header("Location: Login.php");
			exit();
		}

		$stmt->close();
	} else {
		$_SESSION['loginErro'] = "Erro no sistema. Tente novamente.";
		header("Location: Login.php");
		exit();
	}

	$conn->close();
} else {
	// Se não for POST, redireciona para o login
	header("Location: Login.php");
	exit();
}
