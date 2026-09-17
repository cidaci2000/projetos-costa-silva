<?php
$titulo = 'Entrar';
require_once __DIR__ . '/../includes/header.php';

if (usuarioLogado()) redirect(BASE_URL . 'pages/perfil.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $u = $stmt->fetch();

    if ($u && password_verify($senha, $u['senha'])) {
        login((int)$u['id'], $u['nome'], $u['email'], (int)$u['admin']);
        flash("Bem-vindo(a), {$u['nome']}!", 'success');
        redirect(BASE_URL . 'pages/perfil.php');
    } else {
        flash('E-mail ou senha inválidos.', 'error');
    }
}
?>

<section class="page-content">
  <div class="container">
    <div class="form-card">
      <h2>Bem-vindo de volta</h2>
      <p class="subtitle">Entre na sua conta Le Parfum</p>
      <form method="post">
        <div class="form-group">
          <label>E-mail</label>
          <input type="email" name="email" required />
        </div>
        <div class="form-group">
          <label>Senha</label>
          <input type="password" name="senha" required />
        </div>
        <button type="submit" class="btn btn-gold">Entrar</button>
      </form>
      <div class="form-link">
        Não tem conta? <a href="<?= BASE_URL ?>pages/cadastro.php">Cadastre-se</a>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>