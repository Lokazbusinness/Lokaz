<?php
ob_start(); // Active la mise en mémoire tampon pour éviter les erreurs d'affichage
session_start(); // Démarrer la session
include 'header.php';

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur_id'])) {
    header("Location: connexion.php");
    exit();
}

$user_id = $_SESSION['utilisateur_id']; 
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'fr'; // Langue par défaut = français

// Charger les traductions
$translations = [
    'fr' => [
        'profile' => "Mon Profil",
        'update_info' => "Réinitialiser mes informations",
        'reset_password' => "Réinitialiser le mot de passe",
        'change_photo' => "Changer la photo de profil",
        'name' => "Nom complet",
        'username' => "Nom d'utilisateur", 
        'email' => "Email",
        'phone' => "Téléphone",
        'country' => "Pays",
        'city' => "Ville",
        'currency' => "Monnaie",
        'company' => "Nom de l'entreprise",
        'gender' => "Genre",
        'birthdate' => "Date de naissance",
        'retour' => 'retour au dashboard',
        'update_success' => "Informations mises à jour avec succès !",
        'error' => "Une erreur s'est produite, veuillez réessayer."
    ],
    'en' => [
        'profile' => "My Profile",
        'update_info' => "Update my information",
        'reset_password' => "Reset Password",
        'change_photo' => "Change Profile Picture",
        'name' => "Full Name",
        'username' => "Username", 
        'email' => "Email",
        'phone' => "Phone",
        'country' => "Country",
        'city' => "City",
        'currency' => "Currency",
        'company' => "Company Name",
        'gender' => "Gender",
        'birthdate' => "Birthdate",
        'retour' => 'back to dashboard',
        'update_success' => "Information updated successfully!",
        'error' => "An error occurred, please try again."
    ]
];

// Vérifier si la langue existe bien dans le tableau, sinon utiliser 'fr'
$t = $translations[$lang] ?? $translations['fr'];

// Connexion à la base de données
require_once __DIR__ . "/../backend/db.php";

// Récupérer les informations actuelles de l'utilisateur
$query = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$query->execute([$user_id]);
$user = $query->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Utilisateur non trouvé.";
    exit();
}

$type_utilisateur = $user['type'];

// Empêcher les erreurs si les clés n'existent pas
$user['genre'] = $user['genre'] ?? '';
$user['date_naissance'] = $user['date_naissance'] ?? '';

// Gérer la mise à jour des informations
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_info'])) {
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    $pays = $_POST['pays'];
    $ville = $_POST['ville'];
    $monnaie = $_POST['monnaie'];

    if ($type_utilisateur === "professionnel") {
        $entreprise_nom = $_POST['entreprise_nom'];
        $query = $pdo->prepare("UPDATE users SET email = ?, telephone = ?, pays = ?, ville = ?, monnaie = ?, entreprise_nom = ? WHERE id = ?");
        $success = $query->execute([$email, $telephone, $pays, $ville, $monnaie, $entreprise_nom, $user_id]);
    } else {
        $nom_complet = $_POST['nom_complet'];
        $nom_utilisateur = $_POST['nom_utilisateur'];
        $genre = $_POST['genre'];
        $date_naissance = $_POST['date_naissance'];
        $query = $pdo->prepare("UPDATE users SET email = ?, telephone = ?, pays = ?, ville = ?, monnaie = ?, nom_complet = ?, nom_utilisateur = ?, genre = ?, date_naissance = ? WHERE id = ?");
        $success = $query->execute([$email, $telephone, $pays, $ville, $monnaie, $nom_complet, $nom_utilisateur, $genre, $date_naissance, $user_id]);        
    }

    if ($success) {
        $_SESSION['success_message'] = $t['update_success'];
        header("Location:monprofil.php");
        
        exit();
    } else {
        $_SESSION['error_message'] = $t['error'];
    }
}

?>

<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $t['profile'] ?></title>
    <style>
body {
    background-color: #ffffff;
    color: #5a2a27; /* Texte principal en bordeaux brun */
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 40px;
}

/* Titres */
h2 {
    color: #5a2a27;
    font-size: 26px;
    margin-bottom: 20px;
}

/* Messages succès / erreur */
p {
    font-size: 16px;
    color: #5a2a27;
    margin-bottom: 15px;
}

/* Labels */
label {
    display: block;
    margin-top: 15px;
    color: #5a2a27;
    font-weight: 500;
}

/* Champs de saisie */
input[type="text"],
input[type="email"],
input[type="date"],
select {
    width: 100%;
    padding: 10px;
    border: 1px solid #5a2a27;
    border-radius: 6px;
    background-color: #ffffff;
    color: #5a2a27;
    margin-top: 5px;
    box-sizing: border-box;
}

