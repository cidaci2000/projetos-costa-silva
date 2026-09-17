<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/funcoes.php';

$id  = (int)($_POST['produto_id'] ?? 0);
$qtd = max(1, (int)($_POST['quantidade'] ?? 1));

$stmt = $pdo->prepare("SELECT nome FROM produtos WHERE id = ?");
$stmt->execute([$id]);
$produto = $stmt->fetch();

if (!$produto) {
    flash('Produto inválido.', 'error');
    redirect($_SERVER['HTTP_REFERER'] ?? BASE_URL . 'pages/catalogo.php');
}

$_SESSION['carrinho'][$id] = ($_SESSION['carrinho'][$id] ?? 0) + $qtd;

flash("{$produto['nome']} adicionado ao carrinho!", 'success');
redirect($_SERVER['HTTP_REFERER'] ?? BASE_URL . 'pages/catalogo.php');