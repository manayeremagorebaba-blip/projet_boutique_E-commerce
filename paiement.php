<?php
require_once "auth_check.php";
require_once "connexion.php";

$panier = $_SESSION["panier"] ?? [];
$articles = [];
$total = 0;
$erreur = "";

foreach ($panier as $idProduit => $quantite) {
    $requete = $pdo->prepare("SELECT * FROM produit WHERE id_produit = ?");
    $requete->execute([$idProduit]);
    $produit = $requete->fetch();

    if ($produit) {
        $sousTotal = $produit["prix"] * $quantite;
        $total += $sousTotal;
        $articles[] = ["produit" => $produit, "quantite" => $quantite, "sousTotal" => $sousTotal];
    }
}

if (empty($articles)) {
    $_SESSION["message"] = "Votre panier est vide.";
    header("Location: panier.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nomLivraison = trim($_POST["nom_livraison"]);
    $adresse = trim($_POST["adresse"]);
    $telephone = trim($_POST["telephone"]);
    $modePaiement = $_POST["mode_paiement"];

    if ($nomLivraison === "" || $adresse === "" || $telephone === "") {
        $erreur = "Veuillez remplir tous les champs.";
    } else {
        $pdo->beginTransaction();

        $requete = $pdo->prepare("INSERT INTO commande (id_utilisateur, date_commande, total, nom_livraison, adresse, telephone, mode_paiement) VALUES (?, NOW(), ?, ?, ?, ?, ?)");
        $requete->execute([$_SESSION["user_id"], $total, $nomLivraison, $adresse, $telephone, $modePaiement]);

        $idCommande = $pdo->lastInsertId();

        foreach ($articles as $article) {
            $requete = $pdo->prepare("INSERT INTO ligne_commande (id_commande, id_produit, quantite, prix_unitaire) VALUES (?, ?, ?, ?)");
            $requete->execute([$idCommande, $article["produit"]["id_produit"], $article["quantite"], $article["produit"]["prix"]]);
        }

        $pdo->commit();
        unset($_SESSION["panier"]);

        header("Location: confirmation.php?id=" . $idCommande);
        exit;
    }
}

require_once "header.php";
?>
<section class="section-title">
    <h1>Paiement</h1>
    <p>Cette page simule un paiement et enregistre la commande.</p>
</section>

<div class="paiement-layout">
    <section class="table-card">
        <h2>Recapitulatif</h2>
        <?php foreach ($articles as $article): ?>
            <div class="ligne-recap">
                <span><?= htmlspecialchars($article["produit"]["nom"]) ?> x <?= $article["quantite"] ?></span>
                <strong><?= number_format($article["sousTotal"], 2, ',', ' ') ?> EUR</strong>
            </div>
        <?php endforeach; ?>
        <div class="total-recap">
            <span>Total</span>
            <strong><?= number_format($total, 2, ',', ' ') ?> EUR</strong>
        </div>
    </section>

    <section class="auth-card">
        <h2>Informations de livraison</h2>

        <?php if ($erreur !== ""): ?>
            <div class="erreur"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>

        <form method="POST" class="form">
            <label>Nom du destinataire</label>
            <input type="text" name="nom_livraison" required>

            <label>Adresse</label>
            <textarea name="adresse" rows="4" required></textarea>

            <label>Telephone</label>
            <input type="text" name="telephone" required>

            <label>Mode de paiement</label>
            <select name="mode_paiement">
                <option value="Carte bancaire">Carte bancaire</option>
                <option value="Paiement a la livraison">Paiement a la livraison</option>
            </select>

            <button type="submit">Valider la commande</button>
        </form>
    </section>
</div>
<?php require_once "footer.php"; ?>
