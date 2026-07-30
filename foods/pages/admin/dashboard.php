<?php
session_start();

// Verifica se é admin
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Acesso negado!'];
    header('Location: ../../index.php');
    exit();
}

// Conexão com o banco
try {
    $conn = new PDO("mysql:host=localhost;dbname=foods_delivery", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// ============================================
// ESTATÍSTICAS
// ============================================
$totalRestaurantes = $conn->query("SELECT COUNT(*) FROM restaurantes")->fetchColumn();
$totalMotoboys = $conn->query("SELECT COUNT(*) FROM motoboys")->fetchColumn();
$totalClientes = $conn->query("SELECT COUNT(*) FROM usuarios WHERE tipo = 'cliente'")->fetchColumn();
$totalPedidos = $conn->query("SELECT COUNT(*) FROM pedidos")->fetchColumn();

// Pedidos por status
$statusCount = $conn->query("SELECT status, COUNT(*) as total FROM pedidos GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);

// Faturamento total
$faturamento = $conn->query("SELECT SUM(total) FROM pedidos WHERE status = 'entregue'")->fetchColumn();

// ============================================
// ÚLTIMOS PEDIDOS
// ============================================
$pedidos = $conn->query("
    SELECT p.*, u.nome as cliente, r.nome as restaurante 
    FROM pedidos p 
    JOIN usuarios u ON p.cliente_id = u.id 
    JOIN restaurantes r ON p.restaurante_id = r.id 
    ORDER BY p.data_pedido DESC LIMIT 10
")->fetchAll();

// ============================================
// ÚLTIMOS USUÁRIOS CADASTRADOS
// ============================================
$usuarios = $conn->query("
    SELECT * FROM usuarios 
    ORDER BY data_cadastro DESC LIMIT 5
")->fetchAll();

$flash = $_SESSION['flash_message'] ?? null;
unset($_SESSION['flash_message']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Foods Delivery</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../style.css">
    <style>
        /* ===== ADMIN HEADER ===== */
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
        .admin-header h2 {
            font-size: 20px;
        }
        .admin-header h2 i {
            color: #ffd700;
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

        /* ===== STATS ===== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            text-align: center;
            transition: var(--transition);
            border-bottom: 4px solid transparent;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }
        .stat-card i {
            font-size: 32px;
            color: var(--primary);
        }
        .stat-card h3 {
            font-size: 28px;
            margin: 10px 0 5px;
        }
        .stat-card p {
            color: var(--text-muted);
            font-size: 14px;
        }
        .stat-card.primary { border-color: var(--primary); }
        .stat-card.success { border-color: #28a745; }
        .stat-card.info { border-color: #17a2b8; }
        .stat-card.warning { border-color: #ffc107; }
        .stat-card.danger { border-color: #dc3545; }

        /* ===== TABELAS ===== */
        .table-container {
            background: white;
            border-radius: var(--radius);
            padding: 20px;
            box-shadow: var(--shadow);
            overflow-x: auto;
            margin-bottom: 30px;
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
        tr:hover {
            background: #f8f9fa;
        }

        /* ===== STATUS BADGES ===== */
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

        /* ===== TIPO BADGES ===== */
        .tipo-badge {
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }
        .tipo-admin { background: #1a1a2e; color: white; }
        .tipo-cliente { background: #28a745; color: white; }
        .tipo-restaurante { background: var(--primary); color: white; }
        .tipo-motoboy { background: #17a2b8; color: white; }

        /* ===== LAYOUT ===== */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }
        .section-title {
            font-size: 18px;
            font-weight: 600;
            margin: 30px 0 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .section-title i {
            color: var(--primary);
        }

        /* ===== RESPONSIVO ===== */
        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            .admin-header .container {
                flex-direction: column;
                text-align: center;
            }
            .admin-menu {
                justify-content: center;
            }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <header class="admin-header">
        <div class="container">
            <h2><i class="fas fa-crown"></i> Painel Administrativo</h2>
            <nav class="admin-menu">
                <a href="dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
                <a href="restaurants.php"><i class="fas fa-store"></i> Restaurantes</a>
                <a href="couriers.php"><i class="fas fa-motorcycle"></i> Motoboys</a>
                <a href="users.php"><i class="fas fa-users"></i> Usuários</a>
                <a href="../../actions/logout.php" class="sair"><i class="fas fa-sign-out-alt"></i> Sair</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <!-- FLASH MESSAGE -->
        <?php if ($flash): ?>
            <div class="flash-message <?php echo $flash['type']; ?>">
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>

        <!-- BOAS-VINDAS -->
        <div style="margin: 20px 0;">
            <h1>Olá, <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>! 👋</h1>
            <p style="color: var(--text-muted);">Gerencie todos os aspectos do Foods Delivery.</p>
        </div>

        <!-- STATS -->
        <div class="stats-grid">
            <div class="stat-card primary">
                <i class="fas fa-store"></i>
                <h3><?php echo $totalRestaurantes; ?></h3>
                <p>Restaurantes</p>
            </div>
            <div class="stat-card info">
                <i class="fas fa-motorcycle"></i>
                <h3><?php echo $totalMotoboys; ?></h3>
                <p>Motoboys</p>
            </div>
            <div class="stat-card success">
                <i class="fas fa-users"></i>
                <h3><?php echo $totalClientes; ?></h3>
                <p>Clientes</p>
            </div>
            <div class="stat-card warning">
                <i class="fas fa-shopping-bag"></i>
                <h3><?php echo $totalPedidos; ?></h3>
                <p>Pedidos</p>
            </div>
            <div class="stat-card" style="border-color: #ffd700;">
                <i class="fas fa-dollar-sign" style="color: #ffd700;"></i>
                <h3>R$ <?php echo number_format($faturamento ?? 0, 2, ',', '.'); ?></h3>
                <p>Faturamento Total</p>
            </div>
        </div>

        <!-- STATUS DOS PEDIDOS -->
        <div style="display: flex; gap: 15px; flex-wrap: wrap; margin-bottom: 30px; background: white; padding: 20px; border-radius: var(--radius); box-shadow: var(--shadow);">
            <span style="font-weight: 600;">📊 Status dos Pedidos:</span>
            <?php 
            $statusLabels = [
                'pendente' => '🟡 Pendente',
                'confirmado' => '🔵 Confirmado',
                'preparando' => '🟠 Preparando',
                'saiu_entrega' => '🟣 Saiu para Entrega',
                'entregue' => '🟢 Entregue',
                'cancelado' => '🔴 Cancelado'
            ];
            foreach ($statusLabels as $key => $label): 
                $count = $statusCount[$key] ?? 0;
            ?>
                <span style="background: #f8f9fa; padding: 5px 12px; border-radius: 12px; font-size: 13px;">
                    <?php echo $label; ?>: <strong><?php echo $count; ?></strong>
                </span>
            <?php endforeach; ?>
        </div>

        <!-- GRID PRINCIPAL -->
        <div class="dashboard-grid">
            <!-- ÚLTIMOS PEDIDOS -->
            <div>
                <div class="section-title">
                    <i class="fas fa-clock"></i> Últimos Pedidos
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Cliente</th>
                                <th>Restaurante</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pedidos as $p): ?>
                            <tr>
                                <td>#<?php echo $p['id']; ?></td>
                                <td><?php echo htmlspecialchars($p['cliente']); ?></td>
                                <td><?php echo htmlspecialchars($p['restaurante']); ?></td>
                                <td>R$ <?php echo number_format($p['total'], 2, ',', '.'); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $p['status']; ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $p['status'])); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($p['data_pedido'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ÚLTIMOS USUÁRIOS -->
            <div>
                <div class="section-title">
                    <i class="fas fa-user-plus"></i> Últimos Cadastros
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Tipo</th>
                                <th>Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $u): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($u['nome']); ?></td>
                                <td>
                                    <span class="tipo-badge tipo-<?php echo $u['tipo']; ?>">
                                        <?php echo ucfirst($u['tipo']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($u['data_cadastro'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- AÇÕES RÁPIDAS -->
        <div style="margin: 30px 0; display: flex; gap: 15px; flex-wrap: wrap;">
            <a href="restaurants.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Gerenciar Restaurantes
            </a>
            <a href="couriers.php" class="btn btn-secondary">
                <i class="fas fa-plus"></i> Gerenciar Motoboys
            </a>
            <a href="users.php" class="btn" style="background: #17a2b8; color: white;">
                <i class="fas fa-users"></i> Gerenciar Usuários
            </a>
        </div>
    </main>
</body>
</html>