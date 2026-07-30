<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');

$host = 'localhost';
$user = 'root';
$password = '';
$database = 'tropebook';

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) die(json_encode(['success' => false, 'message' => 'Erro de conexão']));

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

switch ($action) {
    case 'create_trope':
        handleCreateTrope($conn, $input);
        break;
    case 'update_trope':
        handleUpdateTrope($conn, $input);
        break;
    case 'delete_trope':
        handleDeleteTrope($conn, $input);
        break;
    case 'create_livro':
        handleCreateLivro($conn, $input);
        break;
    case 'update_livro':
        handleUpdateLivro($conn, $input);
        break;
    case 'delete_livro':
        handleDeleteLivro($conn, $input);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Ação inválida']);
}
$conn->close();

function handleCreateTrope($conn, $data) {
    $nome = $conn->real_escape_string($data['nome'] ?? '');
    $icon = $conn->real_escape_string($data['icon'] ?? '✨');
    $descricao = $conn->real_escape_string($data['descricao'] ?? '');
    $categoria = $conn->real_escape_string($data['categoria'] ?? '');
    $emocao = $conn->real_escape_string($data['emocao'] ?? '');
    $cor = $conn->real_escape_string($data['cor'] ?? '#E86FAC');

    if (empty($nome)) {
        echo json_encode(['success' => false, 'message' => 'Nome é obrigatório']);
        return;
    }

    $sql = "INSERT INTO tropes (nome, icon, descricao, categoria, emocao, cor) 
            VALUES ('$nome', '$icon', '$descricao', '$categoria', '$emocao', '$cor')";

    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Trope criada com sucesso!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro: ' . $conn->error]);
    }
}

function handleUpdateTrope($conn, $data) {
    $id = intval($data['id'] ?? 0);
    $nome = $conn->real_escape_string($data['nome'] ?? '');
    $icon = $conn->real_escape_string($data['icon'] ?? '✨');
    $descricao = $conn->real_escape_string($data['descricao'] ?? '');
    $categoria = $conn->real_escape_string($data['categoria'] ?? '');
    $emocao = $conn->real_escape_string($data['emocao'] ?? '');
    $cor = $conn->real_escape_string($data['cor'] ?? '#E86FAC');

    if ($id <= 0 || empty($nome)) {
        echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
        return;
    }

    $sql = "UPDATE tropes SET nome='$nome', icon='$icon', descricao='$descricao', categoria='$categoria', emocao='$emocao', cor='$cor' WHERE id=$id";

    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Trope atualizada!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro: ' . $conn->error]);
    }
}

function handleDeleteTrope($conn, $data) {
    $id = intval($data['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID inválido']);
        return;
    }
    if ($conn->query("DELETE FROM tropes WHERE id=$id")) {
        echo json_encode(['success' => true, 'message' => 'Trope excluída!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro: ' . $conn->error]);
    }
}

function handleCreateLivro($conn, $data) {
    $titulo = $conn->real_escape_string($data['titulo'] ?? '');
    $autor = $conn->real_escape_string($data['autor'] ?? '');
    $trope_id = $data['trope_id'] ? intval($data['trope_id']) : 'NULL';
    $capa_emoji = $conn->real_escape_string($data['capa_emoji'] ?? '📖');
    $descricao = $conn->real_escape_string($data['descricao'] ?? '');

    if (empty($titulo) || empty($autor)) {
        echo json_encode(['success' => false, 'message' => 'Título e autor são obrigatórios']);
        return;
    }

    $sql = "INSERT INTO livros (titulo, autor, trope_id, capa_emoji, descricao) 
            VALUES ('$titulo', '$autor', $trope_id, '$capa_emoji', '$descricao')";

    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Livro criado com sucesso!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro: ' . $conn->error]);
    }
}

function handleUpdateLivro($conn, $data) {
    $id = intval($data['id'] ?? 0);
    $titulo = $conn->real_escape_string($data['titulo'] ?? '');
    $autor = $conn->real_escape_string($data['autor'] ?? '');
    $trope_id = $data['trope_id'] ? intval($data['trope_id']) : 'NULL';
    $capa_emoji = $conn->real_escape_string($data['capa_emoji'] ?? '📖');
    $descricao = $conn->real_escape_string($data['descricao'] ?? '');

    if ($id <= 0 || empty($titulo) || empty($autor)) {
        echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
        return;
    }

    $sql = "UPDATE livros SET titulo='$titulo', autor='$autor', trope_id=$trope_id, capa_emoji='$capa_emoji', descricao='$descricao' WHERE id=$id";

    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Livro atualizado!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro: ' . $conn->error]);
    }
}

function handleDeleteLivro($conn, $data) {
    $id = intval($data['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID inválido']);
        return;
    }
    if ($conn->query("DELETE FROM livros WHERE id=$id")) {
        echo json_encode(['success' => true, 'message' => 'Livro excluído!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro: ' . $conn->error]);
    }
}
?>