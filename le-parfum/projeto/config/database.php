<?php
// ============================================================
// CONEXÃO COM BANCO DE DADOS - PDO
// ============================================================

define('DB_HOST', 'localhost');
define('DB_NAME', 'leparfum');
define('DB_USER', 'root');
define('DB_PASS', '');

// ---- BASE_URL dinâmico ----
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));

// Se estiver em /pages/, /actions/, /admin/ → volta um nível
if (preg_match('#/(pages|actions|admin)$#', $scriptDir)) {
    $scriptDir = dirname($scriptDir);
}

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];

define('BASE_URL', $protocol . '://' . $host . rtrim($scriptDir, '/') . '/');

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die('Erro ao conectar ao banco: ' . $e->getMessage());
}