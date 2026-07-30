<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'cliente') {
    header('Location: ../../pages/login.php');
    exit();
}

$pedido_id = $_GET['id'] ?? 0;

try {
    $conn = new PDO("mysql:host=localhost;dbname=foods_delivery", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// Verifica se o pedido pertence ao cliente
$stmt = $conn->prepare("SELECT id FROM pedidos WHERE id = ? AND cliente_id = ? AND status = 'pendente'");
$stmt->execute([$pedido_id, $_SESSION['usuario_id']]);
if (!$stmt->fetch()) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Pedido não pode ser cancelado!'];
    header('Location: ../../pages/client/orders.php');
    exit();
}

$stmt = $conn->prepare("UPDATE pedidos SET status = 'cancelado' WHERE id = ?");
$stmt->execute([$pedido_id]);

$_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Pedido cancelado com sucesso!'];
header('Location: ../../pages/client/orders.php');
exit();
?>