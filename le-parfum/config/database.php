<?php
// ============================================================
// CONFIGURAÇÃO DO BANCO DE DADOS - WINDOWS
// ============================================================

// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'le_parfum');
define('DB_USER', 'root');
define('DB_PASS', ''); // Deixe vazio se não tiver senha no XAMPP
define('DB_CHARSET', 'utf8mb4');

// ============================================================
// FUNÇÃO DE CONEXÃO
// ============================================================

function getConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
        return $pdo;
    } catch (PDOException $e) {
        // Retorna erro em JSON para debug
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erro de conexão com o banco de dados',
            'debug' => $e->getMessage()
        ]);
        exit;
    }
}

// ============================================================
// FUNÇÕES AUXILIARES
// ============================================================

function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

function respondJson($success, $data = null, $message = null) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'data' => $data,
        'message' => $message
    ]);
    exit;
}

// Iniciar sessão se não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}