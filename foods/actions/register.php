<?php
session_start();

// Conexão com o banco
try {
    $conn = new PDO("mysql:host=localhost;dbname=foods_delivery", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Erro de conexão com o banco.'];
    header('Location: ../pages/register.php');
    exit();
}

// Pega os dados
$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$cpf = trim($_POST['cpf'] ?? '');
$senha = $_POST['senha'] ?? '';

// Valida
if (empty($nome) || strlen($nome) < 3) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Nome deve ter pelo menos 3 caracteres.'];
    header('Location: ../pages/register.php');
    exit();
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'E-mail inválido.'];
    header('Location: ../pages/register.php');
    exit();
}

if (empty($cpf) || strlen(preg_replace('/[^0-9]/', '', $cpf)) !== 11) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'CPF inválido.'];
    header('Location: ../pages/register.php');
    exit();
}

if (empty($senha) || strlen($senha) < 6) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Senha deve ter pelo menos 6 caracteres.'];
    header('Location: ../pages/register.php');
    exit();
}

// Verifica se email já existe
$stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Este e-mail já está cadastrado.'];
    header('Location: ../pages/register.php');
    exit();
}

// Verifica se CPF já existe
$stmt = $conn->prepare("SELECT id FROM usuarios WHERE cpf = ?");
$stmt->execute([$cpf]);
if ($stmt->fetch()) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Este CPF já está cadastrado.'];
    header('Location: ../pages/register.php');
    exit();
}

// Cria usuário
$hash = password_hash($senha, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, cpf, tipo) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$nome, $email, $hash, $cpf, 'cliente']);

$_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Cadastro realizado com sucesso! Faça login.'];
header('Location: ../pages/login.php');
exit();
?>