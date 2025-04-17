<?php
session_start();
require_once __DIR__ . "/../backend/db.php";
// Mise à jour des informations
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    // Récupération des nouvelles valeurs
    $nom_complet = $_POST['nom_complet'] ?? $nom_complet;
    $email = $_POST['email'] ?? $email;
    $mot_secret = $_POST['mot_secret'] ?? $mot_secret;
    $pays = $_POST['pays'] ?? $pays;
    $ville = $_POST['ville'] ?? $ville;
    $telephone = $_POST['telephone'] ?? $telephone;
    $monnaie = $_POST['monnaie'] ?? $monnaie;
    $nom_entreprise = $_POST['nom_entreprise'] ?? $nom_entreprise;

    // Mise à jour dans la base de données
    $sql = "UPDATE users SET nom_complet=?, email=?, mot_secret=?, pays=?, ville=?, telephone=?, monnaie=?, nom_entreprise=? WHERE id=?";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$nom_complet, $email, $mot_secret, $pays, $ville, $telephone, $monnaie, $nom_entreprise, $user_id])) {
        echo "<script>
                alert('Mise à jour réussie !');
                window.location.href = 'monprofil.php';
              </script>";
    } else {
        echo "<script>alert('Erreur lors de la mise à jour.');</script>";
    }
}
?>