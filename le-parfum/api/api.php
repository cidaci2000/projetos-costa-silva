<?php
// ============================================================
// API LE PARFUM - Endpoints
// ============================================================

// CORS para desenvolvimento local
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Responder requisições OPTIONS (pré-voo CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../config/database.php';

// Obter ação
$action = $_GET['action'] ?? $_POST['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// ============================================================
// ROTEADOR
// ============================================================

try {
    switch ($action) {
        // ---- AUTENTICAÇÃO ----
        case 'login':
            handleLogin();
            break;
        case 'register':
            handleRegister();
            break;
        case 'logout':
            handleLogout();
            break;
        case 'me':
            handleGetUser();
            break;
        
        // ---- PRODUTOS ----
        case 'getProducts':
            handleGetProducts();
            break;
        case 'getProduct':
            handleGetProduct();
            break;
        case 'getCategories':
            handleGetCategories();
            break;
        case 'getFeatured':
            handleGetFeatured();
            break;
        
        // ---- CARRINHO ----
        case 'getCart':
            handleGetCart();
            break;
        case 'addToCart':
            handleAddToCart();
            break;
        case 'removeFromCart':
            handleRemoveFromCart();
            break;
        case 'updateCartQuantity':
            handleUpdateCartQuantity();
            break;
        case 'clearCart':
            handleClearCart();
            break;
        
        // ---- PEDIDOS ----
        case 'checkout':
            handleCheckout();
            break;
        case 'getOrders':
            handleGetOrders();
            break;
        case 'getOrder':
            handleGetOrder();
            break;
        case 'updateOrderStatus':
            handleUpdateOrderStatus();
            break;
        
        // ---- FAVORITOS ----
        case 'getFavorites':
            handleGetFavorites();
            break;
        case 'toggleFavorite':
            handleToggleFavorite();
            break;
        
        // ---- ADMIN ----
        case 'adminStats':
            handleAdminStats();
            break;
        case 'adminProducts':
            handleAdminProducts();
            break;
        case 'adminUsers':
            handleAdminUsers();
            break;
        
        default:
            respondJson(false, null, 'Ação não encontrada');
    }
} catch (Exception $e) {
    respondJson(false, null, 'Erro interno: ' . $e->getMessage());
}

// ============================================================
// IMPLEMENTAÇÕES
// ============================================================

// ─── AUTH ────────────────────────────────────────────────

function handleLogin() {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        // Tentar pegar dados POST normais
        $data = $_POST;
    }
    
    $email = $data['email'] ?? '';
    $senha = $data['senha'] ?? '';
    
    if (empty($email) || empty($senha)) {
        respondJson(false, null, 'E-mail e senha são obrigatórios');
    }
    
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email AND ativo = 1");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();
    
    if (!$user || !password_verify($senha, $user['senha'])) {
        respondJson(false, null, 'E-mail ou senha inválidos');
    }
    
    // Remover senha
    unset($user['senha']);
    
    // Gerar token e salvar na sessão
    $token = generateToken();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_token'] = $token;
    
    // Salvar token no banco
    $stmt = $pdo->prepare("UPDATE usuarios SET token = :token WHERE id = :id");
    $stmt->execute(['token' => $token, 'id' => $user['id']]);
    
    respondJson(true, [
        'usuario' => $user,
        'token' => $token
    ], 'Login realizado com sucesso');
}

function handleRegister() {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        $data = $_POST;
    }
    
    // Validação
    $required = ['nome', 'email', 'senha'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            respondJson(false, null, "Campo '$field' é obrigatório");
        }
    }
    
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        respondJson(false, null, 'E-mail inválido');
    }
    
    if (strlen($data['senha']) < 6) {
        respondJson(false, null, 'Senha deve ter no mínimo 6 caracteres');
    }
    
    $pdo = getConnection();
    
    // Verificar se e-mail já existe
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
    $stmt->execute(['email' => $data['email']]);
    if ($stmt->fetch()) {
        respondJson(false, null, 'Este e-mail já está cadastrado');
    }
    
    // Hash da senha
    $senhaHash = password_hash($data['senha'], PASSWORD_DEFAULT);
    
    // Inserir usuário
    $stmt = $pdo->prepare("
        INSERT INTO usuarios (nome, email, senha, telefone, cidade, cep, endereco)
        VALUES (:nome, :email, :senha, :telefone, :cidade, :cep, :endereco)
    ");
    
    $result = $stmt->execute([
        'nome' => $data['nome'],
        'email' => $data['email'],
        'senha' => $senhaHash,
        'telefone' => $data['telefone'] ?? '',
        'cidade' => $data['cidade'] ?? '',
        'cep' => $data['cep'] ?? '',
        'endereco' => $data['endereco'] ?? ''
    ]);
    
    if ($result) {
        respondJson(true, null, 'Usuário cadastrado com sucesso');
    } else {
        respondJson(false, null, 'Erro ao cadastrar usuário');
    }
}

