<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/funcoes.php';
require_once __DIR__ . '/../includes/auth.php';

if (isAdmin()) redirect(BASE_URL . 'admin/index.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? AND admin = 1");
    $stmt->execute([$email]);
    $u = $stmt->fetch();

    if ($u && password_verify($senha, $u['senha'])) {
        login((int)$u['id'], $u['nome'], $u['email'], 1);
        redirect(BASE_URL . 'admin/index.php');
    } else {
        flash('Credenciais inválidas.', 'error');
    }
}

require_once __DIR__ . '/../includes/header.php';
?>
<section class="page-content">
  <div class="container">
    <div class="form-card">
      <h2>🔐 Admin Le Parfum</h2>
      <form method="post">
        <div class="form-group"><label>E-mail</label><input type="email" name="email" required /></div>
        <div class="form-group"><label>Senha</label><input type="password" name="senha" required /></div>
        <button type="submit" class="btn btn-gold">Entrar</button>
      </form>
    </div>
  </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>