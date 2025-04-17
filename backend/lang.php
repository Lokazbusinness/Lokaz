<?php

// Définir la langue par défaut si elle n'est pas encore définie
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'fr'; // Par défaut : français
}

// Vérifier si un changement de langue est demandé via URL
if (isset($_GET['lang']) && in_array($_GET['lang'], ['fr', 'en'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

// Sélection de la langue
$lang = $_SESSION['lang'];

// Définition des traductions
$lang_fr = [
    "title" => "Lokaz",
    "inscription" => "Inscription",
    "connexion" => "Connexion",
    "nom_complet" => "Nom et Prénom",
    "email" => "Email",
    "entrez_email" => "Entrez votre email",
    "mot_de_passe" => "Mot de passe",
    "afficher_mot_de_passe" => "Afficher le mot de passe",
    "genre" => "Genre",
    "homme" => "Homme",
    "femme" => "Femme",
    "autre" => "Autre",
    "date_naissance" => "Date de naissance",
    "pays" => "Pays",
    "ville" => "Ville",
    "telephone" => "Téléphone",
    "photo_profil" => "Photo de profil", 
    "se_connecter" => "Se connecter",
    "s_inscrire" => "S'inscrire",
    "reinitialisation" => "Réinitialisation du mot de passe",
    "nouveau_mot_de_passe" => "Nouveau mot de passe",
    "confirmer_mot_de_passe" => "Confirmer le mot de passe",
    "changer_mot_de_passe" => "Changer le mot de passe",
    "mot_de_passe_change" => "Votre mot de passe a été mis à jour avec succès.",
    "email_non_trouve" => "Lien expiré ou invalide.",
    "change_language" => "Français"
];

$lang_en = [
    "title" => "Lokaz",
    "inscription" => "Sign Up",
    "connexion" => "Login",
    "nom_complet" => "Full Name",
    "email" => "Email",
    "entrez_email" => "Enter your email",
    "mot_de_passe" => "Password",
    "afficher_mot_de_passe" => "Show password",
    "genre" => "Gender",
    "homme" => "male",
    "femme" => "Female",
    "autre" => "other",
    "date_naissance" => "Date of Birth",
    "pays" => "Country",
    "ville" => "City",
    "telephone" => "Phone",
    "photo_profil" => "Profile picture",
    "se_connecter" => "Log in",
    "s_inscrire" => "Sign Up",
    "reinitialisation" => "Password Reset",
    "nouveau_mot_de_passe" => "New Password",
    "confirmer_mot_de_passe" => "Confirm Password",
    "changer_mot_de_passe" => "Change Password",
    "mot_de_passe_change" => "Your password has been successfully updated.",
    "email_non_trouve" => "Expired or invalid link.",
    "change_language" => "English"
];

// Sélectionner la bonne langue
$langue = ($lang === 'en') ? $lang_en : $lang_fr;
?>

