<?php
require_once '../backend/db.php';

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur_id'])) {
    header("Location: connexion.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT photo_profil FROM users WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$photo = !empty($user['photo_profil']) ? "../uploads/" . htmlspecialchars($user['photo_profil']) : "../images/default.png";

?>

<!-- Affichage de la photo -->
<img src="<?= $photo ?>" alt="Photo de profil" width="150" height="150" style="border-radius: 50%;">
<form action="../backend/traitement_profil.php" method="POST" enctype="multipart/form-data">
    <input type="file" name="photo_profil" accept="image/*" required>
    <button type="submit">Changer la photo/Change photo</button>
</form>

<a href="monprofil.php" class="btn-verifier-identite">Back/Retour</a>

<style>
    /* Général */
body {
    background-color: #f0f0f0;  /* Gris clair pour l'arrière-plan */
    font-family: Arial, sans-serif;  /* Police simple et lisible */
    color: #333;  /* Texte en gris foncé pour contraster */
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

/* Conteneur principal */
.container {
    background-color: #ffffff;  /* Fond blanc pour la section */
    padding: 20px;
    border-radius: 8px;  /* Coins arrondis */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);  /* Ombre légère pour l'effet 3D */
    text-align: center;
    width: 300px;
}

/* Style pour la photo de profil */
img {
    border-radius: 50%;  /* Photo circulaire */
    border: 2px solid #ddd;  /* Bordure gris clair autour de l'image */
    margin-bottom: 15px;
}

/* Formulaire pour changer la photo */
input[type="file"] {
    margin: 15px 0;
    padding: 8px;
    background-color: #f9f9f9;
    border: 1px solid #ccc;
    border-radius: 5px;
    width: 100%;
    box-sizing: border-box;
}

/* Bouton de soumission */
button {
    background-color: #cccccc;  /* Gris clair */
    color: #333;  /* Gris foncé */
    border: 1px solid #bbb;  /* Bordure gris clair */
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    width: 100%;
    margin-top: 10px;
}

button:hover {
    background-color:rgb(165, 161, 161);  /* Gris plus foncé au survol */
    border-color: #999;
}

/* Bouton "Retour" */
a.btn-verifier-identite {
    display: inline-block;
    margin-top: 20px;
    background-color:rgb(175, 169, 169);
    color: #333;
    padding: 10px 20px;
    border-radius: 5px;
    text-decoration: none;
    text-align: center;
}

a.btn-verifier-identite:hover {
    background-color:rgb(29, 28, 28);
}
    </style>
