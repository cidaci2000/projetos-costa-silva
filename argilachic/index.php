<?php
require_once 'includes/functions.php';

$categoria = $_GET['categoria'] ?? 'todos';
$produtos = listarProdutos($categoria);
$categorias = listarCategorias();
$titulo = 'Início';
include 'includes/header.php';
?>

<section class="hero" id="inicio">
    <div class="hero-container">
        <h1>Louças que Contam Histórias</h1>
        <p>Peças de argila sofisticadas, inspiradas na estética praiana e na elegância artesanal.</p>
        <a href="#produtos" class="btn-primario">Explorar Coleção</a>
    </div>
</section>

<section class="secao-sobre" id="sobre">
    <div class="sobre-container">
        <div class="sobre-texto">
            <h2>Nossa História</h2>
            <p>A Argila Chic nasceu da paixão por artesanato sofisticado e sustentável.</p>
            <p>Cada peça é única, feita à mão com técnicas ancestrais e materiais de alta qualidade.</p>
        </div>
        <div class="sobre-imagem" style="overflow:hidden; padding:0;">
            <img src="uploads/produtos/sobre.jpg" alt="Ateliê Argila Chic"
                 style="width:100%; height:100%; object-fit:cover;"
                 onerror="this.style.display='none'; this.parentElement.innerHTML='🏺'; this.parentElement.style.fontSize='5rem'; this.parentElement.style.display='flex'; this.parentElement.style.alignItems='center'; this.parentElement.style.justifyContent='center';">
        </div>
    </div>
</section>

<section class="container-principal" id="produtos">
    <h2 class="secao-titulo">Nossos Produtos</h2>
    <div class="filtros-container">
        <a href="?categoria=todos#produtos" class="filtro-btn <?= $categoria === 'todos' ? 'ativo' : '' ?>">Todos</a>
        <?php foreach ($categorias as $cat): ?>
            <a href="?categoria=<?= $cat['slug'] ?>#produtos"
               class="filtro-btn <?= $categoria === $cat['slug'] ? 'ativo' : '' ?>">
                <?= htmlspecialchars($cat['nome']) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="produtos-grid">
        <?php if (empty($produtos)): ?>
            <p style="grid-column:1/-1; text-align:center;">Nenhum produto encontrado.</p>
        <?php else: foreach ($produtos as $p):
            $preco = precoFinal($p);
            $temPromo = !empty($p['preco_promocional']);
        ?>
            <div class="produto-card">
                <a href="produto.php?id=<?= $p['id'] ?>" class="produto-imagem">
                    <img src="<?= imagemUrl($p['imagem']) ?>" alt="<?= htmlspecialchars($p['nome']) ?>"
                         onerror="this.src='https://via.placeholder.com/400x300/E8D4C4/8B6F47?text=Argila+Chic'">
                </a>
                <div class="produto-info">
                    <h3 class="produto-nome">
                        <?= htmlspecialchars($p['nome']) ?>
                        <?php if ($temPromo): ?><span class="badge-promo">PROMO</span><?php endif; ?>
                    </h3>
                    <p class="produto-descricao"><?= htmlspecialchars($p['descricao']) ?></p>
                    <div class="produto-preco">
                        <?php if ($temPromo): ?>
                            <span class="preco-antigo">R$ <?= number_format($p['preco'], 2, ',', '.') ?></span>
                        <?php endif; ?>
                        R$ <?= number_format($preco, 2, ',', '.') ?>
                    </div>
                    <div class="produto-acoes">
                        <form method="POST" action="carrinho.php" style="flex:1;">
                            <input type="hidden" name="acao" value="adicionar">
                            <input type="hidden" name="produto_id" value="<?= $p['id'] ?>">
                            <button type="submit" class="btn-secundario" style="width:100%;">Adicionar</button>
                        </form>
                        <a href="produto.php?id=<?= $p['id'] ?>" class="btn-info">Detalhes</a>
                    </div>
                </div>
            </div>
        <?php endforeach; endif; ?>
    </div>
</section>

<section class="secao-newsletter" id="contato">
    <div class="newsletter-container">
        <h2>Fique por Dentro das Novidades</h2>
        <p>Receba ofertas exclusivas e atualizações sobre nossas novas coleções</p>
        <form class="newsletter-form" method="POST" action="newsletter.php">
            <input type="email" name="email" placeholder="Seu e-mail" required>
            <button type="submit">Inscrever</button>
        </form>
    </div>
</section>

<?php include 'includes/footer.php'; ?>