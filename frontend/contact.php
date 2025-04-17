<?php 
ob_start();
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
        "contact_title" => "Contactez-nous",
        "intro" => "Vous avez une question ou un commentaire ? N'hésitez pas à nous contacter.",
        "email" => "Vous pouvez nous envoyer un email à l'adresse suivante :",
        "follow" => "Suivez-nous sur nos réseaux :",
    ],
    "en" => [
        "contact_title" => "Contact Us",
        "intro" => "Have a question or comment? Feel free to contact us.",
        "email" => "You can email us at the following address:",
        "follow" => "Follow us on social media:",
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
    background-color: #ffffff; /* Fond blanc */
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
    border: 2px solid rgb(99, 25, 50); /* Bordure bordeaux foncé */
    color: rgb(99, 25, 50); /* Texte bordeaux */
    background-color: white;
}

.lang-switch a.active {
    background-color: rgb(99, 25, 50); /* Fond bordeaux foncé */
    color: white;
}

.content {
    max-width: 900px;
    margin: 30px auto;
    padding: 30px;
    background-color: white;
    border-radius: 20px;
    box-shadow: 0 4px 15px rgba(99, 25, 50, 0.1); /* Ombre légère bordeaux */
    text-align: center;
}

.content h1 {
    color: rgb(99, 25, 50); /* Titre bordeaux foncé */
    margin-bottom: 20px;
}

.content p {
    font-size: 1.1em;
    margin-bottom: 15px;
    color: rgb(133, 62, 89); /* Texte bordeaux plus doux */
}

.social-links {
    margin-top: 25px;
    display: flex;
    justify-content: center;
    gap: 20px;
}

.social-links a {
    text-decoration: none;
    font-size: 1.2em;
    color: rgb(99, 25, 50); /* Liens bordeaux */
    transition: color 0.3s;
}

.social-links a:hover {
    color: rgb(60, 15, 30); /* Plus foncé au survol */
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
    background-color: rgb(83, 28, 43); /* Bordeaux plus foncé au survol */
    transform: scale(1.05);
}

.btn-verifier-identite:active {
    background-color: rgb(60, 20, 30); /* Encore plus foncé lors du clic */
    transform: scale(1);
}
</style>


<!-- Contenu -->
<div class="content">
    <h1><?= $text['contact_title'] ?></h1>
    <p><?= $text['intro'] ?></p>
    <p><strong><?= $text['email'] ?></strong><br><a href="mailto:lokaz.business@gmail.com">lokaz.business@gmail.com</a></p>

    <p><strong><?= $text['follow'] ?></strong></p>
    <div class="social-links">
    <a href="https://www.instagram.com/Lokaz.Business" target="_blank">📸 Instagram</a>
    <a href="https://www.facebook.com/LokazBusiness Afrique" target="_blank">📘 Facebook</a>
    <a href="https://www.twitter.com/@LokazBusiness" target="_blank">🐦 Twitter</a>
    <a href="https://www.tiktok.com/@lokaz.business" target="_blank">🎵 TikTok</a>
</div>
</div>
<a href="dashboard.php" class="btn-verifier-identite">Back/Retour</a>