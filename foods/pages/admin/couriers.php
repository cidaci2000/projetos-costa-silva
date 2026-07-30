<?php
session_start();

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header('Location: ../../index.php');
    exit();
}

try {
    $conn = new PDO("mysql:host=localhost;dbname=foods_delivery", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

$motoboys = $conn->query("
    SELECT m.*, u.nome, u.email, u.telefone 
    FROM motoboys m 
    JOIN usuarios u ON m.usuario_id = u.id 
    ORDER BY u.nome
")->fetchAll();

$flash = $_SESSION['flash_message'] ?? null;
unset($_SESSION['flash_message']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Motoboys</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../style.css">
    <style>
        .admin-header {
            background: #1a1a2e;
            color: white;
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .admin-header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        .admin-menu {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .admin-menu a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 8px;
            transition: var(--transition);
            font-size: 14px;
        }
        .admin-menu a:hover,
        .admin-menu a.active {
            background: var(--primary);
            color: white;
        }
        .admin-menu .sair {
            color: #ff6b6b;
        }
        .admin-menu .sair:hover {
            background: #ff6b6b;
            color: white;
        }
        .table-container {
            background: white;
            border-radius: var(--radius);
            padding: 20px;
            box-shadow: var(--shadow);
            overflow-x: auto;
            margin-top: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th {
            background: #f8f9fa;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
        }
        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }
        .btn-add {
            background: var(--primary);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }
        .btn-add:hover {
            background: var(--primary-dark);
            transform: scale(1.02);
        }
        .btn-sm {
            padding: 5px 12px;
            font-size: 12px;
            border-radius: 6px;
            text-decoration: none;
            display: inline-block;
            transition: var(--transition);
        }
        .btn-sm:hover {
            transform: scale(1.05);
        }
        .btn-edit {
            background: #17a2b8;
            color: white;
        }
        .btn-delete {
            background: #dc3545;
            color: white;
        }
        .status-disponivel {
            color: #28a745;
            font-weight: 600;
        }
        .status-indisponivel {
            color: #dc3545;
            font-weight: 600;
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: var(--text-muted);
        }
        .empty-state i {
            font-size: 48px;
            opacity: 0.5;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="container">
            <h2 style="font-size: 20px;"><i class="fas fa-crown" style="color: #ffd700;"></i> Admin Panel</h2>
            <nav class="admin-menu">
                <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <a href="restaurants.php"><i class="fas fa-store"></i> Restaurantes</a>
                <a href="couriers.php" class="active"><i class="fas fa-motorcycle"></i> Motoboys</a>
                <a href="users.php"><i class="fas fa-users"></i> Usuários</a>
                <a href="../../actions/logout.php" class="sair"><i class="fas fa-sign-out-alt"></i> Sair</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <?php if ($flash): ?>
            <div class="flash-message <?php echo $flash['type']; ?>">
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>

        <div class="header-actions">
            <h2><i class="fas fa-motorcycle"></i> Gerenciar Motoboys</h2>
            <a href="add-courier.php" class="btn-add">
                <i class="fas fa-plus"></i> Novo Motoboy
            </a>
        </div>

        <div class="table-container">
            <?php if (!empty($motoboys)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Moto</th>
                        <th>Placa</th>
                        <th>Status</th>
                        <th>Avaliação</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($motoboys as $m): ?>
                    <tr>
                        <td>#<?php echo $m['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($m['nome']); ?></strong></td>
                        <td><?php echo htmlspecialchars($m['email']); ?></td>
                        <td><?php echo htmlspecialchars($m['modelo_moto']); ?></td>
                        <td><?php echo htmlspecialchars($m['placa']); ?></td>
                        <td>
                            <?php if ($m['disponivel']): ?>
                                <span class="status-disponivel">✓ Disponível</span>
                            <?php else: ?>
                                <span class="status-indisponivel">✗ Indisponível</span>
                            <?php endif; ?>
                        </td>
                        <td>⭐ <?php echo number_format($m['avaliacao'], 1); ?></td>
                        <td style="display: flex; gap: 5px; flex-wrap: wrap;">
                            <a href="#" class="btn-sm btn-edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="../../actions/admin/delete.php?tipo=motoboy&id=<?php echo $m['id']; ?>" 
                               class="btn-sm btn-delete" 
                               onclick="return confirm('Tem certeza que deseja excluir este motoboy?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-motorcycle"></i>
                <h3>Nenhum motoboy cadastrado</h3>
                <p>Clique em "Novo Motoboy" para começar.</p>
            </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>