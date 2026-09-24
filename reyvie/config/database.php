<?php
/**
 * Conexão com o banco de dados usando PDO
 */
class Database {
    private static $host = 'localhost';
    private static $dbname = 'reyvie';
    private static $user = 'root';
    private static $pass = '';
    private static $charset = 'utf8mb4';
    private static $conn = null;

    public static function getConnection() {
        if (self::$conn === null) {
            $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$dbname . ";charset=" . self::$charset;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            try {
                self::$conn = new PDO($dsn, self::$user, self::$pass, $options);
            } catch (PDOException $e) {
                die('Erro ao conectar ao banco: ' . $e->getMessage());
            }
        }
        return self::$conn;
    }
}

// Atalho global
function db() {
    return Database::getConnection();
}