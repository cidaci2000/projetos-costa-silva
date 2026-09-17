<?php
require_once __DIR__ . '/../config/init.php';

// ===== IMAGENS =====
function imagemUrl($arquivo) {
    if (!$arquivo) return UPLOAD_URL . 'placeholder.jpg';
    // Aceita tanto URL externa quanto arquivo local
    if (filter_var($arquivo, FILTER_VALIDATE_URL)) return $arquivo;
    return UPLOAD_URL . htmlspecialchars($arquivo);
}

function uploadImagem($file) {
    if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['erro' => 'Nenhum arquivo enviado ou erro no upload.'];
    }
    if ($file['size'] > UPLOAD_MAX_SIZE) {
        return ['erro' => 'Arquivo maior que 5MB.'];
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if (!in_array($mime, UPLOAD_ALLOWED)) {
        return ['erro' => 'Formato inválido. Use JPG, PNG, WEBP ou GIF.'];
    }
    if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);

    $ext = match($mime) {
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
    };
    $nome = bin2hex(random_bytes(8)) . '-' . time() . '.' . $ext;
    $destino = UPLOAD_DIR . $nome;
    if (!move_uploaded_file($file['tmp_name'], $destino)) {
        return ['erro' => 'Falha ao salvar imagem.'];
    }
    return ['sucesso' => $nome];
}

function deletarImagem($arquivo) {
    if (!$arquivo || filter_var($arquivo, FILTER_VALIDATE_URL)) return;
    $path = UPLOAD_DIR . $arquivo;
    if (file_exists($path)) unlink($path);
}

