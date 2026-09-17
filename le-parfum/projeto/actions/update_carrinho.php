<?php
require_once __DIR__ . '/../includes/funcoes.php';

$id  = (int)($_POST['produto_id'] ?? 0);
$qtd = (int)($_POST['quantidade'] ?? 1);

if ($qtd <= 0) {
    unset($_SESSION['carrinho'][$id]);
} else {
    $_SESSION['carrinho'][$id] = $qtd;
}

redirect(BASE_URL . 'pages/carrinho.php');