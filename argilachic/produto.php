<?php
require_once 'includes/functions.php';

$id = (int)($_GET['id'] ?? 0);
$produto = buscarProduto($id);
if (!$produto) redirecionar('index.php');

$galeria = galeriaProduto($produto);
$preco = precoFinal($produto);
$temPromo = !empty($produto['preco_promocional']);
$titulo = $produto['nome'];
include 'includes/header.php';
?>
<div class="container-principal">
    <div class="produto-detalhe">
        <div>
            <div class="imagem-principal">
                <img id="img-principal" src="<?= imagemUrl($produto['imagem']) ?>"
                     alt="<?= htmlspecialchars($produto['nome']) ?>"
                     onerror="this.src='https://via.placeholder.com/600x600/E8D4C4/8B6F47?text=Argila+Chic'">
            </div>
            <?php if (!empty($galeria)): ?>
                <div class="galeria-miniaturas">
                    <img src="<?= imagemUrl($produto['imagem']) ?>"
                         onclick="document.getElementById('img-principal').src=this.src">
                    <?php foreach ($galeria as $img): ?>
                        <img src="<?= imagemUrl($img) ?>"
                             onclick="document.getElementById('img-principal').src=this.src">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <div>
            <h1 style="color:var(--cor-header); margin-bottom:.5rem;">
                <?= htmlspecialchars($produto['nome']) ?>
                <?php if ($temPromo): ?><span class="badge-promo">PROMO</span><?php endif; ?>
            </h1>
            <p style="color:#888; margin-bottom:1rem;">
                Categoria: <?= htmlspecialchars($produto['categoria_nome']) ?> |
                Estoque: <?= (int)$produto['estoque'] ?> unidades
            </p>
            <div style="font-size:2rem; color:var(--cor-primaria); font-weight:700; margin-bottom:1rem;">
                <?php if ($temPromo): ?>
                    <span class="preco-antigo">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></span>
                <?php endif; ?>
                R$ <?= number_format($preco, 2, ',', '.') ?>
            </div>
            <p style="margin-bottom:2rem; line-height:1.8;">
                <?= nl2br(htmlspecialchars($produto['descricao_longa'] ?? $produto['descricao'])) ?>
            </p>

            <?php if ($produto['estoque'] > 0): ?>
                <form method="POST" action="carrinho.php" style="display:flex; gap:1rem; align-items:center;">
                    <input type="hidden" name="acao" value="adicionar">
                    <input type="hidden" name="produto_id" value="<?= $produto['id'] ?>">
                    <input type="number" name="quantidade" value="1" min="1" max="<?= $produto['estoque'] ?>"
                           style="width:80px; padding:.75rem; border:2px solid #E8E3DD; border-radius:8px;">
                    <button type="submit" class="btn-primario" style="flex:1;">Adicionar ao Carrinho</button>
                </form>
            <?php else: ?>
                <p style="color:var(--cor-erro); font-weight:600;">Produto esgotado</p>
            <?php endif; ?>

            <div style="margin-top:2rem; padding:1rem; background:var(--cor-fundo); border-radius:8px;">
                <p>🚚 Frete grátis em compras acima de R$ 100,00</p>
                <p>🔄 Troca em até 30 dias</p>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>