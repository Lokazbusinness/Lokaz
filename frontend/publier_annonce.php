<?php
session_start(); // Démarrer la session
ob_start(); // Active la mise en mémoire tampon pour éviter les erreurs d'affichage
// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}

require_once '../backend/db.php'; // Connexion à la base de données
include 'header.php';

// Définir la langue
$language = isset($_GET['lang']) ? $_GET['lang'] : 'fr';


// Récupération des informations utilisateur
$utilisateur_id = $_SESSION['user_id'];

// Récupérer la monnaie de l'utilisateur
$sql = "SELECT monnaie FROM users WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$utilisateur_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$monnaie = $user['monnaie'] ?? 'XOF'; // Par défaut XOF si non défini

// Vérifier l'état de l'abonnement dans la table abonnements
$sql_abonnement = "SELECT abonnement_actif FROM abonnements WHERE user_id = ? ORDER BY date_activation DESC LIMIT 1";
$stmt_abonnement = $pdo->prepare($sql_abonnement);
$stmt_abonnement->execute([$utilisateur_id]);
$abonnement = $stmt_abonnement->fetch(PDO::FETCH_ASSOC);

$abonnement_actif = $abonnement['abonnement_actif'] ?? 0; // 0 = pas d'abonnement actif


// Traductions
$translations = [
    'fr' => [
        'title' => 'Publier une annonce',
        'placeholder_title' => "Titre de l'annonce",
        'placeholder_description' => 'Description',
        'placeholder_price' => 'Prix (' . $monnaie . '/jour)',
        'placeholder_country' => 'Pays',
        'placeholder_city' => 'Ville',
        'placeholder_category' => 'Sélectionnez une catégorie',
        'placeholder_image' => 'Image',
        'submit' => 'Publier',
        'error_upload' => "Erreur lors de l'upload de l'image.",
        'error_publication' => "Erreur lors de la publication.",
        'success' => "Annonce publiée avec succès.",
        'not_visible' => "Votre annonce a été publiée, mais elle ne sera disponible que si vous avez un abonnement actif. / Your ad has been published, but it will only be available if you have an active subscription.", 
        'change_language' => 'Changer de langue',
        'french' => 'Français',
        'english' => 'Anglais',
        'select_category' => 'Choisir une catégorie',
        'retour' => 'Retour aux annonces',
        'categories' => ['Électronique', 'Mode', 'Maison', 'Loisirs','Services', 'Immobilier', 'Automobile', 'Pour bébé', 'Événementiels']
    ],
        
    'en' => [
        'title' => 'Post an Ad',
        'placeholder_title' => "Ad Title",
        'placeholder_description' => 'Description',
        'placeholder_price' => 'Price (' . $monnaie . '/day)',
        'placeholder_country' => 'Country',
        'placeholder_city' => 'City',
        'placeholder_category' => 'Select a category',
        'placeholder_image' => 'Image',
        'submit' => 'Post',
        'error_upload' => "Error uploading the image.",
        'error_publication' => "Error while publishing.",
        'success' => "Ad posted successfully.",
        'not_visible' => "Your ad has been published, but it will only be available if you have an active subscription.",
        'change_language' => 'Change Language',
        'french' => 'French',
        'english' => 'English',
        'select_category' => 'Choose a category',
        'retour' => 'Back to listings',
        'categories' => ['Electronics', 'Fashion', 'Home', 'Hobbies','Services', 'Real Estate', 'Automobile', 'Baby Products', 'Event Planning']
    ],
];

