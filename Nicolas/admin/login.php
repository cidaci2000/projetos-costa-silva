<?php
// admin/login.php
require_once __DIR__ . '../config/database.php';
require_once __DIR__ . '../config/auth.php';
require_once __DIR__ . '../models/User.php';

// Se já estiver logado, redireciona
if (Auth::isAdmin()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    
    if (empty($email) || empty($senha)) {
        $error = 'Preencha todos os campos!';
    } else {
        $userModel = new User();
        $user = $userModel->authenticateAdmin($email, $senha);
        
        if ($user) {
            Auth::login($user['id'], 'admin', $user['nome'], $user['email']);
            header('Location: index.php');
            exit;
        } else {
            $error = 'E-mail ou senha incorretos!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin | Nicolas Esportes</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body {
            background: linear-gradient(145deg, #0b1a2e, #162b44);
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
            max-width: 420px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }
        .login-header i {
            font-size: 3rem;
            color: #facc15;
            background: #fef3c7;
            padding: 16px;
            border-radius: 60px;
        }
        .login-header h1 {
            font-size: 1.8rem;
            margin-top: 16px;
            color: #0b1a2e;
        }
        .login-header p {
            color: #64748b;
            margin-top: 4px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: #1e293b;
        }
        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 16px;
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
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
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
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #64748b;
            text-decoration: none;
            font-size: 0.95rem;
        }
        .back-link:hover { color: #0b1a2e; }
        .admin-credentials {
            background: #f1f5f9;
            padding: 16px;
            border-radius: 12px;
            margin-top: 20px;
            font-size: 0.9rem;
            color: #475569;
        }
        .admin-credentials strong { color: #0b1a2e; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <i class="fas fa-user-shield"></i>
            <h1>Admin</h1>
            <p>Faça login para gerenciar a loja</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert-error">
                <i class="fas fa-exclamation-circle"></i> <?= $error ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>E-mail</label>
                <input type="email" name="email" placeholder="admin@nicolasesportes.com" required>
            </div>
            <div class="form-group">
                <label>Senha</label>
                <input type="password" name="senha" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Entrar
            </button>
        </form>
        
        <a href="/nicolas-esportes/" class="back-link">
            <i class="fas fa-arrow-left"></i> Voltar para a loja
        </a>
        
        <div class="admin-credentials">
            <strong>Credenciais padrão:</strong><br>
            E-mail: <strong>admin@nicolasesportes.com</strong><br>
            Senha: <strong>admin123</strong>
        </div>
    </div>
</body>
</html>