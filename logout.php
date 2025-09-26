<?php
// Inicia a sessão
session_start();

// Destroi todas as variáveis da sessão
$_SESSION = array();

// Se desejar, remove o cookie da sessão
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destrói a sessão
session_destroy();

// Redireciona para login com mensagem
header("Location: login.php?msg=logout");
exit();
