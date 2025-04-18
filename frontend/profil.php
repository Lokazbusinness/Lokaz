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
    body {
        background-color: #fff; /* fond blanc */
        color: #4a1410; /* texte brun foncé */
        font-family: Arial, sans-serif;
        height: 100vh;
        margin: 0;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        position: relative;
    }

    h2 {
        font-size: 24px;
        color: #4a1410; /* brun foncé */
        margin-bottom: 30px;
    }

    button {
        background-color: #6e1f1f; /* bordeaux brun */
        color: #fff;
        border: 2px solid #4a1410; /* brun foncé */
        padding: 12px 20px;
        font-size: 16px;
        border-radius: 6px;
        cursor: pointer;
        margin: 10px;
        transition: background-color 0.3s, border-color 0.3s;
    }

    button:hover {
        background-color: #4a1410; /* plus sombre au survol */
        border-color: #4a1410;
    }

    .language-switch {
        display: flex;
        gap: 10px;
    }

    .lang-btn {
        padding: 8px 20px;
        border-radius: 40px;
        border: 2px solid #6e1f1f; /* bordeaux brun */
        background-color: #fff;
        color: #6e1f1f;
        font-weight: bold;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .lang-btn.active {
        background-color: #6e1f1f; /* fond bordeaux brun */
        color: #fff;
        border: none;
    }

    .lang-btn:hover:not(.active) {
        background-color: #f8eaea; /* léger rosé en survol */
        color: #6e1f1f;
    }
    .btn-verifier-identite:hover, {
    background-color: #804040; /* Bordeaux brun plus clair */
    transform: scale(1.05);
    color: #ffffff;
}
</style>

