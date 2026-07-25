<?php
require_once "auth_check.php";

$idProduit = (int) $_POST["id_produit"];
$action = $_POST["action"];

if (isset($_SESSION["panier"][$idProduit])) {
    if ($action === "plus") {
        $_SESSION["panier"][$idProduit]++;
    }

    if ($action === "moins") {
        $_SESSION["panier"][$idProduit]--;
    }

    if ($action === "supprimer" || $_SESSION["panier"][$idProduit] <= 0) {
        unset($_SESSION["panier"][$idProduit]);
    }
}

header("Location: panier.php");
exit;
?>
