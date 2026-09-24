<?php
require_once __DIR__ . '/../includes/functions.php';
if (!isAdmin()) redirect('../login.php');

// Excluir produto
if (isset($_GET['excluir'])) {
    $id = (int)$_GET['excluir'];
    $stmt = db()->prepare("DELETE FROM produtos WHERE id = :id");
    $stmt->execute([':id' => $id]);
    setFlash('sucesso', 'Produto excluído com sucesso.');
    redirect('produtos.php');
}

// Busca
$busca = trim($_GET['q'] ?? '');
if ($busca) {
    $stmt = db()->prepare("
        SELECT p.*, c.nome AS categoria
        FROM produtos p
        LEFT JOIN categorias c ON c.id = p.categoria_id
        WHERE p.nome LIKE :q
        ORDER BY p.id DESC
    ");
    $stmt->execute([':q' => "%$busca%"]);
} else {
    $stmt = db()->query("
        SELECT p.*, c.nome AS categoria
        FROM produtos p
        LEFT JOIN categorias c ON c.id = p.categoria_id
        ORDER BY p.id DESC
    ");
}
$produtos = $stmt->fetchAll();
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Produtos - Admin ReyVie</title>
<?php include __DIR__ . '/../style.php'; ?>
</head>
<body>

<?php if ($flash): ?>
    <div class="notificacao <?= $flash['tipo'] ?>"><?= e($flash['msg']) ?></div>
<?php endif; ?>

<div class="wrap">
    <div class="admin-header">
        <h1>📦 Gerenciar Produtos</h1>
        <div class="admin-actions">
            <a href="produto_form.php" class="btn btn-primary">+ Novo Produto</a>
            <a href="index.php" class="btn btn-secondary">← Painel</a>
        </div>
    </div>

    <!-- BUSCA -->
    <form method="GET" class="search-form" style="margin-bottom:1.5rem; max-width:420px;">
        <input type="text" name="q" placeholder="Buscar produto..." value="<?= e($busca) ?>" style="flex:1;">
        <button class="btn btn-primary">Buscar</button>
        <?php if ($busca): ?>
            <a href="produtos.php" class="btn btn-secondary">Limpar</a>
        <?php endif; ?>
    </form>

    <!-- TABELA -->
    <div style="overflow-x:auto;">
        <table class="tabela-admin">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Imagem</th>
                    <th>Nome</th>
                    <th>Categoria</th>
                    <th>Preço</th>
                    <th>Estoque</th>
                    <th>Destaque</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($produtos)): ?>
                    <tr><td colspan="8" style="text-align:center; padding:2rem;">Nenhum produto encontrado.</td></tr>
                <?php else: ?>
                    <?php foreach ($produtos as $p): $promo = !empty($p['preco_promocional']); ?>
                        <tr>
                            <td><strong>#<?= $p['id'] ?></strong></td>
                            <td>
                                <?php if ($p['imagem']): ?>
                                    <img src="<?= imagemUrl($p['imagem']) ?>" style="width:50px;height:50px;object-fit:cover;border-radius:8px;" alt="">
                                <?php else: ?>
                                    <span style="font-size:1.5rem;"><?= $p['emoji'] ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?= e($p['nome']) ?></td>
                            <td><?= e($p['categoria'] ?? '—') ?></td>
                            <td>
                                <?php if ($promo): ?>
                                    <span style="text-decoration:line-through;color:#999;font-size:.8rem;">
                                        R$ <?= number_format($p['preco'], 2, ',', '.') ?>
                                    </span><br>
                                    <strong style="color:var(--primary-coral);">
                                        R$ <?= number_format($p['preco_promocional'], 2, ',', '.') ?>
                                    </strong>
                                <?php else: ?>
                                    R$ <?= number_format($p['preco'], 2, ',', '.') ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge <?= $p['estoque'] == 0 ? 'badge-erro' : ($p['estoque'] < 10 ? 'badge-aviso' : 'badge-ok') ?>">
                                    <?= (int)$p['estoque'] ?> un.
                                </span>
                            </td>
                            <td><?= $p['destaque'] ? '⭐' : '—' ?></td>
                            <td style="white-space:nowrap;">
                                <a href="produto_form.php?id=<?= $p['id'] ?>" class="btn btn-secondary btn-sm">✏️ Editar</a>
                                <a href="?excluir=<?= $p['id'] ?>" class="btn btn-danger btn-sm"
                                   onclick="return confirm('Tem certeza que deseja excluir este produto?')">🗑️</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>