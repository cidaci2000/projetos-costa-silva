<?php
require_once 'includes/functions.php';

if (!isLogado()) {
    flash('erro', 'Faça login para continuar.');
    redirecionar('login.php');
}

$totais = calcularTotais();
if (empty($totais['itens'])) {
    flash('erro', 'Seu carrinho está vazio.');
    redirecionar('carrinho.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero = criarPedido($_SESSION['usuario_id']);
    if ($numero) {
        flash('sucesso', "Pedido $numero realizado com sucesso!");
        redirecionar('rastreamento.php?pedido=' . urlencode($numero));
    } else {
        flash('erro', 'Erro ao processar pedido. Tente novamente.');
        redirecionar('checkout.php');
    }
}

// Buscar dados do usuário
$pdo = getConnection();
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$user = $stmt->fetch();

$titulo = 'Checkout';
include 'includes/header.php';
?>
<div class="container-principal">
    <h2 class="secao-titulo">Finalizar Compra</h2>

    <div style="display:grid;grid-template-columns:1.5fr 1fr;gap:2rem;" class="checkout-grid">
        <!-- Dados de entrega -->
        <div style="background:#fff;padding:2rem;border-radius:12px;">
            <h3 style="color:var(--cor-header);margin-bottom:1rem;">Dados de Entrega</h3>
            <p><strong>Nome:</strong> <?= htmlspecialchars($user['nome']) ?></p>
            <p><strong>E-mail:</strong> <?= htmlspecialchars($user['email']) ?></p>
            <p><strong>Telefone:</strong> <?= htmlspecialchars($user['telefone'] ?? '-') ?></p>
            <p><strong>Endereço:</strong> <?= htmlspecialchars($user['endereco'] ?? '-') ?></p>
            <p><strong>Cidade:</strong> <?= htmlspecialchars($user['cidade'] ?? '-') ?> - <?= htmlspecialchars($user['estado'] ?? '-') ?></p>
            <p><strong>CEP:</strong> <?= htmlspecialchars($user['cep'] ?? '-') ?></p>

            <?php if (empty($user['endereco'])): ?>
                <p style="color:var(--cor-erro);margin-top:1rem;">
                    ⚠️ Complete seu endereço em <a href="minha-conta.php">Minha Conta</a> antes de finalizar.
                </p>
            <?php endif; ?>
        </div>

        <!-- Resumo do pedido -->
        <div>
            <div class="carrinho-resumo">
                <h3 style="color:var(--cor-header);margin-bottom:1rem;">Resumo</h3>
                <?php foreach ($totais['itens'] as $it): ?>
                    <div class="resumo-linha">
                        <span><?= $it['quantidade'] ?>x <?= htmlspecialchars($it['nome']) ?></span>
                        <span>R$ <?= number_format($it['preco_final'] * $it['quantidade'], 2, ',', '.') ?></span>
                    </div>
                <?php endforeach; ?>
                <hr style="border:none;border-top:1px solid #E8E3DD;margin:.75rem 0;">
                <div class="resumo-linha"><span>Subtotal:</span><span>R$ <?= number_format($totais['subtotal'], 2, ',', '.') ?></span></div>
                <div class="resumo-linha"><span>Frete:</span><span>R$ <?= number_format($totais['frete'], 2, ',', '.') ?></span></div>
                <div class="resumo-total"><span>Total:</span><span>R$ <?= number_format($totais['total'], 2, ',', '.') ?></span></div>
            </div>

            <form method="POST">
                <button type="submit" class="btn-primario" style="width:100%;"
                    <?= empty($user['endereco']) ? 'disabled style="opacity:.5;width:100%;"' : '' ?>>
                    Confirmar Pedido
                </button>
            </form>
        </div>
    </div>
</div>

<style>
@media (max-width: 768px) {
    .checkout-grid { grid-template-columns: 1fr !important; }
}
</style>
<?php include 'includes/footer.php'; ?>