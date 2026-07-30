<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$host = 'localhost';
$user = 'root';
$password = '';
$database = 'loja_esportiva';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die(json_encode([
        'success' => false,
        'message' => 'Erro de conexão com o banco de dados: ' . $conn->connect_error
    ]));
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
    case 'check':
        handleCheck($conn, $input);
        break;
    case 'logout':
        handleLogout();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Ação inválida']);
        break;
}

$conn->close();

// Função de Login
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

// Função de Cadastro
function handleSignup($conn, $data) {
    $name = $conn->real_escape_string($data['name'] ?? '');
    $email = $conn->real_escape_string($data['email'] ?? '');
    $phone = $conn->real_escape_string($data['phone'] ?? '');
    $password = $data['password'] ?? '';

    if (empty($name) || empty($email) || empty($password) || empty($phone)) {
        echo json_encode(['success' => false, 'message' => 'Todos os campos são obrigatórios']);
        return;
    }

    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'A senha deve ter no mínimo 6 caracteres']);
        return;
    }

    // Verificar se o email já existe
    $check = $conn->query("SELECT id FROM usuarios WHERE email = '$email'");
    if ($check && $check->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Este email já está cadastrado']);
        return;
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    // Por padrão, novos usuários são 'user' (não admin)
    $sql = "INSERT INTO usuarios (name, email, phone, password_hash, role) 
            VALUES ('$name', '$email', '$phone', '$passwordHash', 'user')";

    if ($conn->query($sql)) {
        echo json_encode([
            'success' => true,
            'message' => 'Cadastro realizado com sucesso',
            'user' => ['id' => $conn->insert_id, 'name' => $name, 'email' => $email, 'role' => 'user']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar: ' . $conn->error]);
    }
}

// Verificar se usuário está logado
function handleCheck($conn, $data) {
    $userId = intval($data['user_id'] ?? 0);
    if ($userId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
        return;
    }

    $sql = "SELECT id, name, email, phone, role FROM usuarios WHERE id = $userId";
    $result = $conn->query($sql);

    if ($result && $result->num_rows === 1) {
        echo json_encode(['success' => true, 'user' => $result->fetch_assoc()]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Usuário não encontrado']);
    }
}

// Logout
function handleLogout() {
    echo json_encode(['success' => true, 'message' => 'Logout realizado com sucesso']);
}
?>