<?php
// admin/index.php - Dashboard
require_once __DIR__ . '../config/database.php';
require_once __DIR__ . '../config/auth.php';
require_once __DIR__ . '../models/Product.php';

// Proteger página - apenas admin
Auth::requireAdmin();

$productModel = new Product();
$totalProdutos = count($productModel->findAll(false));
$produtosAtivos = count($productModel->findAll(true));
$produtosBaixoEstoque = $productModel->findLowStock(5);
$promocoes = $productModel->findOnSale();
$destaques = $productModel->findDestaques();
$user = Auth::getUser();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Admin Nicolas Esportes</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background: #f1f5f9; }
        
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: 240px;
            background: linear-gradient(180deg, #0b1a2e, #162b44);
            color: white;
            padding: 24px 16px;
            overflow-y: auto;
        }
        .sidebar .logo {
            text-align: center;
            padding: 16px 0 32px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar .logo i { font-size: 2.4rem; color: #facc15; }
        .sidebar .logo h2 { font-size: 1.3rem; margin-top: 8px; }
        .sidebar .logo span { color: #facc15; }
        
        .sidebar .menu { margin-top: 24px; }
        .sidebar .menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: #b9c8dd;
            text-decoration: none;
            border-radius: 12px;
            transition: 0.2s;
            margin-bottom: 4px;
        }
        .sidebar .menu a:hover,
        .sidebar .menu a.active {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        .sidebar .menu a i { width: 20px; text-align: center; }
        .sidebar .menu .logout {
            margin-top: 40px;
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 20px;
        }
        .sidebar .menu .logout a { color: #f87171; }
        .sidebar .menu .logout a:hover { background: rgba(248,113,113,0.1); }
        
        .main {
            margin-left: 240px;
            padding: 24px;
        }
        .main .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 30px;
        }
        .main .header h1 { font-size: 1.8rem; color: #0b1a2e; }
        .main .header .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            background: white;
            padding: 8px 20px 8px 16px;
            border-radius: 40px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .main .header .user-info i { font-size: 1.4rem; color: #facc15; }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 24px;
            border-radius: 20px;
            border: 1px solid #e9edf4;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        }
        .stat-card .number {
            font-size: 2.4rem;
            font-weight: 700;
            color: #0b1a2e;
        }
        .stat-card .label {
            color: #64748b;
            font-weight: 500;
            margin-top: 4px;
        }
        .stat-card .icon {
            float: right;
            font-size: 2rem;
            color: #facc15;
            opacity: 0.3;
        }
        
        .card {
            background: white;
            border-radius: 20px;
            padding: 24px;
            border: 1px solid #e9edf4;
            margin-bottom: 24px;
        }
        .card h3 {
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .card h3 i { color: #facc15; }
        
        .alert-list {
            list-style: none;
        }
        .alert-list li {
            padding: 10px 0;
            border-bottom: 1px solid #eef2f6;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }
        .alert-list li:last-child { border-bottom: none; }
        .alert-list .stock-badge {
            padding: 4px 12px;
            border-radius: 40px;
            font-size: 0.8rem;
            font-weight: 600;
            background: #fee2e2;
            color: #991b1b;
        }
        .alert-list .stock-ok {
            background: #dcfce7;
            color: #166534;
        }
        
        .btn {
            padding: 8px 20px;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
        }
        .btn-primary { background: #facc15; color: #0b1a2e; }
        .btn-primary:hover { background: #fde047; }
        .btn-success { background: #22c55e; color: white; }
        .btn-success:hover { background: #16a34a; }
        .btn-sm { padding: 6px 14px; font-size: 0.8rem; }
        
        @media (max-width: 768px) {
            .sidebar { width: 60px; padding: 16px 8px; }
            .sidebar .logo h2 { display: none; }
            .sidebar .menu a span { display: none; }
            .sidebar .menu a { justify-content: center; padding: 12px; }
            .main { margin-left: 60px; padding: 16px; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <i class="fas fa-football"></i>
            <h2>Nicolas <span>Admin</span></h2>
        </div>
        <div class="menu">
            <a href="index.php" class="active">
                <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
            </a>
            <a href="produtos.php">
                <i class="fas fa-boxes"></i> <span>Produtos</span>
            </a>
            <a href="produto-novo.php">
                <i class="fas fa-plus-circle"></i> <span>Novo Produto</span>
            </a>
            <div class="logout">
                <a href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> <span>Sair</span>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Conteúdo Principal -->
    <div class="main">
        <div class="header">
            <h1><i class="fas fa-tachometer-alt" style="color: #facc15;"></i> Dashboard</h1>
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span><strong><?= htmlspecialchars($user['name']) ?></strong> (<?= $user['email'] ?>)</span>
            </div>
        </div>
        
        <!-- Estatísticas -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon"><i class="fas fa-boxes"></i></div>
                <div class="number"><?= $totalProdutos ?></div>
                <div class="label">Total de Produtos</div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-check-circle"></i></div>
                <div class="number"><?= $produtosAtivos ?></div>
                <div class="label">Produtos Ativos</div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-star"></i></div>
                <div class="number"><?= count($destaques) ?></div>
                <div class="label">Em Destaque</div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-tags"></i></div>
                <div class="number"><?= count($promocoes) ?></div>
                <div class="label">Em Promoção</div>
            </div>
        </div>
        
        <!-- Produtos com baixo estoque -->
        <div class="card">
            <h3><i class="fas fa-exclamation-triangle"></i> Produtos com Baixo Estoque</h3>
            <?php if (empty($produtosBaixoEstoque)): ?>
                <p style="color: #16a34a;"><i class="fas fa-check-circle"></i> Todos os produtos têm estoque adequado!</p>
            <?php else: ?>
                <ul class="alert-list">
                    <?php foreach ($produtosBaixoEstoque as $produto): ?>
                        <li>
                            <span><?= htmlspecialchars($produto['nome']) ?></span>
                            <span>
                                <span class="stock-badge">
                                    <?= $produto['estoque'] ?> unidades
                                </span>
                                <a href="produto-editar.php?id=<?= $produto['id'] ?>" class="btn btn-sm btn-success">
                                    <i class="fas fa-edit"></i> Repor
                                </a>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        
        <!-- Ações rápidas -->
        <div class="card" style="display: flex; gap: 12px; flex-wrap: wrap;">
            <a href="produto-novo.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Adicionar Produto
            </a>
            <a href="produtos.php" class="btn" style="background: #0b1a2e; color: white;">
                <i class="fas fa-list"></i> Gerenciar Produtos
            </a>
            <a href="/nicolas-esportes/" class="btn" style="background: #f1f5f9; color: #475569;">
                <i class="fas fa-store"></i> Ver Loja
            </a>
        </div>
    </div>
</body>
</html>