<?php
ob_start(); // Active la mise en mémoire tampon pour éviter les erreurs d'affichage
session_start();

// Changement de langue
if (isset($_GET['setlang']) && in_array($_GET['setlang'], ['fr', 'en'])) {
    $_SESSION['lang'] = $_GET['setlang'];
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$locale = $_SESSION['lang'] ?? 'fr';

// Traductions
$lang = [
    "fr" => [
        "privacy_title" => "Politique de confidentialité",
        "intro" => "Chez Lokaz, la protection de vos données personnelles est une priorité. Nous nous engageons à garantir leur confidentialité et leur sécurité.",
        "sections" => [
            "Collecte des données" => "Nous collectons uniquement les données nécessaires à la création de votre compte, à la mise en relation et à la gestion des locations.",
            "Utilisation des données" => "Vos données sont utilisées pour personnaliser votre expérience, vous mettre en relation avec d'autres utilisateurs et améliorer nos services.",
            "Stockage des données" => "Les données sont stockées de manière sécurisée sur des serveurs protégés.",
            "Partage des données" => "Nous ne partageons jamais vos données personnelles avec des tiers sans votre consentement, sauf obligation légale.",
            "Sécurité" => "Nous utilisons des technologies modernes pour protéger vos informations contre tout accès non autorisé.",
            "Vos droits" => "Vous pouvez à tout moment consulter, modifier ou supprimer vos données en cliquant sur le bouton mon profil dans votre espace ou en nous contactant.",
        ],
        "conclusion" => "En utilisant Lokaz, vous acceptez notre politique de confidentialité.",
    ],
    "en" => [
        "privacy_title" => "Privacy Policy",
        "intro" => "At Lokaz, protecting your personal data is a priority. We are committed to ensuring its confidentiality and security.",
        "sections" => [
            "Data Collection" => "We only collect data necessary to create your account, connect users, and manage rentals.",
            "Data Usage" => "Your data is used to personalize your experience, connect you with other users, and improve our services.",
            "Data Storage" => "Data is securely stored on protected servers.",
            "Data Sharing" => "We never share your personal data with third parties without your consent, unless required by law.",
            "Security" => "We use modern technologies to protect your information from unauthorized access.",
            "Your Rights" => "You may consult, modify, or delete your data at any time by clicking on the my profile button or by contacting us.",
        ],
        "conclusion" => "By using Lokaz, you agree to our privacy policy.",
    ]
];

$text = $lang[$locale];

include 'header.php';
?>

<!-- Boutons de langue -->
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
    padding: 15px 25px;
    gap: 10px;
}

.lang-switch a {
    text-decoration: none;
    padding: 8px 15px;
    border-radius: 30px;
    font-weight: bold;
    border: 2px solid rgb(83, 25, 48); /* Bordeaux brun */
    color: rgb(83, 25, 48); /* Bordeaux brun */
    background-color: white;
    transition: background-color 0.3s, color 0.3s;
}

.lang-switch a.active {
    background-color: rgb(133, 42, 70); /* Bordeaux foncé */
    color: white;
}

.lang-switch a:hover {
    opacity: 0.8;
}

.content {
    max-width: 900px;
    margin: 30px auto;
    padding: 30px;
    background-color: white;
    border-radius: 20px;
    box-shadow: 0 4px 15px rgba(83, 25, 48, 0.1); /* Ombre bordeaux douce */
}

.content h1 {
    color: rgb(160, 59, 90); /* Bordeaux clair */
    margin-bottom: 20px;
}

.content h2 {
    color: rgb(114, 33, 64); /* Bordeaux foncé */
    margin-top: 20px;
}

.content p {
    font-size: 1.1em;
    margin-bottom: 15px;
    color: rgb(133, 55, 85); /* Bordeaux doux */
}

/* Style du bouton Back/Retour */
.btn-verifier-identite {
    display: inline-block;
    background-color: rgb(83, 11, 41); /* Bordeaux très foncé */
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
    background-color: rgb(66, 9, 33); /* Plus foncé au survol */
    transform: scale(1.05);
}

.btn-verifier-identite:active {
    background-color: rgb(48, 7, 25); /* Encore plus foncé au clic */
    transform: scale(1);
}
</style>


<!-- Contenu -->
<div class="content">
    <h1><?= $text['privacy_title'] ?></h1>
    <p><?= $text['intro'] ?></p>

    <?php foreach ($text['sections'] as $title => $paragraph): ?>
        <h2><?= $title ?></h2>
        <p><?= $paragraph ?></p>
    <?php endforeach; ?>

    <p><strong><?= $text['conclusion'] ?></strong></p>
</div>

<a href="dashboard.php" class="btn-verifier-identite">Back/Retour</a>
