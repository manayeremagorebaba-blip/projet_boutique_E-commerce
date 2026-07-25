<?php
$serveur = "localhost";
$base = "boutique_php";
$utilisateur = "root";
$motdepasse = "";

try {
    $pdo = new PDO("mysql:host=$serveur;dbname=$base;charset=utf8mb4", $utilisateur, $motdepasse);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $erreur) {
    die("Erreur de connexion : " . $erreur->getMessage());
}
?>
