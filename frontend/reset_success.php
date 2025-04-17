<?php
$lang = isset($_GET['lang']) && $_GET['lang'] === 'en' ? 'en' : 'fr';

// Traductions
$texts = [
    'fr' => [
        'success' => 'Mot de passe réinitialisé avec succès !',
        'login' => 'Se connecter',
        'change_lang' => 'English'
    ],
    'en' => [
        'success' => 'Password successfully reset!',
        'login' => 'Login',
        'change_lang' => 'Français'
    ]
];

$t = $texts[$lang];

// Définir l'URL pour changer de langue
$new_lang = $lang === 'fr' ? 'en' : 'fr';
$change_lang_url = "?lang=" . $new_lang;
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $t['success'] ?></title>
    <style>
    body {
        background-color: #fff; /* fond blanc */
        color: #4a1410; /* texte brun foncé */
        font-family: Arial, sans-serif;
        height: 100vh;
        margin: 0;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        position: relative;
    }

    h2 {
        font-size: 24px;
        color: #4a1410; /* brun foncé */
        margin-bottom: 30px;
    }

    button {
        background-color: #6e1f1f; /* bordeaux brun */
        color: #fff;
        border: 2px solid #4a1410; /* brun foncé */
        padding: 12px 20px;
        font-size: 16px;
        border-radius: 6px;
        cursor: pointer;
        margin: 10px;
        transition: background-color 0.3s, border-color 0.3s;
    }

    button:hover {
        background-color: #4a1410; /* plus sombre au survol */
        border-color: #4a1410;
    }

    .language-switch {
        display: flex;
        gap: 10px;
    }

    .lang-btn {
        padding: 8px 20px;
        border-radius: 40px;
        border: 2px solid #6e1f1f; /* bordeaux brun */
        background-color: #fff;
        color: #6e1f1f;
        font-weight: bold;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .lang-btn.active {
        background-color: #6e1f1f; /* fond bordeaux brun */
        color: #fff;
        border: none;
    }

    .lang-btn:hover:not(.active) {
        background-color: #f8eaea; /* léger rosé en survol */
        color: #6e1f1f;
    }
</style>

</head>
<body>
    <!-- Bouton pour changer de langue -->
    <a href="<?= $change_lang_url ?>" class="lang-switch">
        <button><?= $t['change_lang'] ?></button>
    </a>

    <h2><?= $t['success'] ?></h2>

    <!-- Bouton Se connecter -->
    <a href="../frontend/connexion.php?lang=<?= $lang ?>">
        <button><?= $t['login'] ?></button>
    </a>
</body>
</html>
