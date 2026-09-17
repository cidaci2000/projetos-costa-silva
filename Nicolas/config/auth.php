<?php
// config/auth.php

// Iniciar sessão se não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class Auth {
    /**
     * Verifica se o usuário está logado
     */
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']) && isset($_SESSION['user_type']);
    }
    
    /**
     * Verifica se é admin
     */
    public static function isAdmin() {
        return self::isLoggedIn() && $_SESSION['user_type'] === 'admin';
    }
    
    /**
     * Verifica se é cliente
     */
    public static function isCliente() {
        return self::isLoggedIn() && $_SESSION['user_type'] === 'cliente';
    }
    
    /**
     * Redireciona se não estiver logado
     */
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header('Location: login.php');
            exit;
        }
    }
    
    /**
     * Redireciona se não for admin
     */
    public static function requireAdmin() {
        if (!self::isAdmin()) {
            header('Location: login.php');
            exit;
        }
    }
    
    /**
     * Faz login
     */
    public static function login($userId, $userType, $userName, $userEmail) {
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_type'] = $userType;
        $_SESSION['user_name'] = $userName;
        $_SESSION['user_email'] = $userEmail;
        $_SESSION['logged_in_at'] = time();
    }
    
    /**
     * Faz logout
     */
    public static function logout() {
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }
    
    /**
     * Retorna dados do usuário logado
     */
    public static function getUser() {
        if (self::isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'],
                'type' => $_SESSION['user_type'],
                'name' => $_SESSION['user_name'],
                'email' => $_SESSION['user_email']
            ];
        }
        return null;
    }
}