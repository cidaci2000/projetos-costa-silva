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
    case 'login':
        handleLogin($conn, $input);
        break;
    case 'signup':
        handleSignup($conn, $input);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Ação inválida']);
        break;
}

$conn->close();

function handleLogin($conn, $data) {
    $email = $conn->real_escape_string($data['email'] ?? '');
    $password = $data['password'] ?? '';

    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Email e senha são obrigatórios']);
        return;
    }

    $sql = "SELECT id, name, email, phone, role, password_hash FROM usuarios WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password_hash'])) {
            unset($user['password_hash']);
            echo json_encode([
                'success' => true,
                'message' => 'Login realizado com sucesso',
                'user' => $user
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Senha incorreta']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Usuário não encontrado']);
    }
}

function handleSignup($conn, $data) {
    $name = $conn->real_escape_string($data['name'] ?? '');
    $email = $conn->real_escape_string($data['email'] ?? '');
    $phone = $conn->real_escape_string($data['phone'] ?? '');
    $password = $data['password'] ?? '';

    if (empty($name) || empty($email) || empty($phone) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Todos os campos são obrigatórios']);
        return;
    }

    if (strlen($password) < 3) {
        echo json_encode(['success' => false, 'message' => 'A senha deve ter no mínimo 3 caracteres']);
        return;
    }

    $check = $conn->query("SELECT id FROM usuarios WHERE email = '$email'");
    if ($check && $check->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Este email já está cadastrado']);
        return;
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO usuarios (name, email, phone, password_hash, role) 
            VALUES ('$name', '$email', '$phone', '$passwordHash', 'usuario')";

    if ($conn->query($sql)) {
        echo json_encode([
            'success' => true,
            'message' => 'Cadastro realizado com sucesso',
            'user' => [
                'id' => $conn->insert_id,
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'role' => 'usuario'
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar: ' . $conn->error]);
    }
}
?>