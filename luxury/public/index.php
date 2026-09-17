<?php
/**
 * Ponto de entrada da API
 */

// Configuração de CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Content-Type: application/json; charset=utf-8');

// Tratar requisições OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Autoload simples
spl_autoload_register(function ($class) {
    $prefix = '';
    $base_dir = __DIR__ . '/../src/';
    
    $file = $base_dir . str_replace('\\', '/', $class) . '.php';
    
    if (file_exists($file)) {
        require $file;
        return true;
    }
    
    // Tentar em models
    $file = $base_dir . 'models/' . $class . '.php';
    if (file_exists($file)) {
        require $file;
        return true;
    }
    
    // Tentar em controllers
    $file = $base_dir . 'controllers/' . $class . '.php';
    if (file_exists($file)) {
        require $file;
        return true;
    }
    
    return false;
});

// Carregar configuração
require_once __DIR__ . '/../config/database.php';

// Carregar rotas
require_once __DIR__ . '/../src/routes/api.php';

// Obter método e URI
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Remover query string da URI
$uri = strtok($uri, '?');

// Obter corpo da requisição
$requestBody = [];
if ($method === 'POST' || $method === 'PUT') {
    $input = file_get_contents('php://input');
    $requestBody = json_decode($input, true);
    
    // Se não for JSON, usar $_POST
    if (json_last_error() !== JSON_ERROR_NONE) {
        $requestBody = $_POST;
    }
}

// Rotear
try {
    $response = route($method, $uri, $requestBody);
    http_response_code($response['success'] ? 200 : 400);
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno do servidor',
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}