<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'cliente') {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Faça login como cliente!'];
    header('Location: ../login.php');
    exit();
}

$id = $_GET['id'] ?? 0;

try {
    $conn = new PDO("mysql:host=localhost;dbname=foods_delivery", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// Busca restaurante
$stmt = $conn->prepare("SELECT * FROM restaurantes WHERE id = ? AND ativo = 1");
$stmt->execute([$id]);
$restaurante = $stmt->fetch();

if (!$restaurante) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Restaurante não encontrado!'];
    header('Location: restaurants.php');
    exit();
}

// Busca produtos
$stmt = $conn->prepare("SELECT * FROM produtos WHERE restaurante_id = ? AND disponivel = 1 ORDER BY nome");
$stmt->execute([$id]);
$produtos = $stmt->fetchAll();

$flash = $_SESSION['flash_message'] ?? null;
unset($_SESSION['flash_message']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($restaurante['nome']); ?> - Foods Delivery</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../style.css">
    <style>
        nav {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .nav-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        .logo {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
        }
        .nav-links {
            display: flex;
            list-style: none;
            gap: 30px;
        }
        .nav-links a {
            text-decoration: none;
            color: var(--text);
            font-weight: 500;
            transition: var(--transition);
        }
        .nav-links a:hover {
            color: var(--primary);
        }
        .user-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .btn-logout {
            background: transparent;
            border: 2px solid #dc3545;
            color: #dc3545;
            padding: 8px 18px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
        }
        .btn-logout:hover {
            background: #dc3545;
            color: white;
        }
        .restaurant-header {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin: 30px 0;
            background: white;
            border-radius: var(--radius);
            padding: 30px;
            box-shadow: var(--shadow);
        }
        .restaurant-header .info h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }
        .restaurant-header .info .meta {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            color: var(--text-muted);
            font-size: 14px;
        }
        .restaurant-header .info .meta i {
            margin-right: 5px;
        }
        .restaurant-header .info .meta .rating {
            color: #f59e0b;
        }
        .restaurant-img {
            width: 100%;
            height: 200px;
            background-size: cover;
            background-position: center;
            border-radius: var(--radius);
        }
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .product-card {
            background: white;
            border-radius: var(--radius);
            padding: 20px;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }
        .product-card h3 {
            margin: 0 0 5px 0;
        }
        .product-card .price {
            font-size: 20px;
            font-weight: 700;
            color: var(--primary);
        }
        .product-card .desc {
            color: var(--text-muted);
            font-size: 14px;
            margin: 5px 0 10px;
        }
        .product-card .actions {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-top: 10px;
        }
        .product-card .actions input {
            width: 60px;
            padding: 5px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            text-align: center;
        }
        .btn-sm {
            padding: 8px 16px;
            font-size: 14px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: var(--transition);
            font-family: inherit;
        }
        .btn-sm:hover {
            transform: scale(1.05);
        }
        .btn-add-cart {
            background: var(--primary);
            color: white;
        }
        .btn-add-cart:hover {
            background: var(--primary-dark);
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
        footer {
            background: #1a1a2e;
            color: white;
            padding: 40px 0;
            margin-top: 60px;
        }
        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }
        .footer-social {
            display: flex;
            gap: 20px;
        }
        .footer-social a {
            color: white;
            opacity: 0.8;
            transition: var(--transition);
            font-size: 20px;
        }
        .footer-social a:hover {
            opacity: 1;
            transform: scale(1.1);
        }
        @media (max-width: 768px) {
            .restaurant-header {
                grid-template-columns: 1fr;
            }
            .nav-content {
                flex-direction: column;
                text-align: center;
            }
            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
            }
            .user-actions {
                flex-wrap: wrap;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <nav>
        <div class="container nav-content">
            <a href="../../index.php" class="logo">
                <i class="fas fa-utensils"></i> Foods Delivery
            </a>
            <ul class="nav-links">
                <li><a href="../../index.php">Home</a></li>
                <li><a href="restaurants.php" style="color: var(--primary);">Restaurantes</a></li>
                <li><a href="orders.php">Meus Pedidos</a></li>
                <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> Carrinho</a></li>
            </ul>
            <div class="user-actions">
                <span>👋 <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?></span>
                <a href="../../actions/logout.php" class="btn-logout">Sair</a>
            </div>
        </div>
    </nav>

    <main class="container">
        <?php if ($flash): ?>
            <div class="flash-message <?php echo $flash['type']; ?>">
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>

        <a href="restaurants.php" class="btn btn-secondary" style="margin: 20px 0;">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>

        <!-- HEADER DO RESTAURANTE -->
        <div class="restaurant-header">
            <div class="info">
                <h1><?php echo htmlspecialchars($restaurante['nome']); ?></h1>
                <p style="color: var(--text-muted);"><?php echo htmlspecialchars($restaurante['descricao'] ?? ''); ?></p>
                <div class="meta">
                    <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($restaurante['cidade']); ?>, <?php echo htmlspecialchars($restaurante['estado']); ?></span>
                    <span><i class="fas fa-phone"></i> <?php echo htmlspecialchars($restaurante['telefone']); ?></span>
                    <span class="rating"><i class="fas fa-star"></i> <?php echo number_format($restaurante['avaliacao'], 1); ?></span>
                    <?php if ($restaurante['delivery_gratis']): ?>
                        <span style="color: #10b981;"><i class="fas fa-truck"></i> Entrega Grátis</span>
                    <?php else: ?>
                        <span><i class="fas fa-truck"></i> Taxa: R$ <?php echo number_format($restaurante['taxa_entrega'], 2, ',', '.'); ?></span>
                    <?php endif; ?>
                    <span><i class="fas fa-clock"></i> <?php echo $restaurante['tempo_entrega_estimado']; ?> min</span>
                </div>
            </div>
            <div class="restaurant-img" style="background-image: url('<?php echo $restaurante['imagem_url'] ?? 'https://via.placeholder.com/600x200/FF6B35/FFFFFF?text=' . urlencode($restaurante['nome']); ?>');">
            </div>
        </div>

        <!-- CARDÁPIO -->
        <h2 style="margin: 30px 0 20px;"><i class="fas fa-utensils"></i> Cardápio</h2>
        
        <?php if (!empty($produtos)): ?>
        <div class="products-grid">
            <?php foreach ($produtos as $p): ?>
            <div class="product-card">
                <h3><?php echo htmlspecialchars($p['nome']); ?></h3>
                <div class="price">R$ <?php echo number_format($p['preco'], 2, ',', '.'); ?></div>
                <?php if ($p['descricao']): ?>
                    <div class="desc"><?php echo htmlspecialchars($p['descricao']); ?></div>
                <?php endif; ?>
                <form action="../../actions/client/add-to-cart.php" method="POST" class="actions">
                    <input type="hidden" name="produto_id" value="<?php echo $p['id']; ?>">
                    <input type="hidden" name="restaurante_id" value="<?php echo $restaurante['id']; ?>">
                    <input type="number" name="quantidade" value="1" min="1" max="99">
                    <button type="submit" class="btn-sm btn-add-cart">
                        <i class="fas fa-cart-plus"></i> Adicionar
                    </button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-utensils"></i>
            <h3>Cardápio vazio</h3>
            <p>Este restaurante ainda não cadastrou produtos.</p>
        </div>
        <?php endif; ?>
    </main>

    <footer>
        <div class="container footer-content">
            <div>
                <p style="font-weight: 600; margin-bottom: 5px;">
                    <i class="fas fa-utensils"></i> Foods Delivery
                </p>
                <p style="font-size: 14px; opacity: 0.8;">© 2026 Foods Delivery. Trabalho Escolar.</p>
            </div>
            <div class="footer-social">
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-whatsapp"></i></a>
            </div>
        </div>
    </footer>
</body>
</html>