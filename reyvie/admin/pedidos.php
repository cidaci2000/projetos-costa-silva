<?php
require_once __DIR__ . '/../includes/functions.php';
if (!isAdmin()) redirect('../login.php');

// Atualizar status
if (isset($_POST['atualizar_status'])) {
    $pid = (int)$_POST['pedido_id'];
    $novoStatus = $_POST['status'] ?? 'pendente';
    $statusValidos = ['pendente', 'pago', 'enviado', 'entregue', 'cancelado'];
    if (in_array($novoStatus, $statusValidos)) {
        db()->prepare("UPDATE pedidos SET status = :s WHERE id = :id")
            ->execute([':s' => $novoStatus, ':id' => $pid]);
        setFlash('sucesso', "Pedido #$pid atualizado para '$novoStatus'.");
    }
    redirect('pedidos.php');
}

// Ver detalhes
$verId = (int)($_GET['ver'] ?? 0);
$verPedido = null;
$verItens = [];
if ($verId) {
    $stmt = db()->prepare("
        SELECT p.*, u.nome AS usuario, u.email AS usuario_email
        FROM pedidos p
        LEFT JOIN usuarios u ON u.id = p.usuario_id
        WHERE p.id = :id
    ");
    $stmt->execute([':id' => $verId]);
    $verPedido = $stmt->fetch();

    if ($verPedido) {
        $stmt = db()->prepare("
            SELECT pi.*, pr.nome AS produto_nome, pr.imagem, pr.emoji
            FROM pedido_itens pi
            JOIN produtos pr ON pr.id = pi.produto_id
            WHERE pi.pedido_id = :id
        ");
        $stmt->execute([':id' => $verId]);
        $verItens = $stmt->fetchAll();
    }
}

// Filtro por status
$filtro = $_GET['status'] ?? '';
$sql = "
    SELECT p.*, u.nome AS usuario,
           (SELECT COUNT(*) FROM pedido_itens pi WHERE pi.pedido_id = p.id) AS total_itens
    FROM pedidos p
    LEFT JOIN usuarios u ON u.id = p.usuario_id
";
$params = [];
if ($filtro && in_array($filtro, ['pendente','pago','enviado','entregue','cancelado'])) {
    $sql .= " WHERE p.status = :s";
    $params[':s'] = $filtro;
}
$sql .= " ORDER BY p.id DESC";

$stmt = db()->prepare($sql);
$stmt->execute($params);
$pedidos = $stmt->fetchAll();

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pedidos - Admin ReyVie</title>
<?php include __DIR__ . '/../style.php'; ?>
</head>
<body>

<?php if ($flash): ?>
    <div class="notificacao <?= $flash['tipo'] ?>"><?= e($flash['msg']) ?></div>
<?php endif; ?>

<div class="wrap">
    <div class="admin-header">
        <h1>🧾 Gerenciar Pedidos</h1>
        <div class="admin-actions">
            <a href="index.php" class="btn btn-secondary">← Painel</a>
        </div>
    </div>

    <!-- FILTROS -->
    <div class="filtros">
        <a href="pedidos.php" class="btn btn-secondary btn-sm <?= !$filtro ? 'ativo' : '' ?>">Todos</a>
        <?php foreach (['pendente','pago','enviado','entregue','cancelado'] as $s): ?>
            <a href="?status=<?= $s ?>" class="btn btn-secondary btn-sm <?= $filtro === $s ? 'ativo' : '' ?>">
                <?= ucfirst($s) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- DETALHE DE UM PEDIDO -->
    <?php if ($verPedido): ?>
        <div class="admin-panel" style="margin-bottom:2rem;">
            <div class="admin-panel-header">
                <h2>Pedido #<?= $verPedido['id'] ?> — <?= e($verPedido['usuario'] ?? 'Visitante') ?></h2>
                <a href="pedidos.php" class="btn btn-secondary btn-sm">✖ Fechar</a>
            </div>

            <p style="margin-bottom:.5rem;"><strong>E-mail:</strong> <?= e($verPedido['usuario_email'] ?? '—') ?></p>
            <p style="margin-bottom:.5rem;"><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($verPedido['criado_em'])) ?></p>
            <p style="margin-bottom:1rem;">
                <strong>Status:</strong>
                <span class="badge badge-<?= e($verPedido['status']) ?>"><?= e($verPedido['status']) ?></span>
            </p>

            <table class="tabela-admin">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Qtd</th>
                        <th>Preço Unit.</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($verItens as $i): ?>
                        <tr>
                            <td style="display:flex; align-items:center; gap:.6rem;">
                                <?php if ($i['imagem']): ?>
                                    <img src="<?= imagemUrl($i['imagem']) ?>" style="width:40px;height:40px;object-fit:cover;border-radius:6px;">
                                <?php else: ?>
                                    <?= $i['emoji'] ?>
                                <?php endif; ?>
                                <?= e($i['produto_nome']) ?>
                            </td>
                            <td><?= (int)$i['quantidade'] ?></td>
                            <td>R$ <?= number_format($i['preco_unitario'], 2, ',', '.') ?></td>
                            <td><strong>R$ <?= number_format($i['preco_unitario'] * $i['quantidade'], 2, ',', '.') ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="text-align:right;"><strong>Total:</strong></td>
                        <td><strong style="color:var(--primary-coral);">R$ <?= number_format($verPedido['total'], 2, ',', '.') ?></strong></td>
                    </tr>
                </tfoot>
            </table>

            <form method="POST" style="margin-top:1.5rem; display:flex; gap:.5rem; align-items:center;">
                <input type="hidden" name="pedido_id" value="<?= $verPedido['id'] ?>">
                <label style="font-weight:600;">Alterar status:</label>
                <select name="status" style="padding:.5rem; border:2px solid var(--light-pink); border-radius:10px;">
                    <?php foreach (['pendente','pago','enviado','entregue','cancelado'] as $s): ?>
                        <option value="<?= $s ?>" <?= $verPedido['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                    <?php endforeach; ?>
                </select>
                <button name="atualizar_status" class="btn btn-primary">💾 Salvar</button>
            </form>
        </div>
    <?php endif; ?>

    <!-- LISTA DE PEDIDOS -->
    <div style="overflow-x:auto;">
        <table class="tabela-admin">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Cliente</th>
                    <th>Itens</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Data</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pedidos)): ?>
                    <tr><td colspan="7" style="text-align:center; padding:2rem;">Nenhum pedido encontrado.</td></tr>
                <?php else: ?>
                    <?php foreach ($pedidos as $p): ?>
                        <tr>
                            <td><strong>#<?= $p['id'] ?></strong></td>
                            <td><?= e($p['usuario'] ?? 'Visitante') ?></td>
                            <td><?= (int)$p['total_itens'] ?> item(ns)</td>
                            <td><strong>R$ <?= number_format($p['total'], 2, ',', '.') ?></strong></td>
                            <td><span class="badge badge-<?= e($p['status']) ?>"><?= e($p['status']) ?></span></td>
                            <td><?= date('d/m/Y H:i', strtotime($p['criado_em'])) ?></td>
                            <td>
                                <a href="?ver=<?= $p['id'] ?>" class="btn btn-secondary btn-sm">👁️ Ver</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>