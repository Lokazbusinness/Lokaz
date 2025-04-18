<?php
$language = $_GET['lang'] ?? 'fr';

$translations = [
    'fr' => [
        'title' => 'Loue <span class="highlight-red">ce que tu veux</span> <span class="highlight-green">quand tu veux</span>',
        'subtitle' => 'La plus grande plateforme de location d’objets autour de toi.',
        'articles_title' => 'Les articles du moment',
        'why_title' => 'Pourquoi utiliser Lokaz ?',
        'reason_1' => 'Facilité d’accès',
        'reason_1_desc' => 'Accédez aux objets dont vous avez besoin, quand vous en avez besoin, à des prix abordables. La location est la nouvelle manière d’économiser En Afrique!',
        'reason_2' => 'Proximité',
        'reason_2_desc' => 'Lokaz vous permet de trouver des objets de location près de chez vous, facilitant l\'accès à des produits pour tous.',
        'reason_3' => 'Soutien aux communautés locales',
        'reason_3_desc' => 'Notre plateforme favorise l\'économie circulaire et aide à soutenir les petites entreprises locales en Afrique.',
        'reason_4' => 'Transactions sécurisées',
        'reason_4_desc' => 'Nous offrons un système de paiement sécurisé, pour des échanges sûrs entre loueurs et locataires.',
        'services_title' => 'Louez ou proposez des services',
        'services_desc' => 'Sur Lokaz, vous pouvez non seulement louer des objets, mais aussi proposer vos services ou rechercher des prestataires pour tous vos besoins. Que vous soyez un professionnel ou un particulier, Lokaz vous offre une plateforme de mise en relation simple et sécurisée.',
        'final_message' => 'Avec Lokaz Louer malin, partager utile.',
        'signup' => 'Inscription',
        'login' => 'Connexion',
        'admin' => 'Espace Administrateur',
    ],
    'en' => [
        'title' => 'Rent <span class="highlight-red">what you want</span> <span class="highlight-green">when you want</span>',
        'subtitle' => 'The largest item rental platform near you.',
        'articles_title' => 'Trending items',
        'why_title' => 'Why use Lokaz?',
        'reason_1' => 'Easy Access',
        'reason_1_desc' => 'Get access to the items you need, when you need them, at affordable prices. Renting is the new way to save in Africa!',
        'reason_2' => 'Proximity',
        'reason_2_desc' => 'Lokaz helps you find rental items near you, making it easier for everyone to access products.',
        'reason_3' => 'Support for Local Communities',
        'reason_3_desc' => 'Our platform promotes the circular economy and helps support small local businesses in Africa.',
        'reason_4' => 'Secure Transactions',
        'reason_4_desc' => 'We offer a secure payment system, ensuring safe exchanges between renters and users.',
        'services_title' => 'Rent or offer services',
        'services_desc' => 'On Lokaz, you can not only rent items, but also offer your services or find providers for all your needs. Whether you are a professional or an individual, Lokaz offers you a simple and secure platform for connections.',
        'final_message' => 'With Lokaz, Rent Smart, Share Useful.',
        'signup' => 'Sign Up',
        'login' => 'Login',
        'admin' => 'Admin Area',
    ],
];
$t = $translations[$language];
try {
    $conn = new PDO("mysql:host=fdb1030.awardspace.net;port=3306;dbname=4621383_lokaz;charset=utf8", "4621383_lokaz", "200625YAs");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("SELECT titre, image_path, prix, description FROM annonces ORDER BY id DESC LIMIT 6");
    $stmt->execute();

    $annonces = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lokaz - Accueil</title>
    <style> 
/* Styles généraux */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}
body {
    font-family: 'Segoe UI', sans-serif;
    background-color: #ffffff;
}

header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 30px;
    background: #ffffff;
    border-bottom: 1px solid #5c1a0c; /* Bordeaux brun */
}
.logo {
    font-size: 24px;
    font-weight: bold;
    color: #5c1a0c; /* Bordeaux brun */
}
.lang-buttons a {
    margin-left: 10px;
    text-decoration: none;
    color: #ffffff;
    padding: 8px 15px;
    background: #5c1a0c; /* Bordeaux brun */
    border-radius: 20px;
}

.hero {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 60px 20px 40px;
    background: #ffffff;
}
.hero h1 {
    font-size: 38px;
    line-height: 1.4;
    color: #5c1a0c; /* Bordeaux brun */
}
.hero p {
    margin-top: 15px;
    font-size: 18px;
    color: #5c1a0c; /* Bordeaux brun */
}

