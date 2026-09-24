<?php
require_once __DIR__ . '/../includes/functions.php';
if (!isAdmin()) redirect('../login.php');

$id = (int)($_GET['id'] ?? 0);
$produto = $id ? getProduto($id) : null;
$categorias = db()->query("SELECT * FROM categorias ORDER BY nome")->fetchAll();
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $preco = (float)($_POST['preco'] ?? 0);
    $precoPromo = ($_POST['preco_promocional'] ?? '') !== '' ? (float)$_POST['preco_promocional'] : null;
    $emoji = $_POST['emoji'] ?: '💄';
    $categoriaId = $_POST['categoria_id'] ? (int)$_POST['categoria_id'] : null;
    $estoque = (int)($_POST['estoque'] ?? 0);
    $destaque = isset($_POST['destaque']) ? 1 : 0;

    // Imagem: upload ou URL
    $imagem = $_POST['imagem_url'] ?? '';
    if (!empty($_FILES['imagem']['name'])) {
        $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
        $permitidas = ['jpg','jpeg','png','webp','gif'];
        if (in_array($ext, $permitidas)) {
            $uploadDir = __DIR__ . '/../uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $nomeArq = uniqid('prod_') . '.' . $ext;
            if (move_uploaded_file($_FILES['imagem']['tmp_name'], $uploadDir . $nomeArq)) {
                $imagem = $nomeArq;
            }
        } else {
            $erro = 'Formato de imagem inválido. Use JPG, PNG, WEBP ou GIF.';
        }
    }

    if (!$erro) {
        $dados = [
            ':nome' => $nome,
            ':descricao' => $descricao,
            ':preco' => $preco,
            ':preco_promocional' => $precoPromo,
            ':imagem' => $imagem,
            ':emoji' => $emoji,
            ':categoria_id' => $categoriaId,
            ':estoque' => $estoque,
            ':destaque' => $destaque,
        ];

        try {
            if ($id) {
                $dados[':id'] = $id;
                $sql = "UPDATE produtos SET
                            nome = :nome,
                            descricao = :descricao,
                            preco = :preco,
                            preco_promocional = :preco_promocional,
                            imagem = :imagem,
                            emoji = :emoji,
                            categoria_id = :categoria_id,
                            estoque = :estoque,
                            destaque = :destaque
                        WHERE id = :id";
            } else {
                $sql = "INSERT INTO produtos
                            (nome, descricao, preco, preco_promocional, imagem, emoji, categoria_id, estoque, destaque)
                        VALUES
                            (:nome, :descricao, :preco, :preco_promocional, :imagem, :emoji, :categoria_id, :estoque, :destaque)";
            }
            db()->prepare($sql)->execute($dados);
            setFlash('sucesso', $id ? 'Produto atualizado com sucesso!' : 'Produto criado com sucesso!');
            redirect('produtos.php');
        } catch (PDOException $e) {
            $erro = 'Erro ao salvar: ' . $e->getMessage();
        }
    }
}

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $id ? 'Editar' : 'Novo' ?> Produto - Admin</title>
<?php include __DIR__ . '/../style.php'; ?>
</head>
<body>

<?php if ($flash): ?>
    <div class="notificacao <?= $flash['tipo'] ?>"><?= e($flash['msg']) ?></div>
<?php endif; ?>

<div class="wrap" style="max-width:760px;">
    <div class="admin-header">
        <h1><?= $id ? '✏️ Editar Produto' : '➕ Novo Produto' ?></h1>
        <a href="produtos.php" class="btn btn-secondary">← Voltar</a>
    </div>

    <div class="form-box" style="max-width:100%;">
        <?php if ($erro): ?>
            <div class="form-erro"><?= e($erro) ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-col">
                    <label>Nome *</label>
                    <input type="text" name="nome" required value="<?= e($produto['nome'] ?? '') ?>">
                </div>
                <div class="form-col">
                    <label>Categoria</label>
                    <select name="categoria_id">
                        <option value="">-- Selecione --</option>
                        <?php foreach ($categorias as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= ($produto['categoria_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                                <?= e($c['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <label>Descrição</label>
            <textarea name="descricao" rows="3"><?= e($produto['descricao'] ?? '') ?></textarea>

            <div class="form-row">
                <div class="form-col">
                    <label>Preço (R$) *</label>
                    <input type="number" step="0.01" min="0" name="preco" required value="<?= e($produto['preco'] ?? '') ?>">
                </div>
                <div class="form-col">
                    <label>Preço Promocional (R$)</label>
                    <input type="number" step="0.01" min="0" name="preco_promocional" value="<?= e($produto['preco_promocional'] ?? '') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-col">
                    <label>Estoque</label>
                    <input type="number" min="0" name="estoque" value="<?= e($produto['estoque'] ?? 0) ?>">
                </div>
                <div class="form-col">
                    <label>Emoji (fallback)</label>
                    <input type="text" name="emoji" maxlength="5" value="<?= e($produto['emoji'] ?? '💄') ?>">
                </div>
            </div>

            <label>Imagem (upload)</label>
            <input type="file" name="imagem" accept="image/*">
            <?php if (!empty($produto['imagem'])): ?>
                <img class="img-preview" src="<?= imagemUrl($produto['imagem']) ?>" alt="Preview">
            <?php endif; ?>

            <label style="margin-top:1rem;">OU URL da imagem</label>
            <input type="url" name="imagem_url" placeholder="https://..." value="<?= e($produto['imagem'] ?? '') ?>">

            <label class="form-check">
                <input type="checkbox" name="destaque" <?= !empty($produto['destaque']) ? 'checked' : '' ?>>
                Exibir em destaque na home
            </label>

            <div style="display:flex; gap:.5rem; margin-top:1.5rem;">
                <button class="btn btn-primary">💾 Salvar</button>
                <a href="produtos.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>