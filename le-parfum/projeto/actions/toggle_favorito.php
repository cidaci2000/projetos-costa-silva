<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/funcoes.php';
require_once __DIR__ . '/../includes/auth.php';

if (!usuarioLogado()) {
    flash('Faça login para favoritar.', 'error');
    redirect(BASE_URL . 'pages/login.php');
}

$id = (int)($_POST['produto_id'] ?? 0);
$uid = $_SESSION['usuario_id'];

$stmt = $pdo->prepare("SELECT id FROM favoritos WHERE usuario_id = ? AND produto_id = ?");
$stmt->execute([$uid, $id]);

if ($stmt->fetch()) {
    $pdo->prepare("DELETE FROM favoritos WHERE usuario_id = ? AND produto_id = ?")->execute([$uid, $id]);
    flash('Removido dos favoritos.', 'success');
} else {
    $pdo->prepare("INSERT INTO favoritos (usuario_id, produto_id) VALUES (?, ?)")->execute([$uid, $id]);
    flash('Adicionado aos favoritos! ❤️', 'success');
}

redirect($_SERVER['HTTP_REFERER'] ?? BASE_URL . 'pages/catalogo.php');