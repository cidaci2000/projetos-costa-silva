<?php
// api/api_admin.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

require_once '../config/database.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['action'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Ação não especificada']);
    exit;
}

try {
    $pdo = getConnection();
    $action = $input['action'];
    $response = ['success' => false, 'message' => ''];

    switch ($action) {
        case 'create_carro':
            $stmt = $pdo->prepare("
                INSERT INTO carros (marca, modelo, ano, preco, cor, motor, potencia, transmissao, quilometragem, descricao, imagem, destaque, disponivel)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $result = $stmt->execute([
                $input['marca'],
                $input['modelo'],
                $input['ano'],
                $input['preco'],
                $input['cor'] ?? null,
                $input['motor'] ?? null,
                $input['potencia'] ?? null,
                $input['transmissao'] ?? null,
                $input['quilometragem'] ?? 0,
                $input['descricao'] ?? null,
                $input['imagem'] ?? null,
                $input['destaque'] ?? 0,
                $input['disponivel'] ?? 1
            ]);
            
            if ($result) {
                $response = ['success' => true, 'message' => 'Carro criado com sucesso', 'id' => $pdo->lastInsertId()];
            } else {
                $response = ['success' => false, 'message' => 'Erro ao criar carro'];
            }
            break;
            
        case 'update_carro':
            if (!isset($input['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'ID não fornecido']);
                exit;
            }
            
            $stmt = $pdo->prepare("
                UPDATE carros SET
                    marca = ?, modelo = ?, ano = ?, preco = ?,
                    cor = ?, motor = ?, potencia = ?, transmissao = ?,
                    quilometragem = ?, descricao = ?, imagem = ?,
                    destaque = ?, disponivel = ?
                WHERE id = ?
            ");
            
            $result = $stmt->execute([
                $input['marca'],
                $input['modelo'],
                $input['ano'],
                $input['preco'],
                $input['cor'] ?? null,
                $input['motor'] ?? null,
                $input['potencia'] ?? null,
                $input['transmissao'] ?? null,
                $input['quilometragem'] ?? 0,
                $input['descricao'] ?? null,
                $input['imagem'] ?? null,
                $input['destaque'] ?? 0,
                $input['disponivel'] ?? 1,
                $input['id']
            ]);
            
            if ($result) {
                $response = ['success' => true, 'message' => 'Carro atualizado com sucesso'];
            } else {
                $response = ['success' => false, 'message' => 'Erro ao atualizar carro'];
            }
            break;
            
        case 'delete_carro':
            if (!isset($input['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'ID não fornecido']);
                exit;
            }
            
            $stmt = $pdo->prepare("DELETE FROM carros WHERE id = ?");
            $result = $stmt->execute([$input['id']]);
            
            if ($result && $stmt->rowCount() > 0) {
                $response = ['success' => true, 'message' => 'Carro excluído com sucesso'];
            } else {
                $response = ['success' => false, 'message' => 'Erro ao excluir carro'];
            }
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Ação desconhecida']);
            exit;
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>