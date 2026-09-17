<?php
$titulo = 'Carrinho';
require_once __DIR__ . '/../includes/header.php';

$itens = getCarrinhoCompleto($pdo);
$total = totalCarrinhoValor($pdo);
?>

<section class="page-hero">
  <div class="container"><h1>Meu Carrinho</h1></div>
</section>

<section class="page-content">
  <div class="container">
    <?php if (empty($itens)): ?>
      <div style="text-align:center;padding:60px 20px;">
        <div style="font-size:4rem;">🛍️</div>
        <h3>Seu carrinho está vazio</h3>
        <p style="color:var(--gray);margin:10px 0 20px;">Explore nossas fragrâncias!</p>
        <a href="<?= BASE_URL ?>pages/catalogo.php" class="btn btn-gold">Ver Catálogo</a>
      </div>
    <?php else: ?>
      <div style="display:grid;grid-template-columns:1fr 360px;gap:30px;">
        <div>
          <?php foreach ($itens as $item): ?>
            <div style="display:flex;gap:16px;padding:16px;background:#fff;border-radius:12px;margin-bottom:14px;box-shadow:0 4px 20px rgba(0,0,0,0.05);">
              <img src="<?= e($item['imagem']) ?>" style="width:90px;height:90px;object-fit:cover;border-radius:8px;" />
              <div style="flex:1;">
                <h3 style="font-size:1.05rem;"><?= e($item['nome']) ?></h3>
                <p style="color:var(--gray);font-size:0.85rem;"><?= e($item['notas']) ?></p>
                <div style="color:var(--gold);font-weight:700;margin-top:6px;"><?= preco((float)$item['preco']) ?></div>
              </div>
              <div style="display:flex;flex-direction:column;align-items:flex-end;gap:10px;">
                <form method="post" action="<?= BASE_URL ?>actions/update_carrinho.php" style="display:flex;gap:6px;">
                  <input type="hidden" name="produto_id" value="<?= $item['id'] ?>" />
                  <button name="quantidade" value="<?= $item['quantidade'] - 1 ?>" class="qty-btn">−</button>
                  <span style="padding:6px 10px;font-weight:700;"><?= $item['quantidade'] ?></span>
                  <button name="quantidade" value="<?= $item['quantidade'] + 1 ?>" class="qty-btn">+</button>
                </form>
                <div style="font-weight:800;"><?= preco((float)$item['subtotal']) ?></div>
                <form method="post" action="<?= BASE_URL ?>actions/remove_carrinho.php">
                  <input type="hidden" name="produto_id" value="<?= $item['id'] ?>" />
                  <button style="background:none;color:var(--red);font-size:0.85rem;">🗑️ Remover</button>
                </form>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <aside style="background:#fff;border-radius:16px;padding:24px;box-shadow:0 4px 20px rgba(0,0,0,0.05);height:fit-content;">
          <h3 style="margin-bottom:16px;">Resumo</h3>
          <div style="display:flex;justify-content:space-between;margin-bottom:8px;color:var(--gray);">
            <span>Subtotal</span><span><?= preco($total) ?></span>
          </div>
          <div style="display:flex;justify-content:space-between;padding-top:12px;border-top:2px dashed #ddd;font-size:1.2rem;font-weight:800;margin-bottom:20px;">
            <span>Total</span><span><?= preco($total) ?></span>
          </div>
          <a href="<?= BASE_URL ?>pages/checkout.php" class="btn btn-gold" style="width:100%;">✦ Finalizar Compra</a>
          <a href="<?= BASE_URL ?>pages/catalogo.php" class="btn btn-outline" style="width:100%;margin-top:10px;">Continuar Comprando</a>
        </aside>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>