<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/funcoes.php';
require_once __DIR__ . '/../includes/auth.php';

exigirLogin();

$itens = getCarrinhoCompleto($pdo);
if (empty($itens)) {
    flash('Carrinho vazio.', 'error');
    redirect(BASE_URL . 'pages/catalogo.php');
}

$total = totalCarrinhoValor($pdo);

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO pedidos 
        (usuario_id, nome_cliente, email, telefone, cep, endereco, pagamento, total, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'processando')");
    $stmt->execute([
        $_SESSION['usuario_id'],
        $_POST['nome_cliente'],
        $_POST['email'],
        $_POST['telefone'] ?? '',
        $_POST['cep'] ?? '',
        $_POST['endereco'] ?? '',
        $_POST['pagamento'] ?? 'cartao',
        $total
    ]);
    $pedidoId = $pdo->lastInsertId();

    $stmtItem = $pdo->prepare("INSERT INTO pedido_itens 
        (pedido_id, produto_id, nome_produto, preco, quantidade)
        VALUES (?, ?, ?, ?, ?)");

    foreach ($itens as $i) {
        $stmtItem->execute([
            $pedidoId,
            $i['id'],
            $i['nome'],
            $i['preco'],
            $i['quantidade']
        ]);
    }

    $pdo->commit();

    unset($_SESSION['carrinho']);
    flash('Pedido realizado com sucesso! 🎉', 'success');
    redirect(BASE_URL . 'pages/perfil.php');

} catch (Exception $e) {
    $pdo->rollBack();
    flash('Erro ao processar pedido: ' . $e->getMessage(), 'error');
    redirect(BASE_URL . 'pages/checkout.php');
}