/* Boutons */
button {
    background-color: #5a2a27; /* Bordeaux brun */
    color: #ffffff;
    border: none;
    padding: 10px 18px;
    margin: 10px 5px 0 0;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

/* Hover sur les boutons */
button:hover {
    background-color: #3e1919; /* Bordeaux foncé/brun profond */
    transform: scale(1.05);
}

/* Lien contenant un bouton */
a button {
    margin-top: 10px;
}
.language-switch {
  display: flex;
  gap: 10px;
}

.lang-btn {
  padding: 8px 20px;
  border-radius: 40px;
  border: 2px solid #5b1a18; /* bordeaux brun */
  background-color: white;
  color: #5b1a18;
  font-weight: bold;
  text-decoration: none;
  transition: all 0.3s ease;
}

.lang-btn.active {
  background-color: #5b1a18; /* fond bordeaux brun */
  color: white;
  border: none;
}

</style>
</head>
<body>

<!-- Affichage du message de succès ou d'erreur -->
<?php if (isset($_SESSION['success_message'])) : ?>
    <p style="color: green;"><?= $_SESSION['success_message'] ?></p>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])) : ?>
    <p style="color: red;"><?= $_SESSION['error_message'] ?></p>
    <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>

<!-- Boutons de langue -->
<div class="language-switch">
  <a href="?lang=fr" class="lang-btn <?= ($_GET['lang'] ?? 'fr') === 'fr' ? 'active' : '' ?>">FR Français</a>
  <a href="?lang=en" class="lang-btn <?= ($_GET['lang'] ?? '') === 'en' ? 'active' : '' ?>">GB English</a>
</div>

<h2><?= $t['profile'] ?></h2>

<!-- Bouton pour aller sur profil.php et changer la photo -->
<a href="profil.php">
    <button><?= $t['change_photo'] ?></button>
</a>

<form method="post">
    <label for="email"><?= $t['email'] ?> :</label>
    <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" required>

    <label for="telephone"><?= $t['phone'] ?> :</label>
    <input type="text" name="telephone" id="telephone" value="<?= htmlspecialchars($user['telephone']) ?>" required>

    <label for="pays"><?= $t['country'] ?> :</label>
    <input type="text" name="pays" id="pays" value="<?= htmlspecialchars($user['pays']) ?>" required>

    <label for="ville"><?= $t['city'] ?> :</label>
    <input type="text" name="ville" id="ville" value="<?= htmlspecialchars($user['ville']) ?>" required>

    <label for="monnaie"><?= $t['currency'] ?> :</label>
    <input type="text" name="monnaie" id="monnaie" value="<?= htmlspecialchars($user['monnaie']) ?>" required>

    <!-- CHAMPS PROFESSIONNEL -->
    <?php if ($type_utilisateur === "professionnel") : ?>
        <label for="entreprise_nom"><?= $t['company'] ?> :</label>
        <input type="text" name="entreprise_nom" id="entreprise_nom" value="<?= htmlspecialchars($user['entreprise_nom']) ?>" required>
    <?php endif; ?>

    <!-- CHAMPS PARTICULIER -->
    <?php if ($type_utilisateur === "particulier") : ?>
        <label for="nom_complet"><?= $t['name'] ?> :</label>
        <input type="text" name="nom_complet" id="nom_complet" value="<?= htmlspecialchars($user['nom_complet']) ?>" required>

        <label for="nom_utilisateur"><?= $t['username'] ?> :</label>
        <input type="text" name="nom_utilisateur" id="nom_utilisateur" value="<?= htmlspecialchars($user['nom_utilisateur']) ?>" required>


        <label for="genre"><?= $t['gender'] ?> :</label>
        <select name="genre" id="genre">
            <option value="Homme" <?= $user['genre'] == 'Homme' ? 'selected' : '' ?>>Homme</option>
            <option value="Femme" <?= $user['genre'] == 'Femme' ? 'selected' : '' ?>>Femme</option>
            <option value="Autre" <?= $user['genre'] == 'Autre' ? 'selected' : '' ?>>Autre</option>
        </select>

        <label for="date_naissance"><?= $t['birthdate'] ?> :</label>
        <input type="date" name="date_naissance" id="date_naissance" value="<?= htmlspecialchars($user['date_naissance']) ?>" required>
    <?php endif; ?>

    <button type="submit" name="update_info"><?= $t['update_info'] ?></button>
</form>

<!-- Bouton pour changer de mot de passe -->
<form action="reset_password.php" method="get">
    <button type="submit"><?= $t['reset_password'] ?></button>
</form>

<a href="dashboard.php" class="back-button"><?= $translations[$lang]['retour'] ?></a>

</body>
</html>






