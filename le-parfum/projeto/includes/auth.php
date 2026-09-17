<?php
// ============================================================
// FUNÇÕES DE AUTENTICAÇÃO — VERSÃO CORRIGIDA
// ============================================================

/**
 * Verifica se o usuário está logado E se a sessão está íntegra.
 * Se a sessão estiver corrompida (faltando chaves), força logout.
 */
function usuarioLogado(): bool {
    if (empty($_SESSION['usuario_id'])) {
        return false;
    }

    // Sessão corrompida? (login antigo ou manipulação)
    if (!isset($_SESSION['usuario_nome'], $_SESSION['usuario_email'])) {
        // Limpa tudo para evitar warnings
        unset(
            $_SESSION['usuario_id'],
            $_SESSION['usuario_nome'],
            $_SESSION['usuario_email'],
            $_SESSION['usuario_admin']
        );
        return false;
    }

    return true;
}

/**
 * Retorna os dados do usuário logado ou null.
 */
function usuarioAtual(): ?array {
    if (!usuarioLogado()) {
        return null;
    }

    return [
        'id'    => (int) $_SESSION['usuario_id'],
        'nome'  => $_SESSION['usuario_nome']  ?? '',
        'email' => $_SESSION['usuario_email'] ?? '',
        'admin' => (int) ($_SESSION['usuario_admin'] ?? 0),
    ];
}

/**
 * Verifica se é admin.
 */
function isAdmin(): bool {
    return usuarioLogado() && !empty($_SESSION['usuario_admin']);
}

/**
 * Exige login. Redireciona se não estiver logado.
 */
function exigirLogin(): void {
    if (!usuarioLogado()) {
        flash('Faça login para continuar.', 'error');
        redirect(BASE_URL . 'pages/login.php');
    }
}

/**
 * Exige admin. Redireciona se não for admin.
 */
function exigirAdmin(): void {
    if (!isAdmin()) {
        flash('Acesso restrito ao administrador.', 'error');
        redirect(BASE_URL . 'pages/login.php');
    }
}

/**
 * Registra o usuário na sessão.
 */
function login(int $id, string $nome, string $email, int $admin = 0): void {
    // Regenera o ID da sessão para prevenir fixation
    session_regenerate_id(true);

    $_SESSION['usuario_id']    = $id;
    $_SESSION['usuario_nome']  = $nome;
    $_SESSION['usuario_email'] = $email;
    $_SESSION['usuario_admin'] = $admin;
}

/**
 * Encerra a sessão.
 */
function logout(): void {
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
    redirect(BASE_URL . 'pages/login.php');
}