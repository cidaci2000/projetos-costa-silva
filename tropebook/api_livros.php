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

$sql = "SELECT * FROM livros ORDER BY id";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $livros = [];
    while ($row = $result->fetch_assoc()) {
        $livros[] = [
            'id' => (int)$row['id'],
            'titulo' => $row['titulo'],
            'autor' => $row['autor'],
            'trope_id' => $row['trope_id'] ? (int)$row['trope_id'] : null,
            'capa_emoji' => $row['capa_emoji'],
            'descricao' => $row['descricao'],
            'paginas' => (int)$row['paginas'],
            'avaliacao' => (float)$row['avaliacao']
        ];
    }
    echo json_encode($livros);
} else {
    echo json_encode([]);
}
$conn->close();
?>