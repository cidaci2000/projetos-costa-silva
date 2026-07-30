<?php
session_start();

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header('Location: ../../index.php');
    exit();
}

try {
    $conn = new PDO("mysql:host=localhost;dbname=foods_delivery", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Erro de conexão.'];
    header('Location: ../../pages/admin/dashboard.php');
    exit();
}

$tipo = $_GET['tipo'] ?? '';
$id = $_GET['id'] ?? 0;

if (!$tipo || !$id) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Dados inválidos!'];
    header('Location: ../../pages/admin/dashboard.php');
    exit();
}

// Mapeia tabelas
$map = [
    'restaurante' => 'restaurantes',
    'motoboy' => 'motoboys',
    'usuario' => 'usuarios'
];

if (!isset($map[$tipo])) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Tipo inválido!'];
    header('Location: ../../pages/admin/dashboard.php');
    exit();
}

// Verifica se não é admin
if ($tipo === 'usuario') {
    $stmt = $conn->prepare("SELECT tipo FROM usuarios WHERE id = ?");
    $stmt->execute([$id]);
    $userType = $stmt->fetchColumn();
    if ($userType === 'admin') {
        $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Não é possível excluir o administrador!'];
        header('Location: ../../pages/admin/users.php');
        exit();
    }
}

// Exclui
$stmt = $conn->prepare("DELETE FROM {$map[$tipo]} WHERE id = ?");
$stmt->execute([$id]);

$_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Registro excluído com sucesso!'];

$redirects = [
    'restaurante' => '../../pages/admin/restaurants.php',
    'motoboy' => '../../pages/admin/couriers.php',
    'usuario' => '../../pages/admin/users.php'
];
header('Location: ' . ($redirects[$tipo] ?? '../../pages/admin/dashboard.php'));
exit();
?>