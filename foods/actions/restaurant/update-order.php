<?php
session_start();

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'restaurante') {
    header('Location: ../../index.php');
    exit();
}

try {
    $conn = new PDO("mysql:host=localhost;dbname=foods_delivery", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Erro de conexão.'];
    header('Location: ../../pages/restaurant/dashboard.php');
    exit();
}

$pedido_id = $_POST['pedido_id'] ?? 0;
$status = $_POST['status'] ?? '';

if (!$pedido_id || !$status) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Dados inválidos!'];
    header('Location: ../../pages/restaurant/dashboard.php');
    exit();
}

// Verifica se o pedido pertence ao restaurante
$stmt = $conn->prepare("
    SELECT r.id FROM pedidos p 
    JOIN restaurantes r ON p.restaurante_id = r.id 
    WHERE p.id = ? AND r.usuario_id = ?
");
$stmt->execute([$pedido_id, $_SESSION['usuario_id']]);
if (!$stmt->fetch()) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Pedido não encontrado!'];
    header('Location: ../../pages/restaurant/dashboard.php');
    exit();
}

// Atualiza status
$stmt = $conn->prepare("UPDATE pedidos SET status = ? WHERE id = ?");
$stmt->execute([$status, $pedido_id]);

$_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Status do pedido atualizado!'];
header('Location: ../../pages/restaurant/dashboard.php');
exit();
?>