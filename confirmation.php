<?php
require_once "auth_check.php";
require_once "connexion.php";

$idCommande = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

$requete = $pdo->prepare("SELECT * FROM commande WHERE id_commande = ? AND id_utilisateur = ?");
$requete->execute([$idCommande, $_SESSION["user_id"]]);
$commande = $requete->fetch();

if (!$commande) {
    $_SESSION["message"] = "Commande introuvable.";
    header("Location: index.php");
    exit;
}

require_once "header.php";
?>
<section class="confirmation-card">
    <h1>Commande validee</h1>
    <p>Merci pour votre achat. Votre commande a bien ete enregistree.</p>
    <div class="details-commande">
        <p><strong>Numero :</strong> <?= $commande["id_commande"] ?></p>
        <p><strong>Total :</strong> <?= number_format($commande["total"], 2, ',', ' ') ?> EUR</p>
        <p><strong>Livraison :</strong> <?= htmlspecialchars($commande["adresse"]) ?></p>
    </div>
    <a class="btn-principal" href="index.php">Retour a la boutique</a>
</section>
<?php require_once "footer.php"; ?>
