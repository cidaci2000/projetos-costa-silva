<?php
require_once 'includes/functions.php';

if (isLogado()) redirecionar('index.php');

$erro = '';
$old = ['nome'=>'', 'email'=>'', 'telefone'=>'', 'endereco'=>'', 'cidade'=>'', 'estado'=>'', 'cep'=>''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome       = trim($_POST['nome'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $senha      = $_POST['senha'] ?? '';
    $confirma   = $_POST['confirmar_senha'] ?? '';
    $telefone   = trim($_POST['telefone'] ?? '');
    $nascimento = $_POST['data_nascimento'] ?? null;
    $endereco   = trim($_POST['endereco'] ?? '');
    $cidade     = trim($_POST['cidade'] ?? '');
    $estado     = trim($_POST['estado'] ?? '');
    $cep        = trim($_POST['cep'] ?? '');

    $old = compact('nome','email','telefone','endereco','cidade','estado','cep');

    if ($nome === '' || $email === '' || $senha === '') {
        $erro = 'Preencha os campos obrigatórios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'E-mail inválido.';
    } elseif ($senha !== $confirma) {
        $erro = 'As senhas não coincidem.';
    } elseif (strlen($senha) < 6) {
        $erro = 'A senha deve ter no mínimo 6 caracteres.';
    } else {
        try {
            $pdo = getConnection();
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);

            if ($stmt->fetch()) {
                $erro = 'Este e-mail já está cadastrado.';
            } else {
                $hash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("
                    INSERT INTO usuarios 
                        (nome, email, senha, telefone, data_nascimento, endereco, cidade, estado, cep, tipo)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'cliente')
                ");
                $stmt->execute([
                    $nome, $email, $hash, $telefone,
                    $nascimento ?: null,
                    $endereco, $cidade, $estado, $cep
                ]);
                $userId = (int)$pdo->lastInsertId();

                // Sessão
                $_SESSION['usuario_id']    = $userId;
                $_SESSION['usuario_nome']  = $nome;
                $_SESSION['usuario_email'] = $email;
                $_SESSION['usuario_tipo']  = 'cliente';

                session_regenerate_id(true);

                // Migra carrinho
                if (!empty($_SESSION['sessao_id'])) {
                    $pdo->prepare("
                        UPDATE carrinho 
                        SET usuario_id = ?, sessao_id = NULL 
                        WHERE sessao_id = ? AND usuario_id IS NULL
                    ")->execute([$userId, $_SESSION['sessao_id']]);
                }

                flash('sucesso', "Conta criada com sucesso, $nome!");
                redirecionar('index.php');
            }
        } catch (PDOException $e) {
            $erro = 'Erro ao criar conta. Tente novamente.';
            // Em dev: $erro = $e->getMessage();
        }
    }
}

$titulo = 'Cadastro';
include 'includes/header.php';
?>
<div class="container-principal">
    <div class="modal-content" style="margin:0 auto;">
        <div class="modal-header"><h2>Criar Conta</h2></div>

        <?php if ($erro): ?>
            <div style="background:#ffe5e5;color:#a00;padding:1rem;border-radius:8px;margin-bottom:1rem;border-left:4px solid #D64545;">
                <?= htmlspecialchars($erro) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label>Nome Completo *</label>
                    <input type="text" name="nome" value="<?= htmlspecialchars($old['nome']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Data de Nascimento</label>
                    <input type="date" name="data_nascimento">
                </div>
            </div>
            <div class="form-group">
                <label>E-mail *</label>
                <input type="email" name="email" value="<?= htmlspecialchars($old['email']) ?>" required>
            </div>
            <div class="form-group">
                <label>Telefone</label>
                <input type="tel" name="telefone" value="<?= htmlspecialchars($old['telefone']) ?>">
            </div>
            <div class="form-group">
                <label>Endereço</label>
                <input type="text" name="endereco" value="<?= htmlspecialchars($old['endereco']) ?>">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Cidade</label>
                    <input type="text" name="cidade" value="<?= htmlspecialchars($old['cidade']) ?>">
                </div>
                <div class="form-group">
                    <label>Estado</label>
                    <input type="text" name="estado" value="<?= htmlspecialchars($old['estado']) ?>">
                </div>
            </div>
            <div class="form-group">
                <label>CEP</label>
                <input type="text" name="cep" value="<?= htmlspecialchars($old['cep']) ?>">
            </div>
            <div class="form-group">
                <label>Senha * (mínimo 6 caracteres)</label>
                <input type="password" name="senha" required minlength="6">
            </div>
            <div class="form-group">
                <label>Confirmar Senha *</label>
                <input type="password" name="confirmar_senha" required minlength="6">
            </div>
            <button type="submit" class="btn-enviar">Criar Conta</button>
            <p style="text-align:center; margin-top:1rem;">
                Já tem conta? <a href="login.php">Entrar</a>
            </p>
        </form>
    </div>
</div>
<?php include 'includes/footer.php'; ?>