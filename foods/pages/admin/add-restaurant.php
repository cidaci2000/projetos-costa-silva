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
    <title>Adicionar Restaurante</title>
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
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-family: inherit;
            transition: var(--transition);
        }
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary);
        }
        .form-group textarea {
            min-height: 80px;
            resize: vertical;
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
                <a href="restaurants.php" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>

                <h2>Adicionar Restaurante</h2>

                <?php if ($flash): ?>
                    <div class="flash-message <?php echo $flash['type']; ?>">
                        <?php echo $flash['message']; ?>
                    </div>
                <?php endif; ?>

                <form action="../../actions/admin/add-restaurant.php" method="POST">
                    <div class="form-group">
                        <label>Nome do Restaurante</label>
                        <input type="text" name="nome" required>
                    </div>
                    <div class="form-group">
                        <label>CNPJ</label>
                        <input type="text" name="cnpj" placeholder="00.000.000/0000-00" required>
                    </div>
                    <div class="form-group">
                        <label>Email do Proprietário</label>
                        <input type="email" name="email" placeholder="proprietario@email.com" required>
                    </div>
                    <div class="form-group">
                        <label>Senha do Proprietário</label>
                        <input type="password" name="senha" placeholder="********" required>
                    </div>
                    <div class="form-group">
                        <label>Categoria</label>
                        <select name="categoria" required>
                            <option value="">Selecione...</option>
                            <option value="Pizza">Pizza</option>
                            <option value="Hambúrguer">Hambúrguer</option>
                            <option value="Sushi">Sushi</option>
                            <option value="Italiana">Italiana</option>
                            <option value="Mexicana">Mexicana</option>
                            <option value="Brasileira">Brasileira</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Endereço</label>
                        <input type="text" name="endereco" placeholder="Rua, número" required>
                    </div>
                    <div class="form-group">
                        <label>Cidade</label>
                        <input type="text" name="cidade" required>
                    </div>
                    <div class="form-group">
                        <label>Estado</label>
                        <input type="text" name="estado" placeholder="SP" required>
                    </div>
                    <div class="form-group">
                        <label>Telefone</label>
                        <input type="text" name="telefone" placeholder="(11) 99999-8888">
                    </div>
                    <div class="form-group">
                        <label>Taxa de Entrega (R$)</label>
                        <input type="number" name="taxa_entrega" step="0.01" value="0">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Cadastrar Restaurante</button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>