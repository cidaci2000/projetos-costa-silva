<?php
require_once __DIR__ . '/../includes/functions.php';
if (!isAdmin()) redirecionar('../login.php');

$pdo = getConnection();
$id = (int)($_GET['id'] ?? 0);
$categorias = listarCategorias();

$stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
$stmt->execute([$id]);
$produto = $stmt->fetch();
if (!$produto) redirecionar('produtos.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imagem = $produto['imagem'];
    $galeria = json_decode($produto['galeria'] ?? '[]', true) ?: [];

    // Nova imagem principal (opcional)
    if (!empty($_FILES['imagem']['name'])) {
        $up = uploadImagem($_FILES['imagem']);
        if (isset($up['sucesso'])) {
            deletarImagem($produto['imagem']);
            $imagem = $up['sucesso'];
        } else {
            flash('erro', 'Erro: ' . $up['erro']);
            redirecionar('produto-editar.php?id=' . $id);
        }
    }

    // Nova galeria (substitui a antiga se enviada)
    if (!empty($_FILES['galeria']['name'][0])) {
        foreach ($galeria as $g) deletarImagem($g);
        $galeria = [];
        foreach ($_FILES['galeria']['name'] as $i => $name) {
            if ($_FILES['galeria']['error'][$i] === UPLOAD_ERR_OK) {
                $file = [
                    'name' => $name,
                    'tmp_name' => $_FILES['galeria']['tmp_name'][$i],
                    'size' => $_FILES['galeria']['size'][$i],
                    'error' => $_FILES['galeria']['error'][$i],
                ];
                $r = uploadImagem($file);
                if (isset($r['sucesso'])) $galeria[] = $r['sucesso'];
            }
        }
    }

    $pdo->prepare("
        UPDATE produtos SET nome=?, descricao=?, descricao_longa=?, categoria_id=?,
            preco=?, preco_promocional=?, estoque=?, imagem=?, galeria=?, destaque=?, ativo=?
        WHERE id=?
    ")->execute([
        $_POST['nome'],
        $_POST['descricao'],
        $_POST['descricao_longa'] ?? null,
        (int)$_POST['categoria_id'],
        (float)$_POST['preco'],
        !empty($_POST['preco_promocional']) ? (float)$_POST['preco_promocional'] : null,
        (int)$_POST['estoque'],
        $imagem,
        !empty($galeria) ? json_encode($galeria) : null,
        isset($_POST['destaque']) ? 1 : 0,
        isset($_POST['ativo']) ? 1 : 0,
        $id,
    ]);

    flash('sucesso', 'Produto atualizado!');
    redirecionar('produtos.php');
}

$titulo = 'Editar Produto';
include __DIR__ . '/../includes/header.php';
?>
<div class="container-principal">
    <h2 class="secao-titulo">Editar Produto</h2>
    <div style="background:#fff; padding:2rem; border-radius:12px;">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group">
                    <label>Nome *</label>
                    <input name="nome" value="<?= htmlspecialchars($produto['nome']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Categoria *</label>
                    <select name="categoria_id" required>
                        <?php foreach ($categorias as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= $c['id'] == $produto['categoria_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Descrição Curta *</label>
                <input name="descricao" value="<?= htmlspecialchars($produto['descricao']) ?>" required>
            </div>

            <div class="form-group">
                <label>Descrição Longa</label>
                <textarea name="descricao_longa" rows="4" style="width:100%;padding:.75rem;border:2px solid #E8E3DD;border-radius:8px;"><?= htmlspecialchars($produto['descricao_longa'] ?? '') ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Preço *</label>
                    <input type="number" step="0.01" name="preco" value="<?= $produto['preco'] ?>" required>
                </div>
                <div class="form-group">
                    <label>Preço Promocional</label>
                    <input type="number" step="0.01" name="preco_promocional" value="<?= $produto['preco_promocional'] ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Estoque *</label>
                    <input type="number" name="estoque" value="<?= $produto['estoque'] ?>" required>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <label style="display:flex;gap:.5rem;margin-top:.75rem;">
                        <input type="checkbox" name="destaque" value="1" <?= $produto['destaque'] ? 'checked' : '' ?>> Destaque
                    </label>
                    <label style="display:flex;gap:.5rem;">
                        <input type="checkbox" name="ativo" value="1" <?= $produto['ativo'] ? 'checked' : '' ?>> Ativo
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label>Imagem Principal Atual</label><br>
                <img src="<?= imagemUrl($produto['imagem']) ?>" class="img-preview"
                     onerror="this.src='https://via.placeholder.com/120/E8D4C4/8B6F47?text=🏺'">
                <input type="file" name="imagem" accept="image/*" style="margin-top:.5rem;" onchange="previewImg(event,'pv')">
                <img id="pv" class="img-preview" style="display:none;">
            </div>

            <div class="form-group">
                <label>Substituir Galeria (opcional)</label>
                <input type="file" name="galeria[]" accept="image/*" multiple>
                <?php if ($g = galeriaProduto($produto)): ?>
                    <div class="galeria-miniaturas">
                        <?php foreach ($g as $img): ?>
                            <img src="<?= imagemUrl($img) ?>">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <button class="btn-enviar">Salvar Alterações</button>
            <a href="produtos.php" class="btn-info" style="margin-top:1rem;display:inline-block;">Voltar</a>
        </form>
    </div>
</div>
<script>
function previewImg(e, targetId) {
    const file = e.target.files[0]; if (!file) return;
    const reader = new FileReader();
    reader.onload = ev => {
        const img = document.getElementById(targetId);
        img.src = ev.target.result; img.style.display = 'block';
    };
    reader.readAsDataURL(file);
}
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>