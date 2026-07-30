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

$disponivel = $_POST['disponivel'] ?? 1;

$stmt = $conn->prepare("UPDATE motoboys SET disponivel = ? WHERE usuario_id = ?");
$stmt->execute([$disponivel, $_SESSION['usuario_id']]);

$_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Status atualizado!'];
header('Location: ../../pages/courier/dashboard.php');
exit();
?>