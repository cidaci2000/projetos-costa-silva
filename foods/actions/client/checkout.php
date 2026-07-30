<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'cliente') {
    header('Location: ../../pages/login.php');
    exit();
}

if (empty($_SESSION['carrinho'])) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Carrinho vazio!'];
    header('Location: ../../pages/client/cart.php');
    exit();
}

try {
    $conn = new PDO("mysql:host=localhost;dbname=foods_delivery", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

$forma_pagamento = $_POST['forma_pagamento'] ?? 'pix';

// Busca produtos do carrinho
$ids = array_keys($_SESSION['carrinho']);
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$stmt = $conn->prepare("SELECT * FROM produtos WHERE id IN ($placeholders)");
$stmt->execute($ids);
$produtos = $stmt->fetchAll();

$subtotal = 0;
foreach ($produtos as $p) {
    $subtotal += $p['preco'] * $_SESSION['carrinho'][$p['id']];
}

// Busca restaurante (assume que todos os produtos são do mesmo restaurante)
$restaurante_id = $produtos[0]['restaurante_id'] ?? 0;

// Busca taxa de entrega
$stmt = $conn->prepare("SELECT taxa_entrega FROM restaurantes WHERE id = ?");
$stmt->execute([$restaurante_id]);
$taxa_entrega = $stmt->fetchColumn() ?? 0;

$total = $subtotal + $taxa_entrega;

// Cria pedido
$conn->beginTransaction();

$stmt = $conn->prepare("
    INSERT INTO pedidos (cliente_id, restaurante_id, status, forma_pagamento, subtotal, taxa_entrega, total) 
    VALUES (?, ?, 'pendente', ?, ?, ?, ?)
");
$stmt->execute([$_SESSION['usuario_id'], $restaurante_id, $forma_pagamento, $subtotal, $taxa_entrega, $total]);
$pedido_id = $conn->lastInsertId();

// Insere itens
foreach ($produtos as $p) {
    $quantidade = $_SESSION['carrinho'][$p['id']];
    $stmt = $conn->prepare("INSERT INTO itens_pedido (pedido_id, produto_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)");
    $stmt->execute([$pedido_id, $p['id'], $quantidade, $p['preco']]);
}

$conn->commit();

// Limpa carrinho
$_SESSION['carrinho'] = [];

$_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Pedido realizado com sucesso! #' . $pedido_id];
header('Location: ../../pages/client/orders.php');
exit();
?>