<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'cliente') {
    header('Location: ../../pages/login.php');
    exit();
}

$produto_id = $_POST['produto_id'] ?? 0;
$action = $_POST['action'] ?? '';

if (!$produto_id || !isset($_SESSION['carrinho'][$produto_id])) {
    header('Location: ../../pages/client/cart.php');
    exit();
}

if ($action === 'increase') {
    $_SESSION['carrinho'][$produto_id]++;
} elseif ($action === 'decrease') {
    $_SESSION['carrinho'][$produto_id]--;
    if ($_SESSION['carrinho'][$produto_id] <= 0) {
        unset($_SESSION['carrinho'][$produto_id]);
    }
}

header('Location: ../../pages/client/cart.php');
exit();
?>