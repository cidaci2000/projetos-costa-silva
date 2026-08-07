<?php
// api/api_mensagens.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../config/database.php';

try {
    $pdo = getConnection();
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $stmt = $pdo->query("SELECT * FROM mensagens ORDER BY id DESC");
        echo json_encode($stmt->fetchAll());
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || !isset($input['action'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Ação não especificada']);
            exit;
        }
        
        switch ($input['action']) {
            case 'marcar_lida':
                $stmt = $pdo->prepare("UPDATE mensagens SET lida = 1 WHERE id = ?");
                $stmt->execute([$input['id']]);
                echo json_encode(['success' => true, 'message' => 'Mensagem marcada como lida']);
                break;
                
            case 'marcar_respondida':
                $stmt = $pdo->prepare("UPDATE mensagens SET respondida = 1, lida = 1 WHERE id = ?");
                $stmt->execute([$input['id']]);
                echo json_encode(['success' => true, 'message' => 'Mensagem marcada como respondida']);
                break;
                
            case 'excluir':
                $stmt = $pdo->prepare("DELETE FROM mensagens WHERE id = ?");
                $stmt->execute([$input['id']]);
                echo json_encode(['success' => true, 'message' => 'Mensagem excluída']);
                break;
                
            default:
                http_response_code(400);
                echo json_encode(['error' => 'Ação desconhecida']);
        }
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>