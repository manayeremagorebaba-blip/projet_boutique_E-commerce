<?php
session_start();
require_once "connexion.php";

if (isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

$erreur = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = trim($_POST["nom"]);
    $email = trim($_POST["email"]);
    $motdepasse = $_POST["motdepasse"];

    if ($nom === "" || $email === "" || $motdepasse === "") {
        $erreur = "Tous les champs sont obligatoires.";
    } else {
        $motdepasseHash = password_hash($motdepasse, PASSWORD_DEFAULT);

        try {
            $requete = $pdo->prepare("INSERT INTO utilisateur (nom, email, mot_de_passe) VALUES (?, ?, ?)");
            $requete->execute([$nom, $email, $motdepasseHash]);

            $_SESSION["message"] = "Compte cree. Vous pouvez maintenant vous connecter.";
            header("Location: login.php");
            exit;
        } catch (PDOException $e) {
            $erreur = "Cet email existe deja.";
        }
    }
}

require_once "header.php";
?>
<section class="auth-card">
    <h1>Creer un compte</h1>
    <p>Inscrivez-vous pour commander vos produits New-Tech.</p>

    <?php if ($erreur !== ""): ?>
        <div class="erreur"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <form method="POST" class="form">
        <label>Nom complet</label>
        <input type="text" name="nom" placeholder="Votre nom" required>

        <label>Email</label>
        <input type="email" name="email" placeholder="exemple@mail.com" required>

        <label>Mot de passe</label>
        <input type="password" name="motdepasse" placeholder="Votre mot de passe" required>

        <button type="submit">S'inscrire</button>
    </form>

    <p class="lien-form">Deja un compte ? <a href="login.php">Se connecter</a></p>
</section>
<?php require_once "footer.php"; ?>
