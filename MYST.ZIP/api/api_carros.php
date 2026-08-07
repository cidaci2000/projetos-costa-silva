<?php
// api/api_carros.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../config/database.php';

try {
    $pdo = getConnection();
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
        
        if ($id) {
            $stmt = $pdo->prepare("SELECT * FROM carros WHERE id = ?");
            $stmt->execute([$id]);
            $carro = $stmt->fetch();
            
            if ($carro) {
                echo json_encode($carro);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Carro não encontrado']);
            }
        } else {
            $stmt = $pdo->query("SELECT * FROM carros ORDER BY id DESC");
            echo json_encode($stmt->fetchAll());
        }
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>