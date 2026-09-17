<?php
// ========================================
// LOGOUT - Encerra a sessão do usuário
// ========================================

// Inicia a sessão se não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ========================================
// ENCERRA A SESSÃO
// ========================================

// Limpa todas as variáveis de sessão
$_SESSION = array();

// Remove o cookie de sessão
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

// Destroi a sessão
session_destroy();

// ========================================
// REDIRECIONA PARA A PÁGINA INICIAL
// ========================================
header('Location: /2026/PHP/Nicolas/');
exit;
?>