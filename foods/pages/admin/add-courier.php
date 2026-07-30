<?php
session_start();

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header('Location: ../../index.php');
    exit();
}

$flash = $_SESSION['flash_message'] ?? null;
unset($_SESSION['flash_message']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Motoboy</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../style.css">
    <style>
        .admin-header {
            background: #1a1a2e;
            color: white;
            padding: 20px 0;
        }
        .admin-header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .admin-menu {
            display: flex;
            gap: 20px;
        }
        .admin-menu a {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 8px;
            transition: var(--transition);
        }
        .admin-menu a:hover {
            background: var(--primary);
        }
        .form-container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            padding: 40px;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-family: inherit;
            transition: var(--transition);
        }
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary);
        }
        .btn-block {
            width: 100%;
        }
        .btn-back {
            display: inline-block;
            margin-bottom: 20px;
            color: var(--text-muted);
            text-decoration: none;
        }
        .btn-back:hover {
            color: var(--primary);
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="container">
            <h2><i class="fas fa-crown"></i> Admin Panel</h2>
            <div class="admin-menu">
                <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <a href="restaurants.php"><i class="fas fa-store"></i> Restaurantes</a>
                <a href="couriers.php"><i class="fas fa-motorcycle"></i> Motoboys</a>
                <a href="../../actions/logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
            </div>
        </div>
    </header>

    <main>
        <div class="container">
            <div class="form-container">
                <a href="couriers.php" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>

                <h2>Adicionar Motoboy</h2>

                <?php if ($flash): ?>
                    <div class="flash-message <?php echo $flash['type']; ?>">
                        <?php echo $flash['message']; ?>
                    </div>
                <?php endif; ?>

                <form action="../../actions/admin/add-courier.php" method="POST">
                    <div class="form-group">
                        <label>Nome Completo</label>
                        <input type="text" name="nome" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>CPF</label>
                        <input type="text" name="cpf" placeholder="000.000.000-00" required>
                    </div>
                    <div class="form-group">
                        <label>Senha</label>
                        <input type="password" name="senha" placeholder="********" required>
                    </div>
                    <div class="form-group">
                        <label>CNH</label>
                        <input type="text" name="cnh" required>
                    </div>
                    <div class="form-group">
                        <label>Placa da Moto</label>
                        <input type="text" name="placa" placeholder="ABC-1234" required>
                    </div>
                    <div class="form-group">
                        <label>Modelo da Moto</label>
                        <input type="text" name="modelo_moto" placeholder="Honda CG 160" required>
                    </div>
                    <div class="form-group">
                        <label>Cor da Moto</label>
                        <input type="text" name="cor_moto" placeholder="Vermelha">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Cadastrar Motoboy</button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>