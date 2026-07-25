<?php
require_once "auth_check.php";
require_once "connexion.php";

$requete = $pdo->query("SELECT * FROM produit ORDER BY id_produit DESC");
$produits = $requete->fetchAll();

require_once "header.php";
?>
<section class="hero">
    <div>
        <p class="badge">Boutique dynamique en PHP</p>
        <h1>Bienvenue, <?= htmlspecialchars($_SESSION["user_nom"]) ?>.</h1>
        <p>Decouvrez une selection de produits high-tech avec un panier, une connexion et une commande en base de donnees.</p>
        <a class="btn-secondaire" href="#produits">Voir les produits</a>
    </div>
</section>

<section id="produits" class="section-title">
    <h2>Nos produits</h2>
    <p>Produits recuperes automatiquement depuis la base MySQL.</p>
</section>

<div class="grille-produits">
    <?php foreach ($produits as $produit): ?>
        <article class="carte-produit">
            <?php if (!empty($produit["image"]) && file_exists($produit["image"])): ?>
                <img src="<?= htmlspecialchars($produit["image"]) ?>" alt="<?= htmlspecialchars($produit["nom"]) ?>">
            <?php else: ?>
                <div class="image-fallback">New-Tech</div>
            <?php endif; ?>

            <div class="produit-contenu">
                <h3><?= htmlspecialchars($produit["nom"]) ?></h3>
                <p><?= htmlspecialchars($produit["description"]) ?></p>
                <div class="produit-bas">
                    <strong><?= number_format($produit["prix"], 2, ',', ' ') ?> EUR</strong>
                    <span>Stock : <?= $produit["stock"] ?></span>
                </div>

                <form action="ajouter.php" method="POST">
                    <input type="hidden" name="id_produit" value="<?= $produit["id_produit"] ?>">
                    <button type="submit" <?= $produit["stock"] <= 0 ? "disabled" : "" ?>>Ajouter au panier</button>
                </form>
            </div>
        </article>
    <?php endforeach; ?>
</div>
<?php require_once "footer.php"; ?>
