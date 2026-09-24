<?php
require_once 'includes/functions.php';
$destaques = getDestaques(6);
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ReyVie - Produtos de Beleza</title>
<style>
/* =========================================================
   REYVIE — STYLESHEET
   ========================================================= */

/* ===== VARIÁVEIS GLOBAIS ===== */
:root {
    --primary-coral:  #e8857d;
    --primary-pink:   #f4a8a0;
    --light-pink:     #f5d5d0;
    --soft-pink:      #fdf6f5;

    --dark-text:      #333333;
    --gray-text:      #777777;
    --muted-text:     #999999;

    --success-start:  #5FA86D;
    --success-end:    #4A8A58;
    --error-start:    #D64545;
    --error-end:      #B83535;

    --shadow:         0 4px 20px rgba(0, 0, 0, 0.08);
    --radius-sm:      8px;
    --radius-md:      10px;
    --radius-lg:      15px;
    --transition:     .2s ease;
}

/* ===== RESET ===== */
* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: var(--soft-pink);
    color: var(--dark-text);
    line-height: 1.6;
}

/* =========================================================
   1. NOTIFICAÇÕES
   ========================================================= */
.notificacao {
    position: fixed;
    top: 80px;
    right: 20px;
    padding: 1rem 1.5rem;
    border-radius: var(--radius-sm);
    color: #fff;
    font-weight: 600;
    z-index: 2000;
    animation: slideIn .3s ease-out;
    box-shadow: var(--shadow);
    max-width: 400px;
}
.notificacao.sucesso {
    background: linear-gradient(135deg, var(--success-start), var(--success-end));
}
.notificacao.erro {
    background: linear-gradient(135deg, var(--error-start), var(--error-end));
}

@keyframes slideIn {
    from { transform: translateX(120%); opacity: 0; }
    to   { transform: translateX(0);    opacity: 1; }
}

/* =========================================================
   2. LAYOUT PRINCIPAL
   ========================================================= */
.home-container {
    display: grid;
    grid-template-columns: 260px 1fr 280px;
    gap: 2rem;
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
    min-height: 100vh;
}

/* =========================================================
   3. SIDEBAR ESQUERDA
   ========================================================= */
.sidebar-left {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}
.logo-circle {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-coral), var(--primary-pink));
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 700;
    font-size: 1.1rem;
    box-shadow: var(--shadow);
}
.beauty-text {
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--dark-text);
    line-height: 1.2;
}
.loading-text {
    font-size: .8rem;
    color: var(--gray-text);
    font-style: italic;
}
.feature-item {
    display: flex;
    align-items: flex-start;
    gap: .6rem;
    font-size: .9rem;
    color: var(--gray-text);
}
.feature-icon {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-coral), var(--primary-pink));
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .7rem;
    flex-shrink: 0;
    margin-top: 2px;
}

/* =========================================================
   4. CONTEÚDO PRINCIPAL
   ========================================================= */
.main-content {
    display: flex;
    flex-direction: column;
    gap: 2.5rem;
}

/* =========================================================
   5. HERO
   ========================================================= */
.hero-section {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    align-items: center;
    background: #fff;
    border-radius: 20px;
    padding: 2.5rem;
    box-shadow: var(--shadow);
}
.hero-text h1 {
    font-size: 2.2rem;
    margin-bottom: 1rem;
    line-height: 1.2;
}
.hero-text p {
    color: var(--gray-text);
    margin-bottom: 1.5rem;
}
.hero-buttons {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}
.hero-image img {
    width: 100%;
    border-radius: var(--radius-lg);
    display: block;
}

/* =========================================================
   6. BOTÕES
   ========================================================= */
.btn {
    border: none;
    border-radius: 50px;
    padding: .8rem 1.6rem;
    font-weight: 600;
    cursor: pointer;
    font-size: .95rem;
    transition: transform var(--transition), box-shadow var(--transition);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .4rem;
}
.btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow);
}
.btn-primary {
    background: linear-gradient(135deg, var(--primary-coral), var(--primary-pink));
    color: #fff;
}
.btn-secondary {
    background: #fff;
    color: var(--dark-text);
    border: 2px solid var(--light-pink);
}
.play-icon { font-size: .8rem; }

/* =========================================================
   7. PRODUTOS
   ========================================================= */
.products-section {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}
.products-description {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    align-items: center;
}
.products-description h2 { font-size: 1.6rem; }
.products-description p { color: var(--gray-text); }

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1.5rem;
}
.product-card {
    background: #fff;
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow);
    transition: transform var(--transition);
    display: flex;
    flex-direction: column;
}
.product-card:hover { transform: translateY(-4px); }
.product-image {
    width: 100%;
    aspect-ratio: 1 / 1;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 4rem;
    background: linear-gradient(135deg, var(--light-pink), var(--primary-pink));
    overflow: hidden;
}
.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: var(--radius-md);
    display: block;
}
.product-info {
    padding: 1rem;
    flex: 1;
}
.product-name {
    font-weight: 600;
    font-size: .95rem;
    margin-bottom: .4rem;
}
.product-price {
    color: var(--primary-coral);
    font-weight: 700;
    font-size: 1.05rem;
}

