<?php
require_once __DIR__ . '/functions.php';

$totalCarrinho = contarItensCarrinho();
$usuario = usuarioAtual();

// Detecta se estamos em /admin/
$emAdmin = str_contains($_SERVER['PHP_SELF'], '/admin/');
$base = $emAdmin ? '../' : '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo ?? 'Argila Chic') ?> - Argila Chic</title>
    <link rel="stylesheet" href="<?= $base ?>assets/css/style.css">
</head>
<body>
<header>
    <div class="header-container">
        <a href="<?= $base ?>index.php" class="logo">ARGILA CHIC</a>
        <nav>
            <a href="<?= $base ?>index.php#inicio">Início</a>
            <a href="<?= $base ?>index.php#produtos">Produtos</a>
            <a href="<?= $base ?>index.php#sobre">Sobre</a>
            <a href="<?= $base ?>index.php#contato">Contato</a>
            <a href="<?= $base ?>rastreamento.php">Rastrear</a>
        </nav>
        <div class="header-actions">
            <a href="<?= $base ?>carrinho.php" class="btn-carrinho">
                🛒 <span class="carrinho-badge"><?= $totalCarrinho ?></span>
            </a>
            <?php if ($usuario): ?>
                <a href="<?= $base ?>minha-conta.php" class="btn-usuario">
                    <?= htmlspecialchars(explode(' ', $usuario['nome'])[0]) ?>
                </a>
                <?php if (isAdmin()): ?>
                    <a href="<?= $base ?>admin/index.php" class="btn-usuario" style="background:#4A3D2A">⚙️ Admin</a>
                <?php endif; ?>
                <a href="<?= $base ?>logout.php" class="btn-usuario" style="background:#D64545">Sair</a>
            <?php else: ?>
                <a href="<?= $base ?>login.php" class="btn-usuario">Entrar / Cadastro</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<?php if (isset($_SESSION['mensagem'])): ?>
    <div class="notificacao <?= htmlspecialchars($_SESSION['mensagem']['tipo']) ?>">
        <?= htmlspecialchars($_SESSION['mensagem']['texto']) ?>
    </div>
    <?php unset($_SESSION['mensagem']); ?>
<?php endif; ?>