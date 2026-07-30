<?php
session_start();

// 🔥 VERIFICA SE ESTÁ LOGADO
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'cliente') {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Faça login como cliente!'];
    header('Location: ../login.php');
    exit();
}

try {
    $conn = new PDO("mysql:host=localhost;dbname=foods_delivery", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// Busca restaurantes ativos
$stmt = $conn->query("
    SELECT r.*, 
           (SELECT COUNT(*) FROM produtos WHERE restaurante_id = r.id AND disponivel = 1) as total_produtos
    FROM restaurantes r 
    WHERE r.ativo = 1
    ORDER BY r.nome
");
$restaurantes = $stmt->fetchAll();

$flash = $_SESSION['flash_message'] ?? null;
unset($_SESSION['flash_message']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurantes - Foods Delivery</title>
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
        .logo i {
            margin-right: 10px;
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
        .restaurants-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
            margin: 30px 0;
        }
        .restaurant-card {
            background: white;
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
            text-decoration: none;
            color: inherit;
        }
        .restaurant-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-hover);
        }
        .card-img {
            height: 180px;
            background-size: cover;
            background-position: center;
            position: relative;
        }
        .card-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .card-info {
            padding: 20px;
        }
        .card-info h3 {
            margin: 0 0 5px 0;
        }
        .card-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--text-muted);
            font-size: 14px;
        }
        .card-meta .rating {
            color: #f59e0b;
        }
        .delivery-info {
            display: flex;
            gap: 15px;
            margin-top: 10px;
            font-size: 13px;
            color: var(--text-muted);
        }
        .delivery-info i {
            margin-right: 5px;
        }
        .free-delivery {
            color: #10b981;
            font-weight: 600;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-muted);
        }
        .empty-state i {
            font-size: 48px;
            opacity: 0.5;
            margin-bottom: 20px;
        }
        .welcome-banner {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 30px;
            border-radius: var(--radius);
            margin: 20px 0;
            text-align: center;
        }
        .welcome-banner h1 {
            font-size: 28px;
            margin-bottom: 5px;
        }
        .welcome-banner p {
            opacity: 0.9;
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
            .welcome-banner h1 {
                font-size: 22px;
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

        <!-- BANNER DE BOAS-VINDAS -->
        <div class="welcome-banner">
            <h1>🍽️ Olá, <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>!</h1>
            <p>Escolha seu restaurante favorito e faça seu pedido!</p>
        </div>

        <h2 style="margin: 20px 0;"><i class="fas fa-store"></i> Restaurantes Disponíveis</h2>

        <?php if (!empty($restaurantes)): ?>
        <div class="restaurants-grid">
            <?php foreach ($restaurantes as $r): ?>
            <a href="restaurant-detail.php?id=<?php echo $r['id']; ?>" class="restaurant-card">
                <div class="card-img" style="background-image: url('<?php echo $r['imagem_url'] ?? 'https://via.placeholder.com/400x200/FF6B35/FFFFFF?text=Foods'; ?>');">
                    <span class="card-badge">⭐ <?php echo number_format($r['avaliacao'], 1); ?></span>
                </div>
                <div class="card-info">
                    <h3><?php echo htmlspecialchars($r['nome']); ?></h3>
                    <div class="card-meta">
                        <span><?php echo htmlspecialchars($r['categoria'] ?? 'Geral'); ?></span>
                        <span class="rating">⭐ <?php echo number_format($r['avaliacao'], 1); ?></span>
                    </div>
                    <div class="delivery-info">
                        <?php if ($r['delivery_gratis']): ?>
                            <span class="free-delivery"><i class="fas fa-truck"></i> Grátis</span>
                        <?php else: ?>
                            <span><i class="fas fa-truck"></i> R$ <?php echo number_format($r['taxa_entrega'], 2, ',', '.'); ?></span>
                        <?php endif; ?>
                        <span><i class="fas fa-clock"></i> <?php echo $r['tempo_entrega_estimado']; ?> min</span>
                        <span><i class="fas fa-utensils"></i> <?php echo $r['total_produtos']; ?> itens</span>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-store"></i>
            <h3>Nenhum restaurante disponível</h3>
            <p>Volte em breve, novos restaurantes estão chegando!</p>
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