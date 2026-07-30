<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');

$host = 'localhost';
$user = 'root';
$password = '';
$database = 'tropebook';

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) die(json_encode(['error' => 'Erro de conexão']));

$sql = "SELECT * FROM tropes ORDER BY id";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $tropes = [];
    while ($row = $result->fetch_assoc()) {
        $tropes[] = [
            'id' => (int)$row['id'],
            'nome' => $row['nome'],
            'icon' => $row['icon'],
            'descricao' => $row['descricao'],
            'categoria' => $row['categoria'],
            'emocao' => $row['emocao'],
            'livros_count' => (int)$row['livros_count'],
            'cor' => $row['cor']
        ];
    }
    echo json_encode($tropes);
} else {
    echo json_encode([]);
}
$conn->close();
?>