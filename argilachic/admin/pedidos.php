<?php
require_once __DIR__ . '/../includes/functions.php';
if (!isAdmin()) redirecionar('../login.php');

$pdo = getConnection();

// Atualizar status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'status') {
    $pdo->prepare("UPDATE pedidos SET status = ? WHERE id = ?")
        ->execute([$_POST['status'], (int)$_POST['id']]);
    flash('sucesso', 'Status atualizado!');
    redirecionar('pedidos.php' . (isset($_POST['ver']) ? '?ver=' . (int)$_POST['ver'] : ''));
}

// Detalhe
$verId = (int)($_GET['ver'] ?? 0);
$pedidoVer = null;
$itensVer = [];
if ($verId) {
    $stmt = $pdo->prepare("SELECT p.*, u.nome AS cliente, u.email FROM pedidos p JOIN usuarios u ON u.id = p.usuario_id WHERE p.id = ?");
    $stmt->execute([$verId]);
    $pedidoVer = $stmt->fetch();
    if ($pedidoVer) $itensVer = itensDoPedido($pedidoVer['id']);
}

$pedidos = $pdo->query("
    SELECT p.*, u.nome AS cliente
    FROM pedidos p JOIN usuarios u ON u.id = p.usuario_id
    ORDER BY p.criado_em DESC
")->fetchAll();

$titulo = 'Admin - Pedidos';
include __DIR__ . '/../includes/header.php';
?>
<div class="container-principal">
    <h2 class="secao-titulo">Gerenciar Pedidos</h2>

    <?php if ($pedidoVer): ?>
        <div style="background:#fff;padding:2rem;border-radius:12px;margin-bottom:2rem;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:1rem;">
                <h3 style="color:var(--cor-header);">Pedido <?= htmlspecialchars($pedidoVer['numero_pedido']) ?></h3>
                <a href="pedidos.php" class="btn-info">Fechar</a>
            </div>
            <p><strong>Cliente:</strong> <?= htmlspecialchars($pedidoVer['cliente']) ?> (<?= htmlspecialchars($pedidoVer['email']) ?>)</p>
            <p><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($pedidoVer['criado_em'])) ?></p>
            <p><strong>Total:</strong> R$ <?= number_format($pedidoVer['total'], 2, ',', '.') ?></p>

            <form method="POST" style="margin-top:1rem;display:flex;gap:1rem;align-items:end;flex-wrap:wrap;">
                <input type="hidden" name="acao" value="status">
                <input type="hidden" name="id" value="<?= $pedidoVer['id'] ?>">
                <input type="hidden" name="ver" value="<?= $pedidoVer['id'] ?>">
                <div class="form-group" style="margin-bottom:0;min-width:200px;">
                    <label>Alterar Status</label>
                    <select name="status">
                        <?php foreach (['confirmado','preparando','transito','entregue','cancelado'] as $s): ?>
                            <option value="<?= $s ?>" <?= $pedidoVer['status'] === $s ? 'selected' : '' ?>>
                                <?= ucfirst($s) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button class="btn-primario" style="padding:.75rem 1.5rem;">Salvar</button>
            </form>

            <h4 style="margin:1.5rem 0 .75rem;color:var(--cor-header);">Itens</h4>
            <?php foreach ($itensVer as $it): ?>
                <div style="display:flex;align-items:center;gap:1rem;padding:.75rem;background:var(--cor-fundo);border-radius:8px;margin-bottom:.5rem;">
                    <img src="../<?= imagemUrl($it['imagem']) ?>" style="width:60px;height:60px;object-fit:cover;border-radius:8px;"
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

    <?php if (empty($pedidos)): ?>
        <p style="background:#fff;padding:2rem;border-radius:12px;text-align:center;color:#888;">
            Nenhum pedido cadastrado.
        </p>
    <?php else: ?>
        <table class="tabela-admin">
            <thead>
                <tr>
                    <th>Número</th>
                    <th>Cliente</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Data</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pedidos as $p): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($p['numero_pedido']) ?></strong></td>
                        <td><?= htmlspecialchars($p['cliente']) ?></td>
                        <td>R$ <?= number_format($p['total'], 2, ',', '.') ?></td>
                        <td><?= ucfirst($p['status']) ?></td>
                        <td><?= date('d/m/Y', strtotime($p['criado_em'])) ?></td>
                        <td><a href="?ver=<?= $p['id'] ?>" class="btn-info" style="padding:.4rem .8rem;font-size:.8rem;">Ver</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>