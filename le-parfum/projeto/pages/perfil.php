<?php
$titulo = 'Minha Conta';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';

exigirLogin();
$user = usuarioAtual();

// Pedidos
$stmt = $pdo->prepare("SELECT * FROM pedidos WHERE usuario_id = ? ORDER BY criado_em DESC");
$stmt->execute([$user['id']]);
$pedidos = $stmt->fetchAll();

// Favoritos
$stmt = $pdo->prepare("SELECT p.* FROM favoritos f JOIN produtos p ON p.id = f.produto_id WHERE f.usuario_id = ?");
$stmt->execute([$user['id']]);
$favoritos = $stmt->fetchAll();
?>

<section class="page-hero">
  <div class="container"><h1>Minha Conta</h1></div>
</section>

<section class="page-content">
  <div class="container">
    <div class="profile-grid">
      <aside class="profile-sidebar">
        <div class="profile-avatar"><?= strtoupper(substr($user['nome'], 0, 1)) ?></div>
        <div class="profile-name"><?= e($user['nome']) ?></div>
        <div class="profile-email"><?= e($user['email']) ?></div>
        <ul class="profile-menu">
          <li><a href="#pedidos" class="active">📦 Meus Pedidos</a></li>
          <li><a href="#favoritos">❤️ Favoritos</a></li>
          <li><a href="<?= BASE_URL ?>pages/logout.php">🚪 Sair</a></li>
        </ul>
      </aside>

      <div class="profile-content">
        <h2 id="pedidos">📦 Meus Pedidos</h2>
        <?php if (empty($pedidos)): ?>
          <p style="color:var(--gray);">Você ainda não fez nenhum pedido.</p>
        <?php else: ?>
          <table class="orders-table">
            <thead>
              <tr><th>Pedido</th><th>Data</th><th>Total</th><th>Status</th></tr>
            </thead>
            <tbody>
              <?php foreach ($pedidos as $p): ?>
                <tr>
                  <td>#LP-<?= str_pad($p['id'], 5, '0', STR_PAD_LEFT) ?></td>
                  <td><?= date('d/m/Y', strtotime($p['criado_em'])) ?></td>
                  <td><?= preco((float)$p['total']) ?></td>
                  <td>
                    <span class="status-badge status-<?= e($p['status']) ?>">
                      <?= ucfirst($p['status']) ?>
                    </span>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>

        <h2 id="favoritos" style="margin-top:40px;">❤️ Favoritos</h2>
        <?php if (empty($favoritos)): ?>
          <p style="color:var(--gray);">Nenhum favorito ainda.</p>
        <?php else: ?>
          <div class="products-grid" style="margin-top:16px;">
            <?php foreach ($favoritos as $p): ?>
              <div class="product-card">
                <div class="product-image">
                  <img src="<?= e($p['imagem']) ?>" alt="<?= e($p['nome']) ?>" />
                </div>
                <div class="product-info">
                  <h3 class="product-name"><?= e($p['nome']) ?></h3>
                  <div class="product-footer">
                    <div class="product-price"><?= preco((float)$p['preco']) ?></div>
                    <a href="<?= BASE_URL ?>pages/produto.php?id=<?= $p['id'] ?>" class="add-to-cart-btn">Ver</a>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>