<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/Lokaz/backend/db.php';

// Définition de la langue
$lang = $_SESSION['lang'] ?? 'fr';
$translations = [
    'fr' => [
        'access_denied' => "Accès refusé.",
        'no_announcement' => "Aucune annonce spécifiée.",
        'deactivation_success' => "Annonce désactivée avec succès.",
        'deactivation_error' => "Erreur lors de la désactivation."
    ],
    'en' => [
        'access_denied' => "Access denied.",
        'no_announcement' => "No announcement specified.",
        'deactivation_success' => "Ad successfully deactivated.",
        'deactivation_error' => "Error during deactivation."
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
$user_id = $_SESSION['user_id']; // ID de l'utilisateur connecté

// Désactiver l'annonce en mettant "inactif" dans la colonne statut
$stmt = $pdo->prepare("UPDATE annonces SET statut = 'inactif' WHERE id = ? AND utilisateur_id = ?");
if ($stmt->execute([$annonce_id, $user_id])) {
    echo $translations[$lang]['deactivation_success'];
} else {
    echo $translations[$lang]['deactivation_error'];
}
?>

