<?php
require_once __DIR__ . '/../includes/functions.php';
if (!isAdmin()) redirect('../login.php');

// Alternar admin
if (isset($_GET['toggle_admin'])) {
    $uid = (int)$_GET['toggle_admin'];
    if ($uid === (int)$_SESSION['usuario_id']) {
        setFlash('erro', 'Você não pode remover seu próprio admin.');
    } else {
        db()->prepare("UPDATE usuarios SET is_admin = 1 - is_admin WHERE id = :id")->execute([':id' => $uid]);
        setFlash('sucesso', 'Permissão atualizada.');
    }
    redirect('usuarios.php');
}

// Excluir usuário
if (isset($_GET['excluir'])) {
    $uid = (int)$_GET['excluir'];
    if ($uid === (int)$_SESSION['usuario_id']) {
        setFlash('erro', 'Você não pode excluir sua própria conta.');
    } else {
        db()->prepare("DELETE FROM usuarios WHERE id = :id")->execute([':id' => $uid]);
        setFlash('sucesso', 'Usuário excluído.');
    }
    redirect('usuarios.php');
}

$busca = trim($_GET['q'] ?? '');
if ($busca) {
    $stmt = db()->prepare("
        SELECT u.*, (SELECT COUNT(*) FROM pedidos p WHERE p.usuario_id = u.id) AS total_pedidos
        FROM usuarios u
        WHERE u.nome LIKE :q OR u.email LIKE :q
        ORDER BY u.id DESC
    ");
    $stmt->execute([':q' => "%$busca%"]);
} else {
    $stmt = db()->query("
        SELECT u.*, (SELECT COUNT(*) FROM pedidos p WHERE p.usuario_id = u.id) AS total_pedidos
        FROM usuarios u
        ORDER BY u.id DESC
    ");
}
$usuarios = $stmt->fetchAll();
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Usuários - Admin ReyVie</title>
<?php include __DIR__ . '/../style.php'; ?>
</head>
<body>

<?php if ($flash): ?>
    <div class="notificacao <?= $flash['tipo'] ?>"><?= e($flash['msg']) ?></div>
<?php endif; ?>

<div class="wrap">
    <div class="admin-header">
        <h1>👥 Gerenciar Usuários</h1>
        <div class="admin-actions">
            <a href="index.php" class="btn btn-secondary">← Painel</a>
        </div>
    </div>

    <form method="GET" class="search-form" style="margin-bottom:1.5rem; max-width:420px;">
        <input type="text" name="q" placeholder="Buscar por nome ou email..." value="<?= e($busca) ?>" style="flex:1;">
        <button class="btn btn-primary">Buscar</button>
        <?php if ($busca): ?>
            <a href="usuarios.php" class="btn btn-secondary">Limpar</a>
        <?php endif; ?>
    </form>

    <div style="overflow-x:auto;">
        <table class="tabela-admin">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Tipo</th>
                    <th>Pedidos</th>
                    <th>Cadastro</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): $isSelf = $u['id'] == $_SESSION['usuario_id']; ?>
                    <tr>
                        <td><strong>#<?= $u['id'] ?></strong></td>
                        <td><?= e($u['nome']) ?> <?= $isSelf ? '<small style="color:#999;">(você)</small>' : '' ?></td>
                        <td><?= e($u['email']) ?></td>
                        <td>
                            <?php if ($u['is_admin']): ?>
                                <span class="badge badge-ok">Admin</span>
                            <?php else: ?>
                                <span class="badge">Cliente</span>
                            <?php endif; ?>
                        </td>
                        <td><?= (int)$u['total_pedidos'] ?></td>
                        <td><?= date('d/m/Y', strtotime($u['criado_em'])) ?></td>
                        <td style="white-space:nowrap;">
                            <?php if (!$isSelf): ?>
                                <a href="?toggle_admin=<?= $u['id'] ?>" class="btn btn-secondary btn-sm"
                                   onclick="return confirm('Alternar permissão de admin?')">
                                    <?= $u['is_admin'] ? '⬇️ Remover admin' : '⬆️ Tornar admin' ?>
                                </a>
                                <a href="?excluir=<?= $u['id'] ?>" class="btn btn-danger btn-sm"
                                   onclick="return confirm('Excluir usuário? Os pedidos dele permanecerão sem dono.')">🗑️</a>
                            <?php else: ?>
                                <span style="color:#999; font-size:.85rem;">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>