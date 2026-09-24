<?php
require_once 'includes/functions.php';
session_destroy();
session_start();
setFlash('sucesso', 'Você saiu da conta.');
redirect('index.php');