<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$nombrePanier = 0;

if (isset($_SESSION["panier"])) {
    $nombrePanier = array_sum($_SESSION["panier"]);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New-Tech Boutique</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="topbar">
    <a class="logo" href="index.php">New<span>-Tech</span></a>
    <nav>
        <?php if (isset($_SESSION["user_id"])): ?>
            <a href="index.php">Boutique</a>
            <a href="panier.php">Panier (<?= $nombrePanier ?>)</a>
            <a href="logout.php">Deconnexion</a>
        <?php else: ?>
            <a href="login.php">Connexion</a>
            <a href="register.php">Inscription</a>
        <?php endif; ?>
    </nav>
</header>

<?php if (isset($_SESSION["message"])): ?>
    <div class="message">
        <?= htmlspecialchars($_SESSION["message"]) ?>
    </div>
    <?php unset($_SESSION["message"]); ?>
<?php endif; ?>

<main class="container">
