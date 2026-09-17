<?php
$titulo = 'Produtos';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/funcoes.php';
require_once __DIR__ . '/../includes/auth.php';
exigirAdmin();

// Excluir
if (isset($_GET['excluir'])) {
    $pdo->prepare("DELETE FROM produtos WHERE id = ?")->execute([(int)$_GET['excluir']]);
    flash('Produto excluído.', 'success');
    redirect(BASE_URL . 'admin/produtos.php');
}

$produtos = $pdo->query("SELECT p.*, c.nome AS categoria FROM produtos p LEFT JOIN categorias c ON c.id = p.categoria_id ORDER BY p.id DESC")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>
<section class="page-content">
  <div class="container">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
      <h1>📦 Produtos</h1>
      <a href="<?= BASE_URL ?>admin/produto_form.php" class="btn btn-gold">+ Novo Produto</a>
    </div>

    <table class="orders-table" style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.05);">
      <thead>
        <tr><th>ID</th><th>Nome</th><th>Categoria</th><th>Preço</th><th>Ações</th></tr>
      </thead>
      <tbody>
        <?php foreach ($produtos as $p): ?>
          <tr>
            <td><?= $p['id'] ?></td>
            <td><?= e($p['nome']) ?></td>
            <td><?= e($p['categoria'] ?? '-') ?></td>
            <td><?= preco((float)$p['preco']) ?></td>
            <td>
              <a href="<?= BASE_URL ?>admin/produto_form.php?id=<?= $p['id'] ?>" class="btn btn-outline" style="padding:6px 12px;font-size:0.8rem;">Editar</a>
              <a href="?excluir=<?= $p['id'] ?>" onclick="return confirm('Excluir este produto?')" class="btn btn-danger" style="padding:6px 12px;font-size:0.8rem;">Excluir</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>