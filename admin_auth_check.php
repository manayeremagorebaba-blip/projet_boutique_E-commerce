<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// L'accès exige le rôle explicitement attribué par admin_login.php.
// Une session client ne peut jamais accéder à la gestion des produits.
if (($_SESSION['role'] ?? null) !== 'admin' || ($_SESSION['is_admin'] ?? false) !== true) {
    header('Location: index.php');
    exit;
}
