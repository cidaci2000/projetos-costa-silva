<?php
session_start();

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'restaurante') {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Acesso negado!'];
    header('Location: ../../index.php');
    exit();
}

try {
    $conn = new PDO("mysql:host=localhost;dbname=foods_delivery", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// Busca restaurante do usuário
$stmt = $conn->prepare("SELECT * FROM restaurantes WHERE usuario_id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$restaurante = $stmt->fetch();

if (!$restaurante) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Restaurante não encontrado!'];
    header('Location: ../../index.php');
    exit();
}

// Estatísticas
$stmt = $conn->prepare("SELECT COUNT(*) FROM pedidos WHERE restaurante_id = ?");
$stmt->execute([$restaurante['id']]);
$totalPedidos = $stmt->fetchColumn();

$stmt = $conn->prepare("SELECT COUNT(*) FROM produtos WHERE restaurante_id = ? AND disponivel = 1");
$stmt->execute([$restaurante['id']]);
$produtosAtivos = $stmt->fetchColumn();

// Pedidos por status
$stmt = $conn->prepare("SELECT status, COUNT(*) as total FROM pedidos WHERE restaurante_id = ? GROUP BY status");
$stmt->execute([$restaurante['id']]);
$statusCount = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Faturamento
$stmt = $conn->prepare("SELECT SUM(total) FROM pedidos WHERE restaurante_id = ? AND status = 'entregue'");
$stmt->execute([$restaurante['id']]);
$faturamento = $stmt->fetchColumn();

// Últimos pedidos
$stmt = $conn->prepare("
    SELECT p.*, u.nome as cliente 
    FROM pedidos p 
    JOIN usuarios u ON p.cliente_id = u.id 
    WHERE p.restaurante_id = ? 
    ORDER BY p.data_pedido DESC LIMIT 10
");
$stmt->execute([$restaurante['id']]);
$pedidos = $stmt->fetchAll();

// Pedidos pendentes (urgentes)
$stmt = $conn->prepare("
    SELECT COUNT(*) FROM pedidos 
    WHERE restaurante_id = ? AND status IN ('pendente', 'confirmado')
");
$stmt->execute([$restaurante['id']]);
$pedidosPendentes = $stmt->fetchColumn();

$flash = $_SESSION['flash_message'] ?? null;
unset($_SESSION['flash_message']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($restaurante['nome']); ?> - Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../style.css">
    <style>
        /* HEADER */
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

        /* STATS */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            text-align: center;
            border-bottom: 4px solid transparent;
            transition: var(--transition);
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }
        .stat-card i {
            font-size: 28px;
            color: var(--primary);
        }
        .stat-card h3 {
            font-size: 24px;
            margin: 8px 0;
        }
        .stat-card p {
            color: var(--text-muted);
            font-size: 13px;
        }
        .stat-card.primary { border-color: var(--primary); }
        .stat-card.success { border-color: #28a745; }
        .stat-card.warning { border-color: #ffc107; }
        .stat-card.danger { border-color: #dc3545; }
        .stat-card.info { border-color: #17a2b8; }

        /* TABELAS */
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

        /* STATUS BADGES */
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
        .btn-success { background: #28a745; color: white; }
        .btn-warning { background: #ffc107; color: #333; }
        .btn-danger { background: #dc3545; color: white; }

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

        .alert-pedidos {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: var(--radius);
            padding: 15px 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }
        .alert-pedidos strong {
            color: #856404;
        }
        .alert-pedidos a {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
        }
        .alert-pedidos a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .restaurant-header .container {
                flex-direction: column;
                text-align: center;
            }
            .restaurant-menu {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <header class="restaurant-header">
        <div class="container">
            <h2 style="font-size: 20px;"><i class="fas fa-store" style="color: var(--primary);"></i> <?php echo htmlspecialchars($restaurante['nome']); ?></h2>
            <nav class="restaurant-menu">
                <a href="dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
                <a href="products.php"><i class="fas fa-utensils"></i> Cardápio</a>
                <a href="orders.php"><i class="fas fa-shopping-bag"></i> Pedidos</a>
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

        <!-- BOAS-VINDAS -->
        <div style="margin: 20px 0;">
            <h1>Olá, <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>! 👋</h1>
            <p style="color: var(--text-muted);">Gerencie seu restaurante e pedidos aqui.</p>
        </div>

        <!-- ALERTA DE PEDIDOS PENDENTES -->
        <?php if ($pedidosPendentes > 0): ?>
        <div class="alert-pedidos">
            <div>
                <i class="fas fa-exclamation-triangle" style="color: #856404;"></i>
                <strong>Você tem <?php echo $pedidosPendentes; ?> pedido(s) pendente(s)!</strong>
                <span style="color: #856404;">Aguarde sua confirmação.</span>
            </div>
            <a href="orders.php">Ver pedidos →</a>
        </div>
        <?php endif; ?>

        <!-- STATS -->
        <div class="stats-grid">
            <div class="stat-card primary">
                <i class="fas fa-shopping-bag"></i>
                <h3><?php echo $totalPedidos; ?></h3>
                <p>Total de Pedidos</p>
            </div>
            <div class="stat-card success">
                <i class="fas fa-utensils"></i>
                <h3><?php echo $produtosAtivos; ?></h3>
                <p>Produtos Ativos</p>
            </div>
            <div class="stat-card warning">
                <i class="fas fa-hourglass-half"></i>
                <h3><?php echo $pedidosPendentes; ?></h3>
                <p>Pedidos Pendentes</p>
            </div>
            <div class="stat-card" style="border-color: #ffd700;">
                <i class="fas fa-dollar-sign" style="color: #ffd700;"></i>
                <h3>R$ <?php echo number_format($faturamento ?? 0, 2, ',', '.'); ?></h3>
                <p>Faturamento Total</p>
            </div>
        </div>

        <!-- ÚLTIMOS PEDIDOS -->
        <div class="section-title">
            <i class="fas fa-clock"></i> Últimos Pedidos
        </div>
        <div class="table-container">
            <?php if (!empty($pedidos)): ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Data</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pedidos as $p): ?>
                    <tr>
                        <td>#<?php echo $p['id']; ?></td>
                        <td><?php echo htmlspecialchars($p['cliente']); ?></td>
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
                                <select name="status" onchange="this.form.submit()" style="padding: 5px 10px; border-radius: 6px; border: 1px solid #ddd; font-family: inherit; font-size: 12px;">
                                    <option value="pendente" <?php echo $p['status'] == 'pendente' ? 'selected' : ''; ?>>🟡 Pendente</option>
                                    <option value="confirmado" <?php echo $p['status'] == 'confirmado' ? 'selected' : ''; ?>>🔵 Confirmar</option>
                                    <option value="preparando" <?php echo $p['status'] == 'preparando' ? 'selected' : ''; ?>>🟠 Preparando</option>
                                    <option value="saiu_entrega" <?php echo $p['status'] == 'saiu_entrega' ? 'selected' : ''; ?>>🟣 Saiu para Entrega</option>
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
            <div style="text-align: center; padding: 40px; color: var(--text-muted);">
                <i class="fas fa-shopping-bag" style="font-size: 48px; opacity: 0.5; margin-bottom: 15px; display: block;"></i>
                <h3>Nenhum pedido ainda</h3>
                <p>Os pedidos aparecerão aqui quando os clientes começarem a comprar.</p>
            </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>