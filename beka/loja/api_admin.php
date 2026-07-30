<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$host = 'localhost';
$user = 'root';
$password = '';
$database = 'loja_esportiva';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Erro de conexão: ' . $conn->connect_error]));
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

switch ($action) {
    case 'create':
        handleCreate($conn, $input);
        break;
    case 'update':
        handleUpdate($conn, $input);
        break;
    case 'delete':
        handleDelete($conn, $input);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Ação inválida']);
        break;
}

$conn->close();

// Criar produto
function handleCreate($conn, $data) {
    $name = $conn->real_escape_string($data['name'] ?? '');
    $category = $conn->real_escape_string($data['category'] ?? '');
    $price = floatval($data['price'] ?? 0);
    $old_price = isset($data['old_price']) && $data['old_price'] ? floatval($data['old_price']) : 'NULL';
    $description = $conn->real_escape_string($data['description'] ?? '');
    $rating = intval($data['rating'] ?? 5);
    $badge = $conn->real_escape_string($data['badge'] ?? '');
    $image = $conn->real_escape_string($data['image'] ?? '');

    if (empty($name) || empty($category) || $price <= 0) {
        echo json_encode(['success' => false, 'message' => 'Nome, categoria e preço são obrigatórios']);
        return;
    }

    $sql = "INSERT INTO produtos (name, category, price, old_price, description, rating, badge, image) 
            VALUES ('$name', '$category', $price, $old_price, '$description', $rating, '$badge', '$image')";

    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Produto criado com sucesso!', 'id' => $conn->insert_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao criar produto: ' . $conn->error]);
    }
}

// Atualizar produto
function handleUpdate($conn, $data) {
    $id = intval($data['id'] ?? 0);
    $name = $conn->real_escape_string($data['name'] ?? '');
    $category = $conn->real_escape_string($data['category'] ?? '');
    $price = floatval($data['price'] ?? 0);
    $old_price = isset($data['old_price']) && $data['old_price'] ? floatval($data['old_price']) : 'NULL';
    $description = $conn->real_escape_string($data['description'] ?? '');
    $rating = intval($data['rating'] ?? 5);
    $badge = $conn->real_escape_string($data['badge'] ?? '');
    $image = $conn->real_escape_string($data['image'] ?? '');

    if ($id <= 0 || empty($name) || empty($category) || $price <= 0) {
        echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
        return;
    }

    $sql = "UPDATE produtos SET 
            name = '$name',
            category = '$category',
            price = $price,
            old_price = $old_price,
            description = '$description',
            rating = $rating,
            badge = '$badge',
            image = '$image'
            WHERE id = $id";

    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Produto atualizado com sucesso!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao atualizar: ' . $conn->error]);
    }
}

// Excluir produto
function handleDelete($conn, $data) {
    $id = intval($data['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID inválido']);
        return;
    }

    $sql = "DELETE FROM produtos WHERE id = $id";

    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Produto excluído com sucesso!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao excluir: ' . $conn->error]);
    }
}
?>