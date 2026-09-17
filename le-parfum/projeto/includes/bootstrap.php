<?php
// ============================================================
// BOOTSTRAP — ponto único de entrada para todas as páginas
// ============================================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/funcoes.php';
require_once __DIR__ . '/auth.php';

// Variáveis globais disponíveis em todas as páginas
$flash     = flash();
$totalCart = totalCarrinho();
$user      = usuarioAtual();