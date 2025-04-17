<?php
session_start();
ob_start(); // Active la mise en mémoire tampon pour éviter les erreurs d'affichage
if (isset($_GET['setlang']) && in_array($_GET['setlang'], ['fr', 'en'])) {
    $_SESSION['lang'] = $_GET['setlang'];
    header("Location: " . $_SERVER['PHP_SELF']); // Recharge la page pour appliquer la langue
    exit();
}

$locale = $_SESSION['lang'] ?? 'fr';

include 'header.php';
$lang = [
    "fr" => [
        "title" => "À propos de Lokaz",
        "intro" => "Lokaz est une plateforme 100 % africaine qui révolutionne la façon dont nous utilisons les objets du quotidien.",
        "mission_title" => "Notre mission",
        "mission_text" => "Créer un écosystème où chacun peut accéder facilement à des objets sans les acheter, tout en générant des revenus grâce à ce qu'il possède déjà.",
        "values_title" => "Nos valeurs",
        "values" => [
            "Partage et entraide",
            "Économie circulaire",
            "Autonomisation locale",
            "Accessibilité pour tous"
        ],
        "why_title" => "Pourquoi Lokaz ?",
        "why_text" => "Parce qu’en Afrique, nous avons des ressources. Lokaz permet de les mettre à profit de manière intelligente, pratique et durable.",
        "slogan" => "Louer malin, partager utile.",
    ],
    "en" => [
        "title" => "About Lokaz",
        "intro" => "Lokaz is a 100% African platform revolutionizing the way we use everyday items.",
        "mission_title" => "Our Mission",
        "mission_text" => "To build an ecosystem where everyone can access what they need without buying, while earning from what they already own.",
        "values_title" => "Our Values",
        "values" => [
            "Sharing and solidarity",
            "Circular economy",
            "Local empowerment",
            "Accessibility for all"
        ],
        "why_title" => "Why Lokaz?",
        "why_text" => "Because in Africa, we have resources. Lokaz helps use them smartly, practically, and sustainably.",
        "slogan" => "Smart renting, meaningful sharing.",
    ]
];

$locale = $_SESSION['lang'] ?? 'fr';
$text = $lang[$locale];
?>

<div class="lang-switch">
    <a href="?setlang=fr" class="<?= $locale === 'fr' ? 'active' : '' ?>">🇫🇷 Français</a>
    <a href="?setlang=en" class="<?= $locale === 'en' ? 'active' : '' ?>">🇬🇧 English</a>
</div>
<style>
    body {
        margin: 0;
        font-family: 'Segoe UI', sans-serif;
        background: #ffffff; /* Blanc */
    }

    .hero {
        background: url('images/afrique-location.jpg') center/cover no-repeat;
        color: white;
        text-align: center;
        padding: 100px 20px;
        position: relative;
    }

    .hero::after {
        content: "";
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: rgba(0, 0, 0, 0.5); /* Ombre noire légère */
        z-index: 0;
    }

    .hero h1 {
        position: relative;
        z-index: 1;
        font-size: 3em;
        font-weight: bold;
        color: white;
    }

    .content {
        max-width: 900px;
        margin: 30px auto;
        padding: 20px;
        background-color: white;
        border-radius: 20px;
        box-shadow: 0 4px 15px rgba(75, 30, 30, 0.1); /* Bordeaux brun léger */
    }

    .content h2 {
        color: #5E2A2A; /* Bordeaux chocolat foncé */
        margin-top: 20px;
    }

    .content p, .content ul {
        color: #4B1E1E; /* Bordeaux brun profond */
    }

    .content ul {
        padding-left: 20px;
    }

    .slogan {
        text-align: center;
        font-size: 1.5em;
        color: #5E2A2A; /* Bordeaux chocolat */
        margin-top: 30px;
        font-style: italic;
        font-weight: bold;
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
        border: 2px solid #4B1E1E; /* Bordeaux brun */
        color: #4B1E1E; /* Bordeaux brun */
        background-color: white;
        transition: background-color 0.3s, color 0.3s;
    }

    .lang-switch a.active {
        background-color: #5E2A2A; /* Bordeaux chocolat */
        color: white;
    }

    .lang-switch a:hover {
        opacity: 0.8;
    }

    /* Style du bouton Back/Retour */
    .btn-verifier-identite {
        display: inline-block;
        background-color: #4B1E1E; /* Bordeaux brun foncé */
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
        background-color: #3A1616; /* Encore plus foncé */
        transform: scale(1.05);
    }

    .btn-verifier-identite:active {
        background-color: #2A0E0E; /* Très sombre */
        transform: scale(1);
    }
</style>


<div class="hero">
    <h1><?= $text['title'] ?></h1>
</div>

<div class="content">
    <p><?= $text['intro'] ?></p>

    <h2><?= $text['mission_title'] ?></h2>
    <p><?= $text['mission_text'] ?></p>

    <h2><?= $text['values_title'] ?></h2>
    <ul>
        <?php foreach ($text['values'] as $value): ?>
            <li><?= $value ?></li>
        <?php endforeach; ?>
    </ul>

    <h2><?= $text['why_title'] ?></h2>
    <p><?= $text['why_text'] ?></p>

    <div class="slogan">“<?= $text['slogan'] ?>”</div>
</div>


<a href="dashboard.php" class="btn-verifier-identite">Back/Retour</a>

