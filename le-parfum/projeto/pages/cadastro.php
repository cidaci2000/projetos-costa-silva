<?php
$titulo = 'Cadastro';
require_once __DIR__ . '/../includes/header.php';

if (usuarioLogado()) redirect(BASE_URL . 'pages/perfil.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (strlen($senha) < 6) {
        flash('A senha deve ter ao menos 6 caracteres.', 'error');
    } else {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            flash('Este e-mail já está cadastrado.', 'error');
        } else {
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
            $stmt->execute([$nome, $email, $hash]);

            login((int)$pdo->lastInsertId(), $nome, $email, 0);
            flash('Conta criada com sucesso!', 'success');
            redirect(BASE_URL . 'pages/perfil.php');
        }
    }
}
?>

<section class="page-content">
  <div class="container">
    <div class="form-card">
      <h2>Criar Conta</h2>
      <p class="subtitle">Junte-se à Le Parfum</p>
      <form method="post">
        <div class="form-group"><label>Nome</label><input type="text" name="nome" required /></div>
        <div class="form-group"><label>E-mail</label><input type="email" name="email" required /></div>
        <div class="form-group"><label>Senha</label><input type="password" name="senha" required minlength="6" /></div>
        <button type="submit" class="btn btn-gold">Cadastrar</button>
      </form>
      <div class="form-link">Já tem conta? <a href="<?= BASE_URL ?>pages/login.php">Entrar</a></div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>