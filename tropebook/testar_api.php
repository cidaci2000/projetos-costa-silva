<?php
// testar_api.php - Teste de cadastro de usuário
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'tropebook';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die('❌ Erro de conexão: ' . $conn->connect_error);
}

echo "✅ Conectado ao banco 'tropebook'<br><br>";

// Verificar tabela usuarios
$tables = $conn->query("SHOW TABLES LIKE 'usuarios'");
if ($tables->num_rows === 0) {
    echo "❌ Tabela 'usuarios' não existe!<br>";
    echo "<hr>";
    echo "<h3>📝 Criar tabela:</h3>";
    echo "<pre>
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL UNIQUE,
  `phone` varchar(50) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('usuario','bibliotecario','admin') DEFAULT 'usuario',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
</pre>";
    $conn->close();
    exit;
}

// Ver usuários existentes
$result = $conn->query("SELECT id, name, email, role FROM usuarios");
echo "<h3>👥 Usuários cadastrados:</h3>";
if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='8'>";
    echo "<tr><th>ID</th><th>Nome</th><th>Email</th><th>Role</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>{$row['id']}</td><td>{$row['name']}</td><td>{$row['email']}</td><td>{$row['role']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "❌ Nenhum usuário encontrado!<br>";
}

echo "<hr>";
echo "<h3>🧪 Teste de cadastro via API</h3>";

// Simular cadastro
$testName = 'Teste Usuario';
$testEmail = 'teste@tropebook.com';
$testPhone = '(11) 99999-9999';
$testPassword = 'senha123';
$hash = password_hash($testPassword, PASSWORD_DEFAULT);

// Verificar se já existe
$check = $conn->query("SELECT id FROM usuarios WHERE email = '$testEmail'");
if ($check && $check->num_rows > 0) {
    echo "⚠️ Usuário teste já existe!<br>";
} else {
    $sql = "INSERT INTO usuarios (name, email, phone, password_hash, role) 
            VALUES ('$testName', '$testEmail', '$testPhone', '$hash', 'usuario')";
    if ($conn->query($sql)) {
        echo "✅ Usuário teste criado com sucesso!<br>";
        echo "📧 Email: teste@tropebook.com<br>";
        echo "🔑 Senha: senha123<br>";
    } else {
        echo "❌ Erro ao criar usuário teste: " . $conn->error . "<br>";
    }
}

$conn->close();
?>