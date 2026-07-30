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
    header('Location: ../../pages/admin/add-courier.php');
    exit();
}

$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$cpf = trim($_POST['cpf'] ?? '');
$senha = $_POST['senha'] ?? '';
$cnh = trim($_POST['cnh'] ?? '');
$placa = trim($_POST['placa'] ?? '');
$modelo_moto = trim($_POST['modelo_moto'] ?? '');
$cor_moto = trim($_POST['cor_moto'] ?? '');

// Validações
if (empty($nome) || empty($email) || empty($senha) || empty($cpf) || empty($cnh) || empty($placa) || empty($modelo_moto)) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Preencha todos os campos obrigatórios!'];
    header('Location: ../../pages/admin/add-courier.php');
    exit();
}

// Verifica se email já existe
$stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Este e-mail já está cadastrado!'];
    header('Location: ../../pages/admin/add-courier.php');
    exit();
}

// Cria usuário do motoboy
$hash = password_hash($senha, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, cpf, tipo) VALUES (?, ?, ?, ?, 'motoboy')");
$stmt->execute([$nome, $email, $hash, $cpf]);

$usuario_id = $conn->lastInsertId();

// Cria motoboy
$stmt = $conn->prepare("
    INSERT INTO motoboys (usuario_id, cnh, placa, modelo_moto, cor_moto) 
    VALUES (?, ?, ?, ?, ?)
");
$stmt->execute([$usuario_id, $cnh, $placa, $modelo_moto, $cor_moto]);

$_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Motoboy cadastrado com sucesso!'];
header('Location: ../../pages/admin/couriers.php');
exit();
?>