<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$host = 'localhost';
$user = 'root';
$password = '';
$database = 'loja_esportiva';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    echo json_encode(['error' => 'Erro de conexão: ' . $conn->connect_error]);
    exit;
}

// Se for POST, pode ser do admin
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    // Redirecionar para admin se tiver ação
    if (isset($input['action'])) {
        include 'api_admin.php';
        exit;
    }
}

// Buscar produtos (GET)
$sql = "SELECT id, name, category, price, old_price, description, rating, badge, image FROM produtos ORDER BY id";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = [
            'id' => (int)$row['id'],
            'name' => $row['name'],
            'category' => $row['category'],
            'price' => (float)$row['price'],
            'old_price' => $row['old_price'] ? (float)$row['old_price'] : null,
            'description' => $row['description'],
            'rating' => (int)$row['rating'],
            'badge' => $row['badge'],
            'image' => $row['image'] ?? 'https://via.placeholder.com/300x250?text=Produto'
        ];
    }
    echo json_encode($products);
} else {
    echo json_encode([]);
}

$conn->close();
?>