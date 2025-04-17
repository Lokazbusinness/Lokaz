<?php 
session_start();
ob_start();
require '../backend/db.php';
include 'header.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header("Location: connexion.php");
    exit();
}

$utilisateur_id = $_SESSION['utilisateur_id'];

$query = $pdo->prepare("SELECT monnaie FROM users WHERE id = :id");
$query->execute(['id' => $utilisateur_id]);
$user = $query->fetch(PDO::FETCH_ASSOC);
$monnaie_utilisateur = $user['monnaie'] ?? 'XOF';

$taux_conversion = [
    "XOF" => 1, "XAF" => 1, "DZD" => 0.22, "EGP" => 0.050, "MAD" => 0.017,
    "TND" => 0.0048, "NGN" => 1.4, "GHS" => 0.015, "KES" => 0.27, "UGX" => 2.8,
    "TZS" => 2.6, "ETB" => 0.089, "ZAR" => 0.031, "LYD" => 0.0071, "SDG" => 0.97,
    "BWP" => 0.022, "MWK" => 0.90, "MZN" => 0.11, "NAD" => 0.031, "SCR" => 0.021,
    "SLL" => 174, "SOS" => 0.92, "SZL" => 0.031, "CDF" => 4.5, "BIF" => 4.4,
    "DJF" => 0.29, "GNF" => 143, "ERN" => 0.096, "LSL" => 0.031, "MGA" => 9.3,
    "MRU" => 0.057, "RWF" => 1.8, "ZMW" => 0.12, "AOA" => 1.2
];

if (!isset($taux_conversion[$monnaie_utilisateur])) {
    $monnaie_utilisateur = "XOF";
}

if (isset($_GET['lang']) && in_array($_GET['lang'], ['fr', 'en'])) {
    $_SESSION['langue'] = $_GET['lang'];
    header("Location: abonnements.php");
    exit();
}

$langue = $_SESSION['langue'] ?? 'fr';

$textes = [
    'fr' => [
        'titre' => 'Choisissez votre abonnement',
        'prix' => 'Prix',
        'fonctionnalites' => 'Fonctionnalités',
        'souscrire' => 'Souscrire à l\'abonnement',
        'mois' => 'mois',
        'fonctionnalites_list' => [
            'Accès complet à toutes les fonctionnalités',
            'Nombre illimité d’annonces',
            'Mise en avant de vos services'
        ],
        'payer_moov' => 'Payer avec Moov Africa',
        'payer_yas'=> 'payer avec YAS',
        'payer_bancaire'=>'payer par carte bancaire'
    ], // ✅ VIRGULE AJOUTÉE ICI

    'en' => [
        'titre' => 'Choose your subscription',
        'prix' => 'Price',
        'fonctionnalites' => 'Features',
        'souscrire' => 'Subscribe',
        'mois' => 'month',
        'fonctionnalites_list' => [
            'Full access to all features',
            'Unlimited listings',
            'Highlight your services'
        ],
        'payer_moov' => 'Pay with Moov Africa',
        'payer_yas'=>'pay with Yas',
        'payer_bancaire'=>'pay with Credit card'
    ]
];


$prix_xof = 5500;
$prix_converti = round($prix_xof * $taux_conversion[$monnaie_utilisateur], 2);
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($langue) ?>">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($textes[$langue]['titre']) ?></title>
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 30px;
        background-color: #ffffff;
        color: #4a1c1c; /* Bordeaux brun */
    }

    .lang-switch {
        text-align: right;
        padding: 10px;
    }

    .lang-switch a {
        color: #4a1c1c; /* Bordeaux brun */
        text-decoration: none;
        font-weight: bold;
        margin-left: 10px;
    }

    .lang-switch a:hover {
        color: #5a2323; /* Bordeaux brun plus foncé */
    }

    h1 {
        text-align: center;
        color: #4a1c1c; /* Bordeaux brun */
    }

    .abonnement-container {
        display: flex;
        justify-content: center;
        margin-top: 30px;
    }

    .abonnement-card {
        border: 2px solid #4a1c1c; /* Bordure bordeaux brun */
        padding: 20px;
        border-radius: 10px;
        width: 350px;
        background: #fffdfd; /* Blanc très léger rosé */
        color: #4a1c1c; /* Texte bordeaux brun */
        box-shadow: 0 2px 8px rgba(74, 28, 28, 0.1);
    }

    .abonnement-card h2 {
        text-align: center;
        color: #5a2323; /* Bordeaux brun plus foncé */
    }

    .abonnement-card ul {
        margin-left: 20px;
    }

    .bande-action {
        display: flex;
        justify-content: center;
        margin-top: 30px;
        gap: 20px;
    }

    .btn-souscrire {
        display: inline-block;
        padding: 10px 20px;
        background: #4a1c1c; /* Bordeaux brun */
        color: #ffffff;
        border-radius: 5px;
        text-decoration: none;
        font-weight: bold;
        transition: background 0.3s;
    }

    .btn-souscrire:hover {
        background: #5a2323; /* Bordeaux brun plus foncé */
    }

    .btn-verifier-identite {
        display: block;
        text-align: center;
        margin: 30px auto;
        background: #3b1414; /* Bordeaux brun plus foncé */
        color: #ffffff;
        padding: 10px 20px;
        border-radius: 5px;
        text-decoration: none;
        width: fit-content;
        font-weight: bold;
        transition: background 0.3s;
    }

    .btn-verifier-identite:hover {
        background: #2c0d0d; /* Bordeaux brun encore plus sombre */
    }

    .dashboard-item-back:hover {
        background-color: #2c0d0d; /* Bordeaux brun sombre */
        transform: scale(1.05);
        color: #fff;
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
<div class="language-switch">
  <a href="?lang=fr" class="lang-btn <?= ($_GET['lang'] ?? 'fr') === 'fr' ? 'active' : '' ?>">FR Français</a>
  <a href="?lang=en" class="lang-btn <?= ($_GET['lang'] ?? '') === 'en' ? 'active' : '' ?>">GB English</a>
</div>
<h1><?= htmlspecialchars($textes[$langue]['titre']) ?></h1>

<div class="abonnement-container">
    <div class="abonnement-card">
        <h2><?= $prix_converti . ' ' . htmlspecialchars($monnaie_utilisateur) . ' / ' . $textes[$langue]['mois'] ?></h2>
        <h3><?= htmlspecialchars($textes[$langue]['fonctionnalites']) ?> :</h3>
        <ul>
            <?php foreach ($textes[$langue]['fonctionnalites_list'] as $fonctionnalite) : ?>
                <li><?= htmlspecialchars($fonctionnalite) ?></li>
            <?php endforeach; ?>
        </ul>
        <div class="bande-action">
            <a href="paiement_moov.php" class="btn-souscrire">
                <?= htmlspecialchars($textes[$langue]['payer_moov']) ?>
                <a href="paiement_YAS.php" class="btn-souscrire">
                <?= htmlspecialchars($textes[$langue]['payer_yas']) ?>
            </a>
        </div>
    </div>
</div>
<a href="dashboard.php" class="btn-verifier-identite">Back/Retour</a>

</body>
</html>
