<?php
session_start();

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'restaurante') {
    header('Location: ../../index.php');
    exit();
}

try {
    $conn = new PDO("mysql:host=localhost;dbname=foods_delivery", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// Busca restaurante
$stmt = $conn->prepare("SELECT * FROM restaurantes WHERE usuario_id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$restaurante = $stmt->fetch();

if (!$restaurante) {
    header('Location: ../../index.php');
    exit();
}

// Filtros
$statusFilter = $_GET['status'] ?? 'todos';
$search = $_GET['search'] ?? '';

$where = "p.restaurante_id = " . $restaurante['id'];
if ($statusFilter !== 'todos') {
    $where .= " AND p.status = '" . $statusFilter . "'";
}
if ($search) {
    $where .= " AND (u.nome LIKE '%" . $search . "%' OR p.id LIKE '%" . $search . "%')";
}

// Busca pedidos
$stmt = $conn->prepare("
    SELECT p.*, u.nome as cliente, 
           (SELECT COUNT(*) FROM itens_pedido WHERE pedido_id = p.id) as total_itens
    FROM pedidos p 
    JOIN usuarios u ON p.cliente_id = u.id 
    WHERE $where
    ORDER BY p.data_pedido DESC
");
$stmt->execute();
$pedidos = $stmt->fetchAll();

// Contagem por status
$statusCount = $conn->query("
    SELECT status, COUNT(*) as total 
    FROM pedidos 
    WHERE restaurante_id = " . $restaurante['id'] . " 
    GROUP BY status
")->fetchAll(PDO::FETCH_KEY_PAIR);

$flash = $_SESSION['flash_message'] ?? null;
unset($_SESSION['flash_message']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos - <?php echo htmlspecialchars($restaurante['nome']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../style.css">
    <style>
        .restaurant-header {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: white;
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .restaurant-header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        .restaurant-menu {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .restaurant-menu a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 8px;
            transition: var(--transition);
            font-size: 14px;
        }
        .restaurant-menu a:hover,
        .restaurant-menu a.active {
            background: var(--primary);
            color: white;
        }
        .restaurant-menu .sair {
            color: #ff6b6b;
        }
        .restaurant-menu .sair:hover {
            background: #ff6b6b;
            color: white;
        }
        .filters {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin: 20px 0;
            align-items: center;
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
        .filters .badge {
            background: rgba(255,255,255,0.3);
            padding: 1px 8px;
            border-radius: 10px;
            font-size: 11px;
            margin-left: 4px;
        }
        .filters a.active .badge {
            background: rgba(255,255,255,0.3);
        }
        .search-form {
            display: flex;
            gap: 10px;
            margin-left: auto;
        }
        .search-form input {
            padding: 8px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-family: inherit;
        }
        .table-container {
            background: white;
            border-radius: var(--radius);
            padding: 20px;
            box-shadow: var(--shadow);
            overflow-x: auto;
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
        .status-pendente { background: #fff3cd; color: #856404; }
        .status-confirmado { background: #cce5ff; color: #004085; }
        .status-preparando { background: #ffe5d0; color: #e65c00; }
        .status-saiu_entrega { background: #d4edda; color: #155724; }
        .status-entregue { background: #28a745; color: white; }
        .status-cancelado { background: #dc3545; color: white; }
        .btn-sm {
            padding: 5px 12px;
            font-size: 12px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            transition: var(--transition);
            font-family: inherit;
        }
        .btn-sm:hover {
            transform: scale(1.05);
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
        @media (max-width: 768px) {
            .filters {
                flex-direction: column;
                align-items: stretch;
            }
            .search-form {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <header class="restaurant-header">
        <div class="container">
            <h2 style="font-size: 20px;"><i class="fas fa-store" style="color: var(--primary);"></i> <?php echo htmlspecialchars($restaurante['nome']); ?></h2>
            <nav class="restaurant-menu">
                <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <a href="products.php"><i class="fas fa-utensils"></i> Cardápio</a>
                <a href="orders.php" class="active"><i class="fas fa-shopping-bag"></i> Pedidos</a>
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

        <h2 style="margin: 20px 0;"><i class="fas fa-shopping-bag"></i> Gerenciar Pedidos</h2>

        <!-- FILTROS -->
        <div class="filters">
            <a href="orders.php?status=todos" class="<?php echo $statusFilter == 'todos' ? 'active' : ''; ?>">
                Todos <span class="badge"><?php echo array_sum($statusCount); ?></span>
            </a>
            <a href="orders.php?status=pendente" class="<?php echo $statusFilter == 'pendente' ? 'active' : ''; ?>">
                🟡 Pendente <span class="badge"><?php echo $statusCount['pendente'] ?? 0; ?></span>
            </a>
            <a href="orders.php?status=confirmado" class="<?php echo $statusFilter == 'confirmado' ? 'active' : ''; ?>">
                🔵 Confirmado <span class="badge"><?php echo $statusCount['confirmado'] ?? 0; ?></span>
            </a>
            <a href="orders.php?status=preparando" class="<?php echo $statusFilter == 'preparando' ? 'active' : ''; ?>">
                🟠 Preparando <span class="badge"><?php echo $statusCount['preparando'] ?? 0; ?></span>
            </a>
            <a href="orders.php?status=saiu_entrega" class="<?php echo $statusFilter == 'saiu_entrega' ? 'active' : ''; ?>">
                🟣 Saiu Entrega <span class="badge"><?php echo $statusCount['saiu_entrega'] ?? 0; ?></span>
            </a>
            <a href="orders.php?status=entregue" class="<?php echo $statusFilter == 'entregue' ? 'active' : ''; ?>">
                🟢 Entregue <span class="badge"><?php echo $statusCount['entregue'] ?? 0; ?></span>
            </a>
            <a href="orders.php?status=cancelado" class="<?php echo $statusFilter == 'cancelado' ? 'active' : ''; ?>">
                🔴 Cancelado <span class="badge"><?php echo $statusCount['cancelado'] ?? 0; ?></span>
            </a>

            <form class="search-form" method="GET">
                <input type="hidden" name="status" value="<?php echo $statusFilter; ?>">
                <input type="text" name="search" placeholder="Buscar pedido..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn btn-primary" style="padding: 8px 15px; border-radius: 8px; border: none; cursor: pointer;">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>

        <!-- TABELA -->
        <div class="table-container">
            <?php if (!empty($pedidos)): ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Cliente</th>
                        <th>Itens</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Data</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pedidos as $p): ?>
                    <tr>
                        <td><strong>#<?php echo $p['id']; ?></strong></td>
                        <td><?php echo htmlspecialchars($p['cliente']); ?></td>
                        <td><?php echo $p['total_itens']; ?> itens</td>
                        <td>R$ <?php echo number_format($p['total'], 2, ',', '.'); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo $p['status']; ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $p['status'])); ?>
                            </span>
                        </td>
                        <td><?php echo date('d/m/Y H:i', strtotime($p['data_pedido'])); ?></td>
                        <td>
                            <form action="../../actions/restaurant/update-order.php" method="POST" style="display: flex; gap: 5px; flex-wrap: wrap;">
                                <input type="hidden" name="pedido_id" value="<?php echo $p['id']; ?>">
                                <select name="status" onchange="this.form.submit()" style="padding: 5px 10px; border-radius: 6px; border: 1px solid #ddd; font-family: inherit; font-size: 12px; cursor: pointer;">
                                    <option value="pendente" <?php echo $p['status'] == 'pendente' ? 'selected' : ''; ?>>🟡 Pendente</option>
                                    <option value="confirmado" <?php echo $p['status'] == 'confirmado' ? 'selected' : ''; ?>>🔵 Confirmar</option>
                                    <option value="preparando" <?php echo $p['status'] == 'preparando' ? 'selected' : ''; ?>>🟠 Preparando</option>
                                    <option value="saiu_entrega" <?php echo $p['status'] == 'saiu_entrega' ? 'selected' : ''; ?>>🟣 Saiu Entrega</option>
                                    <option value="entregue" <?php echo $p['status'] == 'entregue' ? 'selected' : ''; ?>>🟢 Entregue</option>
                                    <option value="cancelado" <?php echo $p['status'] == 'cancelado' ? 'selected' : ''; ?>>🔴 Cancelar</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-shopping-bag"></i>
                <h3>Nenhum pedido encontrado</h3>
                <p>Não há pedidos com os filtros selecionados.</p>
            </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>