function handleLogout() {
    session_start();
    
    // Limpar token do banco
    if (isset($_SESSION['user_id'])) {
        $pdo = getConnection();
        $stmt = $pdo->prepare("UPDATE usuarios SET token = NULL WHERE id = :id");
        $stmt->execute(['id' => $_SESSION['user_id']]);
    }
    
    session_destroy();
    respondJson(true, null, 'Logout realizado');
}

function handleGetUser() {
    $userId = getAuthenticatedUserId();
    if (!$userId) {
        respondJson(false, null, 'Usuário não autenticado');
    }
    
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch();
    
    if ($user) {
        unset($user['senha']);
        respondJson(true, ['usuario' => $user]);
    } else {
        respondJson(false, null, 'Usuário não encontrado');
    }
}

// ─── PRODUTOS ─────────────────────────────────────────────

function handleGetProducts() {
    $pdo = getConnection();
    $category = $_GET['cat'] ?? '';
    $search = $_GET['search'] ?? '';
    $minPrice = isset($_GET['min']) ? floatval($_GET['min']) : 0;
    $maxPrice = isset($_GET['max']) ? floatval($_GET['max']) : 99999;
    $sort = $_GET['sort'] ?? 'destaque';
    
    $sql = "SELECT p.*, c.nome as categoria_nome 
            FROM produtos p
            LEFT JOIN categorias c ON p.categoria = c.slug
            WHERE p.disponivel = 1
            AND p.preco BETWEEN :min AND :max";
    
    $params = [
        'min' => $minPrice,
        'max' => $maxPrice
    ];
    
    if (!empty($category)) {
        $sql .= " AND p.categoria = :category";
        $params['category'] = $category;
    }
    
    if (!empty($search)) {
        $sql .= " AND (p.nome LIKE :search OR p.marca LIKE :search)";
        $params['search'] = "%$search%";
    }
    
    // Ordenação
    switch ($sort) {
        case 'preco-asc':
            $sql .= " ORDER BY p.preco ASC";
            break;
        case 'preco-desc':
            $sql .= " ORDER BY p.preco DESC";
            break;
        case 'nome':
            $sql .= " ORDER BY p.nome ASC";
            break;
        default:
            $sql .= " ORDER BY p.destaque DESC, p.data_cadastro DESC";
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll();
    
    respondJson(true, ['products' => $products]);
}

function handleGetProduct() {
    $id = $_GET['id'] ?? 0;
    if (!$id) {
        respondJson(false, null, 'ID do produto é obrigatório');
    }
    
    $pdo = getConnection();
    $stmt = $pdo->prepare("
        SELECT p.*, c.nome as categoria_nome 
        FROM produtos p
        LEFT JOIN categorias c ON p.categoria = c.slug
        WHERE p.id = :id
    ");
    $stmt->execute(['id' => $id]);
    $product = $stmt->fetch();
    
    if ($product) {
        respondJson(true, ['product' => $product]);
    } else {
        respondJson(false, null, 'Produto não encontrado');
    }
}

function handleGetCategories() {
    $pdo = getConnection();
    $stmt = $pdo->query("
        SELECT c.*, COUNT(p.id) as total 
        FROM categorias c
        LEFT JOIN produtos p ON c.slug = p.categoria AND p.disponivel = 1
        GROUP BY c.id
        ORDER BY c.nome
    ");
    $categories = $stmt->fetchAll();
    respondJson(true, ['categories' => $categories]);
}

function handleGetFeatured() {
    $pdo = getConnection();
    $stmt = $pdo->query("
        SELECT * FROM produtos 
        WHERE destaque = 1 AND disponivel = 1
        ORDER BY data_cadastro DESC
        LIMIT 8
    ");
    $products = $stmt->fetchAll();
    respondJson(true, ['products' => $products]);
}

// ─── AUTH HELPER ──────────────────────────────────────────

function getAuthenticatedUserId() {
    // Verificar token no header
    $headers = getallheaders();
    $token = $headers['Authorization'] ?? '';
    $token = str_replace('Bearer ', '', trim($token));
    
    if (empty($token)) {
        // Verificar se está na sessão
        if (isset($_SESSION['user_id'])) {
            return $_SESSION['user_id'];
        }
        return null;
    }
    
    // Buscar usuário pelo token
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE token = :token AND ativo = 1");
    $stmt->execute(['token' => $token]);
    $user = $stmt->fetch();
    
    return $user ? $user['id'] : null;
}

function isAdmin($userId) {
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT papel FROM usuarios WHERE id = :id");
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch();
    return $user && $user['papel'] === 'admin';
}

// ─── CARRINHO ─────────────────────────────────────────────

function handleGetCart() {
    session_start();
    $cart = $_SESSION['cart'] ?? [];
    
    $total = 0;
    foreach ($cart as $item) {
        $total += ($item['preco'] ?? 0) * ($item['quantidade'] ?? 0);
    }
    
    respondJson(true, ['cart' => $cart, 'total' => $total]);
}

function handleAddToCart() {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        $data = $_POST;
    }
    
    $productId = $data['product_id'] ?? 0;
    $quantity = $data['quantity'] ?? 1;
    
    if (!$productId) {
        respondJson(false, null, 'ID do produto é obrigatório');
    }
    
    // Buscar produto
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = :id AND disponivel = 1");
    $stmt->execute(['id' => $productId]);
    $product = $stmt->fetch();
    
    if (!$product) {
        respondJson(false, null, 'Produto não disponível');
    }
    
    session_start();
    $cart = $_SESSION['cart'] ?? [];
    
    // Verificar se produto já está no carrinho
    $found = false;
    foreach ($cart as $key => $item) {
        if ($item['id'] == $productId) {
            $cart[$key]['quantidade'] += $quantity;
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        $cart[] = [
            'id' => $product['id'],
            'nome' => $product['nome'],
            'marca' => $product['marca'],
            'preco' => floatval($product['preco']),
            'imagem_emoji' => $product['imagem_emoji'] ?? '🌸',
            'quantidade' => $quantity
        ];
    }
    
    $_SESSION['cart'] = $cart;
    
    // Calcular total
    $total = 0;
    $count = 0;
    foreach ($cart as $item) {
        $total += $item['preco'] * $item['quantidade'];
        $count += $item['quantidade'];
    }
    
    respondJson(true, [
        'cart' => $cart,
        'total' => $total,
        'item_count' => $count
    ], 'Produto adicionado ao carrinho');
}

function handleRemoveFromCart() {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        $data = $_POST;
    }
    
    $productId = $data['product_id'] ?? 0;
    
    if (!$productId) {
        respondJson(false, null, 'ID do produto é obrigatório');
    }
    
    session_start();
    $cart = $_SESSION['cart'] ?? [];
    
    foreach ($cart as $key => $item) {
        if ($item['id'] == $productId) {
            unset($cart[$key]);
            break;
        }
    }
    
    $cart = array_values($cart);
    $_SESSION['cart'] = $cart;
    
    respondJson(true, ['cart' => $cart], 'Produto removido');
}

function handleUpdateCartQuantity() {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        $data = $_POST;
    }
    
    $productId = $data['product_id'] ?? 0;
    $quantity = $data['quantity'] ?? 1;
    
    if (!$productId) {
        respondJson(false, null, 'ID do produto é obrigatório');
    }
    
    if ($quantity <= 0) {
        respondJson(false, null, 'Quantidade deve ser maior que 0');
    }
    
    session_start();
    $cart = $_SESSION['cart'] ?? [];
    
    foreach ($cart as $key => $item) {
        if ($item['id'] == $productId) {
            $cart[$key]['quantidade'] = $quantity;
            break;
        }
    }
    
    $_SESSION['cart'] = $cart;
    
    respondJson(true, ['cart' => $cart], 'Quantidade atualizada');
}

function handleClearCart() {
    session_start();
    $_SESSION['cart'] = [];
    respondJson(true, null, 'Carrinho limpo');
}

// ─── PEDIDOS ──────────────────────────────────────────────

function handleCheckout() {
    $userId = getAuthenticatedUserId();
    if (!$userId) {
        respondJson(false, null, 'Faça login para finalizar o pedido');
    }
    
    session_start();
    $cart = $_SESSION['cart'] ?? [];
    
    if (empty($cart)) {
        respondJson(false, null, 'Carrinho vazio');
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        $data = $_POST;
    }
    
    $endereco = $data['endereco'] ?? '';
    $metodoPagamento = $data['metodo_pagamento'] ?? 'pix';
    
    if (empty($endereco)) {
        respondJson(false, null, 'Endereço de entrega é obrigatório');
    }
    
    $pdo = getConnection();
    $pdo->beginTransaction();
    
    try {
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['preco'] * $item['quantidade'];
        }
        
        // Gerar número do pedido
        $numeroPedido = 'LP-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        // Inserir pedido
        $stmt = $pdo->prepare("
            INSERT INTO pedidos (usuario_id, numero_pedido, total, metodo_pagamento, endereco_entrega)
            VALUES (:usuario_id, :numero_pedido, :total, :metodo_pagamento, :endereco)
        ");
        
        $stmt->execute([
            'usuario_id' => $userId,
            'numero_pedido' => $numeroPedido,
            'total' => $total,
            'metodo_pagamento' => $metodoPagamento,
            'endereco' => $endereco
        ]);
        
        $pedidoId = $pdo->lastInsertId();
        
        // Inserir itens do pedido
        foreach ($cart as $item) {
            $subtotal = $item['preco'] * $item['quantidade'];
            $stmt = $pdo->prepare("
                INSERT INTO itens_pedido (pedido_id, produto_id, nome_produto, preco_unitario, quantidade, subtotal)
                VALUES (:pedido_id, :produto_id, :nome, :preco, :quantidade, :subtotal)
            ");
            $stmt->execute([
                'pedido_id' => $pedidoId,
                'produto_id' => $item['id'],
                'nome' => $item['nome'],
                'preco' => $item['preco'],
                'quantidade' => $item['quantidade'],
                'subtotal' => $subtotal
            ]);
            
            // Atualizar estoque
            $stmt = $pdo->prepare("
                UPDATE produtos SET quantidade_estoque = quantidade_estoque - :qtd
                WHERE id = :id
            ");
            $stmt->execute([
                'qtd' => $item['quantidade'],
                'id' => $item['id']
            ]);
        }
        
        // Limpar carrinho
        $_SESSION['cart'] = [];
        
        $pdo->commit();
        
        respondJson(true, [
            'pedido_id' => $pedidoId,
            'numero_pedido' => $numeroPedido,
            'total' => $total
        ], 'Pedido finalizado com sucesso!');
        
    } catch (Exception $e) {
        $pdo->rollBack();
        respondJson(false, null, 'Erro ao finalizar pedido: ' . $e->getMessage());
    }
}

function handleGetOrders() {
    $userId = getAuthenticatedUserId();
    if (!$userId) {
        respondJson(false, null, 'Usuário não autenticado');
    }
    
    $pdo = getConnection();
    $stmt = $pdo->prepare("
        SELECT * FROM pedidos 
        WHERE usuario_id = :user_id 
        ORDER BY data_pedido DESC
    ");
    $stmt->execute(['user_id' => $userId]);
    $orders = $stmt->fetchAll();
    
    respondJson(true, ['orders' => $orders]);
}

function handleGetOrder() {
    $orderId = $_GET['id'] ?? 0;
    $userId = getAuthenticatedUserId();
    
    if (!$userId) {
        respondJson(false, null, 'Usuário não autenticado');
    }
    
    $pdo = getConnection();
    $stmt = $pdo->prepare("
        SELECT * FROM pedidos WHERE id = :id AND usuario_id = :user_id
    ");
    $stmt->execute(['id' => $orderId, 'user_id' => $userId]);
    $order = $stmt->fetch();
    
    if (!$order) {
        respondJson(false, null, 'Pedido não encontrado');
    }
    
    // Buscar itens
    $stmt = $pdo->prepare("SELECT * FROM itens_pedido WHERE pedido_id = :pedido_id");
    $stmt->execute(['pedido_id' => $orderId]);
    $items = $stmt->fetchAll();
    
    $order['itens'] = $items;
    
    respondJson(true, ['order' => $order]);
}

function handleUpdateOrderStatus() {
    $userId = getAuthenticatedUserId();
    if (!$userId || !isAdmin($userId)) {
        respondJson(false, null, 'Acesso negado');
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        $data = $_POST;
    }
    
    $orderId = $data['order_id'] ?? 0;
    $status = $data['status'] ?? '';
    
    $allowedStatus = ['pendente', 'aprovado', 'enviado', 'entregue', 'cancelado'];
    if (!in_array($status, $allowedStatus)) {
        respondJson(false, null, 'Status inválido');
    }
    
    $pdo = getConnection();
    $stmt = $pdo->prepare("
        UPDATE pedidos SET status = :status WHERE id = :id
    ");
    $result = $stmt->execute(['status' => $status, 'id' => $orderId]);
    
    if ($result) {
        respondJson(true, null, 'Status atualizado');
    } else {
        respondJson(false, null, 'Erro ao atualizar status');
    }
}

// ─── FAVORITOS ────────────────────────────────────────────

function handleGetFavorites() {
    $userId = getAuthenticatedUserId();
    if (!$userId) {
        respondJson(false, null, 'Usuário não autenticado');
    }
    
    $pdo = getConnection();
    $stmt = $pdo->prepare("
        SELECT p.* FROM favoritos f
        JOIN produtos p ON f.produto_id = p.id
        WHERE f.usuario_id = :user_id
    ");
    $stmt->execute(['user_id' => $userId]);
    $favorites = $stmt->fetchAll();
    
    respondJson(true, ['favorites' => $favorites]);
}

function handleToggleFavorite() {
    $userId = getAuthenticatedUserId();
    if (!$userId) {
        respondJson(false, null, 'Usuário não autenticado');
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        $data = $_POST;
    }
    
    $productId = $data['product_id'] ?? 0;
    
    if (!$productId) {
        respondJson(false, null, 'ID do produto é obrigatório');
    }
    
    $pdo = getConnection();
    
    // Verificar se já está nos favoritos
    $stmt = $pdo->prepare("
        SELECT id FROM favoritos WHERE usuario_id = :user_id AND produto_id = :produto_id
    ");
    $stmt->execute(['user_id' => $userId, 'produto_id' => $productId]);
    $exists = $stmt->fetch();
    
    if ($exists) {
        // Remover
        $stmt = $pdo->prepare("
            DELETE FROM favoritos WHERE usuario_id = :user_id AND produto_id = :produto_id
        ");
        $stmt->execute(['user_id' => $userId, 'produto_id' => $productId]);
        respondJson(true, ['favorited' => false], 'Removido dos favoritos');
    } else {
        // Adicionar
        $stmt = $pdo->prepare("
            INSERT INTO favoritos (usuario_id, produto_id) VALUES (:user_id, :produto_id)
        ");
        $stmt->execute(['user_id' => $userId, 'produto_id' => $productId]);
        respondJson(true, ['favorited' => true], 'Adicionado aos favoritos');
    }
}

// ─── ADMIN ────────────────────────────────────────────────

function handleAdminStats() {
    $userId = getAuthenticatedUserId();
    if (!$userId || !isAdmin($userId)) {
        respondJson(false, null, 'Acesso negado');
    }
    
    $pdo = getConnection();
    
    // Total de usuários
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
    $users = $stmt->fetch();
    
    // Total de produtos
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM produtos");
    $products = $stmt->fetch();
    
    // Total de pedidos
    $stmt = $pdo->query("SELECT COUNT(*) as total, SUM(total) as total_vendas FROM pedidos WHERE status != 'cancelado'");
    $orders = $stmt->fetch();
    
    // Pedidos pendentes
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM pedidos WHERE status = 'pendente'");
    $pending = $stmt->fetch();
    
    respondJson(true, [
        'stats' => [
            'usuarios' => intval($users['total']),
            'produtos' => intval($products['total']),
            'pedidos' => intval($orders['total']),
            'vendas' => floatval($orders['total_vendas'] ?? 0),
            'pendentes' => intval($pending['total'])
        ]
    ]);
}

function handleAdminProducts() {
    $userId = getAuthenticatedUserId();
    if (!$userId || !isAdmin($userId)) {
        respondJson(false, null, 'Acesso negado');
    }
    
    $pdo = getConnection();
    $stmt = $pdo->query("SELECT * FROM produtos ORDER BY data_cadastro DESC");
    $products = $stmt->fetchAll();
    
    respondJson(true, ['products' => $products]);
}

function handleAdminUsers() {
    $userId = getAuthenticatedUserId();
    if (!$userId || !isAdmin($userId)) {
        respondJson(false, null, 'Acesso negado');
    }
    
    $pdo = getConnection();
    $stmt = $pdo->query("SELECT id, nome, email, telefone, cidade, data_cadastro, papel, ativo FROM usuarios ORDER BY data_cadastro DESC");
    $users = $stmt->fetchAll();
    
    respondJson(true, ['users' => $users]);
}