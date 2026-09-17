<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/funcoes.php';
require_once __DIR__ . '/auth.php';

$flash     = flash();
$totalCart = totalCarrinho();
$user      = usuarioAtual();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= $titulo ?? 'Le Parfum' ?> - Le Parfum</title>
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css?v=<?php echo time(); ?>" />
</head>
<body>

<nav class="navbar">
  <div class="container navbar-inner">
    <a href="<?= BASE_URL ?>index.php" class="navbar-logo">
      <span class="logo-text">Le Parfum</span>
      <span class="logo-sub">Fragrâncias</span>
    </a>

    <div class="navbar-nav">
      <a href="<?= BASE_URL ?>pages/catalogo.php">Catálogo</a>
      <a href="<?= BASE_URL ?>pages/sobre.php">Sobre</a>
      <a href="<?= BASE_URL ?>pages/contato.php">Contato</a>

      <?php if ($user): ?>
        <a href="<?= BASE_URL ?>pages/perfil.php">👤 <?= e($user['nome']) ?></a>
        <?php if ($user['admin']): ?>
          <a href="<?= BASE_URL ?>admin/index.php">Admin</a>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>pages/logout.php">Sair</a>
      <?php else: ?>
        <a href="<?= BASE_URL ?>pages/login.php">Entrar</a>
      <?php endif; ?>
    </div>

    <div class="navbar-actions">
      <a href="<?= BASE_URL ?>pages/carrinho.php" class="icon-btn" title="Carrinho">
        🛒
        <?php if ($totalCart > 0): ?>
          <span class="cart-count"><?= $totalCart ?></span>
        <?php endif; ?>
      </a>
    </div>
  </div>
</nav>

<?php if ($flash): ?>
  <div class="toast toast-<?= e($flash['tipo']) ?>" id="toast-flash">
    <?= e($flash['msg']) ?>
  </div>
<?php endif; ?>