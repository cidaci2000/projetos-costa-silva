<?php
require_once __DIR__ . '/../includes/functions.php';
if (!isAdmin()) redirecionar('../login.php');

$pdo = getConnection();
$categorias = listarCategorias();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    if ($acao === 'criar') {
        // Upload da imagem principal (obrigatório)
        $upload = uploadImagem($_FILES['imagem'] ?? []);
        if (isset($upload['erro'])) {
            flash('erro', 'Erro na imagem: ' . $upload['erro']);
            redirecionar('produtos.php');
        }

        // Galeria (opcional, múltiplas)
        $galeria = [];
        if (!empty($_FILES['galeria']['name'][0])) {
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

        $stmt = $pdo->prepare("
            INSERT INTO produtos (nome, descricao, descricao_longa, categoria_id, preco, preco_promocional, estoque, imagem, galeria, destaque)
            VALUES (?,?,?,?,?,?,?,?,?,?)
        ");
        $stmt->execute([
            $_POST['nome'],
            $_POST['descricao'],
            $_POST['descricao_longa'] ?? null,
            (int)$_POST['categoria_id'],
            (float)$_POST['preco'],
            !empty($_POST['preco_promocional']) ? (float)$_POST['preco_promocional'] : null,
            (int)$_POST['estoque'],
            $upload['sucesso'],
            !empty($galeria) ? json_encode($galeria) : null,
            isset($_POST['destaque']) ? 1 : 0,
        ]);
        flash('sucesso', 'Produto cadastrado!');
        redirecionar('produtos.php');
    }

    if ($acao === 'remover') {
        $id = (int)$_POST['id'];
        $prod = $pdo->prepare("SELECT imagem, galeria FROM produtos WHERE id = ?");
        $prod->execute([$id]);
        $p = $prod->fetch();
        if ($p) {
            deletarImagem($p['imagem']);
            if ($p['galeria']) {
                foreach (json_decode($p['galeria'], true) ?: [] as $img) deletarImagem($img);
            }
        }
        $pdo->prepare("UPDATE produtos SET ativo = 0 WHERE id = ?")->execute([$id]);
        flash('sucesso', 'Produto desativado.');
        redirecionar('produtos.php');
    }
}

$produtos = $pdo->query("
    SELECT p.*, c.nome AS categoria_nome
    FROM produtos p JOIN categorias c ON c.id = p.categoria_id
    ORDER BY p.id DESC
")->fetchAll();

$titulo = 'Admin - Produtos';
include __DIR__ . '/../includes/header.php';
?>
<div class="container-principal">
    <h2 class="secao-titulo">Gerenciar Produtos</h2>

    <div style="background:#fff; padding:2rem; border-radius:12px; margin-bottom:2rem;">
        <h3 style="margin-bottom:1rem;">Adicionar Novo Produto</h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="acao" value="criar">
            <div class="form-row">
                <div class="form-group">
                    <label>Nome *</label>
                    <input name="nome" required>
                </div>
                <div class="form-group">
                    <label>Categoria *</label>
                    <select name="categoria_id" required>
                        <?php foreach ($categorias as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Descrição Curta *</label>
                <input name="descricao" required maxlength="255">
            </div>

            <div class="form-group">
                <label>Descrição Longa</label>
                <textarea name="descricao_longa" rows="4" style="width:100%;padding:.75rem;border:2px solid #E8E3DD;border-radius:8px;"></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Preço (R$) *</label>
                    <input type="number" step="0.01" name="preco" required>
                </div>
                <div class="form-group">
                    <label>Preço Promocional (R$)</label>
                    <input type="number" step="0.01" name="preco_promocional">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Estoque *</label>
                    <input type="number" name="estoque" value="0" required>
                </div>
                <div class="form-group">
                    <label>Destaque</label>
                    <label style="display:flex; align-items:center; gap:.5rem; margin-top:.75rem;">
                        <input type="checkbox" name="destaque" value="1"> Exibir em destaque
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label>Imagem Principal * (JPG, PNG, WEBP - máx 5MB)</label>
                <input type="file" name="imagem" accept="image/*" required onchange="previewImg(event, 'preview-principal')">
                <img id="preview-principal" class="img-preview" style="display:none;">
            </div>

            <div class="form-group">
                <label>Galeria (múltiplas imagens - opcional)</label>
                <input type="file" name="galeria[]" accept="image/*" multiple>
            </div>

            <button class="btn-enviar">Cadastrar Produto</button>
        </form>
    </div>

    <h3 style="margin-bottom:1rem;">Produtos Cadastrados</h3>
    <div class="produtos-grid">
        <?php foreach ($produtos as $p): ?>
            <div class="produto-card">
                <div class="produto-imagem">
                    <img src="<?= imagemUrl($p['imagem']) ?>"
                         onerror="this.src='https://via.placeholder.com/400x300/E8D4C4/8B6F47?text=🏺'">
                </div>
                <div class="produto-info">
                    <h3 class="produto-nome">
                        <?= htmlspecialchars($p['nome']) ?>
                        <?php if (!$p['ativo']): ?><span class="badge-promo" style="background:#999">INATIVO</span><?php endif; ?>
                    </h3>
                    <p style="font-size:.85rem;color:#888;"><?= htmlspecialchars($p['categoria_nome']) ?> | Estoque: <?= $p['estoque'] ?></p>
                    <div class="produto-preco">R$ <?= number_format($p['preco'], 2, ',', '.') ?></div>
                    <div class="produto-acoes">
                        <a href="produto-editar.php?id=<?= $p['id'] ?>" class="btn-info" style="flex:1;text-align:center;">Editar</a>
                        <form method="POST" onsubmit="return confirm('Desativar produto?')">
                            <input type="hidden" name="acao" value="remover">
                            <input type="hidden" name="id" value="<?= $p['id'] ?>">
                            <button class="btn-remover">Desativar</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
function previewImg(e, targetId) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = ev => {
        const img = document.getElementById(targetId);
        img.src = ev.target.result;
        img.style.display = 'block';
    };
    reader.readAsDataURL(file);
}
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>