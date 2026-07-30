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
    header('Location: ../../pages/admin/add-restaurant.php');
    exit();
}

$nome = trim($_POST['nome'] ?? '');
$cnpj = trim($_POST['cnpj'] ?? '');
$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';
$categoria = $_POST['categoria'] ?? '';
$endereco = trim($_POST['endereco'] ?? '');
$cidade = trim($_POST['cidade'] ?? '');
$estado = trim($_POST['estado'] ?? '');
$telefone = trim($_POST['telefone'] ?? '');
$taxa_entrega = floatval($_POST['taxa_entrega'] ?? 0);

// Validações
if (empty($nome) || empty($email) || empty($senha) || empty($endereco)) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Preencha todos os campos obrigatórios!'];
    header('Location: ../../pages/admin/add-restaurant.php');
    exit();
}

// Verifica se email já existe
$stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Este e-mail já está cadastrado!'];
    header('Location: ../../pages/admin/add-restaurant.php');
    exit();
}

// Cria usuário do restaurante
$hash = password_hash($senha, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, cpf, tipo) VALUES (?, ?, ?, ?, 'restaurante')");
$stmt->execute([$nome, $email, $hash, $cnpj]);

$usuario_id = $conn->lastInsertId();

// Cria restaurante
$stmt = $conn->prepare("
    INSERT INTO restaurantes (usuario_id, nome, cnpj, categoria, endereco, cidade, estado, telefone, taxa_entrega) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
");
$stmt->execute([$usuario_id, $nome, $cnpj, $categoria, $endereco, $cidade, $estado, $telefone, $taxa_entrega]);

$_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Restaurante cadastrado com sucesso!'];
header('Location: ../../pages/admin/restaurants.php');
exit();
?>