/* =========================================================
   8. SIDEBAR DIREITA
   ========================================================= */
.sidebar-right {
    background: #fff;
    border-radius: 20px;
    padding: 1.8rem;
    box-shadow: var(--shadow);
    display: flex;
    flex-direction: column;
    gap: 1rem;
    align-self: start;
}
.sidebar-right h3 { font-size: 1.3rem; line-height: 1.3; }
.sidebar-right p { color: var(--gray-text); font-size: .9rem; }
.sidebar-image img {
    width: 100%;
    border-radius: var(--radius-lg);
    display: block;
}

/* =========================================================
   9. IMAGEM PREVIEW (ADMIN)
   ========================================================= */
.img-preview {
    width: 120px;
    height: 120px;
    object-fit: cover;
    border-radius: var(--radius-md);
    border: 2px solid var(--light-pink);
    margin-top: .5rem;
    display: block;
}

/* =========================================================
   10. TABELAS ADMIN
   ========================================================= */
.tabela-admin {
    width: 100%;
    background: #fff;
    border-radius: var(--radius-lg);
    overflow: hidden;
    border-collapse: collapse;
    box-shadow: var(--shadow);
}
.tabela-admin th,
.tabela-admin td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid var(--light-pink);
}
.tabela-admin th {
    background: linear-gradient(135deg, var(--primary-coral), var(--primary-pink));
    color: #fff;
    font-weight: 600;
}
.tabela-admin tr:last-child td { border-bottom: none; }
.tabela-admin tbody tr { transition: background var(--transition); }
.tabela-admin tbody tr:hover { background: var(--soft-pink); }

/* =========================================================
   11. CARDS ADMIN
   ========================================================= */
.admin-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}
.admin-card {
    background: #fff;
    padding: 1.5rem;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow);
    border-left: 4px solid var(--primary-coral);
    transition: transform var(--transition);
}
.admin-card:hover { transform: translateY(-3px); }
.admin-card h3 {
    font-size: .85rem;
    color: var(--gray-text);
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: .5rem;
}
.admin-card .valor {
    font-size: 2rem;
    font-weight: 700;
    color: var(--dark-text);
}

/* =========================================================
   12. GALERIA DE MINIATURAS
   ========================================================= */
.galeria-miniaturas {
    display: flex;
    gap: .5rem;
    margin-top: .75rem;
    flex-wrap: wrap;
}
.galeria-miniaturas img {
    width: 70px;
    height: 70px;
    object-fit: cover;
    border-radius: var(--radius-sm);
    cursor: pointer;
    border: 2px solid transparent;
    transition: border var(--transition), transform var(--transition);
}
.galeria-miniaturas img:hover {
    border-color: var(--primary-coral);
    transform: scale(1.05);
}

/* =========================================================
   13. PÁGINA DE DETALHE DO PRODUTO
   ========================================================= */
.produto-detalhe {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    background: #fff;
    padding: 2rem;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow);
}
.produto-detalhe .imagem-principal {
    width: 100%;
    aspect-ratio: 1 / 1;
    border-radius: var(--radius-lg);
    overflow: hidden;
    background: linear-gradient(135deg, var(--light-pink), var(--primary-pink));
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 6rem;
}
.produto-detalhe .imagem-principal img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* =========================================================
   14. RESPONSIVO
   ========================================================= */
@media (max-width: 1100px) {
    .home-container { grid-template-columns: 220px 1fr; }
    .sidebar-right { display: none; }
}

@media (max-width: 768px) {
    .home-container {
        grid-template-columns: 1fr;
        padding: 1rem;
    }
    .sidebar-left {
        flex-direction: row;
        flex-wrap: wrap;
        align-items: center;
    }
    .hero-section { grid-template-columns: 1fr; }
    .products-description { grid-template-columns: 1fr; }
    .produto-detalhe { grid-template-columns: 1fr; gap: 1.5rem; }
    .tabela-admin { font-size: .85rem; }
    .tabela-admin th, .tabela-admin td { padding: .6rem; }
    .notificacao {
        left: 10px;
        right: 10px;
        max-width: none;
    }
}
</style>
</head>

<body>

<?php if ($flash): ?>
    <div class="notificacao <?= $flash['tipo'] ?>"><?= e($flash['msg']) ?></div>
<?php endif; ?>

