<?php
// ============================================================
// FUNÇÕES UTILITÁRIAS
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/** Formata preço em Real */
function preco(float $v): string {
    return 'R$ ' . number_format($v, 2, ',', '.');
}

/** Escapa HTML */
function e(?string $v): string {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}

/** Redireciona */
function redirect(string $url): void {
    header("Location: $url");
    exit;
}

/** Mensagem flash */
function flash(string $msg = null, string $tipo = 'success') {
    if ($msg !== null) {
        $_SESSION['flash'] = ['msg' => $msg, 'tipo' => $tipo];
        return null;
    }
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

/** Total de itens no carrinho */
function totalCarrinho(): int {
    $total = 0;
    foreach ($_SESSION['carrinho'] ?? [] as $qtd) {
        $total += $qtd;
    }
    return $total;
}

/** Retorna o carrinho completo com dados dos produtos */
function getCarrinhoCompleto(PDO $pdo): array {
    $carrinho = $_SESSION['carrinho'] ?? [];
    if (empty($carrinho)) return [];

    $ids = array_keys($carrinho);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $produtos = $stmt->fetchAll();

    $itens = [];
    foreach ($produtos as $p) {
        $qtd = $carrinho[$p['id']] ?? 0;
        if ($qtd > 0) {
            $p['quantidade'] = $qtd;
            $p['subtotal'] = $p['preco'] * $qtd;
            $itens[] = $p;
        }
    }
    return $itens;
}

/** Total do carrinho */
function totalCarrinhoValor(PDO $pdo): float {
    $total = 0;
    foreach (getCarrinhoCompleto($pdo) as $item) {
        $total += $item['subtotal'];
    }
    return $total;
}