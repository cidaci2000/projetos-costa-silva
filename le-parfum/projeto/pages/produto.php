<?php
$titulo = 'Produto';
require_once __DIR__ . '/../includes/header.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT p.*, c.nome AS categoria FROM produtos p LEFT JOIN categorias c ON c.id = p.categoria_id WHERE p.id = ?");
$stmt->execute([$id]);
$p = $stmt->fetch();

if (!$p) {
    flash('Produto não encontrado.', 'error');
    redirect(BASE_URL . 'pages/catalogo.php');
}
?>

<section class="page-content">
  <div class="container">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:40px;align-items:start;">
      <div>
        <img src="<?= e($p['imagem']) ?>" alt="<?= e($p['nome']) ?>" style="width:100%;border-radius:16px;" />
      </div>
      <div>
        <div class="product-brand" style="font-size:0.85rem;">Le Parfum · <?= e($p['categoria'] ?? '') ?></div>
        <h1 style="font-size:2rem;margin:8px 0;"><?= e($p['nome']) ?></h1>
        <p style="color:var(--gray);margin-bottom:20px;"><?= e($p['notas']) ?></p>

        <div style="font-size:1.6rem;font-weight:800;color:var(--gold);margin-bottom:20px;">
          <?php if ($p['preco_antigo']): ?>
            <span style="font-size:1rem;color:var(--gray);text-decoration:line-through;font-weight:400;">
              <?= preco((float)$p['preco_antigo']) ?>
            </span>
          <?php endif; ?>
          <?= preco((float)$p['preco']) ?>
        </div>

        <p style="line-height:1.7;margin-bottom:24px;"><?= nl2br(e($p['descricao'])) ?></p>

        <form method="post" action="<?= BASE_URL ?>actions/add_carrinho.php" style="display:flex;gap:12px;flex-wrap:wrap;">
          <input type="hidden" name="produto_id" value="<?= $p['id'] ?>" />
          <input type="number" name="quantidade" value="1" min="1" max="<?= (int)$p['estoque'] ?>" 
                 style="padding:12px;width:80px;border:1px solid #ddd;border-radius:8px;" />
          <button type="submit" class="btn btn-gold">🛒 Adicionar ao Carrinho</button>
        </form>

        <div style="margin-top:20px;color:var(--gray);font-size:0.9rem;">
          ✅ Estoque disponível: <?= (int)$p['estoque'] ?> unidades
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>