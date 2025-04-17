<?php
require_once '../backend/db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Vérifier si l'annonce existe
    $stmt = $pdo->prepare("SELECT * FROM annonces WHERE id = ?");
    $stmt->execute([$id]);
    $annonce = $stmt->fetch();

    $update = $pdo->prepare("UPDATE annonces SET statut = 'actif' WHERE id = ?");
    if ($update->execute([$id])) {
        // Rediriger avec un message de succès
        header("Location: mesannonces.php?message=Annonce activée avec succès");
        exit();
    } else {
        header("Location: mesannonces.php?error=Erreur lors de l'activation de l'annonce");
        exit();
    }
} else {
    header("Location: mesannonces.php?error=ID manquant");
    exit();
}
