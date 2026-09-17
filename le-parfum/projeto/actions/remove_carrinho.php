<?php
require_once __DIR__ . '/../includes/funcoes.php';

$id = (int)($_POST['produto_id'] ?? 0);
unset($_SESSION['carrinho'][$id]);

flash('Item removido do carrinho.', 'success');
redirect(BASE_URL . 'pages/carrinho.php');