<?php
session_start();

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'motoboy') {
    header('Location: ../../index.php');
    exit();
}

try {
    $conn = new PDO("mysql:host=localhost;dbname=foods_delivery", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// Busca motoboy
$stmt = $conn->prepare("SELECT id FROM motoboys WHERE usuario_id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$motoboy = $stmt->fetch();

if (!$motoboy) {
    header('Location: ../../index.php');
    exit();
}

// Todas as entregas do motoboy
$stmt = $conn->prepare("
    SELECT p.*, r.nome as restaurante, u.nome as cliente,
           r.endereco as endereco_restaurante
    FROM pedidos p 
    JOIN restaurantes r ON p.restaurante_id = r.id 
    JOIN usuarios u ON p.cliente_id = u.id 
    WHERE p.motoboy_id = ?
    ORDER BY p.data_pedido DESC
");
$stmt->execute([$motoboy['id']]);
$entregas = $stmt->fetchAll();

$flash = $_SESSION['flash_message'] ?? null;
unset($_SESSION['flash_message']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Entregas</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../style.css">
    <style>
        .courier-header {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: white;
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .courier-header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        .courier-menu {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .courier-menu a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 8px;
            transition: var(--transition);
            font-size: 14px;
        }
        .courier-menu a:hover,
        .courier-menu a.active {
            background: var(--primary);
            color: white;
        }
        .courier-menu .sair {
            color: #ff6b6b;
        }
        .courier-menu .sair:hover {
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
            color: var(--text-muted);
        }
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        .status-entregue { background: #28a745; color: white; }
        .status-cancelado { background: #dc3545; color: white; }
        .status-pendente { background: #fff3cd; color: #856404; }
        .status-preparando { background: #ffe5d0; color: #e65c00; }
        .status-saiu_entrega { background: #d4edda; color: #155724; }
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
        .filters {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin: 20px 0;
        }
        .filters a {
            padding: 6px 15px;
            border-radius: 20px;
            text-decoration: none;
            background: #f8f9fa;
            color: var(--text);
            font-size: 13px;
            transition: var(--transition);
        }
        .filters a:hover,
        .filters a.active {
            background: var(--primary);
            color: white;
        }
        @media (max-width: 768px) {
            .courier-header .container {
                flex-direction: column;
                text-align: center;
            }
            .courier-menu {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <header class="courier-header">
        <div class="container">
            <h2 style="font-size: 20px;"><i class="fas fa-motorcycle" style="color: var(--primary);"></i> <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?></h2>
            <nav class="courier-menu">
                <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <a href="deliveries.php" class="active"><i class="fas fa-truck"></i> Entregas</a>
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

        <h2 style="margin: 20px 0;"><i class="fas fa-history"></i> Histórico de Entregas</h2>

        <div class="table-container">
            <?php if (!empty($entregas)): ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Restaurante</th>
                        <th>Cliente</th>
                        <th>Valor</th>
                        <th>Status</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($entregas as $e): ?>
                    <tr>
                        <td><strong>#<?php echo $e['id']; ?></strong></td>
                        <td><?php echo htmlspecialchars($e['restaurante']); ?></td>
                        <td><?php echo htmlspecialchars($e['cliente']); ?></td>
                        <td>R$ <?php echo number_format($e['total'], 2, ',', '.'); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo $e['status']; ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $e['status'])); ?>
                            </span>
                        </td>
                        <td><?php echo date('d/m/Y H:i', strtotime($e['data_pedido'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-truck"></i>
                <h3>Nenhuma entrega realizada</h3>
                <p>Você ainda não fez nenhuma entrega.</p>
            </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>