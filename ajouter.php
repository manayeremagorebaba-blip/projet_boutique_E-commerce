<?php
require_once "auth_check.php";
require_once "connexion.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $idProduit = (int) $_POST["id_produit"];

    $requete = $pdo->prepare("SELECT * FROM produit WHERE id_produit = ?");
    $requete->execute([$idProduit]);
    $produit = $requete->fetch();

    if ($produit && $produit["stock"] > 0) {
        if (!isset($_SESSION["panier"])) {
            $_SESSION["panier"] = [];
        }

        if (isset($_SESSION["panier"][$idProduit])) {
            $_SESSION["panier"][$idProduit]++;
        } else {
            $_SESSION["panier"][$idProduit] = 1;
        }

        $_SESSION["message"] = "Produit ajoute au panier.";
    }
}

header("Location: panier.php");
exit;
?>
