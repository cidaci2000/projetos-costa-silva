<?php
// test_db.php
require_once 'config/database.php';

try {
    $pdo = getConnection();
    echo "<h1 style='color:green;'>✅ Conexão com o banco de dados estabelecida com sucesso!</h1>";
    echo "<p><strong>Banco:</strong> " . DB_NAME . "</p>";
    echo "<p><strong>Host:</strong> " . DB_HOST . "</p>";
    echo "<p><strong>Charset:</strong> " . DB_CHARSET . "</p>";
    
    // Testar query simples
    $stmt = $pdo->query("SELECT DATABASE() as db, NOW() as agora");
    $row = $stmt->fetch();
    echo "<p><strong>Banco atual:</strong> " . $row['db'] . "</p>";
    echo "<p><strong>Data/Hora:</strong> " . $row['agora'] . "</p>";
    
} catch (Exception $e) {
    echo "<h1 style='color:red;'>❌ Erro de conexão</h1>";
    echo "<p style='color:red;'>" . $e->getMessage() . "</p>";
}
?>