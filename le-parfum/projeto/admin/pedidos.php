<?php
$titulo = 'Pedidos';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/funcoes.php';
require_once __DIR__ . '/../includes/auth.php';
exigirAdmin();

if (isset($_GET['status'], $_GET['id'])) {
    $pdo->prepare("UPDATE pedidos SET status = ? WHERE id = ?")
        ->execute([$_GET['status'], (int)$_GET['id']]);
    flash('Status atualizado.', 'success');
    redirect(BASE_URL . 'admin/pedidos.php');
}

$pedidos = $pdo->query("SELECT * FROM pedidos ORDER BY criado_em DESC")->fetchAll();
require_once __DIR__ . '/../includes/header.php';
?>
<section class="page-content">
  <div class="container">
    <h1 style="margin-bottom:24px;">🧾 Pedidos</h1>
    <table class="orders-table" style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.05);">
      <thead>
        <tr><th>#</th><th>Cliente</th><th>Total</th><th>Pagamento</th><th>Status</th><th>Ações</th></tr>
      </thead>
      <tbody>
        <?php foreach ($pedidos as $p): ?>
          <tr>
            <td>#<?= str_pad($p['id'], 5, '0', STR_PAD_LEFT) ?></td>
            <td><?= e($p['nome_cliente']) ?><br><small style="color:var(--gray);"><?= e($p['email']) ?></small></td>
            <td><?= preco((float)$p['total']) ?></td>
            <td><?= e($p['pagamento']) ?></td>
            <td><span class="status-badge status-<?= e($p['status']) ?>"><?= ucfirst($p['status']) ?></span></td>
            <td>
              <form method="get" style="display:flex;gap:6px;">
                <input type="hidden" name="id" value="<?= $p['id'] ?>" />
                <select name="status" onchange="this.form.submit()" style="padding:6px;border-radius:6px;border:1px solid #ddd;">
                  <option value="">Alterar...</option>
                  <option value="processando">Processando</option>
                  <option value="transito">Em trânsito</option>
                  <option value="entregue">Entregue</option>
                  <option value="cancelado">Cancelado</option>
                </select>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>