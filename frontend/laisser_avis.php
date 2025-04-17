<?php
session_start();
require_once '../backend/db.php';

// Récupération de la langue et de l'ID du loueur
$language = $_GET['lang'] ?? 'fr';
$loueur_id = $_GET['loueur_id'] ?? null;
$utilisateur_id = $_SESSION['utilisateur_id'] ?? null;

// Vérifie si l'utilisateur est connecté
$utilisateur_id = $_SESSION['utilisateur_id'] ?? null;
$language = $_GET['lang'] ?? 'fr';


// Redirection si pas de loueur ou utilisateur non connecté
if (!$loueur_id || !$utilisateur_id) {
    header("Location: connexion.php");
    exit;
}

// Traductions
$translations = [
    'fr' => [
        'title' => 'Laisser un avis',
        'rating_label' => 'Note (1 à 5 étoiles)',
        'comment_label' => 'Votre commentaire',
        'submit_button' => 'Envoyer l\'avis',
        'success' => 'Avis envoyé avec succès !',
        'error' => 'Une erreur s\'est produite.'
    ],
    'en' => [
        'title' => 'Leave a Review',
        'rating_label' => 'Rating (1 to 5 stars)',
        'comment_label' => 'Your comment',
        'submit_button' => 'Submit Review',
        'success' => 'Review submitted successfully!',
        'error' => 'An error occurred.'
    ]
];

$t = $translations[$language];

$message = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $note = $_POST['note'] ?? 0;
    $commentaire = $_POST['commentaire'] ?? '';

    if ($note >= 1 && $note <= 5 && !empty($commentaire)) {
        $stmt = $pdo->prepare("INSERT INTO avis (utilisateur_id, loueur_id, note, commentaire, date_avis) VALUES (?, ?, ?, ?, NOW())");
        $success = $stmt->execute([$utilisateur_id, $loueur_id, $note, $commentaire]);
        $message = $success ? $t['success'] : $t['error'];
    } else {
        $message = $t['error'];
    }
}
?>

<!DOCTYPE html>
<html lang="<?= htmlspecialchars($language) ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $t['title'] ?></title>
    <style>
        body {
            font-family: Arial;
            padding: 20px;
            background-color: #f8f8f8;
        }
        form {
            background: white;
            padding: 20px;
            border-radius: 10px;
            max-width: 500px;
            margin: auto;
        }
        textarea, select {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            background-color:rgb(71, 14, 22);
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background-color:rgb(68, 15, 26);
        }
        .message {
            text-align: center;
            font-weight: bold;
            color: green;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div style="text-align: right; margin-bottom: 20px;">
    <a href="?lang=fr<?= isset($loueur_id) ? '&loueur_id=' . $loueur_id : '' ?>" style="margin-right: 10px;">🇫🇷 Français</a>
    <a href="?lang=en<?= isset($loueur_id) ? '&loueur_id=' . $loueur_id : '' ?>">🇬🇧 English</a>
</div>

<h2><?= htmlspecialchars($t['title']) ?></h2>

<?php if ($message): ?>
    <div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<form method="POST">
    <label for="note"><?= $t['rating_label'] ?> :</label>
    <select name="note" id="note" required>
        <option value="">--</option>
        <?php for ($i = 1; $i <= 5; $i++): ?>
            <option value="<?= $i ?>"><?= $i ?> ⭐</option>
        <?php endfor; ?>
    </select>

    <label for="commentaire"><?= $t['comment_label'] ?> :</label>
    <textarea name="commentaire" id="commentaire" rows="4" required></textarea>

    <button type="submit"><?= $t['submit_button'] ?></button>
</form>

</body>
</html>
