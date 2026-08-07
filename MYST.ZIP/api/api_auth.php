<?php
// api/api_auth.php
// API para autenticação de usuários

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['action'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Ação não especificada']);
    exit;
}

try {
    $pdo = getConnection();
    $action = $input['action'];

    switch ($action) {
        case 'login':
            if (empty($input['email']) || empty($input['password'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Email e senha são obrigatórios']);
                exit;
            }

            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
            $stmt->execute([$input['email']]);
            $user = $stmt->fetch();

            if (!$user) {
                http_response_code(401);
                echo json_encode(['error' => 'Usuário não encontrado']);
                exit;
            }

            // Verificar senha (hash bcrypt)
            if (!password_verify($input['password'], $user['password'])) {
                http_response_code(401);
                echo json_encode(['error' => 'Senha incorreta']);
                exit;
            }

            // Remover senha antes de enviar
            unset($user['password']);
            
            echo json_encode([
                'success' => true,
                'user' => $user,
                'message' => 'Login realizado com sucesso'
            ]);
            break;

        case 'register':
            if (empty($input['name']) || empty($input['email']) || empty($input['password'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Nome, email e senha são obrigatórios']);
                exit;
            }

            if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
                http_response_code(400);
                echo json_encode(['error' => 'Email inválido']);
                exit;
            }

            if (strlen($input['password']) < 6) {
                http_response_code(400);
                echo json_encode(['error' => 'A senha deve ter pelo menos 6 caracteres']);
                exit;
            }

            // Verificar se email já existe
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->execute([$input['email']]);
            if ($stmt->fetch()) {
                http_response_code(400);
                echo json_encode(['error' => 'Este email já está cadastrado']);
                exit;
            }

            // Hash da senha
            $hashedPassword = password_hash($input['password'], PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("
                INSERT INTO usuarios (name, email, password, telefone, role)
                VALUES (?, ?, ?, ?, 'usuario')
            ");
            
            $stmt->execute([
                trim($input['name']),
                trim($input['email']),
                $hashedPassword,
                trim($input['telefone'] ?? '')
            ]);

            $userId = $pdo->lastInsertId();

            echo json_encode([
                'success' => true,
                'message' => 'Cadastro realizado com sucesso!',
                'user' => [
                    'id' => $userId,
                    'name' => $input['name'],
                    'email' => $input['email'],
                    'role' => 'usuario'
                ]
            ]);
            break;

        case 'check_session':
            // Verifica se o usuário está autenticado
            // O frontend mantém o usuário no localStorage
            echo json_encode(['success' => true]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['error' => 'Ação desconhecida']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}