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

$sql = "SELECT id, name, email, phone, role, created_at FROM usuarios ORDER BY id DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    echo json_encode($users);
} else {
    echo json_encode([]);
}
$conn->close();
?>