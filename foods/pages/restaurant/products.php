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
$stmt = $conn->prepare("SELECT * FROM restaurantes WHERE usuario_id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$restaurante = $stmt->fetch();

if (!$restaurante) {
    header('Location: ../../index.php');
    exit();
}

// Busca produtos
$stmt = $conn->prepare("SELECT * FROM produtos WHERE restaurante_id = ? ORDER BY nome");
$stmt->execute([$restaurante['id']]);
$produtos = $stmt->fetchAll();

$flash = $_SESSION['flash_message'] ?? null;
unset($_SESSION['flash_message']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cardápio - <?php echo htmlspecialchars($restaurante['nome']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../style.css">
    <style>
        .restaurant-header {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: white;
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
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
        .restaurant-menu a:hover,
        .restaurant-menu a.active {
            background: var(--primary);
            color: white;
        }
        .restaurant-menu .sair {
            color: #ff6b6b;
        }
        .restaurant-menu .sair:hover {
            background: #ff6b6b;
            color: white;
        }
        .table-container {
            background: white;
            border-radius: var(--radius);
            padding: 20px;
            box-shadow: var(--shadow);
            overflow-x: auto;
            margin-top: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th {
            background: #f8f9fa;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            color: var(--text-muted);
        }
        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }
        .btn-add {
            background: var(--primary);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }
        .btn-add:hover {
            background: var(--primary-dark);
            transform: scale(1.02);
        }
        .btn-sm {
            padding: 5px 12px;
            font-size: 12px;
            border-radius: 6px;
            text-decoration: none;
            display: inline-block;
            transition: var(--transition);
            border: none;
            cursor: pointer;
            font-family: inherit;
        }
        .btn-sm:hover {
            transform: scale(1.05);
        }
        .btn-edit {
            background: #17a2b8;
            color: white;
        }
        .btn-delete {
            background: #dc3545;
            color: white;
        }
        .btn-toggle {
            background: #28a745;
            color: white;
        }
        .btn-toggle.inactive {
            background: #ffc107;
            color: #333;
        }
        .produto-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
            background: #f8f9fa;
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: var(--text-muted);
        }
        .empty-state i {
            font-size: 48px;
            opacity: 0.5;
            margin-bottom: 15px;
        }
        .status-disponivel {
            color: #28a745;
            font-weight: 600;
        }
        .status-indisponivel {
            color: #dc3545;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .restaurant-header .container {
                flex-direction: column;
                text-align: center;
            }
            .restaurant-menu {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <header class="restaurant-header">
        <div class="container">
            <h2 style="font-size: 20px;"><i class="fas fa-store" style="color: var(--primary);"></i> <?php echo htmlspecialchars($restaurante['nome']); ?></h2>
            <nav class="restaurant-menu">
                <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <a href="products.php" class="active"><i class="fas fa-utensils"></i> Cardápio</a>
                <a href="orders.php"><i class="fas fa-shopping-bag"></i> Pedidos</a>
                <a href="../../actions/logout.php" class="sair"><i class="fas fa-sign-out-alt"></i> Sair</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <?php if ($flash): ?>
            <div class="flash-message <?php echo $flash['type']; ?>">
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>

        <div class="header-actions">
            <h2><i class="fas fa-utensils"></i> Cardápio</h2>
            <a href="add-product.php" class="btn-add">
                <i class="fas fa-plus"></i> Novo Produto
            </a>
        </div>

        <div class="table-container">
            <?php if (!empty($produtos)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Imagem</th>
                        <th>Nome</th>
                        <th>Categoria</th>
                        <th>Preço</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($produtos as $p): ?>
                    <tr>
                        <td>
                            <?php if ($p['imagem_url']): ?>
                                <img src="<?php echo htmlspecialchars($p['imagem_url']); ?>" class="produto-img" alt="<?php echo htmlspecialchars($p['nome']); ?>">
                            <?php else: ?>
                                <div class="produto-img" style="display: flex; align-items: center; justify-content: center; background: #e9ecef; color: #adb5bd; font-size: 12px;">Sem img</div>
                            <?php endif; ?>
                        </td>
                        <td><strong><?php echo htmlspecialchars($p['nome']); ?></strong></td>
                        <td><?php echo htmlspecialchars($p['categoria'] ?? 'N/A'); ?></td>
                        <td>R$ <?php echo number_format($p['preco'], 2, ',', '.'); ?></td>
                        <td>
                            <?php if ($p['disponivel']): ?>
                                <span class="status-disponivel">✓ Disponível</span>
                            <?php else: ?>
                                <span class="status-indisponivel">✗ Indisponível</span>
                            <?php endif; ?>
                        </td>
                        <td style="display: flex; gap: 5px; flex-wrap: wrap;">
                            <a href="edit-product.php?id=<?php echo $p['id']; ?>" class="btn-sm btn-edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="../../actions/restaurant/toggle-product.php?id=<?php echo $p['id']; ?>" 
                               class="btn-sm btn-toggle <?php echo $p['disponivel'] ? '' : 'inactive'; ?>">
                                <i class="fas <?php echo $p['disponivel'] ? 'fa-pause' : 'fa-play'; ?>"></i>
                            </a>
                            <a href="../../actions/restaurant/delete-product.php?id=<?php echo $p['id']; ?>" 
                               class="btn-sm btn-delete" 
                               onclick="return confirm('Tem certeza que deseja excluir este produto?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-utensils"></i>
                <h3>Nenhum produto no cardápio</h3>
                <p>Clique em "Novo Produto" para começar a adicionar itens.</p>
            </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>