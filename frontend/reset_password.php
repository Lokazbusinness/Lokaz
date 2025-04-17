<?php 
require_once __DIR__ . "/../backend/db.php";

// Détecter la langue actuelle
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$lang = $_SESSION['lang'] ?? 'fr';

// Textes multilingues
$texts = [
    'fr' => [
        'title' => 'Réinitialiser le mot de passe',
        'email' => 'Adresse e-mail',
        'mot_secret' => 'Mot secret',
        'new_password' => 'Nouveau mot de passe',
        'confirm_password' => 'Confirmer le mot de passe',
        'reset' => 'Réinitialiser',
        'mismatch' => 'Les mots de passe ne correspondent pas.',
        'show_password' => 'Afficher le mot de passe',
        'toggle_language' => 'Switch to English',
        'error' => $_SESSION['reset_errors'] ?? '',
    ],
    'en' => [
        'title' => 'Reset Password',
        'email' => 'Email Address',
        'mot_secret' => 'Secret Code',
        'new_password' => 'New Password',
        'confirm_password' => 'Confirm Password',
        'reset' => 'Reset',
        'mismatch' => 'Passwords do not match.',
        'show_password' => 'Show Password',
        'toggle_language' => 'Passer au Français',
        'error' => $_SESSION['reset_errors'] ?? '',
    ]
];

$t = $texts[$lang];
unset($_SESSION['reset_errors']); // Effacer les erreurs après affichage
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $t['title'] ?></title>
    
    <style>
body {
    background-color: #fff;
    color: #4a1410; /* texte principal brun foncé */
    font-family: Arial, sans-serif;
    margin: 0;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
}

a {
    position: absolute;
    top: 20px;
    right: 20px;
    color: #fff;
    font-weight: bold;
    text-decoration: none;
    background-color: #6e1f1f; /* bordeaux brun */
    padding: 8px 12px;
    border-radius: 4px;
    transition: background 0.3s, color 0.3s;
}

a:hover {
    background-color: #4a1410; /* brun plus foncé */
    color: #fff;
}

h2 {
    color: #4a1410; /* brun foncé */
    font-size: 24px;
    margin-bottom: 20px;
}

form {
    background-color: #fff0f0; /* blanc rosé très doux */
    border: 2px solid #6e1f1f;
    padding: 30px;
    width: 100%;
    max-width: 500px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(110, 31, 31, 0.3);
}

label {
    display: block;
    margin-top: 15px;
    font-weight: bold;
    color: #4a1410;
}

input[type="email"],
input[type="text"],
input[type="password"] {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border: 1px solid #6e1f1f;
    border-radius: 5px;
    background-color: #fff;
    color: #4a1410;
    font-size: 14px;
}

input[type="email"]:focus,
input[type="text"]:focus,
input[type="password"]:focus {
    outline: none;
    border-color: #4a1410;
    background-color: #fff6
}
    </style>
    
    <script>
        function checkPasswords() {
            let pass = document.getElementById('password').value;
            let confirmPass = document.getElementById('confirm_password').value;
            let message = document.getElementById('message');
            if (pass !== confirmPass) {
                message.textContent = "<?= $t['mismatch'] ?>";
                message.style.color = "red";
            } else {
                message.textContent = "";
            }
        }
    </script>
</head>
<body>
    <a href="?lang=<?= $lang === 'fr' ? 'en' : 'fr' ?>"><?= $t['toggle_language'] ?></a>

    <h2><?= $t['title'] ?></h2>
    
    <?php if (!empty($t['error'])): ?>
        <p style="color:red;"><?= $t['error'] ?></p>
    <?php endif; ?>

    <form action="/Lokaz/backend/traitement_reset.php" method="POST">
        <label for="email"><?= $t['email'] ?> :</label>
        <input type="email" id="email" name="email" required><br>

        <label for="mot_secret"><?= $t['mot_secret'] ?> :</label>
        <input type="text" id="mot_secret" name="mot_secret" required><br>

        <label for="password"><?= $t['new_password'] ?> :</label>
        <input type="password" id="password" name="password" required><br>

        <label for="confirm_password"><?= $t['confirm_password'] ?> :</label>
        <input type="password" id="confirm_password" name="confirm_password" onkeyup="checkPasswords()" required><br>

        <span id="message"></span><br>

        <button type="submit"><?= $t['reset'] ?></button>
    </form>
</body>
</html>
