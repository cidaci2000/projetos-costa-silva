<?php
session_start();

// Se já estiver logado, redireciona para sua página
if (isset($_SESSION['usuario_id'])) {
    $redirects = [
        'cliente' => '../pages/client/restaurants.php',  // 🔥 MUDOU PARA restaurants.php
        'admin' => '../pages/admin/dashboard.php',
        'restaurante' => '../pages/restaurant/dashboard.php',
        'motoboy' => '../pages/courier/dashboard.php'
    ];
    header('Location: ' . ($redirects[$_SESSION['tipo_usuario']] ?? '../index.php'));
    exit();
}

// Conexão com o banco
try {
    $conn = new PDO("mysql:host=localhost;dbname=foods_delivery", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Erro de conexão.'];
    header('Location: ../pages/login.php');
    exit();
}

// Pega os dados do POST
$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';
$tipo = $_POST['tipo'] ?? 'cliente';

// Valida campos
if (empty($email) || empty($senha)) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Preencha todos os campos!'];
    $redirectMap = [
        'cliente' => '../pages/login.php',
        'admin' => '../pages/login-admin.php',
        'restaurante' => '../pages/login-restaurant.php',
        'motoboy' => '../pages/login-courier.php'
    ];
    header('Location: ' . ($redirectMap[$tipo] ?? '../pages/login.php'));
    exit();
}

// Busca usuário pelo email e tipo
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ? AND tipo = ?");
$stmt->execute([$email, $tipo]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Usuário não encontrado!'];
    $redirectMap = [
        'cliente' => '../pages/login.php',
        'admin' => '../pages/login-admin.php',
        'restaurante' => '../pages/login-restaurant.php',
        'motoboy' => '../pages/login-courier.php'
    ];
    header('Location: ' . ($redirectMap[$tipo] ?? '../pages/login.php'));
    exit();
}

// Verifica se o usuário está ativo
if (!$user['ativo']) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Usuário desativado!'];
    header('Location: ../pages/login.php');
    exit();
}

// Verifica senha
$senhaValida = false;
if (password_verify($senha, $user['senha'])) {
    $senhaValida = true;
} elseif (md5($senha) === $user['senha']) {
    $hash = password_hash($senha, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
    $stmt->execute([$hash, $user['id']]);
    $senhaValida = true;
}

if (!$senhaValida) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Senha incorreta!'];
    $redirectMap = [
        'cliente' => '../pages/login.php',
        'admin' => '../pages/login-admin.php',
        'restaurante' => '../pages/login-restaurant.php',
        'motoboy' => '../pages/login-courier.php'
    ];
    header('Location: ' . ($redirectMap[$tipo] ?? '../pages/login.php'));
    exit();
}

// Cria sessão
$_SESSION['usuario_id'] = $user['id'];
$_SESSION['usuario_nome'] = $user['nome'];
$_SESSION['tipo_usuario'] = $user['tipo'];

$_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Bem-vindo, ' . $user['nome'] . '!'];

// 🔥 REDIRECIONA CORRETAMENTE - CLIENTE VAI PARA RESTAURANTS.PHP
$redirects = [
    'cliente' => '../pages/client/restaurants.php',  // 🔥 MUDOU
    'admin' => '../pages/admin/dashboard.php',
    'restaurante' => '../pages/restaurant/dashboard.php',
    'motoboy' => '../pages/courier/dashboard.php'
];

header('Location: ' . ($redirects[$user['tipo']] ?? '../index.php'));
exit();
?>