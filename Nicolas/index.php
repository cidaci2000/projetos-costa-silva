<?php
// ========================================
// CONFIGURAÇÃO INICIAL
// ========================================
// Inicia sessão antes de qualquer saída
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Carrega os modelos
require_once __DIR__ . '/models/Product.php';

// ========================================
// LÓGICA DA PÁGINA
// ========================================
$productModel = new Product();
$produtos = $productModel->findAll(true);
$destaques = $productModel->findDestaques();

// Verifica se o usuário está logado
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $_SESSION['user_name'] ?? '';
$userType = $_SESSION['user_type'] ?? '';

// ========================================
// LÓGICA DO CARRINHO
// ========================================

// Inicializa o carrinho se não existir
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

// AJAX - Retorna contagem do carrinho
if (isset($_GET['ajax']) && $_GET['ajax'] === 'cart_count') {
    header('Content-Type: application/json');
    $total = 0;
    foreach ($_SESSION['carrinho'] as $item) {
        $total += $item['quantidade'];
    }
    echo json_encode(['count' => $total]);
    exit;
}

// AJAX - Retorna conteúdo do carrinho
if (isset($_GET['ajax']) && $_GET['ajax'] === 'cart_content') {
    header('Content-Type: text/html');
    $carrinho = $_SESSION['carrinho'] ?? [];
    $totalItens = 0;
    $totalValor = 0;
    
    foreach ($carrinho as $item) {
        $totalItens += $item['quantidade'];
        $totalValor += $item['preco'] * $item['quantidade'];
    }
    ?>
    <?php if (empty($carrinho)): ?>
        <div class="cart-empty">
            <i class="fas fa-shopping-cart"></i>
            <h3>Seu carrinho está vazio</h3>
            <p>Adicione produtos clicando em "Comprar"</p>
        </div>
    <?php else: ?>
        <?php foreach ($carrinho as $item): ?>
            <div class="cart-item" data-id="<?= $item['id'] ?>">
                <div class="cart-item-icon">
                    <i class="fas <?= htmlspecialchars($item['icone'] ?? 'fa-box') ?>"></i>
                </div>
                <div class="cart-item-info">
                    <h4><?= htmlspecialchars($item['nome']) ?></h4>
                    <span class="item-price">
                        R$ <?= number_format($item['preco'], 2, ',', '.') ?> cada
                    </span>
                </div>
                <div class="cart-item-quantity">
                    <button onclick="updateQuantity(<?= $item['id'] ?>, -1)">-</button>
                    <input type="number" value="<?= $item['quantidade'] ?>" 
                           min="1" max="99" 
                           onchange="updateQuantity(<?= $item['id'] ?>, 0, this.value)">
                    <button onclick="updateQuantity(<?= $item['id'] ?>, 1)">+</button>
                </div>
                <button class="cart-item-remove" onclick="removeFromCart(<?= $item['id'] ?>)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        <?php endforeach; ?>

        <div class="cart-total">
            <div class="cart-total-row">
                <span>Total:</span>
                <span class="total-value">R$ <?= number_format($totalValor, 2, ',', '.') ?></span>
            </div>
            <div class="cart-total-row" style="font-size: var(--font-size-sm); color: var(--color-text-light);">
                <span><?= $totalItens ?> item(s)</span>
            </div>
        </div>

        <div class="cart-actions">
            <button class="btn-outline" onclick="clearCart()">
                <i class="fas fa-trash"></i> Limpar
            </button>
            <button class="btn-success" onclick="checkout()">
                <i class="fas fa-check"></i> Finalizar Compra
            </button>
        </div>
    <?php endif; ?>
    <?php
    exit;
}

