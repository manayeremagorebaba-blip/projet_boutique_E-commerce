<?php
require_once "auth_check.php";
require_once "connexion.php";

$requete = $pdo->query("SELECT * FROM produit ORDER BY id_produit DESC");
$produits = $requete->fetchAll();

require_once "header.php";
?>
<?php $estAdmin = (($_SESSION["role"] ?? null) === "admin" && ($_SESSION["is_admin"] ?? false) === true); ?>
<section class="hero">
    <div>
        <h1><?= $estAdmin ? 'Bienvenue, administrateur.' : 'Bienvenue, ' . htmlspecialchars($_SESSION["user_nom"] ?? 'client', ENT_QUOTES, 'UTF-8') . '.' ?></h1>
        <a class="btn-secondaire" href="#produits">Voir les produits</a>
        <?php if ($estAdmin): ?>
            <a class="btn-secondaire admin-shop-add" href="admin.php">Ajouter un produit</a>
        <?php endif; ?>
    </div>
</section>

<section id="produits" class="section-title">
    <h2>Nos produits</h2>
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

                <?php if ($estAdmin): ?>
                    <div class="admin-shop-actions">
                        <a class="btn-secondaire" href="admin.php?edit=<?= (int)$produit["id_produit"] ?>">Modifier ce produit</a>
                    </div>
                <?php else: ?>
                    <form action="ajouter.php" method="POST">
                        <input type="hidden" name="id_produit" value="<?= $produit["id_produit"] ?>">
                        <button type="submit" <?= $produit["stock"] <= 0 ? "disabled" : "" ?>>Ajouter au panier</button>
                    </form>
                <?php endif; ?>
            </div>
        </article>
    <?php endforeach; ?>
</div>
<?php require_once "footer.php"; ?>
