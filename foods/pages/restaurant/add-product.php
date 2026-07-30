<?php
session_start();

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'restaurante') {
    header('Location: ../../index.php');
    exit();
}

try {
    $conn = new PDO("mysql:host=localhost;dbname=foods_delivery", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// Busca restaurante
$stmt = $conn->prepare("SELECT id, nome FROM restaurantes WHERE usuario_id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$restaurante = $stmt->fetch();

if (!$restaurante) {
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
    <title>Novo Produto - <?php echo htmlspecialchars($restaurante['nome']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../style.css">
    <style>
        .restaurant-header {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: white;
            padding: 15px 0;
        }
        .restaurant-header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        .restaurant-menu {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .restaurant-menu a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 8px;
            transition: var(--transition);
            font-size: 14px;
        }
        .restaurant-menu a:hover {
            background: var(--primary);
            color: white;
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
            font-size: 14px;
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
            transition: var(--transition);
        }
        .btn-back:hover {
            color: var(--primary);
        }
        .help-text {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 4px;
        }
    </style>
</head>
<body>
    <header class="restaurant-header">
        <div class="container">
            <h2 style="font-size: 20px;"><i class="fas fa-store" style="color: var(--primary);"></i> <?php echo htmlspecialchars($restaurante['nome']); ?></h2>
            <nav class="restaurant-menu">
                <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <a href="products.php"><i class="fas fa-utensils"></i> Cardápio</a>
                <a href="../../actions/logout.php" class="sair" style="color: #ff6b6b;"><i class="fas fa-sign-out-alt"></i> Sair</a>
            </nav>
        </div>
    </header>

    <main>
        <div class="container">
            <div class="form-container">
                <a href="products.php" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Voltar ao Cardápio
                </a>

                <h2><i class="fas fa-plus-circle" style="color: var(--primary);"></i> Novo Produto</h2>
                <p style="color: var(--text-muted); margin-bottom: 25px;">Adicione um novo item ao cardápio do seu restaurante.</p>

                <?php if ($flash): ?>
                    <div class="flash-message <?php echo $flash['type']; ?>">
                        <?php echo $flash['message']; ?>
                    </div>
                <?php endif; ?>

                <form action="../../actions/restaurant/add-product.php" method="POST">
                    <input type="hidden" name="restaurante_id" value="<?php echo $restaurante['id']; ?>">
                    
                    <div class="form-group">
                        <label>Nome do Produto *</label>
                        <input type="text" name="nome" placeholder="Ex: X-Bacon" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Descrição</label>
                        <textarea name="descricao" placeholder="Descreva o produto..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Preço *</label>
                        <input type="number" name="preco" step="0.01" placeholder="29.90" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Categoria</label>
                        <select name="categoria">
                            <option value="">Selecione...</option>
                            <option value="Hambúrgueres">Hambúrgueres</option>
                            <option value="Pizzas">Pizzas</option>
                            <option value="Massas">Massas</option>
                            <option value="Saladas">Saladas</option>
                            <option value="Sobremesas">Sobremesas</option>
                            <option value="Bebidas">Bebidas</option>
                            <option value="Outros">Outros</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>URL da Imagem</label>
                        <input type="url" name="imagem_url" placeholder="https://exemplo.com/imagem.jpg">
                        <small class="help-text">Cole o link de uma imagem do produto (opcional).</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Disponível no Cardápio</label>
                        <select name="disponivel">
                            <option value="1">Sim</option>
                            <option value="0">Não</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Produto em Destaque</label>
                        <select name="destaque">
                            <option value="0">Não</option>
                            <option value="1">Sim</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save"></i> Cadastrar Produto
                    </button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>