// Adicionar ao carrinho via GET
if (isset($_GET['action']) && $_GET['action'] === 'add' && isset($_GET['id'])) {
    $productId = (int)$_GET['id'];
    $product = $productModel->findById($productId);
    
    if ($product) {
        // CORREÇÃO: Usa o campo correto do produto
        $preco = isset($product['preco_promocional']) && $product['preco_promocional'] > 0 
                 ? $product['preco_promocional'] 
                 : $product['preco'];
        
        if (isset($_SESSION['carrinho'][$productId])) {
            $_SESSION['carrinho'][$productId]['quantidade']++;
        } else {
            $_SESSION['carrinho'][$productId] = [
                'id' => $product['id'],
                'nome' => $product['nome'],
                'preco' => $preco,
                'quantidade' => 1,
                'icone' => $product['icone'] ?? 'fa-box'
            ];
        }
    }
    
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

// Remover item do carrinho
if (isset($_GET['action']) && $_GET['action'] === 'remove' && isset($_GET['id'])) {
    $productId = (int)$_GET['id'];
    if (isset($_SESSION['carrinho'][$productId])) {
        unset($_SESSION['carrinho'][$productId]);
    }
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

// Atualizar quantidade
if (isset($_POST['action']) && $_POST['action'] === 'update' && isset($_POST['id']) && isset($_POST['quantidade'])) {
    $productId = (int)$_POST['id'];
    $quantidade = max(1, (int)$_POST['quantidade']);
    
    if (isset($_SESSION['carrinho'][$productId])) {
        $_SESSION['carrinho'][$productId]['quantidade'] = $quantidade;
    }
    
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

// Limpar carrinho
if (isset($_GET['action']) && $_GET['action'] === 'clear') {
    $_SESSION['carrinho'] = [];
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

// Calcula totais do carrinho
$carrinho = $_SESSION['carrinho'] ?? [];
$totalItens = 0;
$totalValor = 0;

foreach ($carrinho as $item) {
    $totalItens += $item['quantidade'];
    $totalValor += $item['preco'] * $item['quantidade'];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nicolas Esportes · Loja</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* ========================================
           VARIÁVEIS E RESET
        ======================================== */
        :root {
            --color-primary: #0b1a2e;
            --color-primary-light: #1e3a5f;
            --color-secondary: #facc15;
            --color-secondary-light: #fde047;
            --color-bg: #f5f7fc;
            --color-bg-card: #ffffff;
            --color-text: #1e293b;
            --color-text-light: #64748b;
            --color-text-muted: #94a3b8;
            --color-border: #e9edf4;
            --color-border-light: #e2e8f0;
            --color-white: #ffffff;
            --color-success: #22c55e;
            --color-danger: #ef4444;
            --shadow-sm: 0 4px 12px rgba(0, 0, 0, 0.04);
            --shadow-md: 0 8px 24px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 20px 60px rgba(0, 0, 0, 0.06);
            --shadow-xl: 0 20px 36px rgba(0, 0, 0, 0.08);
            --shadow-hero: 0 8px 18px rgba(11, 26, 46, 0.2);
            --shadow-header: 0 8px 20px rgba(0, 0, 0, 0.2);
            --shadow-cart: 0 20px 60px rgba(0, 0, 0, 0.15);
            --spacing-xs: 4px;
            --spacing-sm: 8px;
            --spacing-md: 16px;
            --spacing-lg: 24px;
            --spacing-xl: 32px;
            --spacing-2xl: 48px;
            --spacing-3xl: 56px;
            --radius-sm: 12px;
            --radius-md: 28px;
            --radius-lg: 32px;
            --radius-xl: 40px;
            --radius-full: 60px;
            --font-family: 'Segoe UI', Roboto, sans-serif;
            --font-size-sm: 0.9rem;
            --font-size-base: 1rem;
            --font-size-lg: 1.2rem;
            --font-size-xl: 1.4rem;
            --font-size-2xl: 1.8rem;
            --font-size-3xl: 2.2rem;
            --font-size-4xl: 2.8rem;
            --transition-fast: 0.2s;
            --transition-normal: 0.25s;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-family);
            background: var(--color-bg);
            color: var(--color-text);
        }

        /* ========================================
           UTILITÁRIOS
        ======================================== */
        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 var(--spacing-lg);
        }

        /* ========================================
           HEADER
        ======================================== */
        .header {
            background: linear-gradient(145deg, var(--color-primary), var(--color-primary-light));
            color: var(--color-white);
            padding: 20px 0;
            box-shadow: var(--shadow-header);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: var(--spacing-md);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
        }

        .logo i {
            font-size: 2.4rem;
            color: var(--color-secondary);
        }

        .logo h1 {
            font-size: 2rem;
            font-weight: 700;
        }

        .logo span {
            color: var(--color-secondary);
            font-weight: 300;
        }

        .nav-links {
            display: flex;
            gap: 28px;
            font-weight: 500;
            align-items: center;
            flex-wrap: wrap;
        }

        .nav-links a {
            color: #e2e8f0;
            text-decoration: none;
            transition: var(--transition-fast);
            padding: 6px 0;
            border-bottom: 2px solid transparent;
        }

        .nav-links a:hover {
            color: var(--color-secondary);
            border-bottom-color: var(--color-secondary);
        }

        .nav-links .user-info {
            color: var(--color-secondary);
            font-weight: 600;
        }

        .btn-cart-nav {
            background: var(--color-secondary);
            color: var(--color-primary) !important;
            padding: var(--spacing-sm) 18px;
            border-radius: var(--radius-xl);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            position: relative;
            border-bottom: none !important;
            cursor: pointer;
        }

        .btn-cart-nav:hover {
            background: var(--color-secondary-light);
            transform: scale(1.02);
        }

        .cart-badge {
            background: var(--color-danger);
            color: white;
            border-radius: 50%;
            padding: 2px 8px;
            font-size: 0.7rem;
            font-weight: 700;
            position: absolute;
            top: -8px;
            right: -8px;
            min-width: 20px;
            text-align: center;
        }

        .btn-logout {
            color: #e2e8f0 !important;
            border-bottom: 2px solid transparent !important;
        }

        .btn-logout:hover {
            color: var(--color-danger) !important;
            border-bottom-color: var(--color-danger) !important;
        }

        /* ========================================
           BOTÕES
        ======================================== */
        .btn-primary {
            background: var(--color-primary);
            color: var(--color-white);
            border: none;
            padding: 14px 36px;
            border-radius: var(--radius-full);
            font-weight: 600;
            font-size: var(--font-size-lg);
            display: inline-flex;
            align-items: center;
            gap: var(--spacing-sm);
            cursor: pointer;
            transition: var(--transition-normal);
            box-shadow: var(--shadow-hero);
            text-decoration: none;
        }

        .btn-primary:hover {
            background: var(--color-primary-light);
            transform: translateY(-3px);
        }

        .btn-buy {
            background: var(--color-primary);
            border: none;
            color: var(--color-white);
            padding: var(--spacing-sm) 10px;
            border-radius: var(--radius-full);
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--spacing-sm);
            transition: var(--transition-fast);
            cursor: pointer;
            width: 100%;
            margin-top: auto;
        }

        .btn-buy:hover {
            background: var(--color-secondary);
            color: var(--color-primary);
        }

        .btn-success {
            background: var(--color-success);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: var(--radius-full);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition-fast);
            display: inline-flex;
            align-items: center;
            gap: var(--spacing-sm);
        }

        .btn-success:hover {
            opacity: 0.9;
            transform: scale(1.02);
        }

        .btn-danger {
            background: var(--color-danger);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: var(--radius-full);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition-fast);
            font-size: var(--font-size-sm);
        }

        .btn-danger:hover {
            opacity: 0.9;
        }

        .btn-outline {
            background: transparent;
            color: var(--color-primary);
            border: 2px solid var(--color-border);
            padding: 10px 20px;
            border-radius: var(--radius-full);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition-fast);
        }

        .btn-outline:hover {
            border-color: var(--color-primary);
            background: var(--color-primary);
            color: white;
        }

        /* ========================================
           HERO
        ======================================== */
        .hero {
            background: linear-gradient(135deg, #d9e2f0 0%, #eef2f9 100%);
            padding: var(--spacing-2xl) 0 var(--spacing-xl);
            border-radius: var(--radius-lg);
            margin: var(--spacing-xl) 0 var(--spacing-2xl);
        }

        .hero-grid {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 30px;
            padding: 0 30px;
        }

        .hero-text {
            flex: 1 1 300px;
        }

        .hero-text h2 {
            font-size: var(--font-size-4xl);
            font-weight: 800;
            line-height: 1.2;
            color: var(--color-primary);
        }

        .hero-text h2 i {
            color: var(--color-secondary);
            margin-right: var(--spacing-sm);
        }

        .hero-text p {
            font-size: var(--font-size-lg);
            color: #2d3a4f;
            margin: var(--spacing-md) 0 var(--spacing-lg);
            max-width: 500px;
        }

        .hero-image {
            flex: 0 0 200px;
            font-size: 6rem;
            color: var(--color-primary-light);
            background: rgba(255, 255, 255, 0.3);
            padding: 20px;
            border-radius: var(--radius-full);
            text-align: center;
        }

        /* ========================================
           SEÇÕES
        ======================================== */
        .section-title {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            flex-wrap: wrap;
            margin: var(--spacing-3xl) 0 var(--spacing-lg);
        }

        .section-title h3 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--color-primary);
        }

        .section-title h3 i {
            color: var(--color-secondary);
            margin-right: 10px;
        }

        /* ========================================
           PRODUTOS
        ======================================== */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: var(--spacing-xl) 20px;
        }

        .product-card {
            background: var(--color-bg-card);
            border-radius: var(--radius-md);
            padding: 20px var(--spacing-md) var(--spacing-lg);
            box-shadow: var(--shadow-sm);
            transition: var(--transition-normal);
            display: flex;
            flex-direction: column;
            border: 1px solid var(--color-border);
            text-align: center;
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-xl);
            border-color: var(--color-secondary);
        }

        .product-icon {
            font-size: 4rem;
            color: var(--color-primary);
            background: #f0f4fe;
            width: 100px;
            height: 100px;
            margin: 0 auto 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius-full);
            transition: var(--transition-fast);
        }

        .product-card:hover .product-icon {
            background: #facc1520;
            color: var(--color-secondary);
        }

        .product-card h4 {
            font-size: var(--font-size-lg);
            font-weight: 600;
            margin-bottom: 6px;
        }

        .product-card .price {
            font-size: var(--font-size-xl);
            font-weight: 700;
            color: var(--color-primary);
            margin: var(--spacing-sm) 0 var(--spacing-md);
        }

        .product-card .price small {
            font-weight: 400;
            font-size: var(--font-size-sm);
            color: var(--color-text-light);
            margin-left: 6px;
            text-decoration: line-through;
        }

        .badge {
            background: var(--color-secondary);
            color: var(--color-primary);
            padding: var(--spacing-xs) var(--spacing-sm);
            border-radius: var(--radius-xl);
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            display: inline-block;
            margin-bottom: var(--spacing-sm);
            align-self: center;
        }

        .empty-message {
            grid-column: 1 / -1;
            text-align: center;
            color: var(--color-text-muted);
            padding: 40px;
        }

        /* ========================================
           CARRINHO - MODAL
        ======================================== */
        .cart-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .cart-overlay.active {
            display: flex;
        }

        .cart-modal {
            background: white;
            border-radius: var(--radius-lg);
            max-width: 600px;
            width: 95%;
            max-height: 90vh;
            overflow-y: auto;
            padding: var(--spacing-xl);
            box-shadow: var(--shadow-cart);
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: var(--spacing-md);
            border-bottom: 2px solid var(--color-border);
            margin-bottom: var(--spacing-lg);
        }

        .cart-header h2 {
            font-size: var(--font-size-2xl);
            color: var(--color-primary);
        }

        .cart-header h2 i {
            color: var(--color-secondary);
            margin-right: var(--spacing-sm);
        }

        .cart-close {
            background: none;
            border: none;
            font-size: 1.8rem;
            color: var(--color-text-light);
            cursor: pointer;
            transition: var(--transition-fast);
        }

        .cart-close:hover {
            color: var(--color-danger);
            transform: rotate(90deg);
        }

        .cart-item {
            display: flex;
            align-items: center;
            gap: var(--spacing-md);
            padding: var(--spacing-md) 0;
            border-bottom: 1px solid var(--color-border);
        }

        .cart-item-icon {
            font-size: 2rem;
            color: var(--color-primary);
            background: #f0f4fe;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius-sm);
            flex-shrink: 0;
        }

        .cart-item-info {
            flex: 1;
        }

        .cart-item-info h4 {
            font-size: var(--font-size-base);
            font-weight: 600;
            color: var(--color-primary);
        }

        .cart-item-info .item-price {
            color: var(--color-text-light);
            font-size: var(--font-size-sm);
        }

        .cart-item-quantity {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
        }

        .cart-item-quantity button {
            background: var(--color-border);
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition-fast);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cart-item-quantity button:hover {
            background: var(--color-secondary);
        }

        .cart-item-quantity input {
            width: 50px;
            text-align: center;
            padding: var(--spacing-xs);
            border: 2px solid var(--color-border);
            border-radius: var(--radius-sm);
            font-weight: 600;
        }

        .cart-item-remove {
            background: none;
            border: none;
            color: var(--color-text-light);
            cursor: pointer;
            font-size: 1.2rem;
            transition: var(--transition-fast);
        }

        .cart-item-remove:hover {
            color: var(--color-danger);
        }

        .cart-total {
            padding-top: var(--spacing-lg);
            border-top: 2px solid var(--color-border);
            margin-top: var(--spacing-lg);
        }

        .cart-total-row {
            display: flex;
            justify-content: space-between;
            font-size: var(--font-size-lg);
            font-weight: 600;
            color: var(--color-primary);
        }

        .cart-total-row .total-value {
            font-size: var(--font-size-xl);
            color: var(--color-secondary);
        }

        .cart-actions {
            display: flex;
            gap: var(--spacing-md);
            margin-top: var(--spacing-lg);
            flex-wrap: wrap;
        }

        .cart-actions .btn-success {
            flex: 1;
            justify-content: center;
        }

        .cart-empty {
            text-align: center;
            padding: var(--spacing-2xl) 0;
            color: var(--color-text-muted);
        }

        .cart-empty i {
            font-size: 4rem;
            margin-bottom: var(--spacing-md);
        }

        .cart-empty h3 {
            color: var(--color-text);
            margin-bottom: var(--spacing-sm);
        }

        /* ========================================
           TOAST NOTIFICATION
        ======================================== */
        .toast {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--color-primary);
            color: white;
            padding: var(--spacing-md) var(--spacing-xl);
            border-radius: var(--radius-full);
            box-shadow: var(--shadow-lg);
            z-index: 2000;
            display: none;
            align-items: center;
            gap: var(--spacing-sm);
            animation: slideUp 0.3s ease;
        }

        .toast.show {
            display: flex;
        }

        .toast i {
            color: var(--color-secondary);
        }

        @keyframes slideUp {
            from {
                transform: translateX(-50%) translateY(20px);
                opacity: 0;
            }
            to {
                transform: translateX(-50%) translateY(0);
                opacity: 1;
            }
        }

        /* ========================================
           FOOTER
        ======================================== */
        footer {
            background: var(--color-primary);
            color: #b9c8dd;
            padding: var(--spacing-lg) 0;
            border-radius: var(--radius-xl) var(--radius-xl) 0 0;
            margin-top: 60px;
            text-align: center;
        }

        footer span {
            color: var(--color-secondary);
        }

        footer .footer-sub {
            font-size: var(--font-size-sm);
            opacity: 0.7;
            margin-top: 6px;
        }

        /* ========================================
           ADMIN LINK
        ======================================== */
        .admin-link {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: var(--color-primary);
            color: var(--color-white);
            padding: var(--spacing-sm) 20px;
            border-radius: var(--radius-xl);
            text-decoration: none;
            font-weight: 600;
            font-size: var(--font-size-sm);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            z-index: 999;
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
        }

        .admin-link:hover {
            background: var(--color-primary-light);
        }

        /* ========================================
           RESPONSIVIDADE
        ======================================== */
        @media (max-width: 700px) {
            .header-content {
                flex-direction: column;
                align-items: stretch;
                gap: var(--spacing-sm);
            }
            
            .nav-links {
                justify-content: center;
            }
            
            .hero-text h2 {
                font-size: var(--font-size-3xl);
            }
            
            .hero-image {
                flex: 1 1 100%;
                font-size: 4rem;
            }

            .cart-modal {
                padding: var(--spacing-lg);
                margin: var(--spacing-md);
            }

            .cart-item {
                flex-wrap: wrap;
            }

            .cart-actions {
                flex-direction: column;
            }

            .cart-actions .btn-success {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- ========================================
    TOAST NOTIFICATION
    ======================================== -->
    <div id="toast" class="toast">
        <i class="fas fa-check-circle"></i>
        <span id="toastMessage">Produto adicionado ao carrinho!</span>
    </div>

    <!-- ========================================
    HEADER
    ======================================== -->
    <header class="header">
        <div class="container header-content">
            <div class="logo">
                <i class="fas fa-bolt"></i>
                <h1>Nicolas <span>Esportes</span></h1>
            </div>
            <nav class="nav-links">
                <a href="#produtos"><i class="fas fa-store"></i> Produtos</a>
                <a href="#"><i class="fas fa-tag"></i> Ofertas</a>
                <a href="#"><i class="fas fa-info-circle"></i> Sobre</a>
                
                <?php if ($isLoggedIn): ?>
                    <span class="user-info">
                        <i class="fas fa-user"></i> <?= htmlspecialchars($userName) ?>
                    </span>
                    <?php if ($userType === 'admin'): ?>
                        <a href="/2026/PHP/Nicolas/admin/dashboard.php">
                            <i class="fas fa-tachometer-alt"></i> Painel
                        </a>
                    <?php endif; ?>
                    <a href="/2026/PHP/Nicolas/logout.php" class="btn-logout">
                        <i class="fas fa-sign-out-alt"></i> Sair
                    </a>
                <?php else: ?>
                    <a href="/2026/PHP/Nicolas/cliente/login.php">
                        <i class="fas fa-user"></i> Entrar
                    </a>
                    <a href="/2026/PHP/Nicolas/cliente/cadastro.php" style="background: var(--color-secondary); color: var(--color-primary) !important; padding: var(--spacing-sm) 18px; border-radius: var(--radius-xl); font-weight: 600; border-bottom: none !important;">
                        <i class="fas fa-user-plus"></i> Cadastrar
                    </a>
                <?php endif; ?>
                
                <a class="btn-cart-nav" onclick="toggleCart(event)">
                    <i class="fas fa-shopping-cart"></i> Carrinho
                    <?php if ($totalItens > 0): ?>
                        <span class="cart-badge" id="cartBadge"><?= $totalItens ?></span>
                    <?php endif; ?>
                </a>
            </nav>
        </div>
    </header>

    <!-- ========================================
    MAIN CONTENT
    ======================================== -->
    <main class="container">
        <!-- HERO SECTION -->
        <section class="hero">
            <div class="hero-grid">
                <div class="hero-text">
                    <h2><i class="fas fa-bolt"></i> Nicolas Esportes</h2>
                    <p>Equipamentos de alta performance para todas as modalidades. Qualidade e estilo que fazem a diferença.</p>
                    <a href="#produtos" class="btn-primary">
                        <i class="fas fa-store"></i> Explorar loja
                    </a>
                </div>
                <div class="hero-image">
                    <i class="fas fa-medal"></i>
                </div>
            </div>
        </section>

        <!-- PRODUTOS EM DESTAQUE -->
        <div id="produtos" class="section-title">
            <h3><i class="fas fa-star"></i> Produtos em destaque</h3>
        </div>

        <div class="product-grid">
            <?php if (empty($destaques)): ?>
                <p class="empty-message">
                    <i class="fas fa-box-open"></i> Nenhum produto em destaque no momento.
                </p>
            <?php else: ?>
                <?php foreach ($destaques as $produto): ?>
                    <div class="product-card" data-id="<?= $produto['id'] ?>">
                        <div class="product-icon">
                            <i class="fas <?= htmlspecialchars($produto['icone'] ?? 'fa-box') ?>"></i>
                        </div>
                        <?php if (!empty($produto['badge'])): ?>
                            <span class="badge"><?= htmlspecialchars($produto['badge']) ?></span>
                        <?php endif; ?>
                        <h4><?= htmlspecialchars($produto['nome']) ?></h4>
                        <div class="price">
                            <?php 
                            // CORREÇÃO: Usa os campos corretos
                            $precoAtual = isset($produto['preco_promocional']) && $produto['preco_promocional'] > 0 
                                        ? $produto['preco_promocional'] 
                                        : $produto['preco'];
                            ?>
                            R$ <?= number_format($precoAtual, 2, ',', '.') ?>
                            <?php if (!empty($produto['preco_promocional']) && $produto['preco_promocional'] > 0): ?>
                                <small>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></small>
                            <?php endif; ?>
                        </div>
                        <button class="btn-buy" onclick="addToCart(<?= $produto['id'] ?>, '<?= addslashes($produto['nome']) ?>')">
                            <i class="fas fa-cart-plus"></i> Comprar
                        </button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- TODOS OS PRODUTOS -->
        <div class="section-title" style="margin-top: 40px;">
            <h3><i class="fas fa-list"></i> Todos os Produtos</h3>
        </div>

        <div class="product-grid">
            <?php if (empty($produtos)): ?>
                <p class="empty-message">
                    <i class="fas fa-box-open"></i> Nenhum produto disponível no momento.
                </p>
            <?php else: ?>
                <?php foreach ($produtos as $produto): ?>
                    <div class="product-card" data-id="<?= $produto['id'] ?>">
                        <div class="product-icon">
                            <i class="fas <?= htmlspecialchars($produto['icone'] ?? 'fa-box') ?>"></i>
                        </div>
                        <?php if (!empty($produto['badge'])): ?>
                            <span class="badge"><?= htmlspecialchars($produto['badge']) ?></span>
                        <?php endif; ?>
                        <h4><?= htmlspecialchars($produto['nome']) ?></h4>
                        <div class="price">
                            <?php 
                            // CORREÇÃO: Usa os campos corretos
                            $precoAtual = isset($produto['preco_promocional']) && $produto['preco_promocional'] > 0 
                                        ? $produto['preco_promocional'] 
                                        : $produto['preco'];
                            ?>
                            R$ <?= number_format($precoAtual, 2, ',', '.') ?>
                            <?php if (!empty($produto['preco_promocional']) && $produto['preco_promocional'] > 0): ?>
                                <small>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></small>
                            <?php endif; ?>
                        </div>
                        <button class="btn-buy" onclick="addToCart(<?= $produto['id'] ?>, '<?= addslashes($produto['nome']) ?>')">
                            <i class="fas fa-cart-plus"></i> Comprar
                        </button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <!-- ========================================
    CARRINHO - MODAL
    ======================================== -->
    <div id="cartOverlay" class="cart-overlay" onclick="closeCart(event)">
        <div class="cart-modal" onclick="event.stopPropagation()">
            <div class="cart-header">
                <h2><i class="fas fa-shopping-cart"></i> Meu Carrinho</h2>
                <button class="cart-close" onclick="closeCart()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div id="cartContent">
                <?php if (empty($carrinho)): ?>
                    <div class="cart-empty">
                        <i class="fas fa-shopping-cart"></i>
                        <h3>Seu carrinho está vazio</h3>
                        <p>Adicione produtos clicando em "Comprar"</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($carrinho as $item): ?>
                        <div class="cart-item" data-id="<?= $item['id'] ?>">
                            <div class="cart-item-icon">
                                <i class="fas <?= htmlspecialchars($item['icone'] ?? 'fa-box') ?>"></i>
                            </div>
                            <div class="cart-item-info">
                                <h4><?= htmlspecialchars($item['nome']) ?></h4>
                                <span class="item-price">
                                    R$ <?= number_format($item['preco'], 2, ',', '.') ?> cada
                                </span>
                            </div>
                            <div class="cart-item-quantity">
                                <button onclick="updateQuantity(<?= $item['id'] ?>, -1)">-</button>
                                <input type="number" value="<?= $item['quantidade'] ?>" 
                                       min="1" max="99" 
                                       onchange="updateQuantity(<?= $item['id'] ?>, 0, this.value)">
                                <button onclick="updateQuantity(<?= $item['id'] ?>, 1)">+</button>
                            </div>
                            <button class="cart-item-remove" onclick="removeFromCart(<?= $item['id'] ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    <?php endforeach; ?>

                    <div class="cart-total">
                        <div class="cart-total-row">
                            <span>Total:</span>
                            <span class="total-value">R$ <?= number_format($totalValor, 2, ',', '.') ?></span>
                        </div>
                        <div class="cart-total-row" style="font-size: var(--font-size-sm); color: var(--color-text-light);">
                            <span><?= $totalItens ?> item(s)</span>
                        </div>
                    </div>

                    <div class="cart-actions">
                        <button class="btn-outline" onclick="clearCart()">
                            <i class="fas fa-trash"></i> Limpar
                        </button>
                        <button class="btn-success" onclick="checkout()">
                            <i class="fas fa-check"></i> Finalizar Compra
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ========================================
    FOOTER
    ======================================== -->
    <footer>
        <div class="container">
            <p><i class="fas fa-copyright"></i> 2026 <span>Nicolas Esportes</span> · Todos os direitos reservados.</p>
            <p class="footer-sub">
                Feito com <i class="fas fa-heart" style="color: var(--color-secondary);"></i> para atletas de verdade.
            </p>
        </div>
    </footer>

    <!-- ========================================
    ADMIN LINK
    ======================================== -->
    <a href="/2026/PHP/Nicolas/admin/produtos.php" class="admin-link">
        <i class="fas fa-user-shield"></i> Admin
    </a>

    <!-- ========================================
    SCRIPTS
    ======================================== -->
    <script>
        // ========================================
        // FUNÇÕES DO CARRINHO
        // ========================================

        function addToCart(productId, productName) {
            fetch(`?action=add&id=${productId}`, {
                method: 'GET'
            })
            .then(response => {
                if (response.ok) {
                    showToast(`"${productName}" adicionado ao carrinho!`);
                    updateCartBadge();
                    updateCartContent();
                }
            })
            .catch(error => {
                console.error('Erro ao adicionar ao carrinho:', error);
                window.location.href = `?action=add&id=${productId}`;
            });
        }

        function removeFromCart(productId) {
            if (confirm('Tem certeza que deseja remover este item?')) {
                fetch(`?action=remove&id=${productId}`)
                    .then(() => {
                        updateCartBadge();
                        updateCartContent();
                    });
            }
        }

        function updateQuantity(productId, change, newValue = null) {
            let quantity = newValue;
            
            if (quantity === null) {
                const cartItem = document.querySelector(`.cart-item[data-id="${productId}"]`);
                if (cartItem) {
                    const input = cartItem.querySelector('input[type="number"]');
                    if (input) {
                        quantity = parseInt(input.value) + change;
                        if (quantity < 1) quantity = 1;
                        if (quantity > 99) quantity = 99;
                    }
                }
            }

            if (!quantity || quantity < 1) quantity = 1;
            if (quantity > 99) quantity = 99;

            const formData = new FormData();
            formData.append('action', 'update');
            formData.append('id', productId);
            formData.append('quantidade', quantity);

            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(() => {
                updateCartBadge();
                updateCartContent();
            });
        }

        function clearCart() {
            if (confirm('Tem certeza que deseja limpar o carrinho?')) {
                fetch('?action=clear')
                    .then(() => {
                        updateCartBadge();
                        updateCartContent();
                    });
            }
        }

        function toggleCart(event) {
            if (event) event.preventDefault();
            const overlay = document.getElementById('cartOverlay');
            overlay.classList.toggle('active');
            document.body.style.overflow = overlay.classList.contains('active') ? 'hidden' : '';
        }

        function closeCart(event) {
            if (event && event.target !== event.currentTarget) return;
            const overlay = document.getElementById('cartOverlay');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        function updateCartBadge() {
            fetch('?ajax=cart_count')
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('cartBadge');
                    
                    if (data.count > 0) {
                        if (badge) {
                            badge.textContent = data.count;
                        } else {
                            const cartBtn = document.querySelector('.btn-cart-nav');
                            const newBadge = document.createElement('span');
                            newBadge.className = 'cart-badge';
                            newBadge.id = 'cartBadge';
                            newBadge.textContent = data.count;
                            cartBtn.appendChild(newBadge);
                        }
                    } else if (badge) {
                        badge.remove();
                    }
                })
                .catch(() => {
                    location.reload();
                });
        }

        function updateCartContent() {
            fetch('?ajax=cart_content')
                .then(response => response.text())
                .then(html => {
                    document.getElementById('cartContent').innerHTML = html;
                });
        }

        function checkout() {
            const isLoggedIn = <?= $isLoggedIn ? 'true' : 'false' ?>;
            
            if (isLoggedIn) {
                const totalItems = <?= $totalItens ?>;
                if (totalItems === 0) {
                    showToast('⚠️ Seu carrinho está vazio!');
                    return;
                }
                alert('🛒 Redirecionando para finalização da compra...');
                // window.location.href = '/2026/PHP/Nicolas/checkout.php';
            } else {
                alert('⚠️ Você precisa estar logado para finalizar a compra!');
                window.location.href = '/2026/PHP/Nicolas/cliente/login.php';
            }
        }

        // ========================================
        // TOAST NOTIFICATION
        // ========================================

        let toastTimeout;

        function showToast(message) {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toastMessage');
            
            toastMessage.textContent = message;
            toast.classList.add('show');
            
            clearTimeout(toastTimeout);
            toastTimeout = setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }

        // ========================================
        // KEYBOARD SHORTCUTS
        // ========================================

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeCart();
            }
        });

        // ========================================
        // INICIALIZAÇÃO
        // ========================================

        console.log('🛒 Carrinho inicializado com <?= $totalItens ?> item(s)');
        <?php if ($isLoggedIn): ?>
            console.log('👤 Usuário logado: <?= htmlspecialchars($userName) ?>');
        <?php endif; ?>
    </script>
</body>
</html>