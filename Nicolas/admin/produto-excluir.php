<?php
// admin/produto-excluir.php
require_once __DIR__ . '../models/Product.php';

$productModel = new Product();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header('Location: produtos.php?error=ID do produto não informado');
    exit;
}

$product = $productModel->findById($id);
if (!$product) {
    header('Location: produtos.php?error=Produto não encontrado');
    exit;
}

if ($productModel->delete($id)) {
    header('Location: produtos.php?success=Produto excluído com sucesso!');
} else {
    header('Location: produtos.php?error=Erro ao excluir produto');
}
exit;
?>