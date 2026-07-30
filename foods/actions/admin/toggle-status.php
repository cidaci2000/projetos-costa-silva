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

// Mapeia tabelas e campos
$map = [
    'restaurante' => ['table' => 'restaurantes', 'field' => 'ativo'],
    'usuario' => ['table' => 'usuarios', 'field' => 'ativo']
];

if (!isset($map[$tipo])) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Tipo inválido!'];
    header('Location: ../../pages/admin/dashboard.php');
    exit();
}

// Busca status atual
$stmt = $conn->prepare("SELECT {$map[$tipo]['field']} FROM {$map[$tipo]['table']} WHERE id = ?");
$stmt->execute([$id]);
$current = $stmt->fetchColumn();

if ($current === false) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Registro não encontrado!'];
    header('Location: ../../pages/admin/dashboard.php');
    exit();
}

// Alterna status
$newStatus = $current ? 0 : 1;
$stmt = $conn->prepare("UPDATE {$map[$tipo]['table']} SET {$map[$tipo]['field']} = ? WHERE id = ?");
$stmt->execute([$newStatus, $id]);

$_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Status atualizado com sucesso!'];

// Redireciona de volta
$redirects = [
    'restaurante' => '../../pages/admin/restaurants.php',
    'usuario' => '../../pages/admin/users.php'
];
header('Location: ' . ($redirects[$tipo] ?? '../../pages/admin/dashboard.php'));
exit();
?>