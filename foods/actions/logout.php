<?php
session_start();

// Destroi a sessão
$_SESSION = array();
session_destroy();

// Remove cookie de sessão
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redireciona para home
header('Location: ../index.php');
exit();
?>