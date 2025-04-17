<?php
session_start();
require_once '../backend/db.php';
include 'header.php';

$lang = $_GET['lang'] ?? 'fr';
$loueur_id = $_GET['loueur_id'] ?? ($_SESSION['utilisateur_id'] ?? null);

if (!$loueur_id) {
    echo "Aucun loueur spécifié.";
    exit;
}

// Traductions
$translations = [
    'fr' => [
        'title' => 'Avis reçus',
        'back' => 'Retour',
        'rating' => 'Note',
        'comment' => 'Commentaire',
        'written_by' => 'Écrit par',
        'no_reviews' => 'Aucun avis pour l’instant.',
    ],
    'en' => [
        'title' => 'Received Reviews',
        'back' => 'Back',
        'rating' => 'Rating',
        'comment' => 'Comment',
        'written_by' => 'Written by',
        'no_reviews' => 'No reviews yet.',
    ]
];
$t = $translations[$lang];

// Récupérer les avis du loueur
$sql = "SELECT avis.*, users.nom_utilisateur 
        FROM avis 
        JOIN users ON avis.utilisateur_id = users.id 
        WHERE avis.loueur_id = ?
        ORDER BY date_avis DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$loueur_id]);
$avis = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($t['title']) ?></title>
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 30px;
        background-color: #fff8f8; /* blanc légèrement rosé */
        color: #4a1c1c; /* bordeaux foncé pour le texte */
    }

    .lang-switch {
        text-align: right;
        margin-bottom: 20px;
    }

    .lang-switch a {
        text-decoration: none;
        margin-left: 10px;
        font-weight: bold;
        color: #800000; /* bordeaux */
    }

    .lang-switch a:hover {
        color: #a73333;
    }

    h1 {
        color: #800000; /* bordeaux */
        margin-bottom: 25px;
    }

    .review {
        background: #ffffff; /* blanc pur */
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(128, 0, 0, 0.1); /* ombre subtile bordeaux */
        margin-bottom: 20px;
    }

    .note {
        font-weight: bold;
        color: #a73333; /* bordeaux clair */
        margin-bottom: 5px;
    }

    .auteur {
        font-style: italic;
        font-size: 0.9em;
        color: #5a0f0f; /* bordeaux foncé doux */
        margin-top: 10px;
    }

    .no-avis {
        font-style: italic;
        color: #b86e6e; /* bordeaux grisé */
    }

    .btn-back {
        display: inline-block;
        margin-top: 30px;
        background: #800000; /* bordeaux profond */
        color: #ffffff;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        transition: background 0.3s;
    }

    .btn-back:hover {
        background: #5a0f0f; /* bordeaux encore plus foncé */
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

<!-- Boutons de changement de langue -->
<div class="lang-switch">
    <a href="?lang=fr&loueur_id=<?= $loueur_id ?>">🇫🇷 Français</a>
    <a href="?lang=en&loueur_id=<?= $loueur_id ?>">🇬🇧 English</a>
</div>

<h1><?= htmlspecialchars($t['title']) ?></h1>

<?php if (count($avis) === 0): ?>
    <p class="no-avis"><?= htmlspecialchars($t['no_reviews']) ?></p>
<?php else: ?>
    <?php foreach ($avis as $a): ?>
        <div class="review">
            <div class="note"><?= htmlspecialchars($t['rating']) ?> : <?= (int)$a['note'] ?>/5</div>
            <p><?= nl2br(htmlspecialchars($a['commentaire'])) ?></p>
            <div class="auteur"><?= htmlspecialchars($t['written_by']) ?> : <?= htmlspecialchars($a['nom_utilisateur']) ?></div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<a class="btn-back" href="dashboard.php?lang=<?= $lang ?>"><?= htmlspecialchars($t['back']) ?></a>

</body>
</html>
