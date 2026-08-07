<?php
// api/api_contato.php
// API para receber mensagens de contato - VERSÃO CORRIGIDA

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Ativar logs
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Função de log
function logMsg($msg) {
    $logFile = __DIR__ . '/../logs/contato.log';
    $dir = dirname($logFile);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - " . $msg . "\n", FILE_APPEND);
}

logMsg("=== NOVA REQUISIÇÃO ===");
logMsg("METHOD: " . $_SERVER['REQUEST_METHOD']);

// OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// GET (teste)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode([
        'status' => 'ok',
        'message' => 'API de contato funcionando',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
}

// Verificar se é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido. Use POST.']);
    exit;
}

// Pegar dados
$rawInput = file_get_contents('php://input');
logMsg("RAW INPUT: " . $rawInput);

$input = json_decode($rawInput, true);
logMsg("Dados decodificados: " . print_r($input, true));

// Validar
if (!$input) {
    logMsg("ERRO: JSON inválido");
    http_response_code(400);
    echo json_encode(['error' => 'JSON inválido']);
    exit;
}

if (empty($input['nome']) || empty($input['email']) || empty($input['mensagem'])) {
    logMsg("ERRO: Campos obrigatórios faltando");
    http_response_code(400);
    echo json_encode([
        'error' => 'Nome, email e mensagem são obrigatórios',
        'received' => $input
    ]);
    exit;
}

// Conectar ao banco
try {
    require_once __DIR__ . '/../config/database.php';
    $pdo = getConnection();
    logMsg("✅ Conexão com banco OK");

    // Verificar se a tabela existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'mensagens'");
    if ($stmt->rowCount() === 0) {
        logMsg("⚠️ Tabela 'mensagens' não existe! Criando...");
        
        // Criar tabela
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS mensagens (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL,
                telefone VARCHAR(20) DEFAULT NULL,
                assunto VARCHAR(200) DEFAULT NULL,
                mensagem TEXT NOT NULL,
                lida TINYINT(1) DEFAULT 0,
                respondida TINYINT(1) DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_email (email),
                INDEX idx_lida (lida),
                INDEX idx_respondida (respondida)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        logMsg("✅ Tabela 'mensagens' criada");
    }

    // Preparar INSERT
    $sql = "INSERT INTO mensagens (nome, email, telefone, assunto, mensagem, lida, respondida, created_at) 
            VALUES (?, ?, ?, ?, ?, 0, 0, NOW())";
    
    logMsg("SQL: " . $sql);

    $stmt = $pdo->prepare($sql);
    
    $result = $stmt->execute([
        trim($input['nome']),
        trim($input['email']),
        isset($input['telefone']) ? trim($input['telefone']) : null,
        isset($input['assunto']) ? trim($input['assunto']) : 'Contato via Site',
        trim($input['mensagem'])
    ]);

    logMsg("Resultado INSERT: " . ($result ? 'SUCESSO' : 'FALHA'));

    if ($result) {
        $id = $pdo->lastInsertId();
        logMsg("✅ ID gerado: " . $id);
        
        echo json_encode([
            'success' => true,
            'message' => 'Mensagem enviada com sucesso!',
            'id' => (int)$id,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    } else {
        logMsg("❌ Falha no INSERT");
        http_response_code(500);
        echo json_encode(['error' => 'Erro ao salvar mensagem']);
    }

} catch (PDOException $e) {
    logMsg("❌ ERRO PDO: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Erro no banco de dados: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    logMsg("❌ ERRO: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erro: ' . $e->getMessage()]);
}