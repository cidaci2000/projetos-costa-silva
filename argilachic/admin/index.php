<?php
require_once __DIR__ . '/../includes/functions.php';
if (!isAdmin()) redirecionar('../login.php');

$pdo = getConnection();

// Estatísticas
$totalProdutos = $pdo->query("SELECT COUNT(*) FROM produtos WHERE ativo = 1")->fetchColumn();
$totalUsuarios = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE tipo = 'cliente'")->fetchColumn();
$totalPedidos  = $pdo->query("SELECT COUNT(*) FROM pedidos")->fetchColumn();
$faturamento   = $pdo->query("SELECT COALESCE(SUM(total),0) FROM pedidos WHERE status != 'cancelado'")->fetchColumn();

// Últimos pedidos
$ultimosPedidos = $pdo->query("
    SELECT p.*, u.nome AS cliente
    FROM pedidos p JOIN usuarios u ON u.id = p.usuario_id
    ORDER BY p.criado_em DESC LIMIT 5
")->fetchAll();

$titulo = 'Admin - Dashboard';
include __DIR__ . '/../includes/header.php';
?>
<div class="container-principal">
    <h2 class="secao-titulo">Painel Administrativo</h2>

    <div class="admin-cards">
        <div class="admin-card">
            <h3>Produtos Ativos</h3>
            <div class="valor"><?= $totalProdutos ?></div>
        </div>
        <div class="admin-card">
            <h3>Clientes</h3>
            <div class="valor"><?= $totalUsuarios ?></div>
        </div>
        <div class="admin-card">
            <h3>Pedidos</h3>
            <div class="valor"><?= $totalPedidos ?></div>
        </div>
        <div class="admin-card">
            <h3>Faturamento</h3>
            <div class="valor" style="font-size:1.4rem;">R$ <?= number_format($faturamento, 2, ',', '.') ?></div>
        </div>
    </div>

    <div style="display:flex; gap:1rem; margin-bottom:2rem; flex-wrap:wrap;">
        <a href="produtos.php" class="btn-primario">Gerenciar Produtos</a>
        <a href="pedidos.php" class="btn-primario" style="background:var(--cor-header);">Gerenciar Pedidos</a>
    </div>

    <h3 style="color:var(--cor-header); margin-bottom:1rem;">Últimos Pedidos</h3>
    <?php if (empty($ultimosPedidos)): ?>
        <p style="background:#fff;padding:2rem;border-radius:12px;text-align:center;color:#888;">
            Nenhum pedido ainda.
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
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ultimosPedidos as $p): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($p['numero_pedido']) ?></strong></td>
                        <td><?= htmlspecialchars($p['cliente']) ?></td>
                        <td>R$ <?= number_format($p['total'], 2, ',', '.') ?></td>
                        <td><?= ucfirst($p['status']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($p['criado_em'])) ?></td>
                        <td><a href="pedidos.php?ver=<?= $p['id'] ?>" class="btn-info" style="padding:.4rem .8rem;font-size:.8rem;">Ver</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>