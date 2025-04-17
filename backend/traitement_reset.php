<?php
session_start();
require_once __DIR__ . "/../backend/db.php";

$lang = $_SESSION['lang'] ?? 'fr';

// Textes multilingues pour erreurs et succès
$texts = [
    'fr' => [
        'empty_fields' => "Veuillez remplir tous les champs.",
        'invalid_credentials' => "Email ou mot secret incorrect.",
        'password_mismatch' => "Les mots de passe ne correspondent pas.",
        'reset_success' => "Mot de passe réinitialisé avec succès !"
    ],
    'en' => [
        'empty_fields' => "Please fill in all fields.",
        'invalid_credentials' => "Email or secret code incorrect.",
        'password_mismatch' => "Passwords do not match.",
        'reset_success' => "Password successfully reset!"
    ]
];

$t = $texts[$lang];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $mot_secret = trim($_POST['mot_secret']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Vérifier si tous les champs sont remplis
    if (empty($email) || empty($mot_secret) || empty($password) || empty($confirm_password)) {
        $_SESSION['reset_errors'] = $t['empty_fields'];
        header("Location: ../frontend/reset_password.php");
        exit();
    }

    // Vérifier si les mots de passe correspondent
    if ($password !== $confirm_password) {
        $_SESSION['reset_errors'] = $t['password_mismatch'];
        header("Location: ../frontend/reset_password.php");
        exit();
    }

    // Vérifier si l'email et le mot secret sont corrects
    $sql = "SELECT id FROM users WHERE email = :email AND mot_secret = :mot_secret";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':mot_secret', $mot_secret);
    $stmt->execute();
    $user = $stmt->fetch();

    if (!$user) {
        $_SESSION['reset_errors'] = $t['invalid_credentials'];
        header("Location: ../frontend/reset_password.php");
        exit();
    }

    // Hacher le nouveau mot de passe
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Mettre à jour le mot de passe
    $update_sql = "UPDATE users SET mot_de_passe = :password WHERE email = :email";
    $update_stmt = $pdo->prepare($update_sql);
    $update_stmt->bindParam(':password', $hashed_password);
    $update_stmt->bindParam(':email', $email);

    if ($update_stmt->execute()) {
        $_SESSION['reset_success'] = $t['reset_success'];
        header("Location: ../frontend/reset_success.php");
        exit();
    } else {
        $_SESSION['reset_errors'] = "Une erreur s'est produite, veuillez réessayer.";
        header("Location: ../frontend/reset_password.php");
        exit();
    }
}
?>

