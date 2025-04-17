<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/Lokaz/backend/db.php';

// Définition de la langue
$lang = $_SESSION['lang'] ?? 'fr';
$translations = [
    'fr' => [
        'access_denied' => "Accès refusé. / Access denied.",
        'no_announcement' => "Aucune annonce spécifiée. / No announcement specified. ",
        'update_success' => "Annonce mise à jour avec succès. / Ad successfully updated.",
        'update_error' => "Erreur lors de la mise à jour. / Error during update. ",
        'not_found' => "Annonce introuvable ou vous n'avez pas les droits pour la modifier. / Ad not found or you don't have permission to modify it."
    ],
    'en' => [
        'access_denied' => "Access denied.",
        'no_announcement' => "No announcement specified.",
        'update_success' => "Ad successfully updated.",
        'update_error' => "Error during update.",
        'not_found' => "Ad not found or you don't have permission to modify it."
    ]
];

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo $translations[$lang]['access_denied'];
    exit;
}

// Vérifier si un ID d'annonce est envoyé
if (!isset($_GET['id'])) {
    echo $translations[$lang]['no_announcement'];
    exit;
}

$annonce_id = $_GET['id'];
$user_id = $_SESSION['user_id']; // Récupérer l'ID de l'utilisateur connecté

// Récupérer les données de l'annonce actuelle
$stmt = $pdo->prepare("SELECT titre, description, prix FROM annonces WHERE id = ? AND utilisateur_id = ?");
$stmt->execute([$annonce_id, $user_id]);
$annonce = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$annonce) {
    echo $translations[$lang]['not_found'];
    exit;
}

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nouveau_titre = $_POST['titre'];
    $nouvelle_description = $_POST['description'];
    $nouveau_prix = $_POST['prix'];

    // Mettre à jour l'annonce
    $stmt = $pdo->prepare("UPDATE annonces SET titre = ?, description = ?, prix = ? WHERE id = ? AND utilisateur_id = ?");
    if ($stmt->execute([$nouveau_titre, $nouvelle_description, $nouveau_prix, $annonce_id, $user_id])) {
        echo $translations[$lang]['update_success'];
    } else {
        echo $translations[$lang]['update_error'];
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Annonce</title>
    <style>
/* Style général */
body {
    font-family: Arial, sans-serif;
    background-color: #ffffff; /* Blanc pour le fond */
    color: #800000; /* Bordeaux pour le texte */
    margin: 0;
    padding: 20px;
}

h2 {
    color: #800000;
    font-size: 24px;
    text-align: center;
    margin-bottom: 30px;
}

/* Formulaire */
form {
    background-color: #fff5f7; /* Fond clair avec une légère teinte rosée */
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(128, 0, 0, 0.1); /* Ombre bordeaux subtile */
    max-width: 600px;
    margin: auto;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
    color: #800000; /* Bordeaux */
}

input[type="text"], input[type="number"], textarea {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #aa3b3b; /* Bordure bordeaux plus clair */
    border-radius: 5px;
    font-size: 16px;
    color: #800000;
    background-color: #ffffff;
}

textarea {
    resize: vertical;
    min-height: 100px;
}

/* Bouton de soumission */
button[type="submit"] {
    background-color: #800000; /* Bordeaux pur */
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    width: 100%;
}

button[type="submit"]:hover {
    background-color: #660000; /* Bordeaux plus foncé au survol */
}

/* Lien retour */
a {
    display: inline-block;
    margin-top: 20px;
    color: #800000;
    text-decoration: none;
    padding: 10px;
    background-color: #fff0f3; /* Fond légèrement teinté rosé */
    border-radius: 5px;
    text-align: center;
    width: 100%;
}

a:hover {
    background-color: #f4d6dc; /* Teinte rosée plus marquée au survol */
}
</style>

</head>
<body>

<h2>Modifier l'annonce / Edit ad </h2>
<form method="POST">
    <label for="titre">Titre / Title :</label>
    <input type="text" id="titre" name="titre" value="<?= htmlspecialchars($annonce['titre']) ?>" required><br>

    <label for="description">Description / Description  :</label>
    <textarea id="description" name="description" required><?= htmlspecialchars($annonce['description']) ?></textarea><br>

    <label for="prix">Prix / Price :</label>
    <input type="number" id="prix" name="prix" value="<?= htmlspecialchars($annonce['prix']) ?>" required><br>

    <button type="submit">Mettre à jour / Update </button>
</form>

<a href="mesannonces.php">Retour / back </a>

</body>
</html>