.annonces-section {
    padding: 40px 20px;
}
.annonces-title {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 20px;
    color: #5c1a0c; /* Bordeaux brun */
}

.annonces-slider {
    display: flex;
    gap: 20px;
    overflow: hidden;
    padding-bottom: 10px;
    width: 100%;
    animation: scrollInfinite 20s linear infinite;
}
.annonces-slider::-webkit-scrollbar {
    display: none;
}

.annonce-card {
    min-width: 250px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 12px rgba(92, 26, 12, 0.1); /* Bordeaux brun transparent */
    padding: 15px;
    flex-shrink: 0;
    scroll-snap-align: start;
    transition: transform 0.3s;
}
.annonce-card:hover {
    transform: scale(1.05);
}
.annonce-card img {
    width: 100%;
    height: 160px;
    object-fit: cover;
    border-radius: 10px;
}
.annonce-card h3 {
    font-size: 18px;
    margin: 10px 0 5px;
    color: #5c1a0c; /* Bordeaux brun */
}
.annonce-card .prix {
    color: #5c1a0c; /* Bordeaux brun */
    font-weight: bold;
}
.annonce-card .distance {
    font-size: 14px;
    color: #5c1a0c; /* Bordeaux brun */
}

@media (min-width: 768px) {
    .hero h1 { font-size: 48px; }
    .annonces-title { font-size: 28px; }
}

/* Section de raison d'utilisation de Lokaz */
.why-use {
    padding: 60px 20px;
    background: #ffffff;
    text-align: center;
}
.why-use h2 {
    font-size: 28px;
    margin-bottom: 40px;
    color: #5c1a0c; /* Bordeaux brun */
}

/* Slider pour les raisons d'utilisation */
.why-slider {
    display: flex;
    gap: 30px;
    justify-content: center;
    overflow: hidden;
    position: relative;
}

.why-card {
    background-color: #ffffff;
    border-radius: 15px;
    padding: 25px;
    width: 250px;
    box-shadow: 0 4px 10px rgba(92, 26, 12, 0.1); /* Bordeaux brun transparent */
    transform: scale(0.9);
    opacity: 0;
    animation: slideIn 12s infinite;
    animation-delay: 3s;
}

.why-card h3 {
    font-size: 20px;
    color: #5c1a0c; /* Bordeaux brun */
    margin-bottom: 10px;
}
.why-card p {
    font-size: 16px;
    color: #5c1a0c; /* Bordeaux brun */
}

@keyframes slideIn {
    0% {
        transform: scale(0.9);
        opacity: 0;
    }
    20% {
        transform: scale(1);
        opacity: 1;
    }
    50% {
        transform: scale(1);
        opacity: 1;
    }
    80% {
        transform: scale(0.9);
        opacity: 0;
    }
    100% {
        transform: scale(0.9);
        opacity: 0;
    }
}

@keyframes scrollInfinite {
    0% {
        transform: translateX(0);
    }
    100% {
        transform: translateX(-100%);
    }
}

.buttons {
    text-align: center;
    margin-top: 40px;
}
.buttons a {
    padding: 10px 20px;
    background-color: #5c1a0c; /* Bordeaux brun */
    color: #ffffff;
    text-decoration: none;
    border-radius: 5px;
    margin: 10px;
}

/* Section Services */
.services-section {
    padding: 40px 20px;
    background: #f4e6e6;
    color: #5c1a0c; /* Bordeaux brun */
    text-align: center;
    margin-top: 40px;
    border-radius: 1px;
    box-shadow: 0 4px 12px rgba(92, 26, 12, 0.1); /* Bordeaux brun transparent */
}

.services-section h2 {
    font-size: 28px;
    margin-bottom: 20px;
}
.services-section p {
    font-size: 16px;
    margin-top: 10px;
}
.services-section ul {
    list-style: none;
    margin-top: 20px;
    padding-left: 0;
}
.services-section ul li {
    font-size: 16px;
    margin: 5px 0;
    color: #5c1a0c; /* Bordeaux brun */
}

