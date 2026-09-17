<?php
require_once 'includes/functions.php';

$numero = $_GET['pedido'] ?? '';
$pedido = null;
$erro = '';
$itens = [];

if ($numero) {
    $pedido = buscarPedidoPorNumero($numero);
    if ($pedido) $itens = itensDoPedido($pedido['id']);
    else $erro = 'Pedido não encontrado.';
}

$titulo = 'Rastreamento';
include 'includes/header.php';

$statusMap = ['confirmado'=>1,'preparando'=>2,'transito'=>3,'entregue'=>4];
$labels = [1=>'Pedido Confirmado',2=>'Preparando',3=>'Em Trânsito',4=>'Entregue'];
?>
<div class="container-principal">
    <h2 class="secao-titulo">Rastrear Pedido</h2>

    <form method="GET" style="display:flex; gap:1rem; justify-content:center; margin-bottom:2rem; flex-wrap:wrap;">
        <input type="text" name="pedido" placeholder="Ex: PED-2026-001234"
               value="<?= htmlspecialchars($numero) ?>" required
               style="padding:.75rem 1.5rem;border:2px solid var(--cor-primaria);border-radius:25px;min-width:300px;">
        <button class="filtro-btn ativo">Buscar</button>
    </form>

    <?php if ($erro): ?><p style="color:var(--cor-erro);text-align:center;"><?= $erro ?></p><?php endif; ?>

    <?php if ($pedido):
        $s = $statusMap[$pedido['status']] ?? 1;
    ?>
        <div style="background:#fff;padding:2rem;border-radius:12px;">
            <div class="rastreamento-status">
                <?php foreach ($labels as $num => $label): ?>
                    <div class="status-item">
                        <div class="status-circulo <?= $num <= $s ? 'ativo' : '' ?>">
                            <?= $num <= $s ? '✓' : $num ?>
                        </div>
                        <div class="status-label"><?= $label ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="rastreamento-detalhes">
                <div class="detalhe-linha"><span class="detalhe-label">Pedido:</span><span><?= htmlspecialchars($pedido['numero_pedido']) ?></span></div>
                <div class="detalhe-linha"><span class="detalhe-label">Status:</span><span><?= $labels[$s] ?></span></div>
                <div class="detalhe-linha"><span class="detalhe-label">Data:</span><span><?= date('d/m/Y', strtotime($pedido['criado_em'])) ?></span></div>
                <div class="detalhe-linha"><span class="detalhe-label">Previsão:</span><span><?= date('d/m/Y', strtotime($pedido['previsao_entrega'])) ?></span></div>
                <div class="detalhe-linha"><span class="detalhe-label">Transportadora:</span><span><?= htmlspecialchars($pedido['transportadora']) ?></span></div>
                <div class="detalhe-linha"><span class="detalhe-label">Rastreio:</span><span><?= htmlspecialchars($pedido['codigo_rastreio']) ?></span></div>
                <div class="detalhe-linha"><span class="detalhe-label">Total:</span><span>R$ <?= number_format($pedido['total'], 2, ',', '.') ?></span></div>
            </div>

            <?php if ($itens): ?>
                <h3 style="margin-top:2rem; color:var(--cor-header);">Itens do Pedido</h3>
                <div style="margin-top:1rem;">
                    <?php foreach ($itens as $it): ?>
                        <div style="display:flex;align-items:center;gap:1rem;padding:.75rem;background:var(--cor-fundo);border-radius:8px;margin-bottom:.5rem;">
                            <img src="<?= imagemUrl($it['imagem']) ?>" style="width:60px;height:60px;object-fit:cover;border-radius:8px;"
                                 onerror="this.src='https://via.placeholder.com/60/E8D4C4/8B6F47?text=🏺'">
                            <div style="flex:1;">
                                <div style="font-weight:600;"><?= htmlspecialchars($it['nome']) ?></div>
                                <div style="color:#888;font-size:.9rem;">
                                    <?= $it['quantidade'] ?>x R$ <?= number_format($it['preco_unitario'], 2, ',', '.') ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>