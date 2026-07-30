<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'cliente') {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Faça login para ver seus pedidos!'];
    header('Location: ../login.php');
    exit();
}

try {
    $conn = new PDO("mysql:host=localhost;dbname=foods_delivery", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// Busca pedidos do cliente
$stmt = $conn->prepare("
    SELECT p.*, r.nome as restaurante, r.imagem_url,
           (SELECT COUNT(*) FROM itens_pedido WHERE pedido_id = p.id) as total_itens
    FROM pedidos p 
    JOIN restaurantes r ON p.restaurante_id = r.id 
    WHERE p.cliente_id = ?
    ORDER BY p.data_pedido DESC
");
$stmt->execute([$_SESSION['usuario_id']]);
$pedidos = $stmt->fetchAll();

$flash = $_SESSION['flash_message'] ?? null;
unset($_SESSION['flash_message']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Pedidos - Foods Delivery</title>
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
        .orders-grid {
            display: grid;
            gap: 20px;
            margin: 30px 0;
        }
        .order-card {
            background: white;
            border-radius: var(--radius);
            padding: 20px;
            box-shadow: var(--shadow);
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 20px;
            align-items: center;
            transition: var(--transition);
        }
        .order-card:hover {
            transform: translateX(5px);
            box-shadow: var(--shadow-hover);
        }
        .order-card .order-img {
            width: 80px;
            height: 80px;
            border-radius: 12px;
            background-size: cover;
            background-position: center;
        }
        .order-card .order-info h3 {
            margin: 0 0 5px 0;
        }
        .order-card .order-info .order-meta {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            color: var(--text-muted);
            font-size: 14px;
        }
        .order-card .order-info .order-meta i {
            margin-right: 5px;
        }
        .order-card .order-status {
            text-align: right;
        }
        .order-card .order-status .total {
            font-size: 20px;
            font-weight: 700;
            color: var(--primary);
        }
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        .status-pendente { background: #fff3cd; color: #856404; }
        .status-confirmado { background: #cce5ff; color: #004085; }
        .status-preparando { background: #ffe5d0; color: #e65c00; }
        .status-saiu_entrega { background: #d4edda; color: #155724; }
        .status-entregue { background: #28a745; color: white; }
        .status-cancelado { background: #dc3545; color: white; }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-muted);
        }
        .empty-state i {
            font-size: 64px;
            opacity: 0.3;
            margin-bottom: 20px;
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
        .btn-sm {
            padding: 5px 12px;
            font-size: 12px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            transition: var(--transition);
            font-family: inherit;
        }
        .btn-sm:hover {
            transform: scale(1.05);
        }
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        @media (max-width: 768px) {
            .order-card {
                grid-template-columns: 1fr;
                text-align: center;
            }
            .order-card .order-status {
                text-align: center;
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
                <li><a href="restaurants.php">Restaurantes</a></li>
                <li><a href="orders.php" style="color: var(--primary);">Meus Pedidos</a></li>
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

        <h2 style="margin: 20px 0;"><i class="fas fa-history"></i> Meus Pedidos</h2>

        <?php if (!empty($pedidos)): ?>
        <div class="orders-grid">
            <?php foreach ($pedidos as $p): ?>
            <div class="order-card">
                <div class="order-img" style="background-image: url('<?php echo $p['imagem_url'] ?? 'https://via.placeholder.com/80/FF6B35/FFFFFF?text=Foods'; ?>');">
                </div>
                <div class="order-info">
                    <h3>Pedido #<?php echo $p['id']; ?></h3>
                    <div class="order-meta">
                        <span><i class="fas fa-store"></i> <?php echo htmlspecialchars($p['restaurante']); ?></span>
                        <span><i class="fas fa-utensils"></i> <?php echo $p['total_itens']; ?> itens</span>
                        <span><i class="fas fa-calendar"></i> <?php echo date('d/m/Y H:i', strtotime($p['data_pedido'])); ?></span>
                        <span><i class="fas fa-credit-card"></i> <?php echo ucfirst($p['forma_pagamento']); ?></span>
                    </div>
                </div>
                <div class="order-status">
                    <div class="total">R$ <?php echo number_format($p['total'], 2, ',', '.'); ?></div>
                    <span class="status-badge status-<?php echo $p['status']; ?>">
                        <?php echo ucfirst(str_replace('_', ' ', $p['status'])); ?>
                    </span>
                    <?php if ($p['status'] == 'pendente'): ?>
                    <div style="margin-top: 5px;">
                        <a href="../../actions/client/cancel-order.php?id=<?php echo $p['id']; ?>" 
                           class="btn-sm btn-danger" 
                           onclick="return confirm('Cancelar este pedido?')">
                            Cancelar
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-shopping-bag"></i>
            <h3>Você ainda não fez nenhum pedido</h3>
            <p>Explore nossos restaurantes e faça seu primeiro pedido!</p>
            <a href="restaurants.php" class="btn btn-primary" style="margin-top: 20px;">
                <i class="fas fa-utensils"></i> Ver Restaurantes
            </a>
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