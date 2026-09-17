<?php
// cliente/login.php

// ========================================
// CONFIGURAÇÃO INICIAL
// ========================================
// Inicia a sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Carrega os arquivos necessários
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/User.php';

// ========================================
// VERIFICA SE JÁ ESTÁ LOGADO
// ========================================
if (Auth::isCliente()) {
    header('Location: /2026/PHP/Nicolas/');
    exit;
}

// ========================================
// PROCESSAMENTO DO LOGIN
// ========================================
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    
    if (empty($email) || empty($senha)) {
        $error = 'Preencha todos os campos!';
    } else {
        $userModel = new User();
        $user = $userModel->authenticateCliente($email, $senha);
        
        if ($user) {
            Auth::login($user['id'], 'cliente', $user['nome'], $user['email']);
            header('Location: /2026/PHP/Nicolas/');
            exit;
        } else {
            $error = 'E-mail ou senha incorretos!';
        }
    }
}

// Verifica se veio com mensagem de logout
$logoutSuccess = isset($_GET['logout']) && $_GET['logout'] === 'success';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Nicolas Esportes</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body {
            background: #f5f7fc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-container {
            background: white;
            border-radius: 32px;
            padding: 48px 40px;
            max-width: 400px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.06);
            border: 1px solid #e9edf4;
        }
        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }
        .login-header i {
            font-size: 2.4rem;
            color: #facc15;
        }
        .login-header h1 { font-size: 1.8rem; margin-top: 8px; color: #0b1a2e; }
        .login-header p { color: #64748b; }
        
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 4px;
            color: #1e293b;
        }
        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: 0.2s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #facc15;
            box-shadow: 0 0 0 4px #facc1530;
        }
        .btn-login {
            width: 100%;
            padding: 14px;
            background: #0b1a2e;
            color: white;
            border: none;
            border-radius: 60px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
        }
        .btn-login:hover {
            background: #1e3a5f;
            transform: translateY(-2px);
        }
        
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            border: 1px solid #fca5a5;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            border: 1px solid #86efac;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .links {
            text-align: center;
            margin-top: 16px;
        }
        .links a {
            color: #0b1a2e;
            text-decoration: none;
            font-weight: 500;
        }
        .links a:hover { text-decoration: underline; }
        .links .back-link {
            display: block;
            margin-top: 8px;
            color: #64748b;
            font-weight: 400;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <i class="fas fa-user-circle"></i>
            <h1>Bem-vindo</h1>
            <p>Faça login na sua conta</p>
        </div>
        
        <?php if ($logoutSuccess): ?>
            <div class="alert-success">
                <i class="fas fa-check-circle"></i> Você foi desconectado com sucesso!
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert-error">
                <i class="fas fa-exclamation-circle"></i> <?= $error ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>E-mail</label>
                <input type="email" name="email" placeholder="seu@email.com" required>
            </div>
            <div class="form-group">
                <label>Senha</label>
                <input type="password" name="senha" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Entrar
            </button>
        </form>
        
        <div class="links">
            <a href="cadastro.php">Criar nova conta</a>
            <a href="/2026/PHP/Nicolas/" class="back-link">
                <i class="fas fa-arrow-left"></i> Voltar para a loja
            </a>
        </div>
    </div>
</body>
</html>