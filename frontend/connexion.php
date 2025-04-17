<?php
require_once '../backend/db.php';


// Détection de la langue (français par défaut)
$language = isset($_GET['lang']) ? $_GET['lang'] : 'fr';

// Traductions
$translations = [
    'fr' => [
        'title' => 'Connexion',
        'email_placeholder' => 'Adresse e-mail',
        'password_placeholder' => 'Mot de passe',
        'login_button' => 'Se connecter',
        'reset_password' => 'Réinitialiser le mot de passe',
        'fill_all_fields' => "Veuillez remplir tous les champs.",
        'toggle_password' => "Afficher les mots de passe",
        'incorrect_login' => "Email ou mot de passe incorrect."
    ],
    'en' => [
        'title' => 'Login',
        'email_placeholder' => 'Email Address',
        'password_placeholder' => 'Password',
        'login_button' => 'Log in',
        'reset_password' => 'Reset Password',
        'fill_all_fields' => "Please fill in all fields.",
        'toggle_password' => "Show Passwords",
        'incorrect_login' => "Incorrect email or password."
    ],
];

// Sélection de la langue
$t = $translations[$language];
?>

<!DOCTYPE html>
<html lang="<?= htmlspecialchars($language) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($t['title']) ?></title>
    <style>   
/* Couleurs principales corrigées en bordeaux foncé / blanc */
:root {
    --bordeaux-principal: #4B0000; /* Bordeaux foncé */
    --bordeaux-hover: #330000;     /* Bordeaux encore plus foncé pour hover */
    --fond-rosé: #ffffff;          /* Fond blanc */
    --texte-principal: #4B0000;    /* Texte en bordeaux foncé */
}

/* Style global */
body {
    font-family: Arial, sans-serif;
    background-color: var(--fond-rosé);
    color: var(--texte-principal);
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: center;
    height: 100vh;
}

h2 {
    text-align: center;
    color: var(--bordeaux-principal);
    margin-top: 20px;
}

form {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(75, 0, 0, 0.15); /* bordeaux foncé transparent */
    width: 300px;
    margin-top: 20px;
}

input[type="email"],
input[type="password"] {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    border: 1px solid var(--bordeaux-principal);
    border-radius: 6px;
    font-size: 16px;
    background-color: #ffffff; /* blanc pur */
}

button {
    width: 100%;
    padding: 12px;
    background-color: var(--bordeaux-principal);
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s;
}

button:hover {
    background-color: var(--bordeaux-hover);
}

a {
    color: var(--bordeaux-principal);
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

.btn-verifier-identite {
    display: inline-block;
    background-color: var(--bordeaux-principal);
    color: white;
    padding: 12px 24px;
    font-size: 16px;
    border: none;
    border-radius: 6px;
    text-decoration: none;
    text-align: center;
    transition: background-color 0.3s, transform 0.2s;
}

.btn-verifier-identite:hover {
    background-color: var(--bordeaux-hover);
    transform: scale(1.05);
}

.btn-verifier-identite:active {
    background-color: #1f0000; /* bordeaux ultra foncé pour clic */
    transform: scale(1);
}
</style>


</head>
<body>

    <h2><?= htmlspecialchars($t['title']) ?></h2>

    <!-- Formulaire de connexion -->
    <form action="../backend/traitement_connexion.php?lang=<?= htmlspecialchars($language) ?>" method="POST">
        <input type="email" name="email" placeholder="<?= htmlspecialchars($t['email_placeholder']) ?>" required>
        <input type="password" name="mot_de_passe" placeholder="<?= htmlspecialchars($t['password_placeholder']) ?>" required>

        <input type="checkbox" id="togglePassword"> <span><?= $t['toggle_password'] ?></span><br>
        
        <button type="submit"><?= htmlspecialchars($t['login_button']) ?></button>
    </form>

    <!-- Affichage des messages d'erreur -->
    <?php if (isset($_SESSION['login_error'])): ?>
        <p style="color: red;"><?= htmlspecialchars($_SESSION['login_error']); ?></p>
        <?php unset($_SESSION['login_error']); ?>
    <?php endif; ?>

    <!-- Bouton de réinitialisation du mot de passe -->
    <?php if (isset($_SESSION['show_reset_button']) && $_SESSION['show_reset_button']): ?>
        <a href="reset_password.php?lang=<?= htmlspecialchars($language) ?>" style="color: blue; text-decoration: underline;">
            <?= htmlspecialchars($t['reset_password']); ?>
        </a>
        <?php unset($_SESSION['show_reset_button']); ?>
    <?php endif; ?>

    <p>
        <a href="?lang=fr" <?= $language == 'fr' ? 'style="font-weight:bold;"' : '' ?>>Français</a> |
        <a href="?lang=en" <?= $language == 'en' ? 'style="font-weight:bold;"' : '' ?>>English</a>
    </p>

</body>
</form>
   <script>
        document.getElementById("togglePassword").addEventListener("change", function() {
            let passwordField = document.getElementById("mot_de_passe");
            let confirmPasswordField = document.getElementById("confirmer_mot_de_passe");
            let type = this.checked ? "text" : "password";
            passwordField.type = type;
            confirmPasswordField.type = type;
        });
 </script>

<a href="Homepage.php" class="btn-verifier-identite">Back/Retour</a>
</html>

