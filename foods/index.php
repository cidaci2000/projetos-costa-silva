<?php
session_start();

// 🔥 SE FOR CLIENTE, REDIRECIONA PARA RESTAURANTS
if (isset($_SESSION['usuario_id']) && $_SESSION['tipo_usuario'] === 'cliente') {
    header('Location: pages/client/restaurants.php');
    exit();
}

// Verifica se está logado
$isLoggedIn = isset($_SESSION['usuario_id']);
$userName = $_SESSION['usuario_nome'] ?? '';
$userType = $_SESSION['tipo_usuario'] ?? '';



// Conexão com o banco
try {
    $conn = new PDO("mysql:host=localhost;dbname=foods_delivery", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $conn->query("SELECT * FROM restaurantes LIMIT 6");
    $restaurantes = $stmt->fetchAll();
} catch (PDOException $e) {
    $restaurantes = [];
}

$flash = $_SESSION['flash_message'] ?? null;
unset($_SESSION['flash_message']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foods Delivery</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
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
        .user-actions span {
            font-weight: 500;
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
    </style>
</head>
<body>
    <!-- NAVEGAÇÃO -->
    <nav>
        <div class="container">
            <div class="nav-content">
                <a href="index.php" class="logo">
                    <i class="fas fa-utensils"></i> Foods Delivery
                </a>
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="#">Restaurantes</a></li>
                    <?php if ($isLoggedIn && $userType === 'admin'): ?>
                        <li><a href="pages/admin/dashboard.php">Admin</a></li>
                    <?php endif; ?>
                    <?php if ($isLoggedIn && $userType === 'restaurante'): ?>
                        <li><a href="pages/restaurant/dashboard.php">Dashboard</a></li>
                    <?php endif; ?>
                    <?php if ($isLoggedIn && $userType === 'motoboy'): ?>
                        <li><a href="pages/courier/dashboard.php">Entregas</a></li>
                    <?php endif; ?>
                </ul>
                <div class="user-actions">
                    <?php if ($isLoggedIn): ?>
                        <span>👋 <?php echo htmlspecialchars($userName); ?></span>
                        <a href="actions/logout.php" class="btn-logout">Sair</a>
                    <?php else: ?>
                        <a href="pages/login.php" class="btn btn-primary">Entrar</a>
                        <a href="pages/register.php" class="btn btn-secondary">Cadastrar</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <main>
        <!-- MENSAGEM FLASH -->
        <?php if ($flash): ?>
        <div class="container">
            <div class="flash-message <?php echo $flash['type']; ?>">
                <?php echo $flash['message']; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- HERO -->
        <section class="hero">
            <div class="container hero-content">
                <div class="hero-text">
                    <h1>Fome de <span>algo bom?</span></h1>
                    <p>Descubra restaurantes incríveis e receba seus pratos favoritos onde estiver.</p>
                    <a href="#" class="btn btn-primary">Peça agora!</a>
                </div>
                <div class="hero-image">
                    <img src="https://img.freepik.com/free-photo/delicious-burger-with-fresh-ingredients_23-2150857908.jpg" alt="Burger">
                </div>
            </div>
        </section>

        <!-- CATEGORIAS -->
        <section class="container">
            <div class="section-header">
                <h2>Categorias</h2>
                <a href="#" class="view-all">Ver todos →</a>
            </div>
            <div class="categories-grid">
                <div class="category-card">
                    <i class="fas fa-pizza-slice"></i>
                    <span>Pizza</span>
                </div>
                <div class="category-card">
                    <i class="fas fa-hamburger"></i>
                    <span>Burger</span>
                </div>
                <div class="category-card">
                    <i class="fas fa-fish"></i>
                    <span>Sushi</span>
                </div>
                <div class="category-card">
                    <i class="fas fa-utensils"></i>
                    <span>Italiana</span>
                </div>
                <div class="category-card">
                    <i class="fas fa-taco"></i>
                    <span>Mexicana</span>
                </div>
                <div class="category-card">
                    <i class="fas fa-leaf"></i>
                    <span>Vegetariana</span>
                </div>
            </div>
        </section>

        <!-- RESTAURANTES -->
        <section class="container">
            <div class="section-header">
                <h2>Restaurantes em Destaque</h2>
                <a href="#" class="view-all">Ver todos →</a>
            </div>
            
            <?php if (!empty($restaurantes)): ?>
            <div class="restaurants-grid">
                <?php foreach ($restaurantes as $r): ?>
                <div class="restaurant-card">
                    <div class="card-img" style="background-image: url('<?php echo $r['imagem_url'] ?? 'https://via.placeholder.com/400x200/FF6B35/FFFFFF?text=Foods'; ?>');">
                        <span class="card-badge">⭐ 4.8</span>
                    </div>
                    <div class="card-info">
                        <h3><?php echo htmlspecialchars($r['nome']); ?></h3>
                        <div class="card-meta">
                            <span><?php echo htmlspecialchars($r['categoria'] ?? 'Geral'); ?></span>
                            <span class="rating">⭐ 4.8 (120)</span>
                        </div>
                        <div class="delivery-info">
                            <span><i class="fas fa-truck"></i> Entrega Rápida</span>
                            <span><i class="fas fa-clock"></i> 30-45 min</span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-utensils"></i>
                <h3>Nenhum restaurante cadastrado</h3>
                <p>Execute o script SQL para criar os dados.</p>
            </div>
            <?php endif; ?>
        </section>

        <!-- BENEFÍCIOS -->
        <section class="benefits">
            <div class="container">
                <h2>Por que escolher o Foods Delivery?</h2>
                <div class="benefits-grid">
                    <div class="benefit-card">
                        <i class="fas fa-motorcycle"></i>
                        <h3>Entrega Rápida</h3>
                        <p>Receba em até 45 minutos</p>
                    </div>
                    <div class="benefit-card">
                        <i class="fas fa-shield-alt"></i>
                        <h3>Pagamento Seguro</h3>
                        <p>Várias formas de pagamento</p>
                    </div>
                    <div class="benefit-card">
                        <i class="fas fa-store"></i>
                        <h3>Variedade</h3>
                        <p>Mais de 100 restaurantes</p>
                    </div>
                    <div class="benefit-card">
                        <i class="fas fa-star"></i>
                        <h3>Avaliações</h3>
                        <p>Opiniões de clientes reais</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- RODAPÉ -->
    <footer>
        <div class="container footer-content">
            <div>
                <p class="footer-logo"><i class="fas fa-utensils"></i> Foods Delivery</p>
                <p>© 2026 Foods Delivery. Trabalho Escolar.</p>
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