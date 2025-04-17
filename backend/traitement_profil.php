<?php
require_once '../backend/db.php';

if (!isset($_SESSION['user_id'])) {
    die("Accès refusé !");
}

$user_id = $_SESSION['user_id'];

if (!empty($_FILES['photo_profil']['name'])) {
    $dossier_upload = "../uploads/";
    $nom_fichier = time() . "_" . basename($_FILES["photo_profil"]["name"]);
    $chemin_complet = $dossier_upload . $nom_fichier;

    if (move_uploaded_file($_FILES["photo_profil"]["tmp_name"], $chemin_complet)) {
        // Mettre à jour la base de données
        $sql = "UPDATE users SET photo_profil = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nom_fichier, $user_id]);
        
        if ($stmt->execute()) {
            header("Location: ../frontend/profil.php"); // Redirection après succès
            exit();
        } else {
            echo "Erreur lors de la mise à jour de la photo.";
        }
    } else {
        echo "Erreur lors du téléchargement de l'image.";
    }
}
?>
