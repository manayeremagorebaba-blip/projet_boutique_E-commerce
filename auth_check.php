<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// La boutique est accessible au client connecté comme à l'administrateur.
$estClient = (($_SESSION['role'] ?? null) === 'client' && isset($_SESSION['user_id']));
$estAdmin = (($_SESSION['role'] ?? null) === 'admin' && ($_SESSION['is_admin'] ?? false) === true);
if (!$estClient && !$estAdmin) {
    header('Location: login.php');
    exit;
}
