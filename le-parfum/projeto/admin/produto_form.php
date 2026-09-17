<?php
$titulo = 'Produto';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/funcoes.php';
require_once __DIR__ . '/../includes/auth.php';
exigirAdmin();

$id = (int)($_GET['id'] ?? 0);
$produto = [
    'nome' => '', 'notas' => '', 'descricao' => '', 'preco' => '',
    'preco_antigo' => '', 'imagem' => '', 'categoria_id' => '',
    'destaque' => 0, 'lancamento' => 0, 'oferta' => 0, 'estoque' => 100
];

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
    $stmt->execute([$id]);
    $produto = $stmt->fetch() ?: $produto;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = [
        'nome'         => trim($_POST['nome']),
        'notas'        => trim($_POST['notas']),
        'descricao'    => trim($_POST['descricao']),
        'preco'        => (float)$_POST['preco'],
        'preco_antigo' => $_POST['preco_antigo'] !== '' ? (float)$_POST['preco_antigo'] : null,
        'imagem'       => trim($_POST['imagem']),
        'categoria_id' => (int)$_POST['categoria_id'],
        'destaque'     => isset($_POST['destaque']) ? 1 : 0,
        'lancamento'   => isset($_POST['lancamento']) ? 1 : 0,
        'oferta'       => isset($_POST['oferta']) ? 1 : 0,
        'estoque'      => (int)$_POST['estoque'],
    ];

    if ($id) {
        $sql = "UPDATE produtos SET nome=:nome, notas=:notas, descricao=:descricao,
                preco=:preco, preco_antigo=:preco_antigo, imagem=:imagem,
                categoria_id=:categoria_id, destaque=:destaque, lancamento=:lancamento,
                oferta=:oferta, estoque=:estoque WHERE id=:id";
        $dados['id'] = $id;
        $pdo->prepare($sql)->execute($dados);
        flash('Produto atualizado!', 'success');
    } else {
        $sql = "INSERT INTO produtos 
                (nome, notas, descricao, preco, preco_antigo, imagem, categoria_id, destaque, lancamento, oferta, estoque)
                VALUES (:nome, :notas, :descricao, :preco, :preco_antigo, :imagem, :categoria_id, :destaque, :lancamento, :oferta, :estoque)";
        $pdo->prepare($sql)->execute($dados);
        flash('Produto cadastrado!', 'success');
    }
    redirect(BASE_URL . 'admin/produtos.php');
}

$cats = $pdo->query("SELECT * FROM categorias")->fetchAll();
require_once __DIR__ . '/../includes/header.php';
?>
<section class="page-content">
  <div class="container" style="max-width:700px;">
    <h1 style="margin-bottom:20px;"><?= $id ? 'Editar' : 'Novo' ?> Produto</h1>

    <form method="post" style="background:#fff;padding:28px;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,0.05);">
      <div class="form-group"><label>Nome</label><input type="text" name="nome" value="<?= e($produto['nome']) ?>" required /></div>
      <div class="form-group"><label>Notas</label><input type="text" name="notas" value="<?= e($produto['notas']) ?>" /></div>
      <div class="form-group"><label>Descrição</label><textarea name="descricao" rows="4" style="width:100%;padding:12px;border:1px solid #ddd;border-radius:8px;font-family:inherit;"><?= e($produto['descricao']) ?></textarea></div>
      <div class="form-row">
        <div class="form-group"><label>Preço</label><input type="number" step="0.01" name="preco" value="<?= $produto['preco'] ?>" required /></div>
        <div class="form-group"><label>Preço antigo (opcional)</label><input type="number" step="0.01" name="preco_antigo" value="<?= $produto['preco_antigo'] ?>" /></div>
      </div>
      <div class="form-group"><label>URL da imagem</label><input type="text" name="imagem" value="<?= e($produto['imagem']) ?>" /></div>
      <div class="form-row">
        <div class="form-group">
          <label>Categoria</label>
          <select name="categoria_id" style="width:100%;padding:12px;border:1px solid #ddd;border-radius:8px;">
            <?php foreach ($cats as $c): ?>
              <option value="<?= $c['id'] ?>" <?= $produto['categoria_id'] == $c['id'] ? 'selected' : '' ?>><?= e($c['nome']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group"><label>Estoque</label><input type="number" name="estoque" value="<?= $produto['estoque'] ?>" /></div>
      </div>
      <div class="form-group" style="display:flex;gap:20px;">
        <label><input type="checkbox" name="destaque" <?= $produto['destaque'] ? 'checked' : '' ?> /> Destaque</label>
        <label><input type="checkbox" name="lancamento" <?= $produto['lancamento'] ? 'checked' : '' ?> /> Lançamento</label>
        <label><input type="checkbox" name="oferta" <?= $produto['oferta'] ? 'checked' : '' ?> /> Oferta</label>
      </div>
      <button type="submit" class="btn btn-gold" style="width:100%;">Salvar</button>
      <a href="<?= BASE_URL ?>admin/produtos.php" class="btn btn-outline" style="width:100%;margin-top:10px;">Voltar</a>
    </form>
  </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>