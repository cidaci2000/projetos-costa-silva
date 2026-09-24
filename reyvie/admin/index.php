<?php
require_once __DIR__ . '/../includes/functions.php';
if (!isAdmin()) redirect('../login.php');

// Estatísticas
$totalProdutos = (int) db()->query("SELECT COUNT(*) FROM produtos")->fetchColumn();
$totalUsuarios = (int) db()->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
$totalPedidos  = (int) db()->query("SELECT COUNT(*) FROM pedidos")->fetchColumn();
$receita       = (float) db()->query("SELECT COALESCE(SUM(total),0) FROM pedidos")->fetchColumn();

// Últimos 5 pedidos com nome do usuário
$ultimosPedidos = db()->query("
    SELECT p.id, p.total, p.status, p.criado_em, u.nome AS usuario
    FROM pedidos p
    LEFT JOIN usuarios u ON u.id = p.usuario_id
    ORDER BY p.id DESC
    LIMIT 5
")->fetchAll();

// Produtos com estoque baixo (< 10)
$estoqueBaixo = db()->query("
    SELECT id, nome, estoque
    FROM produtos
    WHERE estoque < 10
    ORDER BY estoque ASC
    LIMIT 5
")->fetchAll();

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Painel Admin - ReyVie</title>
<?php include __DIR__ . '/../style.php'; ?>
</head>
<body>

<?php if ($flash): ?>
    <div class="notificacao <?= $flash['tipo'] ?>"><?= e($flash['msg']) ?></div>
<?php endif; ?>

<div class="wrap">
    <!-- CABEÇALHO -->
    <div class="admin-header">
        <h1>⚙️ Painel Administrativo</h1>
        <div class="admin-actions">
            <a href="produtos.php" class="btn btn-primary">📦 Produtos</a>
            <a href="pedidos.php" class="btn btn-secondary">🧾 Pedidos</a>
            <a href="usuarios.php" class="btn btn-secondary">👥 Usuários</a>
            <a href="../index.php" class="btn btn-secondary">🌐 Ver Site</a>
            <a href="../logout.php" class="btn btn-secondary">🚪 Sair</a>
        </div>
    </div>

    <!-- CARDS DE ESTATÍSTICAS -->
    <div class="admin-cards">
        <div class="admin-card">
            <h3>Produtos</h3>
            <div class="valor"><?= $totalProdutos ?></div>
        </div>
        <div class="admin-card">
            <h3>Usuários</h3>
            <div class="valor"><?= $totalUsuarios ?></div>
        </div>
        <div class="admin-card">
            <h3>Pedidos</h3>
            <div class="valor"><?= $totalPedidos ?></div>
        </div>
        <div class="admin-card">
            <h3>Receita Total</h3>
            <div class="valor">R$ <?= number_format($receita, 2, ',', '.') ?></div>
        </div>
    </div>

    <!-- GRID DE TABELAS -->
    <div class="admin-grid">
        <!-- ÚLTIMOS PEDIDOS -->
        <div class="admin-panel">
            <div class="admin-panel-header">
                <h2>🧾 Últimos Pedidos</h2>
                <a href="pedidos.php" class="btn btn-secondary btn-sm">Ver todos</a>
            </div>
            <?php if (empty($ultimosPedidos)): ?>
                <p class="empty">Nenhum pedido ainda.</p>
            <?php else: ?>
                <table class="tabela-admin">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ultimosPedidos as $p): ?>
                            <tr>
                                <td><strong>#<?= $p['id'] ?></strong></td>
                                <td><?= e($p['usuario'] ?? 'Visitante') ?></td>
                                <td>R$ <?= number_format($p['total'], 2, ',', '.') ?></td>
                                <td><span class="badge badge-<?= e($p['status']) ?>"><?= e($p['status']) ?></span></td>
                                <td><?= date('d/m/Y H:i', strtotime($p['criado_em'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- ESTOQUE BAIXO -->
        <div class="admin-panel">
            <div class="admin-panel-header">
                <h2>⚠️ Estoque Baixo</h2>
                <a href="produtos.php" class="btn btn-secondary btn-sm">Gerenciar</a>
            </div>
            <?php if (empty($estoqueBaixo)): ?>
                <p class="empty">✅ Todos os produtos com estoque adequado.</p>
            <?php else: ?>
                <table class="tabela-admin">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Estoque</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($estoqueBaixo as $p): ?>
                            <tr>
                                <td><?= e($p['nome']) ?></td>
                                <td>
                                    <span class="badge <?= $p['estoque'] == 0 ? 'badge-erro' : 'badge-aviso' ?>">
                                        <?= (int)$p['estoque'] ?> un.
                                    </span>
                                </td>
                                <td>
                                    <a href="produto_form.php?id=<?= $p['id'] ?>" class="btn btn-secondary btn-sm">Editar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>