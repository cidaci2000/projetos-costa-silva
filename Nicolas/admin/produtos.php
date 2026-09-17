<?php
// admin/produtos.php
require_once __DIR__ . '../config/database.php';
require_once __DIR__ . '../config/auth.php';
require_once __DIR__ . '../models/Product.php';

// Proteger - apenas admin
Auth::requireAdmin();

$productModel = new Product();
$products = $productModel->findAll(false);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos | Admin Nicolas Esportes</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background: #f1f5f9; }
        
        .header {
            background: linear-gradient(145deg, #0b1a2e, #162b44);
            color: white;
            padding: 20px 0;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
        }
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }
        .header h1 { display: flex; align-items: center; gap: 12px; font-size: 1.8rem; }
        .header h1 i { color: #facc15; }
        
        .btn {
            padding: 10px 24px;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
            border: none;
            cursor: pointer;
            font-size: 0.95rem;
        }
        .btn-primary { background: #facc15; color: #0b1a2e; }
        .btn-primary:hover { background: #fde047; transform: scale(1.02); }
        .btn-success { background: #22c55e; color: white; }
        .btn-success:hover { background: #16a34a; }
        .btn-danger { background: #ef4444; color: white; }
        .btn-danger:hover { background: #dc2626; }
        .btn-warning { background: #f59e0b; color: white; }
        .btn-warning:hover { background: #d97706; }
        .btn-outline {
            background: transparent;
            color: white;
            border: 1px solid rgba(255,255,255,0.2);
        }
        .btn-outline:hover { background: rgba(255,255,255,0.1); }
        .btn-sm {
            padding: 6px 14px;
            font-size: 0.85rem;
            border-radius: 30px;
        }
        .btn-edit { background: #dbeafe; color: #1e40af; }
        .btn-edit:hover { background: #bfdbfe; }
        .btn-delete { background: #fee2e2; color: #991b1b; }
        .btn-delete:hover { background: #fecaca; }
        
        .card {
            background: white;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.04);
            border: 1px solid #e9edf4;
        }
        
        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .alert-info { background: #dbeafe; color: #1e40af; border: 1px solid #93c5fd; }
        
        .table-wrapper { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead {
            background: #f8fafc;
            border-bottom: 2px solid #e9edf4;
        }
        th, td {
            padding: 14px 16px;
            text-align: left;
            border-bottom: 1px solid #eef2f6;
        }
        th { font-weight: 600; color: #475569; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; }
        
        .badge-tag {
            background: #facc15;
            padding: 2px 12px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            display: inline-block;
        }
        .status-active { color: #22c55e; font-weight: 600; }
        .status-inactive { color: #94a3b8; }
        .stock-low { color: #ef4444; font-weight: 600; }
        .stock-ok { color: #22c55e; }
        
        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 20px;
        }
        .toolbar input {
            padding: 10px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 30px;
            font-size: 0.95rem;
            min-width: 250px;
            transition: 0.2s;
        }
        .toolbar input:focus {
            outline: none;
            border-color: #facc15;
            box-shadow: 0 0 0 4px #facc1530;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #94a3b8;
        }
        .empty-state i { font-size: 4rem; margin-bottom: 16px; opacity: 0.5; display: block; }
        
        @media (max-width: 768px) {
            .header-content { flex-direction: column; text-align: center; }
            .toolbar { flex-direction: column; align-items: stretch; }
            .toolbar input { min-width: auto; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container header-content">
            <h1><i class="fas fa-boxes"></i> Gerenciar Produtos</h1>
            <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                <a href="/nicolas-esportes/" class="btn btn-outline">
                    <i class="fas fa-store"></i> Ver Loja
                </a>
                <a href="produto-novo.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Novo Produto
                </a>
            </div>
        </div>
    </div>
    
    <div class="container">
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_GET['success']) ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="toolbar">
                <span><strong>Total:</strong> <?= count($products) ?> produtos</span>
                <input type="text" id="searchInput" placeholder="🔍 Buscar produto...">
            </div>
            
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Produto</th>
                            <th>Categoria</th>
                            <th>Preço</th>
                            <th>Estoque</th>
                            <th>Status</th>
                            <th style="text-align: center;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="fas fa-box-open"></i>
                                        <p>Nenhum produto cadastrado.</p>
                                        <a href="produto-novo.php" style="color: #0b1a2e; font-weight: 600;">
                                            Cadastrar primeiro produto
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td>#<?= $product['id'] ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($product['nome']) ?></strong>
                                        <?php if ($product['badge']): ?>
                                            <span class="badge-tag"><?= htmlspecialchars($product['badge']) ?></span>
                                        <?php endif; ?>
                                        <div style="font-size: 0.85rem; color: #64748b; margin-top: 4px;">
                                            <i class="fas <?= htmlspecialchars($product['icone'] ?? 'fa-box') ?>"></i>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($product['categoria_nome'] ?? 'Sem categoria') ?></td>
                                    <td>
                                        <strong>R$ <?= number_format($product['preco_atual'], 2, ',', '.') ?></strong>
                                        <?php if ($product['preco_promocional']): ?>
                                            <br>
                                            <span style="text-decoration: line-through; color: #94a3b8; font-size: 0.85rem;">
                                                R$ <?= number_format($product['preco'], 2, ',', '.') ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="<?= $product['estoque'] <= 5 ? 'stock-low' : 'stock-ok' ?>">
                                            <?= $product['estoque'] ?> un.
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($product['ativo']): ?>
                                            <span class="status-active"><i class="fas fa-check-circle"></i> Ativo</span>
                                        <?php else: ?>
                                            <span class="status-inactive"><i class="fas fa-circle"></i> Inativo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: center;">
                                        <div style="display: flex; gap: 6px; justify-content: center; flex-wrap: wrap;">
                                            <a href="produto-editar.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="produto-excluir.php?id=<?= $product['id'] ?>" 
                                               class="btn btn-sm btn-delete"
                                               onclick="return confirm('Tem certeza que deseja excluir este produto?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script>
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const search = this.value.toLowerCase();
            const rows = document.querySelectorAll('table tbody tr');
            rows.forEach(row => {
                if (row.querySelector('.empty-state')) return;
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(search) ? '' : 'none';
            });
        });
    </script>
</body>
</html>