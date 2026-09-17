<?php
$titulo = 'Início';
require_once __DIR__ . '/includes/header.php';

$destaques   = $pdo->query("SELECT * FROM produtos WHERE destaque = 1 ORDER BY id ASC LIMIT 4")->fetchAll();
$lancamentos = $pdo->query("SELECT * FROM produtos WHERE lancamento = 1 ORDER BY id ASC LIMIT 4")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Le Parfum - Início</title>

  <!-- ============================================================
       CSS INLINE (para testar sem depender do arquivo externo)
       ============================================================ -->
  <style>
    :root {
      --gold: #c9a96a;
      --gold-light: #e0c88a;
      --gold-dark: #a88a4a;
      --black: #0d0d0d;
      --dark: #1a1a1a;
      --gray: #6b6b6b;
      --gray-light: #f5f5f5;
      --white: #ffffff;
      --red: #e74c3c;
      --green: #27ae60;
      --radius: 8px;
      --radius-lg: 16px;
      --shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
      --shadow-lg: 0 10px 40px rgba(0, 0, 0, 0.15);
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
      background: var(--white);
      color: var(--black);
      line-height: 1.6;
    }

    img { max-width: 100%; display: block; }
    a { text-decoration: none; color: inherit; }
    button { cursor: pointer; border: none; font-family: inherit; }

    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
    }

    /* NAVBAR */
    .navbar {
      position: sticky;
      top: 0;
      z-index: 1000;
      background: rgba(255,255,255,0.98);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid rgba(0,0,0,0.06);
    }
    .navbar-inner {
      display: flex;
      align-items: center;
      justify-content: space-between;
      height: 72px;
      gap: 20px;
    }
    .navbar-logo {
      display: flex;
      flex-direction: column;
      line-height: 1.1;
    }
    .logo-text {
      font-size: 1.4rem;
      font-weight: 800;
      letter-spacing: 2px;
      color: var(--black);
      text-transform: uppercase;
    }
    .logo-sub {
      font-size: 0.6rem;
      letter-spacing: 4px;
      color: var(--gold);
      text-transform: uppercase;
      font-weight: 600;
    }
    .navbar-nav {
      display: flex;
      gap: 28px;
      align-items: center;
    }
    .navbar-nav a {
      font-size: 0.9rem;
      font-weight: 500;
      color: var(--dark);
      position: relative;
      padding: 4px 0;
      transition: color 0.3s;
    }
    .navbar-nav a:hover { color: var(--gold); }
    .navbar-nav a::after {
      content: '';
      position: absolute;
      bottom: 0; left: 0;
      width: 0; height: 2px;
      background: var(--gold);
      transition: width 0.3s;
    }
    .navbar-nav a:hover::after { width: 100%; }

    .navbar-actions { display: flex; align-items: center; gap: 8px; }
    .icon-btn {
      position: relative;
      width: 44px; height: 44px;
      border-radius: 50%;
      background: transparent;
      font-size: 1.3rem;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: background 0.3s;
      text-decoration: none;
    }
    .icon-btn:hover { background: var(--gray-light); }
    .cart-count {
      position: absolute;
      top: 2px; right: 2px;
      background: var(--gold);
      color: var(--white);
      font-size: 0.65rem;
      font-weight: 700;
      width: 18px; height: 18px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    /* BOTÕES */
    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      padding: 12px 28px;
      border-radius: var(--radius);
      font-weight: 600;
      font-size: 0.95rem;
      transition: all 0.3s;
      border: 2px solid transparent;
    }
    .btn-gold {
      background: var(--gold);
      color: var(--white);
    }
    .btn-gold:hover {
      background: var(--gold-dark);
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(201,169,106,0.4);
    }
    .btn-outline {
      background: transparent;
      border-color: var(--gold);
      color: var(--gold);
    }

    /* HERO */
    .catalog-hero {
      background: linear-gradient(135deg, var(--black) 0%, var(--dark) 100%);
      color: var(--white);
      padding: 80px 0;
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    .catalog-hero::before {
      content: '';
      position: absolute;
      top: -50%; right: -10%;
      width: 500px; height: 500px;
      background: radial-gradient(circle, rgba(201,169,106,0.15) 0%, transparent 70%);
      border-radius: 50%;
    }
    .catalog-hero h1 {
      font-size: 2.6rem;
      font-weight: 800;
      letter-spacing: 1px;
      margin-bottom: 12px;
      position: relative;
    }
    .catalog-hero p {
      color: var(--gold-light);
      font-size: 1.1rem;
      position: relative;
      margin-bottom: 24px;
    }

    /* SEÇÕES */
    .catalog-section {
      padding: 60px 0;
      background: var(--gray-light);
    }
    .catalog-section.white { background: var(--white); }
    .section-title {
      font-size: 1.6rem;
      font-weight: 800;
      margin-bottom: 28px;
      color: var(--black);
    }

    /* GRID DE PRODUTOS */
    .products-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
      gap: 24px;
    }
    .product-card {
      background: var(--white);
      border-radius: var(--radius-lg);
      overflow: hidden;
      box-shadow: var(--shadow);
      transition: all 0.3s;
      display: flex;
      flex-direction: column;
    }
    .product-card:hover {
      transform: translateY(-6px);
      box-shadow: var(--shadow-lg);
    }
    .product-image {
      position: relative;
      aspect-ratio: 1;
      overflow: hidden;
      background: var(--gray-light);
    }
    .product-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.5s;
    }
    .product-card:hover .product-image img { transform: scale(1.08); }

    .product-badges {
      position: absolute;
      top: 12px; left: 12px;
      display: flex;
      flex-direction: column;
      gap: 6px;
    }
    .badge {
      font-size: 0.7rem;
      font-weight: 700;
      padding: 4px 10px;
      border-radius: 20px;
      color: var(--white);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .badge-gold { background: var(--gold); }
    .badge-new { background: var(--green); }
    .badge-sale { background: var(--red); }

    .product-info { padding: 18px; display: flex; flex-direction: column; flex: 1; }
    .product-brand {
      font-size: 0.7rem;
      text-transform: uppercase;
      letter-spacing: 2px;
      color: var(--gold);
      font-weight: 700;
      margin-bottom: 4px;
    }
    .product-name {
      font-size: 1.05rem;
      font-weight: 700;
      color: var(--black);
      margin-bottom: 6px;
    }
    .product-notes {
      font-size: 0.8rem;
      color: var(--gray);
      margin-bottom: 16px;
      flex: 1;
    }
    .product-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 10px;
      margin-top: auto;
    }
    .product-price {
      font-size: 1.05rem;
      font-weight: 800;
      color: var(--black);
      display: flex;
      flex-direction: column;
      line-height: 1.2;
    }
    .product-price-old {
      font-size: 0.75rem;
      color: var(--gray);
      text-decoration: line-through;
      font-weight: 400;
    }
    .add-to-cart-btn {
      background: var(--black);
      color: var(--white);
      padding: 10px 16px;
      border-radius: var(--radius);
      font-size: 0.8rem;
      font-weight: 700;
      white-space: nowrap;
      text-decoration: none;
      display: inline-block;
      transition: all 0.3s;
    }
    .add-to-cart-btn:hover {
      background: var(--gold);
      transform: translateY(-2px);
    }

    /* CTA */
    .cta-section {
      background: var(--black);
      color: var(--white);
      padding: 80px 0;
      text-align: center;
    }
    .cta-section h2 {
      font-size: 2rem;
      margin-bottom: 12px;
    }
    .cta-section p {
      color: var(--gold-light);
      margin-bottom: 28px;
      font-size: 1.05rem;
    }

    /* TOAST */
    .toast {
      position: fixed;
      top: 90px; right: 24px;
      background: var(--dark);
      color: var(--white);
      padding: 14px 20px;
      border-radius: var(--radius);
      font-size: 0.9rem;
      font-weight: 500;
      box-shadow: var(--shadow-lg);
      z-index: 3000;
      border-left: 4px solid var(--gold);
      animation: toastIn 0.4s ease;
      max-width: 340px;
    }
    .toast-success { background: var(--green); border-left-color: #1e7e34; }
    .toast-error { background: var(--red); border-left-color: #a71d2a; }
    @keyframes toastIn {
      from { opacity: 0; transform: translateX(100%); }
      to { opacity: 1; transform: translateX(0); }
    }

    /* FOOTER */
    .footer {
      background: var(--black);
      color: var(--white);
      padding: 60px 0 30px;
    }
    .footer-grid {
      display: grid;
      grid-template-columns: 2fr 1fr 1fr 1fr;
      gap: 40px;
      margin-bottom: 40px;
    }
    .footer-logo {
      font-size: 1.4rem;
      font-weight: 800;
      letter-spacing: 2px;
      color: var(--gold);
      text-transform: uppercase;
      margin-bottom: 12px;
    }
    .footer-desc {
      color: #999;
      font-size: 0.9rem;
      margin-bottom: 20px;
    }
    .footer-social { display: flex; gap: 12px; }
    .footer-social a {
      width: 40px; height: 40px;
      border-radius: 50%;
      background: rgba(255,255,255,0.08);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.1rem;
      transition: all 0.3s;
    }
    .footer-social a:hover { background: var(--gold); transform: translateY(-3px); }
    .footer-col h4 {
      font-size: 0.9rem;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      margin-bottom: 18px;
      color: var(--gold);
    }
    .footer-col ul {
      list-style: none;
      display: flex;
      flex-direction: column;
      gap: 10px;
    }
    .footer-col a {
      color: #999;
      font-size: 0.9rem;
      transition: color 0.3s;
    }
    .footer-col a:hover { color: var(--gold); }
    .footer-bottom {
      border-top: 1px solid rgba(255,255,255,0.1);
      padding-top: 24px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 12px;
      color: #666;
      font-size: 0.85rem;
    }

    /* RESPONSIVO */
    @media (max-width: 768px) {
      .navbar-nav { display: none; }
      .catalog-hero h1 { font-size: 1.8rem; }
      .products-grid { grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 14px; }
      .product-info { padding: 12px; }
      .product-name { font-size: 0.9rem; }
      .product-notes { font-size: 0.72rem; }
      .add-to-cart-btn { padding: 8px 10px; font-size: 0.72rem; }
      .footer-grid { grid-template-columns: 1fr; gap: 28px; }
      .footer-bottom { flex-direction: column; text-align: center; }
    }
  </style>
</head>
<body>

<!-- NAVBAR -->


<!-- FLASH -->
<?php if ($flash): ?>
  <div class="toast toast-<?php echo e($flash['tipo']); ?>" id="toast-flash">
    <?php echo e($flash['msg']); ?>
  </div>
<?php endif; ?>

<!-- HERO -->
<section class="catalog-hero">
  <div class="container">
    <h1>Bem-vindo à Le Parfum</h1>
    <p>Descubra fragrâncias exclusivas que marcam presença</p>
    <a href="<?php echo BASE_URL; ?>pages/catalogo.php" class="btn btn-gold">
      Ver Catálogo Completo
    </a>
  </div>
</section>

<!-- DESTAQUES -->
<section class="catalog-section">
  <div class="container">
    <h2 class="section-title">⭐ Destaques</h2>
    <div class="products-grid">
      <?php if (empty($destaques)): ?>
        <p style="color:#6b6b6b;">Nenhum destaque cadastrado.</p>
      <?php else: ?>
        <?php foreach ($destaques as $p): ?>
          <div class="product-card">
            <div class="product-image">
              <img src="<?php echo e($p['imagem']); ?>" alt="<?php echo e($p['nome']); ?>" loading="lazy" />
              <div class="product-badges">
                <span class="badge badge-gold">⭐ Destaque</span>
              </div>
            </div>
            <div class="product-info">
              <div class="product-brand">Le Parfum</div>
              <h3 class="product-name"><?php echo e($p['nome']); ?></h3>
              <p class="product-notes"><?php echo e($p['notas']); ?></p>
              <div class="product-footer">
                <div class="product-price"><?php echo preco((float)$p['preco']); ?></div>
                <a href="<?php echo BASE_URL; ?>pages/produto.php?id=<?php echo (int)$p['id']; ?>" class="add-to-cart-btn">
                  Ver
                </a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- LANÇAMENTOS -->
<section class="catalog-section white">
  <div class="container">
    <h2 class="section-title">✨ Lançamentos</h2>
    <div class="products-grid">
      <?php if (empty($lancamentos)): ?>
        <p style="color:#6b6b6b;">Nenhum lançamento cadastrado.</p>
      <?php else: ?>
        <?php foreach ($lancamentos as $p): ?>
          <div class="product-card">
            <div class="product-image">
              <img src="<?php echo e($p['imagem']); ?>" alt="<?php echo e($p['nome']); ?>" loading="lazy" />
              <div class="product-badges">
                <span class="badge badge-new">✨ Lançamento</span>
              </div>
            </div>
            <div class="product-info">
              <div class="product-brand">Le Parfum</div>
              <h3 class="product-name"><?php echo e($p['nome']); ?></h3>
              <p class="product-notes"><?php echo e($p['notas']); ?></p>
              <div class="product-footer">
                <div class="product-price"><?php echo preco((float)$p['preco']); ?></div>
                <a href="<?php echo BASE_URL; ?>pages/produto.php?id=<?php echo (int)$p['id']; ?>" class="add-to-cart-btn">
                  Ver
                </a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta-section">
  <div class="container">
    <h2>Encontre sua fragrância ideal</h2>
    <p>Explore nossa coleção completa com mais de 15 perfumes exclusivos</p>
    <a href="<?php echo BASE_URL; ?>pages/catalogo.php" class="btn btn-gold">Explorar Catálogo</a>
  </div>
</section>

<!-- FOOTER -->
<footer class="footer">
  <div class="container">
    <div class="footer-grid">
      <div>
        <div class="footer-logo">Le Parfum</div>
        <p class="footer-desc">Fragrâncias exclusivas para quem busca sofisticação e elegância.</p>
        <div class="footer-social">
          <a href="#">📱</a><a href="#">📷</a><a href="#">🐦</a>
        </div>
      </div>
      <div class="footer-col">
        <h4>Institucional</h4>
        <ul>
          <li><a href="<?php echo BASE_URL; ?>pages/sobre.php">Sobre Nós</a></li>
          <li><a href="#">Blog</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Atendimento</h4>
        <ul>
          <li><a href="<?php echo BASE_URL; ?>pages/contato.php">Contato</a></li>
          <li><a href="#">FAQ</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Minha Conta</h4>
        <ul>
          <li><a href="<?php echo BASE_URL; ?>pages/perfil.php">Meus Pedidos</a></li>
          <li><a href="<?php echo BASE_URL; ?>pages/perfil.php#favoritos">Favoritos</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <p>© <?php echo date('Y'); ?> Le Parfum. Todos os direitos reservados.</p>
      <p>💳 Parcelamos em até 6x sem juros</p>
    </div>
  </div>
</footer>

<script>
  // Auto-remove o toast flash
  const flash = document.getElementById('toast-flash');
  if (flash) {
    setTimeout(() => {
      flash.style.transition = 'opacity 0.4s, transform 0.4s';
      flash.style.opacity = '0';
      flash.style.transform = 'translateX(100%)';
      setTimeout(() => flash.remove(), 400);
    }, 3500);
  }
</script>
</body>
</html>