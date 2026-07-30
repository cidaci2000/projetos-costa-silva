<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$host = 'localhost';
$user = 'root';
$password = '';
$database = 'flow_biblioteca';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    echo json_encode(['error' => 'Erro de conexão: ' . $conn->connect_error]);
    exit;
}

$sql = "SELECT id, nome, autor, icon, categoria, descricao, resumo, link_resumo, disponivel FROM livros ORDER BY id";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $livros = [];
    while ($row = $result->fetch_assoc()) {
        $livros[] = [
            'id' => (int)$row['id'],
            'nome' => $row['nome'],
            'autor' => $row['autor'],
            'icon' => $row['icon'],
            'categoria' => $row['categoria'],
            'descricao' => $row['descricao'],
            'resumo' => $row['resumo'],
            'link_resumo' => $row['link_resumo'],
            'disponivel' => (int)$row['disponivel']
        ];
    }
    echo json_encode($livros);
} else {
    echo json_encode([]);
}

$conn->close();
?>