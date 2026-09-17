<?php
$titulo = 'Painel Admin';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/funcoes.php';
require_once __DIR__ . '/../includes/auth.php';
exigirAdmin();

$totalProdutos = $pdo->query("SELECT COUNT(*) FROM produtos")->fetchColumn();
$totalPedidos  = $pdo->query("SELECT COUNT(*) FROM pedidos")->fetchColumn();
$totalUsuarios = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
$receita       = $pdo->query("SELECT COALESCE(SUM(total),0) FROM pedidos WHERE status != 'cancelado'")->fetchColumn();

require_once __DIR__ . '/../includes/header.php';
?>
<section class="page-content">
  <div class="container">
    <h1 style="margin-bottom:24px;">📊 Painel Administrativo</h1>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;margin-bottom:40px;">
      <div style="background:#fff;padding:24px;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,0.05);">
        <div style="color:var(--gray);font-size:0.85rem;">Produtos</div>
        <div style="font-size:2rem;font-weight:800;"><?= $totalProdutos ?></div>
      </div>
      <div style="background:#fff;padding:24px;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,0.05);">
        <div style="color:var(--gray);font-size:0.85rem;">Pedidos</div>
        <div style="font-size:2rem;font-weight:800;"><?= $totalPedidos ?></div>
      </div>
      <div style="background:#fff;padding:24px;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,0.05);">
        <div style="color:var(--gray);font-size:0.85rem;">Usuários</div>
        <div style="font-size:2rem;font-weight:800;"><?= $totalUsuarios ?></div>
      </div>
      <div style="background:#fff;padding:24px;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,0.05);">
        <div style="color:var(--gray);font-size:0.85rem;">Receita</div>
        <div style="font-size:1.6rem;font-weight:800;color:var(--gold);"><?= preco((float)$receita) ?></div>
      </div>
    </div>

    <div style="display:flex;gap:12px;flex-wrap:wrap;">
      <a href="<?= BASE_URL ?>admin/produtos.php" class="btn btn-gold">📦 Gerenciar Produtos</a>
      <a href="<?= BASE_URL ?>admin/pedidos.php" class="btn btn-outline">🧾 Ver Pedidos</a>
      <a href="<?= BASE_URL ?>pages/logout.php" class="btn btn-dark">Sair</a>
    </div>
  </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>