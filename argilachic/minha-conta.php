<?php
require_once 'includes/functions.php';

if (!isLogado()) redirecionar('login.php');

$pdo = getConnection();
$userId = $_SESSION['usuario_id'];

// Atualizar dados
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo->prepare("
        UPDATE usuarios SET nome=?, telefone=?, data_nascimento=?, endereco=?, cidade=?, estado=?, cep=?
        WHERE id=?
    ")->execute([
        $_POST['nome'],
        $_POST['telefone'] ?? null,
        !empty($_POST['data_nascimento']) ? $_POST['data_nascimento'] : null,
        $_POST['endereco'] ?? null,
        $_POST['cidade'] ?? null,
        $_POST['estado'] ?? null,
        $_POST['cep'] ?? null,
        $userId
    ]);
    $_SESSION['usuario_nome'] = $_POST['nome'];
    flash('sucesso', 'Dados atualizados!');
    redirecionar('minha-conta.php');
}

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

$pedidos = listarPedidosUsuario($userId);

$titulo = 'Minha Conta';
include 'includes/header.php';
?>
<div class="container-principal">
    <h2 class="secao-titulo">Minha Conta</h2>

    <div style="display:grid;grid-template-columns:1fr 1.2fr;gap:2rem;" class="conta-grid">
        <!-- Dados pessoais -->
        <div style="background:#fff;padding:2rem;border-radius:12px;">
            <h3 style="color:var(--cor-header);margin-bottom:1rem;">Dados Pessoais</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Nome Completo</label>
                    <input name="nome" value="<?= htmlspecialchars($user['nome']) ?>" required>
                </div>
                <div class="form-group">
                    <label>E-mail (não editável)</label>
                    <input value="<?= htmlspecialchars($user['email']) ?>" disabled style="background:#f5f5f5;">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Telefone</label>
                        <input name="telefone" value="<?= htmlspecialchars($user['telefone'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Data de Nascimento</label>
                        <input type="date" name="data_nascimento" value="<?= htmlspecialchars($user['data_nascimento'] ?? '') ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Endereço</label>
                    <input name="endereco" value="<?= htmlspecialchars($user['endereco'] ?? '') ?>">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Cidade</label>
                        <input name="cidade" value="<?= htmlspecialchars($user['cidade'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Estado</label>
                        <input name="estado" value="<?= htmlspecialchars($user['estado'] ?? '') ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>CEP</label>
                    <input name="cep" value="<?= htmlspecialchars($user['cep'] ?? '') ?>">
                </div>
                <button class="btn-enviar">Salvar Alterações</button>
            </form>
        </div>

        <!-- Histórico de pedidos -->
        <div style="background:#fff;padding:2rem;border-radius:12px;">
            <h3 style="color:var(--cor-header);margin-bottom:1rem;">Meus Pedidos</h3>
            <?php if (empty($pedidos)): ?>
                <p style="color:#888;text-align:center;padding:2rem 0;">
                    Você ainda não fez nenhum pedido.
                </p>
                <a href="index.php#produtos" class="btn-primario" style="display:block;text-align:center;">
                    Ver Produtos
                </a>
            <?php else: ?>
                <?php foreach ($pedidos as $p): ?>
                    <div style="padding:1rem;background:var(--cor-fundo);border-radius:8px;margin-bottom:.75rem;">
                        <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:.5rem;">
                            <strong><?= htmlspecialchars($p['numero_pedido']) ?></strong>
                            <span style="color:var(--cor-primaria);font-weight:700;">
                                R$ <?= number_format($p['total'], 2, ',', '.') ?>
                            </span>
                        </div>
                        <div style="font-size:.9rem;color:#888;margin:.25rem 0;">
                            <?= date('d/m/Y', strtotime($p['criado_em'])) ?> • Status: <?= ucfirst($p['status']) ?>
                        </div>
                        <a href="rastreamento.php?pedido=<?= urlencode($p['numero_pedido']) ?>"
                           class="btn-info" style="padding:.4rem .8rem;font-size:.8rem;margin-top:.5rem;">
                            Rastrear
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
@media (max-width: 768px) {
    .conta-grid { grid-template-columns: 1fr !important; }
}
</style>
<?php include 'includes/footer.php'; ?>