<?php
session_start();

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'motoboy') {
    header('Location: ../../index.php');
    exit();
}

try {
    $conn = new PDO("mysql:host=localhost;dbname=foods_delivery", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Erro de conexão.'];
    header('Location: ../../pages/courier/dashboard.php');
    exit();
}

$pedido_id = $_POST['pedido_id'] ?? 0;

if (!$pedido_id) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Pedido inválido!'];
    header('Location: ../../pages/courier/dashboard.php');
    exit();
}

// Busca motoboy
$stmt = $conn->prepare("SELECT id FROM motoboys WHERE usuario_id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$motoboy = $stmt->fetch();

if (!$motoboy) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Motoboy não encontrado!'];
    header('Location: ../../pages/courier/dashboard.php');
    exit();
}

// Atualiza pedido
$stmt = $conn->prepare("UPDATE pedidos SET motoboy_id = ? WHERE id = ? AND status = 'saiu_entrega'");
$stmt->execute([$motoboy['id'], $pedido_id]);

if ($stmt->rowCount() > 0) {
    $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Entrega aceita com sucesso!'];
} else {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Este pedido não está mais disponível!'];
}

header('Location: ../../pages/courier/dashboard.php');
exit();
?>