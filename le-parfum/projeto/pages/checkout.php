<?php
$titulo = 'Checkout';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';

exigirLogin();

$itens = getCarrinhoCompleto($pdo);
$total = totalCarrinhoValor($pdo);

if (empty($itens)) {
    flash('Seu carrinho está vazio.', 'error');
    redirect(BASE_URL . 'pages/catalogo.php');
}

$user = usuarioAtual();
?>

<section class="page-hero">
  <div class="container"><h1>Finalizar Compra</h1></div>
</section>

<section class="page-content">
  <div class="container">
    <form method="post" action="<?= BASE_URL ?>actions/finalizar_pedido.php" class="checkout-grid">
      <div class="checkout-form">
        <h2>📦 Dados de Entrega</h2>
        <div class="form-row">
          <div class="form-group">
            <label>Nome completo</label>
            <input type="text" name="nome_cliente" value="<?= e($user['nome']) ?>" required />
          </div>
          <div class="form-group">
            <label>E-mail</label>
            <input type="email" name="email" value="<?= e($user['email']) ?>" required />
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>CEP</label>
            <input type="text" name="cep" required />
          </div>
          <div class="form-group">
            <label>Telefone</label>
            <input type="text" name="telefone" required />
          </div>
        </div>
        <div class="form-group">
          <label>Endereço completo</label>
          <input type="text" name="endereco" required />
        </div>

        <h2 style="margin-top:28px;">💳 Pagamento</h2>
        <div class="form-group">
          <label>Forma de pagamento</label>
          <select name="pagamento" style="width:100%;padding:12px;border:1px solid #ddd;border-radius:8px;">
            <option value="cartao">Cartão de Crédito</option>
            <option value="pix">PIX</option>
            <option value="boleto">Boleto</option>
          </select>
        </div>
        <button type="submit" class="btn btn-gold" style="width:100%;margin-top:20px;padding:16px;">
          ✦ Confirmar Pedido
        </button>
      </div>

      <aside class="order-summary">
        <h2>Resumo do Pedido</h2>
        <?php foreach ($itens as $i): ?>
          <div class="summary-item">
            <span><?= e($i['nome']) ?> × <?= $i['quantidade'] ?></span>
            <span><?= preco((float)$i['subtotal']) ?></span>
          </div>
        <?php endforeach; ?>
        <div class="summary-total">
          <span>Total</span><span><?= preco($total) ?></span>
        </div>
      </aside>
    </form>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>