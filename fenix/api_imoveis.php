<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$host = 'localhost';
$user = 'root';
$password = '';
$database = 'fenix_imobiliaria';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    echo json_encode(['error' => 'Erro de conexão: ' . $conn->connect_error]);
    exit;
}

$sql = "SELECT id, title, price, location, rooms, baths, area, status, featured, img, description FROM imoveis ORDER BY id";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $imoveis = [];
    while ($row = $result->fetch_assoc()) {
        $imoveis[] = [
            'id' => (int)$row['id'],
            'title' => $row['title'],
            'price' => $row['price'],
            'location' => $row['location'],
            'rooms' => (int)$row['rooms'],
            'baths' => (int)$row['baths'],
            'area' => $row['area'],
            'status' => $row['status'],
            'featured' => (bool)$row['featured'],
            'img' => $row['img'],
            'description' => $row['description']
        ];
    }
    echo json_encode($imoveis);
} else {
    echo json_encode([]);
}

$conn->close();
?>