<?php
require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Calcula preço final considerando promoção
 */
function precoFinal($produto) {
    if (!empty($produto['preco_promocional']) && $produto['preco_promocional'] > 0) {
        return (float) $produto['preco_promocional'];
    }
    return (float) $produto['preco'];
}

/**
 * Retorna URL da imagem (fallback para placeholder)
 */
function imagemUrl($imagem) {
    if (empty($imagem)) {
        return 'https://picsum.photos/seed/reyvie/600/600';
    }
    if (preg_match('#^https?://#', $imagem)) {
        return $imagem;
    }
    return 'uploads/' . htmlspecialchars($imagem);
}

/**
 * Busca produtos em destaque
 */
function getDestaques($limit = 6) {
    $stmt = db()->prepare("SELECT * FROM produtos WHERE destaque = 1 ORDER BY id DESC LIMIT :limit");
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

/**
 * Busca todos os produtos
 */
function getProdutos($busca = '') {
    if ($busca) {
        $stmt = db()->prepare("SELECT * FROM produtos WHERE nome LIKE :busca ORDER BY id DESC");
        $stmt->execute([':busca' => "%$busca%"]);
    } else {
        $stmt = db()->query("SELECT * FROM produtos ORDER BY id DESC");
    }
    return $stmt->fetchAll();
}

/**
 * Busca um produto
 */
function getProduto($id) {
    $stmt = db()->prepare("SELECT * FROM produtos WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
}

/**
 * ID do carrinho (sessão ou usuário)
 */
function getCartKey() {
    if (!empty($_SESSION['usuario_id'])) {
        return ['tipo' => 'usuario', 'valor' => $_SESSION['usuario_id']];
    }
    if (empty($_SESSION['sessao_id'])) {
        $_SESSION['sessao_id'] = bin2hex(random_bytes(16));
    }
    return ['tipo' => 'sessao', 'valor' => $_SESSION['sessao_id']];
}

/**
 * Adiciona ao carrinho
 */
function adicionarCarrinho($produtoId, $qtd = 1) {
    $key = getCartKey();
    $coluna = $key['tipo'] === 'usuario' ? 'usuario_id' : 'sessao_id';

    // Verifica se já existe
    $stmt = db()->prepare("SELECT id, quantidade FROM carrinho WHERE $coluna = :v AND produto_id = :p");
    $stmt->execute([':v' => $key['valor'], ':p' => $produtoId]);
    $item = $stmt->fetch();

    if ($item) {
        $stmt = db()->prepare("UPDATE carrinho SET quantidade = quantidade + :q WHERE id = :id");
        $stmt->execute([':q' => $qtd, ':id' => $item['id']]);
    } else {
        $stmt = db()->prepare("INSERT INTO carrinho ($coluna, produto_id, quantidade) VALUES (:v, :p, :q)");
        $stmt->execute([':v' => $key['valor'], ':p' => $produtoId, ':q' => $qtd]);
    }
    return true;
}

/**
 * Lista itens do carrinho
 */
function getCarrinho() {
    $key = getCartKey();
    $coluna = $key['tipo'] === 'usuario' ? 'usuario_id' : 'sessao_id';
    $stmt = db()->prepare("
        SELECT c.id as item_id, c.quantidade, p.*
        FROM carrinho c
        JOIN produtos p ON p.id = c.produto_id
        WHERE c.$coluna = :v
    ");
    $stmt->execute([':v' => $key['valor']]);
    return $stmt->fetchAll();
}

/**
 * Total do carrinho
 */
function totalCarrinho() {
    $itens = getCarrinho();
    $total = 0;
    foreach ($itens as $i) {
        $total += precoFinal($i) * $i['quantidade'];
    }
    return $total;
}

/**
 * Conta itens do carrinho
 */
function countCarrinho() {
    $itens = getCarrinho();
    $c = 0;
    foreach ($itens as $i) $c += $i['quantidade'];
    return $c;
}

/**
 * Notificação flash
 */
function setFlash($tipo, $msg) {
    $_SESSION['flash'] = ['tipo' => $tipo, 'msg' => $msg];
}

function getFlash() {
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

/**
 * Verifica se está logado
 */
function isLogged() {
    return !empty($_SESSION['usuario_id']);
}

function isAdmin() {
    return !empty($_SESSION['is_admin']);
}

/**
 * Redireciona
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Escapa HTML
 */
function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}