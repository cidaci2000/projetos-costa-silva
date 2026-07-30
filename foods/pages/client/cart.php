<?php
session_start();

// Verifica se está logado como cliente
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'cliente') {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Faça login como cliente para acessar o carrinho!'];
    header('Location: ../login.php');
    exit();
}

// Inicializa carrinho
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

try {
    $conn = new PDO("mysql:host=localhost;dbname=foods_delivery", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// Processa remoção
if (isset($_GET['remove'])) {
    $id = $_GET['remove'];
    if (isset($_SESSION['carrinho'][$id])) {
        unset($_SESSION['carrinho'][$id]);
        $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Item removido do carrinho!'];
    }
    header('Location: cart.php');
    exit();
}

// Processa limpeza
if (isset($_GET['clear'])) {
    $_SESSION['carrinho'] = [];
    $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Carrinho esvaziado!'];
    header('Location: cart.php');
    exit();
}

// Busca produtos do carrinho
$itens = [];
$total = 0;
$totalItens = 0;

if (!empty($_SESSION['carrinho'])) {
    $ids = array_keys($_SESSION['carrinho']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $conn->prepare("SELECT p.*, r.nome as restaurante_nome, r.id as restaurante_id 
                            FROM produtos p 
                            JOIN restaurantes r ON p.restaurante_id = r.id 
                            WHERE p.id IN ($placeholders)");
    $stmt->execute($ids);
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($produtos as $p) {
        $quantidade = $_SESSION['carrinho'][$p['id']];
        $subtotal = $p['preco'] * $quantidade;
        $itens[] = [
            'produto' => $p,
            'quantidade' => $quantidade,
            'subtotal' => $subtotal
        ];
        $total += $subtotal;
        $totalItens += $quantidade;
    }
}

$flash = $_SESSION['flash_message'] ?? null;
unset($_SESSION['flash_message']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho - Foods Delivery</title>
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
        .cart-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin: 30px 0;
        }
        .cart-items {
            background: white;
            border-radius: var(--radius);
            padding: 20px;
            box-shadow: var(--shadow);
        }
        .cart-item {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr auto;
            gap: 15px;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        .cart-item:last-child {
            border-bottom: none;
        }
        .cart-item .item-name {
            font-weight: 600;
        }
        .cart-item .item-restaurante {
            color: var(--text-muted);
            font-size: 13px;
        }
        .cart-item .item-price {
            font-weight: 600;
            color: var(--primary);
        }
        .cart-item .item-quantity {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .cart-item .item-quantity button {
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 2px 10px;
            cursor: pointer;
            font-size: 16px;
            transition: var(--transition);
        }
        .cart-item .item-quantity button:hover {
            background: var(--primary);
            color: white;
        }
        .cart-item .item-remove {
            color: #dc3545;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 18px;
            transition: var(--transition);
        }
        .cart-item .item-remove:hover {
            transform: scale(1.2);
        }
        .cart-summary {
            background: white;
            border-radius: var(--radius);
            padding: 20px;
            box-shadow: var(--shadow);
            align-self: start;
            position: sticky;
            top: 100px;
        }
        .cart-summary h3 {
            margin-bottom: 20px;
        }
        .cart-summary .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .cart-summary .summary-row.total {
            font-weight: 700;
            font-size: 18px;
            border-bottom: none;
            padding-top: 15px;
        }
        .cart-summary .btn-checkout {
            width: 100%;
            margin-top: 15px;
            padding: 12px;
            font-size: 16px;
        }
        .empty-cart {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-muted);
        }
        .empty-cart i {
            font-size: 64px;
            opacity: 0.3;
            margin-bottom: 20px;
        }
        .empty-cart .btn {
            margin-top: 20px;
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
            .cart-container {
                grid-template-columns: 1fr;
            }
            .cart-item {
                grid-template-columns: 1fr;
                gap: 5px;
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
            .cart-summary {
                position: static;
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
                <li><a href="orders.php">Meus Pedidos</a></li>
                <li><a href="cart.php" style="color: var(--primary);"><i class="fas fa-shopping-cart"></i> Carrinho</a></li>
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

        <h2 style="margin: 20px 0;"><i class="fas fa-shopping-cart"></i> Meu Carrinho</h2>

        <?php if (!empty($itens)): ?>
        <div class="cart-container">
            <div class="cart-items">
                <?php foreach ($itens as $item): ?>
                <div class="cart-item">
                    <div>
                        <div class="item-name"><?php echo htmlspecialchars($item['produto']['nome']); ?></div>
                        <div class="item-restaurante">
                            <i class="fas fa-store"></i> <?php echo htmlspecialchars($item['produto']['restaurante_nome']); ?>
                        </div>
                    </div>
                    <div class="item-price">R$ <?php echo number_format($item['produto']['preco'], 2, ',', '.'); ?></div>
                    <div class="item-quantity">
                        <form action="../../actions/client/update-cart.php" method="POST" style="display: flex; gap: 5px; align-items: center;">
                            <input type="hidden" name="produto_id" value="<?php echo $item['produto']['id']; ?>">
                            <input type="hidden" name="action" value="decrease">
                            <button type="submit">-</button>
                        </form>
                        <span><?php echo $item['quantidade']; ?></span>
                        <form action="../../actions/client/update-cart.php" method="POST" style="display: flex; gap: 5px; align-items: center;">
                            <input type="hidden" name="produto_id" value="<?php echo $item['produto']['id']; ?>">
                            <input type="hidden" name="action" value="increase">
                            <button type="submit">+</button>
                        </form>
                    </div>
                    <a href="cart.php?remove=<?php echo $item['produto']['id']; ?>" class="item-remove" onclick="return confirm('Remover este item do carrinho?')">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
                <?php endforeach; ?>
                <div style="text-align: right; padding-top: 15px;">
                    <a href="cart.php?clear=1" class="btn btn-danger" onclick="return confirm('Esvaziar o carrinho?')">
                        <i class="fas fa-trash"></i> Esvaziar Carrinho
                    </a>
                </div>
            </div>

            <div class="cart-summary">
                <h3>Resumo do Pedido</h3>
                <div class="summary-row">
                    <span>Subtotal (<?php echo $totalItens; ?> itens)</span>
                    <span>R$ <?php echo number_format($total, 2, ',', '.'); ?></span>
                </div>
                <div class="summary-row">
                    <span>Taxa de Entrega</span>
                    <span>R$ 0,00</span>
                </div>
                <div class="summary-row total">
                    <span>Total</span>
                    <span>R$ <?php echo number_format($total, 2, ',', '.'); ?></span>
                </div>
                <a href="checkout.php" class="btn btn-primary btn-checkout">
                    <i class="fas fa-check"></i> Finalizar Pedido
                </a>
            </div>
        </div>
        <?php else: ?>
        <div class="empty-cart">
            <i class="fas fa-shopping-cart"></i>
            <h3>Seu carrinho está vazio</h3>
            <p>Explore nossos restaurantes e adicione seus pratos favoritos!</p>
            <a href="restaurants.php" class="btn btn-primary">
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