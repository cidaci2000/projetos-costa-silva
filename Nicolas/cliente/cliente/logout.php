<?php
// cliente/logout.php
require_once __DIR__ . '/../config/auth.php';

// Verifica se o usuário está logado como cliente
if (!Auth::isCliente()) {
    header('Location: /2026/PHP/Nicolas/');
    exit;
}

// Realiza o logout
Auth::logout();

// Redireciona para o login do cliente com mensagem
header('Location: /2026/PHP/Nicolas/cliente/login.php?logout=success');
exit;