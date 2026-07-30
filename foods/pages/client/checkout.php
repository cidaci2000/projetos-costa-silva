<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'cliente') {
    header('Location: ../login.php');
    exit();
}

if (empty($_SESSION['carrinho'])) {
    header('Location: cart.php');
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
    <title>Finalizar Pedido - Foods Delivery</title>
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
        .checkout-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin: 30px 0;
        }
        .checkout-form {
            background: white;
            border-radius: var(--radius);
            padding: 30px;
            box-shadow: var(--shadow);
        }
        .checkout-form h3 {
            margin-bottom: 20px;
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
        .form-group select {
            width: 100%;
            padding: 10px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-family: inherit;
            transition: var(--transition);
        }
        .form-group select:focus {
            outline: none;
            border-color: var(--primary);
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
        .btn-block {
            width: 100%;
            margin-top: 15px;
            padding: 12px;
            font-size: 16px;
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
            .checkout-container {
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

        <h2 style="margin: 20px 0;"><i class="fas fa-check-circle"></i> Finalizar Pedido</h2>

        <div class="checkout-container">
            <div class="checkout-form">
                <h3>Forma de Pagamento</h3>
                <form action="../../actions/client/checkout.php" method="POST">
                    <div class="form-group">
                        <label>Selecione a forma de pagamento</label>
                        <select name="forma_pagamento" required>
                            <option value="pix">PIX</option>
                            <option value="credito">Cartão de Crédito</option>
                            <option value="debito">Cartão de Débito</option>
                            <option value="dinheiro">Dinheiro</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-check"></i> Confirmar Pedido
                    </button>
                </form>
            </div>

            <div class="cart-summary">
                <h3>Resumo do Pedido</h3>
                <?php
                $total = 0;
                $totalItens = 0;
                foreach ($_SESSION['carrinho'] as $id => $qtd) {
                    $totalItens += $qtd;
                }
                ?>
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
                <a href="cart.php" class="btn btn-secondary btn-block">
                    <i class="fas fa-arrow-left"></i> Voltar ao Carrinho
                </a>
            </div>
        </div>
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