<?php
require_once 'includes/functions.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    if ($email) {
        try {
            getConnection()->prepare("INSERT INTO newsletter (email) VALUES (?)")->execute([$email]);
            flash('sucesso', "E-mail $email inscrito!");
        } catch (Exception $e) {
            flash('erro', 'Este e-mail já está inscrito.');
        }
    }
}
redirecionar($_SERVER['HTTP_REFERER'] ?? 'index.php');