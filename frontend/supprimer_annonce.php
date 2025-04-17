<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/Lokaz/backend/db.php';

// Définition de la langue
$lang = $_SESSION['lang'] ?? 'fr';
$translations = [
    'fr' => [
        'access_denied' => "Accès refusé.",
        'no_announcement' => "Aucune annonce spécifiée.",
        'delete_success' => "Annonce supprimée avec succès.",
        'delete_error' => "Erreur lors de la suppression."
    ],
    'en' => [
        'access_denied' => "Access denied.",
        'no_announcement' => "No announcement specified.",
        'delete_success' => "Ad successfully deleted.",
        'delete_error' => "Error during deletion."
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

// Supprimer l'annonce
$stmt = $pdo->prepare("DELETE FROM annonces WHERE id = ? AND utilisateur_id = ?");
if ($stmt->execute([$annonce_id, $user_id])) {
    echo $translations[$lang]['delete_success'];
} else {
    echo $translations[$lang]['delete_error'];
}
?>


