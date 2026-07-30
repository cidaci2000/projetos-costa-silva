<?php
// verificar_admin.php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'loja_esportiva';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die('Erro de conexão: ' . $conn->connect_error);
}

echo "<h2>🔍 Verificando Usuários</h2>";

$sql = "SELECT id, name, email, role, password_hash FROM usuarios";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Nome</th><th>Email</th><th>Role</th><th>Hash (primeiros 20)</th></tr>";
    while ($row = $result->fetch_assoc()) {
        $hashPreview = substr($row['password_hash'], 0, 20) . '...';
        $roleColor = $row['role'] === 'admin' ? '🟢' : '🔵';
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['name']}</td>
                <td>{$row['email']}</td>
                <td><strong>{$roleColor} {$row['role']}</strong></td>
                <td>{$hashPreview}</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "❌ Nenhum usuário encontrado!";
}

echo "<hr>";
echo "<h3>📝 Comandos SQL para corrigir:</h3>";
echo "<pre>
-- Criar admin (senha: admin123)
INSERT INTO usuarios (name, email, phone, password_hash, role) VALUES 
('Administrador', 'admin@bekaesporte.com', '(11) 99999-9999', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Ou atualizar um usuário existente para admin
UPDATE usuarios SET role = 'admin' WHERE email = 'admin@bekaesporte.com';
</pre>";

$conn->close();
?>