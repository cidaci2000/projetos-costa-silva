<?php
// admin/produto-editar.php
require_once __DIR__ . '../models/Product.php';

$productModel = new Product();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header('Location: produtos.php?error=ID do produto não informado');
    exit;
}

$product = $productModel->findById($id);
if (!$product) {
    header('Location: produtos.php?error=Produto não encontrado');
    exit;
}

$categorias = $productModel->getCategorias();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'categoria_id' => $_POST['categoria_id'] ?: null,
        'nome' => trim($_POST['nome']),
        'descricao' => trim($_POST['descricao'] ?? ''),
        'preco' => str_replace(',', '.', $_POST['preco']),
        'preco_promocional' => !empty($_POST['preco_promocional']) ? str_replace(',', '.', $_POST['preco_promocional']) : null,
        'estoque' => (int)$_POST['estoque'],
        'imagem' => trim($_POST['imagem'] ?? ''),
        'icone' => trim($_POST['icone'] ?? 'fa-box'),
        'badge' => trim($_POST['badge'] ?? ''),
        'destaque' => isset($_POST['destaque']) ? 1 : 0,
        'ativo' => isset($_POST['ativo']) ? 1 : 0
    ];
    
    if ($productModel->update($id, $data)) {
        header('Location: produtos.php?success=Produto atualizado com sucesso!');
        exit;
    } else {
        $error = 'Erro ao atualizar produto. Tente novamente.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Produto | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background: #f1f5f9; }
        
        .header {
            background: linear-gradient(145deg, #0b1a2e, #162b44);
            color: white;
            padding: 20px 0;
            margin-bottom: 30px;
        }
        .container {
            max-width: 800px;
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
        .header h1 { display: flex; align-items: center; gap: 12px; }
        .header h1 i { color: #facc15; }
        
        .card {
            background: white;
            border-radius: 20px;
            padding: 32px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.04);
            border: 1px solid #e9edf4;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            font-size: 0.95rem;
        }
        .form-group .required { color: #ef4444; }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 10px 14px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: 0.2s;
            background: #fafbfc;
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            outline: none;
            border-color: #facc15;
            box-shadow: 0 0 0 4px #facc1530;
        }
        .form-group textarea { min-height: 80px; resize: vertical; }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 0;
        }
        .checkbox-group input[type="checkbox"] {
            width: 20px;
            height: 20px;
            accent-color: #facc15;
        }
        .help-text {
            font-size: 0.85rem;
            color: #94a3b8;
            margin-top: 4px;
        }
        
        .btn {
            padding: 12px 28px;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            cursor: pointer;
            transition: 0.2s;
        }
        .btn-primary { background: #0b1a2e; color: white; }
        .btn-primary:hover { background: #1e3a5f; }
        .btn-secondary { background: #f1f5f9; color: #475569; }
        .btn-secondary:hover { background: #e2e8f0; }
        
        .btn-group {
            display: flex;
            gap: 12px;
            margin-top: 24px;
            flex-wrap: wrap;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #64748b;
            text-decoration: none;
            margin-bottom: 20px;
        }
        .back-link:hover { color: #0b1a2e; }
        
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            border: 1px solid #fca5a5;
        }
        
        .preview-icon {
            font-size: 2rem;
            padding: 10px;
            background: #f1f5f9;
            border-radius: 12px;
            display: inline-block;
            margin-top: 4px;
        }
        
        @media (max-width: 600px) {
            .form-row { grid-template-columns: 1fr; }
            .card { padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container header-content">
            <h1><i class="fas fa-edit"></i> Editar Produto</h1>
            <a href="produtos.php" class="btn" style="background: transparent; color: white; border: 1px solid rgba(255,255,255,0.2);">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    
    <div class="container">
        <a href="produtos.php" class="back-link"><i class="fas fa-arrow-left"></i> Lista de produtos</a>
        
        <?php if (isset($error)): ?>
            <div class="alert-error"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
        <?php endif; ?>
        
        <div class="card">
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label>Nome do Produto <span class="required">*</span></label>
                        <input type="text" name="nome" required value="<?= htmlspecialchars($product['nome']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Categoria</label>
                        <select name="categoria_id">
                            <option value="">Selecione...</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= $product['categoria_id'] == $cat['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Descrição</label>
                    <textarea name="descricao" placeholder="Descreva o produto..."><?= htmlspecialchars($product['descricao']) ?></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Preço <span class="required">*</span></label>
                        <input type="text" name="preco" required value="<?= number_format($product['preco'], 2, ',', '.') ?>">
                    </div>
                    <div class="form-group">
                        <label>Preço Promocional</label>
                        <input type="text" name="preco_promocional" value="<?= $product['preco_promocional'] ? number_format($product['preco_promocional'], 2, ',', '.') : '' ?>">
                        <div class="help-text">Deixe em branco se não houver promoção</div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Estoque <span class="required">*</span></label>
                        <input type="number" name="estoque" required value="<?= $product['estoque'] ?>">
                    </div>
                    <div class="form-group">
                        <label>Ícone (Font Awesome)</label>
                        <input type="text" name="icone" placeholder="fa-running" value="<?= htmlspecialchars($product['icone'] ?? 'fa-box') ?>">
                        <div class="help-text">Ex: fa-running, fa-basketball-ball</div>
                        <div class="preview-icon">
                            <i class="fas <?= htmlspecialchars($product['icone'] ?? 'fa-box') ?>"></i>
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Badge / Selo</label>
                        <input type="text" name="badge" placeholder="novo, oferta, destaque" value="<?= htmlspecialchars($product['badge'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Imagem (URL)</label>
                        <input type="text" name="imagem" placeholder="https://exemplo.com/imagem.jpg" value="<?= htmlspecialchars($product['imagem'] ?? '') ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="checkbox-group">
                        <input type="checkbox" name="destaque" value="1" <?= $product['destaque'] ? 'checked' : '' ?>>
                        <label style="font-weight: 500;">Destacar produto</label>
                    </div>
                    <div class="checkbox-group">
                        <input type="checkbox" name="ativo" value="1" <?= $product['ativo'] ? 'checked' : '' ?>>
                        <label style="font-weight: 500;">Produto ativo</label>
                    </div>
                </div>
                
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Atualizar</button>
                    <a href="produtos.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>