$t = $translations[$language];

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifier que tous les champs sont bien remplis
    if (empty($_POST['titre']) || empty($_POST['description']) || empty($_POST['prix']) || empty($_POST['pays']) || empty($_POST['ville']) || empty($_POST['categorie'])) {
        echo "<p style='color:red;'>{$t['error_publication']} - Champs manquants</p>";
        exit();
    }

    // Récupérer et nettoyer les données
    $titre = htmlspecialchars($_POST['titre']);
    $description = htmlspecialchars($_POST['description']);
    $prix = trim($_POST['prix']);
    $pays = htmlspecialchars($_POST['pays']);
    $ville = htmlspecialchars($_POST['ville']);
    $categorie = htmlspecialchars($_POST['categorie']);
    $image_path = "";

    // Vérifier que le prix est bien un nombre positif
    if (!is_numeric($prix) || $prix <= 0) {
        echo "<p style='color:red;'>{$t['error_publication']} - Prix invalide</p>";
        exit();
    }

    // Vérifier si un fichier est envoyé
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../backend/uploads/"; // Assurez-vous que ce dossier existe
        $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        // Vérifier si l'extension est valide
        if (!in_array($imageFileType, $allowed_types)) {
            echo "<p style='color:red;'>Format de fichier non autorisé.</p>";
            exit();
        }

        // Générer un nom unique pour l'image
        $image_name = uniqid("img_") . "." . $imageFileType;
        $image_path = $target_dir . $image_name;

        // Déplacer l'image vers le dossier d'upload
        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $image_path)) {
            echo "<p style='color:red;'>{$t['error_upload']}</p>";
            exit();
        }
    }

    // Enregistrement dans la base de données
    $sql = "INSERT INTO annonces (utilisateur_id, titre, description, prix, monnaie, pays, ville, categorie, image_path, date_publication) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([$utilisateur_id, $titre, $description, $prix, $monnaie, $pays, $ville, $categorie, $image_path])) {
        // Vérifier l'abonnement
        if ($abonnement_actif != 1) {
            echo "<div style='position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); 
            background: rgba(216, 184, 191, 0.8); color: white; padding: 20px; border-radius: 10px; text-align: center; font-size: 18px;'>
            {$t['not_visible']}<br><br> 
            <a href='abonnements.php?lang=<?= $language ?>' style='color: Brown Bordeaux; text-decoration: underline; font-weight: bold;'>Activer un abonnement / Activate a subscription</a>
            </div>
            <a href='annonces.php?lang=$language' style='color:Brown Bordeaux ; text-decoration: underline; font-weight: bold;'> retour aux annonces / Back to listings </a></div>";
            exit();
        }
        header("Location: mesannonces.php?message=" . urlencode("Annonce publiée avec succès."));
        exit();
    } else {
        $_SESSION['error_message'] = "Erreur lors de la publication.";
        header("Location: publier_annonce.php?lang=$language");
        exit();
    }
}
ob_end_flush(); // Libère le contenu de la mémoire tampon
?>

<!DOCTYPE html>
<html lang="<?= $language ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $t['title'] ?></title>
    <style>
/* Style global */
body {
    font-family: Arial, sans-serif;
    background-color: #ffffff;
    color: #5a2a27; /* Texte principal en bordeaux brun */
    margin: 0;
    padding: 0;
}

h2 {
    text-align: center;
    color: #5a2a27; /* Titre en bordeaux brun */
    margin-top: 30px;
    font-size: 28px;
}