<div class="home-container">
    <!-- SIDEBAR ESQUERDA -->
    <aside class="sidebar-left">
        <div class="logo-circle">ReyVie</div>
        <div class="beauty-text">Produtos<br>de Beleza</div>
        <div class="loading-text">Carregando...</div>

        <div class="feature-item"><div class="feature-icon">✓</div><div class="feature-text">Produtos de alta qualidade</div></div>
        <div class="feature-item"><div class="feature-icon">✓</div><div class="feature-text">Maquiagem nacional e internacional de todos os tipos</div></div>
        <div class="feature-item"><div class="feature-icon">✓</div><div class="feature-text">Todas as marcas possíveis (incluindo as de celebridades...)</div></div>

        <div style="margin-top:1.5rem; display:flex; flex-direction:column; gap:.5rem;">
            <a href="produtos.php" class="btn btn-secondary" style="text-align:center;">🛍️ Produtos</a>
            <a href="carrinho.php" class="btn btn-secondary" style="text-align:center;">🛒 Carrinho (<?= countCarrinho() ?>)</a>
            <?php if (isLogged()): ?>
                <?php if (isAdmin()): ?>
                    <a href="admin/index.php" class="btn btn-secondary" style="text-align:center;">⚙️ Admin</a>
                <?php endif; ?>
                <a href="logout.php" class="btn btn-secondary" style="text-align:center;">Sair</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-secondary" style="text-align:center;">Entrar</a>
            <?php endif; ?>
        </div>
    </aside>

    <!-- CONTEÚDO PRINCIPAL -->
    <main class="main-content">
        <section class="hero-section">
            <div class="hero-text">
                <h1>Deixe sua maquiagem brilhar.</h1>
                <p>Experimente nossos melhores produtos do mercado, para todos os tons de pele, do mais claro ao mais escuro.</p>
                <div class="hero-buttons">
                    <a href="produtos.php" class="btn btn-primary">Comprar Agora →</a>
                    <button class="btn btn-secondary"><span class="play-icon">▶</span> Como usar</button>
                </div>
            </div>
            <div class="hero-image">
                <img src="https://images.unsplash.com/photo-1596704017254-9b121068fb31?w=800" alt="Modelo maquiagem">
            </div>
        </section>

        <section class="products-section">
            <div class="products-description">
                <div><h2>Nossas bases e corretivos</h2></div>
                <div><p>Desperte sua beleza interior e realce sua beleza natural com nossa requintada coleção de maquiagem.</p></div>
            </div>

            <div class="products-grid">
                <?php foreach (array_slice($destaques, 0, 3) as $p):
                    $preco = precoFinal($p); $promo = !empty($p['preco_promocional']); ?>
                    <div class="product-card">
                        <a href="produto.php?id=<?= $p['id'] ?>" style="text-decoration:none;color:inherit;">
                            <div class="product-image">
                                <?php if ($p['imagem']): ?>
                                    <img src="<?= imagemUrl($p['imagem']) ?>" alt="<?= e($p['nome']) ?>" loading="lazy">
                                <?php else: ?>
                                    <?= $p['emoji'] ?>
                                <?php endif; ?>
                            </div>
                            <div class="product-info">
                                <div class="product-name"><?= e($p['nome']) ?></div>
                                <div class="product-price">
                                    <?php if ($promo): ?>
                                        <span style="text-decoration:line-through;color:#999;font-size:.85rem;">
                                            R$ <?= number_format($p['preco'], 2, ',', '.') ?>
                                        </span>
                                    <?php endif; ?>
                                    R$ <?= number_format($preco, 2, ',', '.') ?>
                                </div>
                            </div>
                        </a>
                        <form method="POST" action="carrinho.php" style="padding:0 1rem 1rem;">
                            <input type="hidden" name="acao" value="adicionar">
                            <input type="hidden" name="produto_id" value="<?= $p['id'] ?>">
                            <button class="btn btn-primary" style="width:100%;font-size:.85rem;padding:.6rem;">Adicionar</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>

            <div style="margin-top:2rem;text-align:center;">
                <h3 style="font-size:1.5rem;margin-bottom:1rem;">Produtos de Beleza Naturais</h3>
                <div class="products-grid">
                    <?php foreach (array_slice($destaques, 3, 3) as $p):
                        $preco = precoFinal($p); ?>
                        <div class="product-card">
                            <a href="produto.php?id=<?= $p['id'] ?>" style="text-decoration:none;color:inherit;">
                                <div class="product-image">
                                    <?php if ($p['imagem']): ?>
                                        <img src="<?= imagemUrl($p['imagem']) ?>" alt="<?= e($p['nome']) ?>" loading="lazy">
                                    <?php else: ?>
                                        <?= $p['emoji'] ?>
                                    <?php endif; ?>
                                </div>
                                <div class="product-info">
                                    <div class="product-name"><?= e($p['nome']) ?></div>
                                    <div class="product-price">R$ <?= number_format($preco, 2, ',', '.') ?></div>
                                </div>
                            </a>
                            <form method="POST" action="carrinho.php" style="padding:0 1rem 1rem;">
                                <input type="hidden" name="acao" value="adicionar">
                                <input type="hidden" name="produto_id" value="<?= $p['id'] ?>">
                                <button class="btn btn-primary" style="width:100%;font-size:.85rem;padding:.6rem;">Adicionar</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>

    <!-- SIDEBAR DIREITA -->
    <aside class="sidebar-right">
        <h3>Deixe sua maquiagem brilhar.</h3>
        <p>Nossa incrível experiência com bases multi-opções é única e inexplicável, para todos os tipos de pele do mundo!</p>
        <div class="sidebar-image">
            <img src="https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=600" alt="Modelo 2">
        </div>
    </aside>
</div>

</body>
</html>