<?php
ob_start(); // Active la mise en mémoire tampon pour éviter les erreurs d'affichage
session_start();

// Gestion de la langue
if (isset($_GET['setlang']) && in_array($_GET['setlang'], ['fr', 'en'])) {
    $_SESSION['lang'] = $_GET['setlang'];
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$locale = $_SESSION['lang'] ?? 'fr';

// Traductions
$lang = [
    "fr" => [
        "terms_title" => "Conditions d'utilisation",
        "terms_intro" => "Bienvenue sur Lokaz ! En utilisant notre site, vous acceptez les règles suivantes :",
        "terms" => [
            "L'utilisateur s'engage à fournir des informations exactes lors de l'inscription.",
            "Les objets mis en location doivent être en bon état de fonctionnement.",
            "Lokaz n’est pas responsable des pertes, vols ou dommages pendant une location.",
            "Les paiements doivent être effectués uniquement via les méthodes proposées par la plateforme.",
            "Toute tentative de fraude ou de contournement du système peut entraîner une suspension de compte.",
            "En utilisant Lokaz, vous acceptez de respecter les lois en vigueur dans votre pays."
        ],
        "terms_footer" => "Merci de faire confiance à Lokaz pour vos besoins de location en Afrique.",
    ],
    "en" => [
        "terms_title" => "Terms of Use",
        "terms_intro" => "Welcome to Lokaz! By using our website, you agree to the following rules:",
        "terms" => [
            "Users must provide accurate information during registration.",
            "Items listed for rent must be in good working condition.",
            "Lokaz is not responsible for loss, theft, or damage during a rental.",
            "Payments must be made only through the platform's accepted methods.",
            "Any attempt to cheat or bypass the system may result in account suspension.",
            "By using Lokaz, you agree to comply with the laws in your country."
        ],
        "terms_footer" => "Thank you for trusting Lokaz for your rental needs in Africa.",
    ]
];

$text = $lang[$locale];

// Inclure l'en-tête
include 'header.php';
?>

<!-- Boutons pour changer la langue -->
<div class="lang-switch">
    <a href="?setlang=fr" class="<?= $locale === 'fr' ? 'active' : '' ?>">🇫🇷 Français</a>
    <a href="?setlang=en" class="<?= $locale === 'en' ? 'active' : '' ?>">🇬🇧 English</a>
</div>
<!-- Styles -->
<style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background-color: #f5f5f5; /* Blanc cassé */
        margin: 0;
        padding: 0;
    }

    .lang-switch {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 15px 25px;
    }

    .lang-switch a {
        text-decoration: none;
        padding: 8px 15px;
        border-radius: 30px;
        font-weight: bold;
        border: 2px solid rgb(99, 25, 50); /* Bordeaux brun */
        color: rgb(99, 25, 50);
        background-color: white;
        transition: 0.3s;
    }

    .lang-switch a.active {
        background-color: rgb(99, 25, 50); /* Bordeaux brun */
        color: white;
    }

    .lang-switch a:hover {
        opacity: 0.9;
    }

    .content {
        max-width: 900px;
        margin: 30px auto;
        padding: 30px;
        background-color: white;
        border-radius: 20px;
        box-shadow: 0 4px 15px rgba(99, 25, 50, 0.1); /* Ombre légère bordeaux */
    }

    .content h1 {
        color: rgb(109, 35, 55); /* Bordeaux foncé */
        margin-bottom: 20px;
    }

    .content ul {
        padding-left: 25px;
    }

    .content li {
        margin-bottom: 10px;
        color: rgb(138, 48, 70); /* Bordeaux clair */
    }

    .content p {
        font-size: 1.1em;
        margin-bottom: 20px;
        color: rgb(161, 73, 95); /* Bordeaux doux */
    }

    /* Style du bouton Back/Retour */
    .btn-verifier-identite {
        display: inline-block;
        background-color: rgb(107, 37, 53); /* Bordeaux brun */
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
        background-color: rgb(83, 28, 43); /* Bordeaux foncé */
        transform: scale(1.05);
    }

    .btn-verifier-identite:active {
        background-color: rgb(60, 20, 30); /* Bordeaux très foncé */
        transform: scale(1);
    }
</style>


<!-- Contenu -->
<div class="content">
    <h1><?= $text['terms_title'] ?></h1>
    <p><?= $text['terms_intro'] ?></p>
    <ul>
        <?php foreach ($text['terms'] as $term): ?>
            <li><?= $term ?></li>
        <?php endforeach; ?>
    </ul>
    <p><strong><?= $text['terms_footer'] ?></strong></p>
</div>

<a href="dashboard.php" class="btn-verifier-identite">Back/Retour</a>


