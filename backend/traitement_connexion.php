<?php
require_once '../backend/db.php';
session_start(); // Assure que la session est bien démarrée

// Détection de la langue (français par défaut)
$language = isset($_GET['lang']) ? $_GET['lang'] : 'fr';

// Traductions des messages
$translations = [
    'fr' => [
        'fill_all_fields' => "Veuillez remplir tous les champs.",
        'incorrect_login' => "Email ou mot de passe incorrect.",
        'reset_password' => "Réinitialiser le mot de passe"
    ],
    'en' => [
        'fill_all_fields' => "Please fill in all fields.",
        'incorrect_login' => "Incorrect email or password.",
        'reset_password' => "Reset Password"
    ],
];

// Sélection de la langue
$t = $translations[$language];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];

    if (empty($email) || empty($mot_de_passe)) {
        $_SESSION['login_error'] = $t['fill_all_fields'];
        header("Location: ../frontend/connexion.php?lang=" . $language);
        exit();
    }

    // Vérifier si l'utilisateur existe et récupérer son type
    $sql = "SELECT id, nom_complet, mot_de_passe, type FROM users WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch();

    if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
        // Stocker l'ID utilisateur en session
        $_SESSION['utilisateur_id'] = $user['id'];
        $_SESSION['user_id'] = $user['id']; // Déjà présent, mais doublé pour éviter les erreurs
        $_SESSION['user_name'] = $user['nom_complet'];
        $_SESSION['user_type'] = $user['type']; // Ajout du type d'utilisateur
        
        // Rendre la session persistante avec des cookies
        setcookie("utilisateur_id", $user['id'], time() + 86400 * 30, "/"); // Cookie 30 jours
        setcookie("user_id", $user['id'], time() + 86400 * 30, "/"); // Garde la cohérence avec $_SESSION
        setcookie("user_name", $user['nom_complet'], time() + 86400 * 30, "/");
        setcookie("user_type", $user['type'], time() + 86400 * 30, "/");

        header("Location: ../frontend/annonces.php?lang=" . $language);
        exit();
    } else {
        $_SESSION['login_error'] = $t['incorrect_login'];
        $_SESSION['show_reset_button'] = true; // Active le bouton de réinitialisation
        header("Location: ../frontend/connexion.php?lang=" . $language);
        exit();
    }
}
?>




