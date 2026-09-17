<?php
$titulo = 'Catálogo';
require_once __DIR__ . '/../includes/header.php';

// ---------- Filtros ----------
$categorias = $_GET['categorias'] ?? [];
$precoMin   = (float)($_GET['preco_min'] ?? 0);
$precoMax   = (float)($_GET['preco_max'] ?? 9999);
$busca      = trim($_GET['busca'] ?? '');
$ordem      = $_GET['ordem'] ?? 'destaque';

$sql = "SELECT p.*, c.slug AS categoria_slug 
        FROM produtos p 
        LEFT JOIN categorias c ON c.id = p.categoria_id 
        WHERE p.preco BETWEEN :min AND :max";
$params = [':min' => $precoMin, ':max' => $precoMax];

if (!empty($categorias)) {
    $ph = [];
    foreach ($categorias as $i => $cat) {
        $ph[] = ":cat$i";
        $params[":cat$i"] = $cat;
    }
    $sql .= " AND c.slug IN (" . implode(',', $ph) . ")";
}

if ($busca !== '') {
    $sql .= " AND (p.nome LIKE :busca OR p.notas LIKE :busca)";
    $params[':busca'] = "%$busca%";
}

switch ($ordem) {
    case 'preco-asc':  $sql .= " ORDER BY p.preco ASC"; break;
    case 'preco-desc': $sql .= " ORDER BY p.preco DESC"; break;
    case 'nome':       $sql .= " ORDER BY p.nome ASC"; break;
    default:           $sql .= " ORDER BY p.destaque DESC, p.id ASC";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$produtos = $stmt->fetchAll();

// ---------- Favoritos do usuário ----------
$favoritos = [];
if (usuarioLogado()) {
    $stmt = $pdo->prepare("SELECT produto_id FROM favoritos WHERE usuario_id = ?");
    $stmt->execute([$_SESSION['usuario_id']]);
    $favoritos = array_column($stmt->fetchAll(), 'produto_id');
}
?>

<section class="catalog-hero">
  <div class="container">
    <h1>Catálogo de Fragrâncias</h1>
    <p>Explore nossa coleção de perfumes exclusivos</p>
  </div>
</section>

<section class="catalog-section">
  <div class="container">
    <div class="catalog-layout">
      <aside class="catalog-sidebar">
        <h3 class="sidebar-title">Filtros</h3>
        <form method="get" id="filtros-form">
          <div class="filter-group">
            <div class="filter-label">Categorias</div>
            <div class="filter-options">
              <?php
              $cats = $pdo->query("SELECT * FROM categorias")->fetchAll();
              foreach ($cats as $c):
                $checked = in_array($c['slug'], $categorias) ? 'checked' : '';
              ?>
                <label class="filter-option">
                  <input type="checkbox" name="categorias[]" value="<?= e($c['slug']) ?>" <?= $checked ?> />
                  <?= e($c['nome']) ?>
                </label>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="filter-group">
            <div class="filter-label">Faixa de Preço</div>
            <div class="price-range">
              <input type="number" name="preco_min" class="price-input" value="<?= $precoMin ?>" />
              <span>até</span>
              <input type="number" name="preco_max" class="price-input" value="<?= $precoMax ?>" />
            </div>
          </div>

          <input type="hidden" name="busca" value="<?= e($busca) ?>" />
          <input type="hidden" name="ordem" value="<?= e($ordem) ?>" />
          <button type="submit" class="btn btn-gold" style="width:100%;margin-top:8px;">Aplicar Filtros</button>
          <a href="<?= BASE_URL ?>pages/catalogo.php" class="btn btn-outline" style="width:100%;margin-top:8px;">Limpar</a>
        </form>
      </aside>

      <div class="catalog-main">
        <div class="catalog-toolbar">
          <div class="catalog-results"><?= count($produtos) ?> perfume(s) encontrado(s)</div>
          <form method="get" style="display:flex;gap:10px;flex-wrap:wrap;">
            <?php foreach ($categorias as $c): ?>
              <input type="hidden" name="categorias[]" value="<?= e($c) ?>" />
            <?php endforeach; ?>
            <input type="hidden" name="preco_min" value="<?= $precoMin ?>" />
            <input type="hidden" name="preco_max" value="<?= $precoMax ?>" />
            <div class="catalog-search">
              <input type="text" name="busca" placeholder="Buscar perfumes..." value="<?= e($busca) ?>" />
              <button type="submit">🔍</button>
            </div>
            <select class="catalog-sort" name="ordem" onchange="this.form.submit()">
              <option value="destaque" <?= $ordem==='destaque'?'selected':'' ?>>Destaque</option>
              <option value="preco-asc" <?= $ordem==='preco-asc'?'selected':'' ?>>Menor Preço</option>
              <option value="preco-desc" <?= $ordem==='preco-desc'?'selected':'' ?>>Maior Preço</option>
              <option value="nome" <?= $ordem==='nome'?'selected':'' ?>>Nome A-Z</option>
            </select>
          </form>
        </div>

        <div class="products-grid">
          <?php if (empty($produtos)): ?>
            <div class="no-results">
              <h3>Nenhum perfume encontrado</h3>
              <p>Tente ajustar os filtros.</p>
            </div>
          <?php else: ?>
            <?php foreach ($produtos as $p): 
              $isFav = in_array($p['id'], $favoritos);
            ?>
              <div class="product-card">
                <div class="product-image">
                  <img src="<?= e($p['imagem']) ?>" alt="<?= e($p['nome']) ?>" loading="lazy" />
                  <div class="product-badges">
                    <?php if ($p['destaque']): ?><span class="badge badge-gold">⭐ Destaque</span><?php endif; ?>
                    <?php if ($p['lancamento']): ?><span class="badge badge-new">✨ Lançamento</span><?php endif; ?>
                    <?php if ($p['oferta']): ?><span class="badge badge-sale">🔥 Oferta</span><?php endif; ?>
                  </div>
                  <form method="post" action="<?= BASE_URL ?>actions/toggle_favorito.php" style="display:inline;">
                    <input type="hidden" name="produto_id" value="<?= $p['id'] ?>" />
                    <button type="submit" class="product-wishlist <?= $isFav ? 'active' : '' ?>">
                      <?= $isFav ? '❤️' : '♡' ?>
                    </button>
                  </form>
                </div>
                <div class="product-info">
                  <div class="product-brand">Le Parfum</div>
                  <h3 class="product-name"><?= e($p['nome']) ?></h3>
                  <p class="product-notes"><?= e($p['notas']) ?></p>
                  <div class="product-footer">
                    <div class="product-price">
                      <?php if ($p['preco_antigo']): ?>
                        <span class="product-price-old"><?= preco((float)$p['preco_antigo']) ?></span>
                      <?php endif; ?>
                      <?= preco((float)$p['preco']) ?>
                    </div>
                    <form method="post" action="<?= BASE_URL ?>actions/add_carrinho.php">
                      <input type="hidden" name="produto_id" value="<?= $p['id'] ?>" />
                      <button type="submit" class="add-to-cart-btn">+ Adicionar</button>
                    </form>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>