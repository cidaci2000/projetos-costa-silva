<?php
// cliente/cadastro.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userModel = new User();
    
    // Validar senha
    if ($_POST['senha'] !== $_POST['senha_confirmacao']) {
        $error = 'As senhas não coincidem!';
    } elseif (strlen($_POST['senha']) < 6) {
        $error = 'A senha deve ter pelo menos 6 caracteres!';
    } else {
        $data = [
            'nome' => trim($_POST['nome']),
            'email' => trim($_POST['email']),
            'senha' => $_POST['senha'],
            'telefone' => trim($_POST['telefone'] ?? ''),
            'cpf' => trim($_POST['cpf'] ?? ''),
            'endereco' => trim($_POST['endereco'] ?? ''),
            'cidade' => trim($_POST['cidade'] ?? ''),
            'estado' => trim($_POST['estado'] ?? ''),
            'cep' => trim($_POST['cep'] ?? '')
        ];
        
        $result = $userModel->createCliente($data);
        if (isset($result['success'])) {
            $success = true;
        } else {
            $error = $result['error'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro | Nicolas Esportes</title>
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
        .container {
            background: white;
            border-radius: 32px;
            padding: 48px 40px;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.06);
            border: 1px solid #e9edf4;
        }
        .header {
            text-align: center;
            margin-bottom: 32px;
        }
        .header i {
            font-size: 2.4rem;
            color: #facc15;
        }
        .header h1 { font-size: 1.8rem; margin-top: 8px; color: #0b1a2e; }
        .header p { color: #64748b; }
        
        .form-group {
            margin-bottom: 16px;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 4px;
            font-size: 0.9rem;
            color: #1e293b;
        }
        .form-group label .required { color: #ef4444; }
        .form-group input {
            width: 100%;
            padding: 10px 14px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 0.95rem;
            transition: 0.2s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #facc15;
            box-shadow: 0 0 0 4px #facc1530;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        
        .btn-register {
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
            margin-top: 8px;
        }
        .btn-register:hover {
            background: #1e3a5f;
            transform: translateY(-2px);
        }
        
        .alert {
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }
        
        .login-link {
            text-align: center;
            margin-top: 16px;
            color: #64748b;
        }
        .login-link a {
            color: #0b1a2e;
            font-weight: 600;
            text-decoration: none;
        }
        .login-link a:hover { text-decoration: underline; }
        
        @media (max-width: 480px) {
            .form-row { grid-template-columns: 1fr; }
            .container { padding: 24px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <i class="fas fa-user-plus"></i>
            <h1>Criar Conta</h1>
            <p>Cadastre-se na Nicolas Esportes</p>
        </div>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                Cadastro realizado com sucesso! <a href="login.php" style="color: #166534; font-weight: 600;">Faça login</a>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?= $error ?>
            </div>
        <?php endif; ?>
        
        <?php if (!$success): ?>
        <form method="POST">
            <div class="form-group">
                <label>Nome completo <span class="required">*</span></label>
                <input type="text" name="nome" required placeholder="Seu nome completo">
            </div>
            
            <div class="form-group">
                <label>E-mail <span class="required">*</span></label>
                <input type="email" name="email" required placeholder="seu@email.com">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Senha <span class="required">*</span></label>
                    <input type="password" name="senha" required placeholder="Mínimo 6 caracteres">
                </div>
                <div class="form-group">
                    <label>Confirmar senha <span class="required">*</span></label>
                    <input type="password" name="senha_confirmacao" required placeholder="Digite novamente">
                </div>
            </div>
            
            <div class="form-group">
                <label>Telefone</label>
                <input type="text" name="telefone" placeholder="(11) 99999-8888">
            </div>
            
            <div class="form-group">
                <label>CPF</label>
                <input type="text" name="cpf" placeholder="000.000.000-00">
            </div>
            
            <div class="form-group">
                <label>Endereço</label>
                <input type="text" name="endereco" placeholder="Rua, número, complemento">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Cidade</label>
                    <input type="text" name="cidade" placeholder="São Paulo">
                </div>
                <div class="form-group">
                    <label>Estado</label>
                    <input type="text" name="estado" placeholder="SP" maxlength="2">
                </div>
            </div>
            
            <div class="form-group">
                <label>CEP</label>
                <input type="text" name="cep" placeholder="00000-000">
            </div>
            
            <button type="submit" class="btn-register">
                <i class="fas fa-user-plus"></i> Cadastrar
            </button>
        </form>
        <?php endif; ?>
        
        <div class="login-link">
            Já tem conta? <a href="login.php">Faça login</a>
        </div>
        <div class="login-link" style="margin-top: 8px;">
            <a href="/nicolas-esportes/" style="color: #64748b; font-weight: 400;">
                <i class="fas fa-arrow-left"></i> Voltar para a loja
            </a>
        </div>
    </div>
</body>
</html>