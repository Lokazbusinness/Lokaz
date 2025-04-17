<?php
// Démarrer la session uniquement si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Détruire la session
session_unset();
session_destroy();

// Supprimer les cookies de session (si utilisés)
setcookie("user_id", "", time() - 3600, "/");
setcookie("user_name", "", time() - 3600, "/");

// Rediriger vers la page de connexion
header("Location: ../frontend/connexion.php");
exit();
?>