// ===== PRODUTOS =====
function listarProdutos($categoriaSlug = null, $destaque = false) {
    $pdo = getConnection();
    $sql = "SELECT p.*, c.slug AS categoria_slug, c.nome AS categoria_nome
            FROM produtos p
            JOIN categorias c ON c.id = p.categoria_id
            WHERE p.ativo = 1";
    $params = [];
    if ($categoriaSlug && $categoriaSlug !== 'todos') {
        $sql .= " AND c.slug = ?";
        $params[] = $categoriaSlug;
    }
    if ($destaque) $sql .= " AND p.destaque = 1";
    $sql .= " ORDER BY p.id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function buscarProduto($id) {
    $pdo = getConnection();
    $stmt = $pdo->prepare("
        SELECT p.*, c.slug AS categoria_slug, c.nome AS categoria_nome
        FROM produtos p
        JOIN categorias c ON c.id = p.categoria_id
        WHERE p.id = ? AND p.ativo = 1
    ");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function listarCategorias() {
    $pdo = getConnection();
    return $pdo->query("SELECT * FROM categorias ORDER BY id")->fetchAll();
}

function precoFinal($produto) {
    return $produto['preco_promocional'] ?? $produto['preco'];
}

function galeriaProduto($produto) {
    if (empty($produto['galeria'])) return [];
    $g = json_decode($produto['galeria'], true);
    return is_array($g) ? $g : [];
}

// ===== CARRINHO =====
function obterItensCarrinho() {
    $pdo = getConnection();
    if (isLogado()) {
        $stmt = $pdo->prepare("
            SELECT c.id, c.quantidade, p.id AS produto_id, p.nome, p.preco, p.preco_promocional, p.imagem
            FROM carrinho c JOIN produtos p ON p.id = c.produto_id
            WHERE c.usuario_id = ?
        ");
        $stmt->execute([$_SESSION['usuario_id']]);
    } else {
        $stmt = $pdo->prepare("
            SELECT c.id, c.quantidade, p.id AS produto_id, p.nome, p.preco, p.preco_promocional, p.imagem
            FROM carrinho c JOIN produtos p ON p.id = c.produto_id
            WHERE c.sessao_id = ?
        ");
        $stmt->execute([$_SESSION['sessao_id']]);
    }
    return $stmt->fetchAll();
}

function contarItensCarrinho() {
    return array_sum(array_column(obterItensCarrinho(), 'quantidade'));
}

function adicionarAoCarrinho($produtoId, $qtd = 1) {
    $pdo = getConnection();
    if (!buscarProduto($produtoId)) return false;

    if (isLogado()) {
        $stmt = $pdo->prepare("SELECT id FROM carrinho WHERE usuario_id = ? AND produto_id = ?");
        $stmt->execute([$_SESSION['usuario_id'], $produtoId]);
    } else {
        $stmt = $pdo->prepare("SELECT id FROM carrinho WHERE sessao_id = ? AND produto_id = ?");
        $stmt->execute([$_SESSION['sessao_id'], $produtoId]);
    }
    $item = $stmt->fetch();

    if ($item) {
        $pdo->prepare("UPDATE carrinho SET quantidade = quantidade + ? WHERE id = ?")
            ->execute([$qtd, $item['id']]);
    } else {
        if (isLogado()) {
            $pdo->prepare("INSERT INTO carrinho (usuario_id, produto_id, quantidade) VALUES (?,?,?)")
                ->execute([$_SESSION['usuario_id'], $produtoId, $qtd]);
        } else {
            $pdo->prepare("INSERT INTO carrinho (sessao_id, produto_id, quantidade) VALUES (?,?,?)")
                ->execute([$_SESSION['sessao_id'], $produtoId, $qtd]);
        }
    }
    return true;
}

function atualizarQuantidade($itemId, $qtd) {
    if ($qtd < 1) return false;
    getConnection()->prepare("UPDATE carrinho SET quantidade = ? WHERE id = ?")
        ->execute([$qtd, $itemId]);
    return true;
}

function removerDoCarrinho($itemId) {
    getConnection()->prepare("DELETE FROM carrinho WHERE id = ?")->execute([$itemId]);
}

function limparCarrinho() {
    $pdo = getConnection();
    if (isLogado()) {
        $pdo->prepare("DELETE FROM carrinho WHERE usuario_id = ?")->execute([$_SESSION['usuario_id']]);
    } else {
        $pdo->prepare("DELETE FROM carrinho WHERE sessao_id = ?")->execute([$_SESSION['sessao_id']]);
    }
}

function calcularTotais() {
    $itens = obterItensCarrinho();
    $subtotal = 0;
    foreach ($itens as $item) {
        $preco = $item['preco_promocional'] ?? $item['preco'];
        $subtotal += $preco * $item['quantidade'];
        $itens[array_search($item, $itens, true)]['preco_final'] = $preco;
    }
    $frete = $subtotal > 100 ? 0 : ($subtotal > 0 ? 15 : 0);
    return ['subtotal' => $subtotal, 'frete' => $frete, 'total' => $subtotal + $frete, 'itens' => $itens];
}

// ===== PEDIDOS =====
function criarPedido($usuarioId) {
    $pdo = getConnection();
    $totais = calcularTotais();
    if (empty($totais['itens'])) return false;

    try {
        $pdo->beginTransaction();
        $numero = 'PED-' . date('Y') . '-' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
        $previsao = date('Y-m-d', strtotime('+5 days'));

        $pdo->prepare("
            INSERT INTO pedidos (usuario_id, numero_pedido, subtotal, frete, total, previsao_entrega, codigo_rastreio)
            VALUES (?,?,?,?,?,?,?)
        ")->execute([
            $usuarioId, $numero,
            $totais['subtotal'], $totais['frete'], $totais['total'],
            $previsao, 'BR' . rand(100000000, 999999999) . 'BR'
        ]);
        $pedidoId = $pdo->lastInsertId();

        $stmtItem = $pdo->prepare("INSERT INTO itens_pedido (pedido_id, produto_id, quantidade, preco_unitario) VALUES (?,?,?,?)");
        foreach ($totais['itens'] as $item) {
            $stmtItem->execute([$pedidoId, $item['produto_id'], $item['quantidade'], $item['preco_final']]);
            $pdo->prepare("UPDATE produtos SET estoque = estoque - ? WHERE id = ?")
                ->execute([$item['quantidade'], $item['produto_id']]);
        }

        limparCarrinho();
        $pdo->commit();
        return $numero;
    } catch (Exception $e) {
        $pdo->rollBack();
        return false;
    }
}

function listarPedidosUsuario($usuarioId) {
    $stmt = getConnection()->prepare("SELECT * FROM pedidos WHERE usuario_id = ? ORDER BY criado_em DESC");
    $stmt->execute([$usuarioId]);
    return $stmt->fetchAll();
}

function buscarPedidoPorNumero($numero) {
    $stmt = getConnection()->prepare("SELECT * FROM pedidos WHERE numero_pedido = ?");
    $stmt->execute([$numero]);
    return $stmt->fetch();
}

function itensDoPedido($pedidoId) {
    $stmt = getConnection()->prepare("
        SELECT ip.*, p.nome, p.imagem
        FROM itens_pedido ip JOIN produtos p ON p.id = ip.produto_id
        WHERE ip.pedido_id = ?
    ");
    $stmt->execute([$pedidoId]);
    return $stmt->fetchAll();
}