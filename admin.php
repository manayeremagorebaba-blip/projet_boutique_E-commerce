<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'admin_config.php';

// admin.php est le point d'entrée unique : sans session admin, afficher la connexion.
if (($_SESSION['role'] ?? null) !== 'admin' || ($_SESSION['is_admin'] ?? false) !== true) {
    $erreurConnexion = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'admin_login') {
        $email = trim($_POST['email'] ?? '');
        $motdepasse = $_POST['motdepasse'] ?? '';
        if (hash_equals(ADMIN_EMAIL, $email) && password_verify($motdepasse, ADMIN_PASSWORD_HASH)) {
            session_regenerate_id(true);
            // Une session admin ne conserve aucun droit ni panier de client.
            unset($_SESSION['user_id'], $_SESSION['user_nom'], $_SESSION['panier'], $_SESSION['role'], $_SESSION['is_admin']);
            $_SESSION['role'] = 'admin';
            $_SESSION['is_admin'] = true;
            $_SESSION['admin_email'] = $email;
            $_SESSION['admin_csrf'] = bin2hex(random_bytes(32));
            header('Location: admin.php');
            exit;
        }
        $erreurConnexion = 'Identifiants administrateur incorrects.';
    }
    require_once 'header.php';
    ?>
    <section class="auth-card">
        <h1>Connexion administrateur</h1>
        <p>Connectez-vous pour gérer les produits de New-Tech Boutique.</p>
        <?php if ($erreurConnexion): ?><div class="erreur"><?= htmlspecialchars($erreurConnexion, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
        <form method="POST" action="admin.php" class="form">
            <input type="hidden" name="action" value="admin_login">
            <label for="email">Adresse administrateur</label>
            <input id="email" type="email" name="email" required autocomplete="username">
            <label for="motdepasse">Mot de passe</label>
            <input id="motdepasse" type="password" name="motdepasse" required autocomplete="current-password">
            <button type="submit">Se connecter à l'administration</button>
        </form>
    </section>
    <?php
    require_once 'footer.php';
    exit;
}

require_once 'admin_auth_check.php';
require_once 'connexion.php';

if (empty($_SESSION['admin_csrf'])) $_SESSION['admin_csrf'] = bin2hex(random_bytes(32));
$csrf = $_SESSION['admin_csrf'];
$message = '';
$erreur = '';
$edition = null;

