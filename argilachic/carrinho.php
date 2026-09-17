<?php
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    if ($acao === 'adicionar') {
        adicionarAoCarrinho((int)$_POST['produto_id'], (int)($_POST['quantidade'] ?? 1));
        flash('sucesso', 'Produto adicionado ao carrinho!');
        redirecionar($_SERVER['HTTP_REFERER'] ?? 'index.php');
    }
    if ($acao === 'atualizar') {
        atualizarQuantidade((int)$_POST['item_id'], (int)$_POST['quantidade']);
        redirecionar('carrinho.php');
    }
    if ($acao === 'remover') {
        removerDoCarrinho((int)$_POST['item_id']);
        flash('sucesso', 'Produto removido.');
        redirecionar('carrinho.php');
    }
    if ($acao === 'finalizar') {
        if (!isLogado()) {
            flash('erro', 'Faça login para finalizar.');
            redirecionar('login.php');
        }
        $numero = criarPedido($_SESSION['usuario_id']);
        if ($numero) {
            flash('sucesso', "Pedido $numero realizado com sucesso!");
            redirecionar('rastreamento.php?pedido=' . urlencode($numero));
        } else {
            flash('erro', 'Erro ao processar pedido.');
            redirecionar('carrinho.php');
        }
    }
}

$totais = calcularTotais();
$titulo = 'Carrinho';
include 'includes/header.php';
?>
<div class="container-principal">
    <h2 class="secao-titulo">Seu Carrinho</h2>

    <?php if (empty($totais['itens'])): ?>
        <div class="carrinho-vazio" style="background:#fff; padding:3rem; border-radius:12px; text-align:center;">
            <p>Seu carrinho está vazio</p>
            <a href="index.php#produtos" class="btn-primario" style="margin-top:1rem;">Ver Produtos</a>
        </div>
    <?php else: ?>
        <div class="carrinho-itens">
            <?php foreach ($totais['itens'] as $item): ?>
                <div class="carrinho-item">
                    <img src="<?= imagemUrl($item['imagem']) ?>"
                         style="width:70px;height:70px;object-fit:cover;border-radius:8px;margin-right:1rem;"
                         onerror="this.src='https://via.placeholder.com/70/E8D4C4/8B6F47?text=🏺'">
                    <div class="carrinho-item-info">
                        <div class="carrinho-item-nome"><?= htmlspecialchars($item['nome']) ?></div>
                        <div class="carrinho-item-preco">
                            R$ <?= number_format($item['preco_final'], 2, ',', '.') ?>
                        </div>
                        <form method="POST" class="carrinho-item-quantidade"
                              style="display:inline-flex; align-items:center; gap:.5rem; margin-top:.5rem;">
                            <input type="hidden" name="acao" value="atualizar">
                            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                            <button type="submit" name="quantidade" value="<?= $item['quantidade'] - 1 ?>" class="btn-quantidade">-</button>
                            <span><?= $item['quantidade'] ?></span>
                            <button type="submit" name="quantidade" value="<?= $item['quantidade'] + 1 ?>" class="btn-quantidade">+</button>
                        </form>
                    </div>
                    <form method="POST">
                        <input type="hidden" name="acao" value="remover">
                        <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                        <button class="btn-remover">Remover</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="carrinho-resumo">
            <div class="resumo-linha"><span>Subtotal:</span><span>R$ <?= number_format($totais['subtotal'], 2, ',', '.') ?></span></div>
            <div class="resumo-linha"><span>Frete:</span><span>R$ <?= number_format($totais['frete'], 2, ',', '.') ?></span></div>
            <div class="resumo-total"><span>Total:</span><span>R$ <?= number_format($totais['total'], 2, ',', '.') ?></span></div>
        </div>

        <form method="POST">
            <input type="hidden" name="acao" value="finalizar">
            <button type="submit" class="btn-primario" style="width:100%;">Finalizar Pedido</button>
        </form>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>