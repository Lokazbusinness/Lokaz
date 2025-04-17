<?php 
require_once __DIR__ . "/../backend/db.php";


// Définition de la langue
$lang = $_SESSION['lang'] ?? 'fr';

// Traductions intégrées
$translations = [
    'fr' => [
        'email_used' => "Cet email est déjà utilisé.",
        'phone_used' => "Ce numéro de téléphone est déjà utilisé.",
        'password_mismatch' => "Les mots de passe ne correspondent pas.",
        'success' => "Inscription réussie ! Redirection en cours...",
        'error' => "Erreur lors de l'inscription."
    ],
    'en' => [
        'email_used' => "This email is already in use.",
        'phone_used' => "This phone number is already in use.",
        'password_mismatch' => "Passwords do not match.",
        'success' => "Registration successful! Redirecting...",
        'error' => "Registration error."
    ]
];

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entreprise_nom=trim($_POST["entreprise_nom"]);
    $email=trim($_POST["email"]);
    $telephone=trim($_POST["telephone"]);
    $pays=trim($_POST["pays"]);
    $ville=trim($_POST["ville"]);
    $mot_secret=trim($_POST["mot_secret"]);
    $mot_de_passe=trim($_POST["mot_de_passe"]);
    $conf_mot_de_passe=trim($_POST["conf_mot_de_passe"]);
    $monnaie=trim($_POST["monnaie"]);
    $type="professionnel";
}
    
    
    // Vérification si les mots de passe correspondent
    if ($mot_de_passe !== $conf_mot_de_passe) {
        echo "<p style='color: red;'>{$translations[$lang]['password_mismatch']}</p>";
        exit;
    }

    // Vérifier si l'email est déjà utilisé
    $check_email = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $check_email->execute([$email]);

    if ($check_email->rowCount() > 0) {
        echo "<p style='color: red;'>{$translations[$lang]['email_used']}</p>";
        exit;
    }

    // Vérifier si le téléphone est déjà utilisé
    $check_phone = $pdo->prepare("SELECT id FROM users WHERE telephone = ?");
    $check_phone->execute([$telephone]);

    if ($check_phone->rowCount() > 0) {
        echo "<p style='color: red;'>{$translations[$lang]['phone_used']}</p>";
        exit;
    }

     // Gestion de l'upload de la photo de profil
     $photo_profil = "default.png";
     if (!empty($_FILES["photo_profil"]["name"])) {
         $target_dir = "../uploads/";
         $photo_profil = time() . "_" . basename($_FILES["photo_profil"]["name"]);
         $target_file = $target_dir . $photo_profil;
         move_uploaded_file($_FILES["photo_profil"]["tmp_name"], $target_file);
     }

    // Hash du mot de passe
$hashed_password = password_hash($mot_de_passe, PASSWORD_DEFAULT);

// Insérer dans la base de données
$stmt = $pdo->prepare("INSERT INTO users (email, telephone, entreprise_nom, pays, ville, mot_secret, mot_de_passe, monnaie, photo_profil, type) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$insert = $stmt->execute([$email, $telephone, $entreprise_nom, $pays, $ville, $mot_secret, $hashed_password, $monnaie, $photo_profil, $type]);

if ($insert) {
    echo "<p style='color: green;'>{$translations[$lang]['success']}</p>";
    header("refresh:2;url=../frontend/connexion.php");
    exit(); // Ajoute un exit pour éviter que le script continue après la redirection
} else {
    echo "<p style='color: red;'>{$translations[$lang]['error']}</p>";
}