function nettoyerTexte($valeur) {
    return trim((string)$valeur);
}
function urlImageProduit(?string $image): ?string {
    // Utiliser exactement le même chemin que la boutique (index.php).
    // Ne pas reconstruire ni remplacer le chemin enregistré en base : il s'agit
    // de la source réelle de l'image affichée aux clients.
    $image = trim((string)$image);
    if ($image === '' || str_contains($image, "\0")) return null;
    if (filter_var($image, FILTER_VALIDATE_URL)) {
        return preg_match('~^https?://~i', $image) ? $image : null;
    }
    $chemin = str_replace('\\', '/', $image);
    if (str_contains($chemin, '..')) return null;
    return is_file(__DIR__ . '/' . ltrim($chemin, '/')) ? $chemin : null;
}
function enregistrerImage(array $fichier, string $ancienneImage = ''): string {
    if (($fichier['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return $ancienneImage;
    if ($fichier['error'] !== UPLOAD_ERR_OK) throw new RuntimeException('Le téléversement de l’image a échoué.');
    if ($fichier['size'] > 5 * 1024 * 1024) throw new RuntimeException('L’image ne doit pas dépasser 5 Mo.');
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($fichier['tmp_name']);
    $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
    if (!isset($extensions[$mime])) throw new RuntimeException('Format non accepté. Utilisez JPG, PNG, WEBP ou GIF.');
    $dossier = __DIR__ . '/images/products';
    if (!is_dir($dossier) && !mkdir($dossier, 0755, true) && !is_dir($dossier)) throw new RuntimeException('Impossible de créer le dossier des images.');
    $nom = bin2hex(random_bytes(16)) . '.' . $extensions[$mime];
    if (!move_uploaded_file($fichier['tmp_name'], $dossier . '/' . $nom)) throw new RuntimeException('Impossible d’enregistrer l’image.');
    return 'images/products/' . $nom;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($csrf, (string)($_POST['csrf_token'] ?? ''))) {
        $erreur = 'La session du formulaire a expiré. Rechargez la page et réessayez.';
    } else {
        $action = $_POST['action'] ?? '';
        try {
            if ($action === 'save') {
                $id = (int)($_POST['id_produit'] ?? 0);
                $nom = nettoyerTexte($_POST['nom'] ?? '');
                $description = nettoyerTexte($_POST['description'] ?? '');
                $prix = filter_var($_POST['prix'] ?? '', FILTER_VALIDATE_FLOAT);
                $stock = filter_var($_POST['stock'] ?? '', FILTER_VALIDATE_INT);
                if ($nom === '' || mb_strlen($nom) > 100) throw new RuntimeException('Le nom est obligatoire (100 caractères maximum).');
                if ($description === '') throw new RuntimeException('La description est obligatoire.');
                if ($prix === false || $prix < 0 || $prix > 99999999.99) throw new RuntimeException('Saisissez un prix valide et positif.');
                if ($stock === false || $stock < 0) throw new RuntimeException('Le stock doit être un nombre entier positif ou nul.');
                $ancienneImage = '';
                if ($id > 0) {
                    $check = $pdo->prepare('SELECT image FROM produit WHERE id_produit = ?');
                    $check->execute([$id]);
                    $existant = $check->fetch();
                    if (!$existant) throw new RuntimeException('Ce produit n’existe plus.');
                    $ancienneImage = $existant['image'] ?? '';
                }
                $image = enregistrerImage($_FILES['image'] ?? [], $ancienneImage);
                if ($id > 0) {
                    $stmt = $pdo->prepare('UPDATE produit SET nom = ?, description = ?, prix = ?, image = ?, stock = ? WHERE id_produit = ?');
                    $stmt->execute([$nom, $description, $prix, $image, $stock, $id]);
                    $message = 'Produit modifié avec succès.';
                } else {
                    $stmt = $pdo->prepare('INSERT INTO produit (nom, description, prix, image, stock) VALUES (?, ?, ?, ?, ?)');
                    $stmt->execute([$nom, $description, $prix, $image, $stock]);
                    $message = 'Produit ajouté avec succès à la boutique et à la base de données.';
                }
            } elseif ($action === 'delete') {
                $id = (int)($_POST['id_produit'] ?? 0);
                if ($id <= 0) throw new RuntimeException('Produit invalide.');
                try {
                    $stmt = $pdo->prepare('DELETE FROM produit WHERE id_produit = ?');
                    $stmt->execute([$id]);
                    $message = $stmt->rowCount() ? 'Produit retiré de la boutique.' : 'Produit introuvable.';
                } catch (PDOException $e) {
                    if (($e->errorInfo[1] ?? null) == 1451) {
                        throw new RuntimeException('Ce produit figure déjà dans une commande : la base empêche sa suppression pour conserver l’historique. Il faut le conserver ou ajouter une fonction d’archivage.');
                    }
                    throw $e;
                }
            }
        } catch (RuntimeException $e) {
            $erreur = $e->getMessage();
        } catch (PDOException $e) {
            $erreur = 'Une erreur de base de données est survenue. Vérifiez les données puis réessayez.';
        }
    }
}

if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM produit WHERE id_produit = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $edition = $stmt->fetch() ?: null;
}
$produits = $pdo->query('SELECT * FROM produit ORDER BY id_produit DESC')->fetchAll();
require_once 'header.php';
?>
<section class="admin-entete">
    <div><p class="badge">Espace sécurisé</p><h1>Administration des produits</h1><p>Ajoutez, modifiez ou retirez des produits sans intervenir directement dans MySQL.</p></div>
    <a class="btn-secondaire" href="admin_logout.php">Déconnexion admin</a>
</section>
<?php if ($message): ?><div class="message"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
<?php if ($erreur): ?><div class="erreur admin-alert"><?= htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
<section class="admin-form-card">
    <h2><?= $edition ? 'Modifier le produit' : 'Ajouter un produit' ?></h2>
    <form method="POST" enctype="multipart/form-data" class="form admin-form">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id_produit" value="<?= (int)($edition['id_produit'] ?? 0) ?>">
        <label for="nom">Nom du produit</label>
        <input id="nom" name="nom" maxlength="100" required value="<?= htmlspecialchars($edition['nom'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="4" required><?= htmlspecialchars($edition['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        <div class="admin-fields-row">
            <div><label for="prix">Prix (€)</label><input id="prix" type="number" name="prix" min="0" max="99999999.99" step="0.01" required value="<?= htmlspecialchars((string)($edition['prix'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></div>
            <div><label for="stock">Stock</label><input id="stock" type="number" name="stock" min="0" step="1" required value="<?= htmlspecialchars((string)($edition['stock'] ?? '0'), ENT_QUOTES, 'UTF-8') ?>"></div>
        </div>
        <label for="image">Image du produit (JPG, PNG, WEBP ou GIF, 5 Mo maximum)</label>
        <input id="image" type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif">
        <?php if (!empty($edition['image'])): ?>
            <?php $imageEdition = urlImageProduit($edition['image']); ?>
            <div class="admin-current-image">
                <?php if ($imageEdition): ?><img class="admin-edit-thumb" src="<?= htmlspecialchars($imageEdition, ENT_QUOTES, 'UTF-8') ?>" alt="Image actuelle de <?= htmlspecialchars($edition['nom'], ENT_QUOTES, 'UTF-8') ?>"><?php else: ?><span class="admin-image-missing">Image introuvable dans le dossier du projet.</span><?php endif; ?>
                <p>Image actuelle : <?= htmlspecialchars(basename($edition['image']), ENT_QUOTES, 'UTF-8') ?>. Laissez le champ vide pour la conserver.</p>
            </div>
        <?php endif; ?>
        <div class="admin-actions"><button type="submit"><?= $edition ? 'Enregistrer les modifications' : 'Ajouter le produit' ?></button><?php if ($edition): ?><a class="btn-secondaire" href="admin.php">Annuler</a><?php endif; ?></div>
    </form>
</section>
<section class="admin-list-section">
    <h2>Produits de la boutique (<?= count($produits) ?>)</h2>
    <div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Image</th><th>Produit</th><th>Prix</th><th>Stock</th><th>Actions</th></tr></thead><tbody>
    <?php foreach ($produits as $produit): ?><tr>
        <?php $imageProduit = urlImageProduit($produit['image'] ?? ''); ?>
        <td class="admin-image-cell" data-label="Image">
            <?php if ($imageProduit): ?>
                <img class="admin-thumb" src="<?= htmlspecialchars($imageProduit, ENT_QUOTES, 'UTF-8') ?>" alt="Photo de <?= htmlspecialchars($produit['nom'], ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
            <?php else: ?>
                <div class="admin-thumb-placeholder" title="Aucune image disponible"><span>📷</span><small>Image indisponible</small></div>
            <?php endif; ?>
        </td>
        <td data-label="Produit"><strong><?= htmlspecialchars($produit['nom'], ENT_QUOTES, 'UTF-8') ?></strong><small><?= htmlspecialchars(mb_strimwidth($produit['description'], 0, 90, '…'), ENT_QUOTES, 'UTF-8') ?></small></td>
        <td data-label="Prix"><?= number_format((float)$produit['prix'], 2, ',', ' ') ?> €</td><td data-label="Stock"><?= (int)$produit['stock'] ?></td>
        <td data-label="Actions"><div class="admin-actions"><a class="btn-secondaire" href="admin.php?edit=<?= (int)$produit['id_produit'] ?>">Modifier</a><form method="POST" onsubmit="return confirm('Retirer ce produit de la base de données ?');"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id_produit" value="<?= (int)$produit['id_produit'] ?>"><button class="btn-danger" type="submit">Retirer</button></form></div></td>
    </tr><?php endforeach; ?>
    <?php if (!$produits): ?><tr><td colspan="5">Aucun produit dans la boutique.</td></tr><?php endif; ?>
    </tbody></table></div>
</section>
<?php require_once 'footer.php'; ?>
