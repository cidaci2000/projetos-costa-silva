<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$host = 'localhost';
$user = 'root';
$password = '';
$database = 'flow_biblioteca';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Erro de conexão: ' . $conn->connect_error]));
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

switch ($action) {
    case 'create':
        handleCreate($conn, $input);
        break;
    case 'update':
        handleUpdate($conn, $input);
        break;
    case 'delete':
        handleDelete($conn, $input);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Ação inválida']);
        break;
}

$conn->close();

function handleCreate($conn, $data) {
    $nome = $conn->real_escape_string($data['nome'] ?? '');
    $autor = $conn->real_escape_string($data['autor'] ?? '');
    $icon = $conn->real_escape_string($data['icon'] ?? '📘');
    $categoria = $conn->real_escape_string($data['categoria'] ?? '');
    $descricao = $conn->real_escape_string($data['descricao'] ?? '');
    $resumo = $conn->real_escape_string($data['resumo'] ?? '');
    $link_resumo = $conn->real_escape_string($data['link_resumo'] ?? '');
    $disponivel = intval($data['disponivel'] ?? 1);

    if (empty($nome) || empty($autor)) {
        echo json_encode(['success' => false, 'message' => 'Nome e autor são obrigatórios']);
        return;
    }

    $sql = "INSERT INTO livros (nome, autor, icon, categoria, descricao, resumo, link_resumo, disponivel) 
            VALUES ('$nome', '$autor', '$icon', '$categoria', '$descricao', '$resumo', '$link_resumo', $disponivel)";

    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Livro criado com sucesso!', 'id' => $conn->insert_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao criar livro: ' . $conn->error]);
    }
}

function handleUpdate($conn, $data) {
    $id = intval($data['id'] ?? 0);
    $nome = $conn->real_escape_string($data['nome'] ?? '');
    $autor = $conn->real_escape_string($data['autor'] ?? '');
    $icon = $conn->real_escape_string($data['icon'] ?? '📘');
    $categoria = $conn->real_escape_string($data['categoria'] ?? '');
    $descricao = $conn->real_escape_string($data['descricao'] ?? '');
    $resumo = $conn->real_escape_string($data['resumo'] ?? '');
    $link_resumo = $conn->real_escape_string($data['link_resumo'] ?? '');
    $disponivel = intval($data['disponivel'] ?? 1);

    if ($id <= 0 || empty($nome) || empty($autor)) {
        echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
        return;
    }

    $sql = "UPDATE livros SET 
            nome = '$nome',
            autor = '$autor',
            icon = '$icon',
            categoria = '$categoria',
            descricao = '$descricao',
            resumo = '$resumo',
            link_resumo = '$link_resumo',
            disponivel = $disponivel
            WHERE id = $id";

    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Livro atualizado com sucesso!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao atualizar: ' . $conn->error]);
    }
}

function handleDelete($conn, $data) {
    $id = intval($data['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID inválido']);
        return;
    }

    $sql = "DELETE FROM livros WHERE id = $id";

    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Livro excluído com sucesso!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao excluir: ' . $conn->error]);
    }
}
?>