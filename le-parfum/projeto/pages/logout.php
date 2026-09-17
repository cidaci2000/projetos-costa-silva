<?php
// ============================================================
// LOGOUT — encerra a sessão e volta para o login
// ============================================================
require_once __DIR__ . '/../includes/bootstrap.php';

// Mensagem antes de destruir a sessão
flash('Você saiu da sua conta. Até logo! 👋', 'success');

// Encerra a sessão
logout();