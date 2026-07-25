<?php
require_once "auth_check.php";
require_once "connexion.php";

$panier = $_SESSION["panier"] ?? [];
$articles = [];
$total = 0;

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

require_once "header.php";
?>
<section class="section-title">
    <h1>Mon panier</h1>
    <p>Verifiez vos articles avant de passer au paiement.</p>
</section>

<?php if (empty($articles)): ?>
    <div class="bloc-vide">
        <h2>Votre panier est vide.</h2>
        <p>Ajoutez un produit depuis la boutique.</p>
        <a class="btn-secondaire" href="index.php">Retour boutique</a>
    </div>
<?php else: ?>
    <div class="panier-layout">
        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Prix</th>
                        <th>Quantite</th>
                        <th>Sous-total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($articles as $article): ?>
                        <tr>
                            <td><?= htmlspecialchars($article["produit"]["nom"]) ?></td>
                            <td><?= number_format($article["produit"]["prix"], 2, ',', ' ') ?> EUR</td>
                            <td>
                                <form action="maj_panier.php" method="POST" class="quantite-form">
                                    <input type="hidden" name="id_produit" value="<?= $article["produit"]["id_produit"] ?>">
                                    <button name="action" value="moins">-</button>
                                    <span><?= $article["quantite"] ?></span>
                                    <button name="action" value="plus">+</button>
                                    <button name="action" value="supprimer" class="btn-danger">Supprimer</button>
                                </form>
                            </td>
                            <td><?= number_format($article["sousTotal"], 2, ',', ' ') ?> EUR</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <aside class="resume-card">
            <h2>Resume</h2>
            <p>Total a payer</p>
            <strong><?= number_format($total, 2, ',', ' ') ?> EUR</strong>
            <a class="btn-principal" href="paiement.php">Passer au paiement</a>
        </aside>
    </div>
<?php endif; ?>
<?php require_once "footer.php"; ?>
