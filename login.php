<?php
session_start();
require_once "connexion.php";

if (isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

$erreur = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $motdepasse = $_POST["motdepasse"];

    $requete = $pdo->prepare("SELECT * FROM utilisateur WHERE email = ?");
    $requete->execute([$email]);
    $utilisateur = $requete->fetch();

    if ($utilisateur && password_verify($motdepasse, $utilisateur["mot_de_passe"])) {
        $_SESSION["user_id"] = $utilisateur["id_utilisateur"];
        $_SESSION["user_nom"] = $utilisateur["nom"];
        header("Location: index.php");
        exit;
    } else {
        $erreur = "Email ou mot de passe incorrect.";
    }
}

require_once "header.php";
?>
<section class="auth-card">
    <h1>Connexion</h1>
    <p>Connectez-vous pour acceder a la boutique.</p>

    <?php if ($erreur !== ""): ?>
        <div class="erreur"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <form method="POST" class="form">
        <label>Email</label>
        <input type="email" name="email" placeholder="exemple@mail.com" required>

        <label>Mot de passe</label>
        <input type="password" name="motdepasse" placeholder="Votre mot de passe" required>

        <button type="submit">Se connecter</button>
    </form>

    <p class="lien-form">Pas encore de compte ? <a href="register.php">S'inscrire</a></p>
</section>
<?php require_once "footer.php"; ?>
