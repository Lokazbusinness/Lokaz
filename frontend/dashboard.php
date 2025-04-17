<?php
session_start();
require_once '../backend/db.php';
include 'header.php';

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 🔹 Récupérer les informations de l'utilisateur
$stmt = $pdo->prepare("SELECT nom_utilisateur, entreprise_nom, type FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("❌ Utilisateur non trouvé.");
}

// 🔹 Définir les variables
$nom_utilisateur = $user['nom_utilisateur'] ?? 'Utilisateur';
$nom_entreprise = $user['entreprise_nom'] ?? '';
$type_utilisateur = $user['type'] ?? 'particulier';

// 🔹 Gestion de la langue via SESSION
if (isset($_GET['lang'])) {
    $_SESSION['langue'] = $_GET['lang'];
}
$langue = $_SESSION['langue'] ?? 'fr';

// 🔹 Traductions FR/EN
$traductions = [
    'fr' => [
        'welcome' => 'Bienvenue dans ton espace',
        'transactions' => 'Mes transactions',
        'wallet' => 'Porte-monnaie',
        'ads' => 'Mes annonces',
        'profile' => 'Mon profil',
        'reviews' => 'Mes avis',
        'back_to_ads' => 'Retour aux annonces',
        'change_lang' => 'Changer de langue :',
        'verify_identity' => 'Vérifier mon identité',  // Ajout de la traduction
        'connaissances' => 'Nous connaitre',
        'subscribe_subscription' => 'Souscrire à l\'abonnement'  // Ajout de la traduction
    ],
    'en' => [
        'welcome' => 'Welcome to your space',
        'transactions' => 'My transactions',
        'wallet' => 'Wallet',
        'ads' => 'My ads',
        'profile' => 'My profile',
        'reviews' => 'My reviews',
        'back_to_ads' => 'Back to ads',
        'change_lang' => 'Change language:',
        'verify_identity' => 'Verify my identity',  // Ajout de la traduction
        'connaissances' =>'to know about us',
        'subscribe_subscription' => 'Subscribe to subscription'  // Ajout de la traduction
    ]
];

// Sélection de la langue
$t = $traductions[$langue];
?>

<!DOCTYPE html>
<html lang="<?= htmlspecialchars($langue) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style> 
/* Grille deux colonnes */
.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-top: 20px;
}

/* Style général des boutons */
.dashboard-item,
.btn-verifier-identite,
.btn-abonnement,
.dashboard-item-back {
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #5a2a27; /* Bordeaux brun */
    color: #ffffff;
    padding: 14px;
    border-radius: 8px;
    font-size: 16px;
    text-decoration: none;
    text-align: center;
    border: none;
    transition: background-color 0.3s ease, transform 0.2s ease;
    box-sizing: border-box;
}

/* Icônes dans les boutons */
.dashboard-item span {
    font-size: 28px;
    margin-right: 8px;
}

/* Hover : bordeaux plus clair */
.dashboard-item:hover,
.btn-verifier-identite:hover,
.btn-abonnement:hover,
.dashboard-item-back:hover {
    background-color: #804040; /* Bordeaux brun plus clair */
    transform: scale(1.05);
    color: #ffffff;
}

/* Bouton retour - plus petit mais dans le même style */
.dashboard-item-back {
    font-size: 14px;
    padding: 8px 16px;
    width: fit-content;
    margin-top: 15px;
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

<!-- Boutons pour changer la langue -->
<div class="language-switch">
  <a href="?lang=fr" class="lang-btn <?= ($_GET['lang'] ?? 'fr') === 'fr' ? 'active' : '' ?>">FR Français</a>
  <a href="?lang=en" class="lang-btn <?= ($_GET['lang'] ?? '') === 'en' ? 'active' : '' ?>">GB English</a>
</div>

<main class="dashboard-container">
    <h2>
        <?= $t['welcome'] ?>, 
        <?= htmlspecialchars($type_utilisateur === 'professionnel' && !empty($nom_entreprise) ? $nom_entreprise : $nom_utilisateur) ?>
    </h2>

    <div class="dashboard-grid">
    <a href="mesannonces.php" class="dashboard-item"><?= $t['ads'] ?></a>
    <a href="monprofil.php" class="dashboard-item"><?= $t['profile'] ?></a>
    <a href="avis.php" class="dashboard-item"><?= $t['reviews'] ?></a>
    <a href="verifier_identite.php" class="dashboard-item btn-verifier-identite"><?= $t['verify_identity'] ?></a>
    <a href="abonnements.php" class="dashboard-item btn-abonnement"><?= $t['subscribe_subscription'] ?></a>
    <a href="footer.php" class="dashboard-item"><?= $t['connaissances'] ?></a>
</div>

<a href="annonces.php" class="dashboard-item-back"><?= $t['back_to_ads'] ?></a>


</html>