/* Bouton de changement de langue */
button:not([type="submit"]) {
    position: absolute;
    top: 20px;
    right: 20px;
    background-color: #5a2a27; /* Fond du bouton en bordeaux brun */
    color: white;
    padding: 10px 18px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button:not([type="submit"]):hover {
    background-color: #3b1616; /* Bordeaux brun plus foncé au survol */
}

/* Conteneur du formulaire */
form {
    max-width: 800px;
    margin: 50px auto;
    background-color: #fffafa; /* Blanc légèrement rosé */
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 0 15px rgba(90, 42, 39, 0.15);
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
}

/* Labels */
form label {
    width: 100%;
    color: #5a2a27; /* Bordeaux brun pour les labels */
    font-weight: bold;
    margin: 10px 0 5px;
}

/* Champs de saisie */
form input,
form select {
    width: calc(50% - 10px);
    padding: 10px;
    border: 2px solid #5a2a27; /* Bordure des champs en bordeaux brun */
    background-color: #ffffff;
    color: #1a0e0e;
    border-radius: 4px;
    margin-bottom: 20px;
}

/* Pleine largeur si nécessaire */
form input[type="file"],
form input[type="date"],
form select[multiple],
form textarea {
    width: 100%;
}

/* Bouton de soumission */
form button[type="submit"] {
    width: 100%;
    background-color: #5a2a27; /* Fond du bouton en bordeaux brun */
    color: #ffffff;
    padding: 14px;
    font-size: 16px;
    border: none;
    border-radius: 6px;
    margin-top: 10px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    align-self: flex-end;
}

/* Effet au survol */
form button[type="submit"]:hover {
    background-color: #3b1616; /* Bordeaux brun plus foncé au survol */
}

/* Liens dans le formulaire */
form a {
    display: inline-block;
    margin-top: 15px;
    color: #5a2a27; /* Liens en bordeaux brun */
    text-decoration: none;
    font-weight: bold;
}

form a:hover {
    color: #3b1616; /* Bordeaux brun foncé au survol */
}

/* Bouton spécial */
.btn-verifier-identite {
    display: inline-block;
    background-color: #5a2a27; /* Fond du bouton spécial en bordeaux brun */
    color: white;
    padding: 12px 24px;
    font-size: 16px;
    border: none;
    border-radius: 6px;
    text-decoration: none;
    text-align: center;
    transition: background-color 0.3s, transform 0.2s;
}

.btn-verifier-identite:hover {
    background-color: #3b1616; /* Bordeaux brun plus foncé au survol */
    transform: scale(1.05);
}

.btn-verifier-identite:active {
    background-color: #2a0e0e; /* Bordeaux brun très foncé quand actif */
    transform: scale(1);
}

/* Responsive */
@media (max-width: 768px) {
    form input,
    form select {
        width: 100%;
        margin-right: 0;
    }
}
/* Bouton Retour aux annonces */
.btn-retour {
    background-color: rgb(92, 25, 17); /* Bordeaux brun */
    color: #fff;
    padding: 10px 20px;
    font-size: 15px;
    border: none;
    border-radius: 6px;
    text-decoration: none;
    display: inline-block;
    margin-bottom: 20px;
    transition: background-color 0.3s, transform 0.2s;
}

.btn-retour:hover {
    background-color: rgb(122, 33, 24); /* Plus clair au survol */
    transform: scale(1.03);
}

/* Bouton Publier */
form button[type="submit"] {
    background-color: rgb(92, 25, 17); /* Bordeaux brun */
    color: #fff;
    padding: 14px;
    font-size: 16px;
    border: none;
    border-radius: 6px;
    width: 100%;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

form button[type="submit"]:hover {
    background-color: rgb(122, 33, 24); /* Plus clair au survol */
}

</style>


</head>
<body>
    <h1><?= $t['title'] ?></h1>

    <p><?= $t['change_language'] ?> :
        <a href="?lang=fr"><?= $t['french'] ?></a> |
        <a href="?lang=en"><?= $t['english'] ?></a>
    </p>

    <form action="publier_annonce.php" method="post" enctype="multipart/form-data">
        <input type="file" name="image" accept="image/*">
        <input type="text" name="titre" placeholder="<?= $t['placeholder_title'] ?>" required>
        <input type="text" name="description" placeholder="<?= $t['placeholder_description'] ?>" required>
        <input type="number" name="prix" placeholder="<?= $t['placeholder_price'] ?>" required>
        <input type="text" name="pays" placeholder="<?= $t['placeholder_country'] ?>" required>
        <input type="text" name="ville" placeholder="<?= $t['placeholder_city'] ?>" required>
        <select name="categorie" required>
            <option value=""><?= $t['select_category'] ?></option>
            <?php foreach ($t['categories'] as $cat): ?>
                <option value="<?= $cat ?>"><?= $cat ?></option>
            <?php endforeach; ?>
        </select>
        <input type="submit" value="<?= $t['submit'] ?>">
    </form>

</body>
<a href="annonces.php" class="btn-retour"><?= $t['retour']; ?></a>
</html>