/* Message final */
.final-message {
    padding: 60px 20px;
    text-align: center;
    background: #ffffff;
    color: #5c1a0c; /* Bordeaux brun */
}
   /* Style du bouton Back/Retour */
   .btn-verifier-identite {
    display: inline-block;
    background-color:rgb(29, 10, 16); /* Gris moyen pour le fond du bouton */
    color: white; /* Texte en blanc */
    padding: 12px 24px; /* Espacement interne pour un bouton bien dimensionné */
    font-size: 16px;
    border: none;
    border-radius: 6px; /* Bords arrondis */
    text-decoration: none; /* Supprime le souligné du lien */
    text-align: center;
    transition: background-color 0.3s, transform 0.2s;
}

.btn-verifier-identite:hover {
    background-color:rgb(160, 100, 118); /* Gris plus foncé lors du survol */
    transform: scale(1.05); /* Légère augmentation de la taille au survol pour l'effet de "clic" */
}

.btn-verifier-identite:active {
    background-color: #6e6e6e; /* Gris encore plus foncé lors du clic */
    transform: scale(1); /* Normalise la taille du bouton lors du clic */
}
.language-switch {
  display: flex;
  gap: 10px;
}

.lang-btn {
  padding: 8px 20px;
  border-radius: 40px;
  border: 2px solid #5b1a18; /* bordeaux brun */
  background-color: white;
  color: #5b1a18;
  font-weight: bold;
  text-decoration: none;
  transition: all 0.3s ease;
}

.lang-btn.active {
  background-color: #5b1a18; /* fond bordeaux brun */
  color: white;
  border: none;
}


</style>



</head>
<body>

<header>
    <div class="logo">Lokaz</div>

    
    <a href="admin_connexion.php" class="btn-verifier-identite">Espace Administrateur</a>

    <div class="language-switch">
  <a href="?lang=fr" class="lang-btn <?= ($_GET['lang'] ?? 'fr') === 'fr' ? 'active' : '' ?>">FR Français</a>
  <a href="?lang=en" class="lang-btn <?= ($_GET['lang'] ?? '') === 'en' ? 'active' : '' ?>">GB English</a>
</div>


</header>
<section class="hero">
    <h1><?= $t['title']; ?></h1>
    <p><?= $t['subtitle']; ?></p>
</section>

<section class="buttons">
    <a href="choix_inscription.php"><?= $t['signup']; ?></a>
    <a href="connexion.php"><?= $t['login']; ?></a>
</section>

<section class="annonces-section">
    <h2 class="annonces-title"><?= $t['articles_title']; ?></h2>
    <div class="annonces-slider">
        <?php foreach ($annonces as $annonce): ?>
            <div class="annonce-card">
                <img src="<?= htmlspecialchars($annonce['image_path']) ?>" alt="<?= htmlspecialchars($annonce['titre']) ?>">
                <h3><?= htmlspecialchars($annonce['titre']) ?></h3>
        
                <div class="distance"><?= htmlspecialchars($annonce['description']) ?></div>
            </div>
        <?php endforeach; ?>
    </div>
</section>


<section class="why-use">
    <h2><?= $t['why_title']; ?></h2>
    <div class="why-slider">
        <div class="why-card">
            <h3><?= $t['reason_1']; ?></h3>
            <p><?= $t['reason_1_desc']; ?></p>
        </div>
        <div class="why-card">
            <h3><?= $t['reason_2']; ?></h3>
            <p><?= $t['reason_2_desc']; ?></p>
        </div>
        <div class="why-card">
            <h3><?= $t['reason_3']; ?></h3>
            <p><?= $t['reason_3_desc']; ?></p>
        </div>
        <div class="why-card">
            <h3><?= $t['reason_4']; ?></h3>
            <p><?= $t['reason_4_desc']; ?></p>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const slider = document.getElementById('slider');
    const items = Array.from(slider.children);
    const totalItems = items.length;
    let currentIndex = 0;

    // Fonction pour défiler les annonces
    function moveSlider() {
        // On fait défiler vers la prochaine annonce
        currentIndex = (currentIndex + 1) % totalItems; // On revient au début une fois la fin atteinte
        slider.scrollLeft = items[currentIndex].offsetLeft;
    }

    // Défilement automatique toutes les 3 secondes
    setInterval(moveSlider, 3000); // 3000 ms = 3 secondes
});
</script>

<section class="services-section">
    <div class="services-message">
        <h2><?= $t['services_title']; ?></h2>
        <p><?= $t['services_desc']; ?></p>
    </div>
</section>

<section class="final-message">
    <h2><?= $t['final_message']; ?></h2>
</section>
