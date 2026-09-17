<?php
require_once 'includes/functions.php';

// Já logado? Redireciona
if (isLogado()) {
    redirecionar('index.php');
}

$erro = '';
$emailPreenchido = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $emailPreenchido = htmlspecialchars($email);

    if ($email === '' || $senha === '') {
        $erro = 'Preencha e-mail e senha.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'E-mail inválido.';
    } else {
        try {
            $pdo = getConnection();
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            // DEBUG - descomente se precisar investigar
            // var_dump($user); exit;

            if (!$user) {
                $erro = 'E-mail não encontrado.';
            } elseif (!password_verify($senha, $user['senha'])) {
                $erro = 'Senha incorreta.';
            } else {
                // Login OK
                $_SESSION['usuario_id']    = (int)$user['id'];
                $_SESSION['usuario_nome']  = $user['nome'];
                $_SESSION['usuario_email'] = $user['email'];
                $_SESSION['usuario_tipo']  = $user['tipo'] ?? 'cliente';

                // Regenera ID de sessão (segurança)
                session_regenerate_id(true);

                // Migra carrinho anônimo para o usuário
                if (!empty($_SESSION['sessao_id'])) {
                    $pdo->prepare("
                        UPDATE carrinho 
                        SET usuario_id = ?, sessao_id = NULL 
                        WHERE sessao_id = ? AND usuario_id IS NULL
                    ")->execute([$user['id'], $_SESSION['sessao_id']]);
                }

                flash('sucesso', 'Bem-vindo, ' . $user['nome'] . '!');
                redirecionar('index.php');
            }
        } catch (PDOException $e) {
            $erro = 'Erro no servidor. Tente novamente.';
            // Em dev: $erro = $e->getMessage();
        }
    }
}

$titulo = 'Login';
include 'includes/header.php';
?>
<div class="container-principal">
    <div class="modal-content" style="margin:0 auto;">
        <div class="modal-header"><h2>Entrar</h2></div>

        <?php if ($erro): ?>
            <div style="background:#ffe5e5;color:#a00;padding:1rem;border-radius:8px;margin-bottom:1rem;border-left:4px solid #D64545;">
                <?= htmlspecialchars($erro) ?>
            </div>
        <?php endif; ?>

        <form method="POST" autocomplete="on">
            <div class="form-group">
                <label>E-mail</label>
                <input type="email" name="email" value="<?= $emailPreenchido ?>" required autofocus>
            </div>
            <div class="form-group">
                <label>Senha</label>
                <input type="password" name="senha" required>
            </div>
            <button type="submit" class="btn-enviar">Entrar</button>
            <p style="text-align:center; margin-top:1rem;">
                Não tem conta? <a href="cadastro.php">Cadastre-se</a>
            </p>
        </form>
    </div>
</div>
<?php include 'includes/footer.php'; ?>