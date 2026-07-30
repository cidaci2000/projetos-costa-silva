<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'cliente') {
    header('Location: ../../pages/login.php');
    exit();
}

$produto_id = $_POST['produto_id'] ?? 0;
$quantidade = intval($_POST['quantidade'] ?? 1);

if (!$produto_id) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Produto inválido!'];
    header('Location: ../../pages/client/restaurants.php');
    exit();
}

if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

if (isset($_SESSION['carrinho'][$produto_id])) {
    $_SESSION['carrinho'][$produto_id] += $quantidade;
} else {
    $_SESSION['carrinho'][$produto_id] = $quantidade;
}

$_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Produto adicionado ao carrinho!'];
header('Location: ../../pages/client/restaurant-detail.php?id=' . $_GET['restaurante_id'] ?? '');
exit();
?>