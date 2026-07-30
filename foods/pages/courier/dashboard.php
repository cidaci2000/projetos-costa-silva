<?php
session_start();

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'motoboy') {
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

// Busca motoboy
$stmt = $conn->prepare("SELECT * FROM motoboys WHERE usuario_id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$motoboy = $stmt->fetch();

if (!$motoboy) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Motoboy não encontrado!'];
    header('Location: ../../index.php');
    exit();
}

// Entregas disponíveis (status = saiu_entrega e sem motoboy)
$stmt = $conn->prepare("
    SELECT p.*, r.nome as restaurante, u.nome as cliente, 
           r.endereco as endereco_restaurante,
           r.telefone as telefone_restaurante
    FROM pedidos p 
    JOIN restaurantes r ON p.restaurante_id = r.id 
    JOIN usuarios u ON p.cliente_id = u.id 
    WHERE p.status = 'saiu_entrega' AND p.motoboy_id IS NULL
    ORDER BY p.data_pedido ASC
");
$stmt->execute();
$entregasDisponiveis = $stmt->fetchAll();

// Minhas entregas ativas
$stmt = $conn->prepare("
    SELECT p.*, r.nome as restaurante, u.nome as cliente,
           r.endereco as endereco_restaurante
    FROM pedidos p 
    JOIN restaurantes r ON p.restaurante_id = r.id 
    JOIN usuarios u ON p.cliente_id = u.id 
    WHERE p.motoboy_id = ? AND p.status NOT IN ('entregue', 'cancelado')
    ORDER BY p.data_pedido DESC
");
$stmt->execute([$motoboy['id']]);
$minhasEntregas = $stmt->fetchAll();

// Histórico de entregas
$stmt = $conn->prepare("
    SELECT COUNT(*) FROM pedidos 
    WHERE motoboy_id = ? AND status = 'entregue'
");
$stmt->execute([$motoboy['id']]);
$entregasRealizadas = $stmt->fetchColumn();

// Avaliação média
$stmt = $conn->prepare("
    SELECT AVG(nota_motoboy) FROM avaliacoes WHERE motoboy_id = ?
");
$stmt->execute([$motoboy['id']]);
$avaliacaoMedia = $stmt->fetchColumn() ?? 0;

$flash = $_SESSION['flash_message'] ?? null;
unset($_SESSION['flash_message']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motoboy - Foods Delivery</title>
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
        .stat-card.info { border-color: #17a2b8; }

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
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        .status-saiu_entrega { background: #d4edda; color: #155724; }
        .status-preparando { background: #ffe5d0; color: #e65c00; }
        .status-pendente { background: #fff3cd; color: #856404; }
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
        .btn-danger { background: #dc3545; color: white; }

        .toggle-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }
        .toggle-btn.disponivel {
            background: #28a745;
            color: white;
        }
        .toggle-btn.indisponivel {
            background: #dc3545;
            color: white;
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

        .info-box {
            background: #f8f9fa;
            padding: 15px 20px;
            border-radius: var(--radius);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        .info-box .moto-info {
            display: flex;
            gap: 20px;
            font-size: 14px;
        }
        .info-box .moto-info i {
            color: var(--primary);
            margin-right: 5px;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .courier-header .container {
                flex-direction: column;
                text-align: center;
            }
            .courier-menu {
                justify-content: center;
            }
            .info-box {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <header class="courier-header">
        <div class="container">
            <h2 style="font-size: 20px;"><i class="fas fa-motorcycle" style="color: var(--primary);"></i> <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?></h2>
            <nav class="courier-menu">
                <a href="dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
                <a href="deliveries.php"><i class="fas fa-truck"></i> Entregas</a>
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

        <!-- INFO MOTOBOY -->
        <div class="info-box">
            <div class="moto-info">
                <span><i class="fas fa-motorcycle"></i> <?php echo htmlspecialchars($motoboy['modelo_moto']); ?></span>
                <span><i class="fas fa-id-card"></i> Placa: <?php echo htmlspecialchars($motoboy['placa']); ?></span>
                <span><i class="fas fa-star"></i> Avaliação: <?php echo number_format($avaliacaoMedia, 1); ?></span>
            </div>
            <div>
                <form action="../../actions/courier/toggle-status.php" method="POST" style="display: inline;">
                    <input type="hidden" name="disponivel" value="<?php echo $motoboy['disponivel'] ? 0 : 1; ?>">
                    <button type="submit" class="toggle-btn <?php echo $motoboy['disponivel'] ? 'disponivel' : 'indisponivel'; ?>">
                        <i class="fas <?php echo $motoboy['disponivel'] ? 'fa-check-circle' : 'fa-times-circle'; ?>"></i>
                        <?php echo $motoboy['disponivel'] ? 'Disponível' : 'Indisponível'; ?>
                    </button>
                </form>
            </div>
        </div>

        <!-- STATS -->
        <div class="stats-grid">
            <div class="stat-card primary">
                <i class="fas fa-truck"></i>
                <h3><?php echo $entregasRealizadas; ?></h3>
                <p>Entregas Realizadas</p>
            </div>
            <div class="stat-card info">
                <i class="fas fa-clock"></i>
                <h3><?php echo count($minhasEntregas); ?></h3>
                <p>Entregas em Andamento</p>
            </div>
            <div class="stat-card warning">
                <i class="fas fa-bell"></i>
                <h3><?php echo count($entregasDisponiveis); ?></h3>
                <p>Entregas Disponíveis</p>
            </div>
            <div class="stat-card success">
                <i class="fas fa-star"></i>
                <h3><?php echo number_format($avaliacaoMedia, 1); ?></h3>
                <p>Avaliação Média</p>
            </div>
        </div>

        <!-- ENTREGAS DISPONÍVEIS -->
        <?php if (!empty($entregasDisponiveis)): ?>
        <div class="section-title">
            <i class="fas fa-bell" style="color: #ffc107;"></i> Entregas Disponíveis <span class="badge" style="background: #dc3545; color: white; padding: 2px 10px; border-radius: 12px; font-size: 12px;"><?php echo count($entregasDisponiveis); ?></span>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Pedido</th>
                        <th>Restaurante</th>
                        <th>Cliente</th>
                        <th>Valor</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($entregasDisponiveis as $e): ?>
                    <tr>
                        <td><strong>#<?php echo $e['id']; ?></strong></td>
                        <td><?php echo htmlspecialchars($e['restaurante']); ?></td>
                        <td><?php echo htmlspecialchars($e['cliente']); ?></td>
                        <td>R$ <?php echo number_format($e['total'], 2, ',', '.'); ?></td>
                        <td>
                            <form action="../../actions/courier/accept-delivery.php" method="POST">
                                <input type="hidden" name="pedido_id" value="<?php echo $e['id']; ?>">
                                <button type="submit" class="btn-sm btn-success">
                                    <i class="fas fa-check"></i> Aceitar
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- MINHAS ENTREGAS -->
        <div class="section-title">
            <i class="fas fa-truck"></i> Minhas Entregas em Andamento
        </div>
        <div class="table-container">
            <?php if (!empty($minhasEntregas)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Pedido</th>
                        <th>Restaurante</th>
                        <th>Cliente</th>
                        <th>Status</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($minhasEntregas as $e): ?>
                    <tr>
                        <td><strong>#<?php echo $e['id']; ?></strong></td>
                        <td><?php echo htmlspecialchars($e['restaurante']); ?></td>
                        <td><?php echo htmlspecialchars($e['cliente']); ?></td>
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
                <h3>Nenhuma entrega em andamento</h3>
                <p>Você pode aceitar entregas disponíveis acima.</p>
            </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>