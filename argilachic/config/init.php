<?php
session_start();
require_once __DIR__ . '/database.php';

if (!isset($_SESSION['sessao_id'])) {
    $_SESSION['sessao_id'] = bin2hex(random_bytes(16));
}

function isLogado() { return isset($_SESSION['usuario_id']); }
function isAdmin() { return isLogado() && ($_SESSION['usuario_tipo'] ?? '') === 'admin'; }
function usuarioAtual() {
    if (!isLogado()) return null;
    return [
        'id' => $_SESSION['usuario_id'],
        'nome' => $_SESSION['usuario_nome'],
        'email' => $_SESSION['usuario_email'],
        'tipo' => $_SESSION['usuario_tipo'],
    ];
}
function redirecionar($url) { header("Location: $url"); exit; }
function jsonResponse($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}
function flash($tipo, $texto) {
    $_SESSION['mensagem'] = ['tipo' => $tipo, 'texto' => $texto];
}