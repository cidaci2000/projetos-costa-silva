<?php
session_start();
$flash = $_SESSION['flash_message'] ?? null;
unset($_SESSION['flash_message']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Foods Delivery</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #fff5f0 0%, #ffe8e0 100%);
            padding: 20px;
        }

        .auth-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 40px;
            max-width: 420px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
        }

        .auth-card h2 {
            text-align: center;
            font-size: 28px;
            margin-bottom: 5px;
        }

        .auth-card .subtitle {
            text-align: center;
            color: var(--text-muted);
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
            transition: var(--transition);
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary);
        }

        .btn-block {
            width: 100%;
        }

        .text-center {
            text-align: center;
        }

        .mt-15 {
            margin-top: 15px;
        }

        .text-primary {
            color: var(--primary);
            text-decoration: none;
        }

        .text-primary:hover {
            text-decoration: underline;
        }

        .btn-back {
            display: inline-block;
            margin-bottom: 20px;
            color: var(--text-muted);
            text-decoration: none;
            transition: var(--transition);
        }

        .btn-back:hover {
            color: var(--primary);
        }

        .text-muted {
            color: var(--text-muted);
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <a href="../index.php" class="btn-back">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>

            <h2>Criar Conta</h2>
            <p class="subtitle">Cadastre-se para começar a pedir</p>

            <?php if ($flash): ?>
                <div class="flash-message <?php echo $flash['type']; ?>">
                    <?php echo $flash['message']; ?>
                </div>
            <?php endif; ?>

            <form action="../actions/register.php" method="POST">
                <div class="form-group">
                    <label>Nome Completo</label>
                    <input type="text" name="nome" placeholder="Seu nome" required>
                </div>
                <div class="form-group">
                    <label>E-mail</label>
                    <input type="email" name="email" placeholder="seu@email.com" required>
                </div>
                <div class="form-group">
                    <label>CPF</label>
                    <input type="text" name="cpf" placeholder="000.000.000-00" required>
                </div>
                <div class="form-group">
                    <label>Senha</label>
                    <input type="password" name="senha" placeholder="********" required minlength="6">
                    <small class="text-muted">Mínimo 6 caracteres</small>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Criar Conta</button>
            </form>

            <p class="text-center mt-15">
                Já tem conta? <a href="login.php" class="text-primary">Fazer Login</a>
            </p>
        </div>
    </div>
</body>
</html>