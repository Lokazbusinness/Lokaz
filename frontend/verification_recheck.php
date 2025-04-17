<?php
session_start();
require_once '../backend/db.php';

// Vérifier la langue
$lang = isset($_GET['lang']) && $_GET['lang'] === 'en' ? 'en' : 'fr';

// Traductions
$translations = [
    'fr' => [
        'title' => 'Revérification d\'identité',
        'message_error' => 'Les informations fournies doivent correspondre aux documents d\'identité, sinon votre vérification échouera.',
        'message_success' => 'Votre demande de vérification a été soumise avec succès.',
        'form_name' => 'Nom',
        'form_first_name' => 'Prénom',
        'form_nationality' => 'Nationalité',
        'form_residence' => 'Pays de résidence',
        'form_birth_date' => 'Date de naissance',
        'form_gender' => 'Sexe',
        'form_place_of_birth' => 'Lieu de naissance',
        'form_submit' => 'Soumettre',
        'form_file_photo' => 'Photo de vous',
        'form_file_identity' => 'Photo du document d\'identité (CNI, passeport, permis)',
        'rejected_verification' => 'Votre demande a été rejetée. Vous pouvez la soumettre à nouveau.',
        'error_files' => 'Erreur lors de l\'envoi des fichiers.',
    ],
    'en' => [
        'title' => 'Identity Re-verification',
        'message_error' => 'The information provided must match the identity documents, otherwise your verification will fail.',
        'message_success' => 'Your verification request has been successfully submitted.',
        'form_name' => 'Last Name',
        'form_first_name' => 'First Name',
        'form_nationality' => 'Nationality',
        'form_residence' => 'Country of Residence',
        'form_birth_date' => 'Date of Birth',
        'form_gender' => 'Gender',
        'form_place_of_birth' => 'Place of Birth',
        'form_submit' => 'Submit',
        'form_file_photo' => 'Your Photo',
        'form_file_identity' => 'Identity Document Photo (ID card, passport, driving license)',
        'rejected_verification' => 'Your request was rejected. You can submit it again.',
        'error_files' => 'Error during file upload.',
    ]
];

$messages = $translations[$lang];

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    die("Accès refusé.");
}

$user_id = $_SESSION['user_id'];
$message = "";

// Vérification du formulaire de soumission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $photo_path = null;
    $doc_path = null;

    // Upload photo utilisateur
    if (isset($_FILES['photo_utilisateur']) && $_FILES['photo_utilisateur']['error'] === 0) {
        $filename = uniqid() . '_' . basename($_FILES['photo_utilisateur']['name']);
        $destination = '../uploads/identites/' . $filename;
        if (move_uploaded_file($_FILES['photo_utilisateur']['tmp_name'], $destination)) {
            $photo_path = $destination;
        }
    }

    // Upload document d'identité
    if (isset($_FILES['document_identite']) && $_FILES['document_identite']['error'] === 0) {
        $filename = uniqid() . '_' . basename($_FILES['document_identite']['name']);
        $destination = '../uploads/identites/' . $filename;
        if (move_uploaded_file($_FILES['document_identite']['tmp_name'], $destination)) {
            $doc_path = $destination;
        }
    }

    // Sauvegarde des infos si les fichiers sont uploadés
    if ($photo_path && $doc_path) {
        $stmt = $pdo->prepare("
            INSERT INTO verifications_identite (
                user_id, photo_utilisateur, document_identite, nom, prenom, nationalite,
                pays_residence, date_naissance, sexe, lieu_naissance, statut
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([ 
            $user_id,
            $photo_path,
            $doc_path,
            $_POST['nom'],
            $_POST['prenom'],
            $_POST['nationalite'],
            $_POST['pays_residence'],
            $_POST['date_naissance'],
            $_POST['sexe'],
            $_POST['lieu_naissance'],
            'en attente' // Statut initial : en attente
        ]);
        $message = $messages['message_success'];
    } else {
        $message = $messages['error_files'];
    }
}
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($messages['title']) ?></title>
    <style>
    body { 
        font-family: Arial, sans-serif; 
        background-color: #ffffff; /* Fond blanc */
        color: #4a1c1c; /* Texte bordeaux brun */
        margin: 0; 
        padding: 20px;
    }

    h2 {
        color: #4a1c1c; /* Titre bordeaux brun */
    }

    p {
        font-size: 16px;
        margin-bottom: 20px;
    }

    p a {
        color: #5c1e1e; /* Lien bordeaux brun */
        text-decoration: none;
    }

    p a:hover {
        color: #7a2e2e; /* Lien bordeaux plus vif au hover */
    }

    form {
        background-color: #fffafa; /* Blanc rosé léger */
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(74, 28, 28, 0.1); /* Ombre bordeaux douce */
        max-width: 600px;
        margin: auto;
    }

    label {
        display: block;
        margin-bottom: 8px;
        font-weight: bold;
    }

    input[type="text"], input[type="date"], select, input[type="file"] {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #a67979; /* Bordure bordeaux clair */
        border-radius: 5px;
        font-size: 16px;
        color: #4a1c1c; /* Texte bordeaux brun */
    }

    button[type="submit"] {
        background-color: #5c1e1e; /* Bordeaux brun */
        color: white;
        padding: 12px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        width: 100%;
    }

    button[type="submit"]:hover {
        background-color: #3d1414; /* Bordeaux plus foncé au hover */
    }

    .error, .success {
        color: #a30000; /* Rouge bordeaux intense pour erreurs/succès */
        font-weight: bold;
        margin-bottom: 20px;
    }

    .lang-switch {
        text-align: right;
        margin-bottom: 20px;
    }

    .lang-switch a {
        margin-left: 10px;
        color: white;
        font-weight: bold;
        padding: 10px;
        background-color: #5c1e1e; /* Bordeaux brun pour bouton */
        border-radius: 5px;
        text-decoration: none;
    }

    .lang-switch a:hover {
        background-color: #3d1414; /* Plus foncé au hover */
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


<!-- Boutons de changement de langue -->
<div class="language-switch">
  <a href="?lang=fr" class="lang-btn <?= ($_GET['lang'] ?? 'fr') === 'fr' ? 'active' : '' ?>">FR Français</a>
  <a href="?lang=en" class="lang-btn <?= ($_GET['lang'] ?? '') === 'en' ? 'active' : '' ?>">GB English</a>
</div>


<h2><?= htmlspecialchars($messages['title']) ?></h2>

<p style="color: red;">
    <?= htmlspecialchars($messages['message_error']) ?>
</p>

<?php if ($message): ?>
    <p><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<!-- Formulaire pour soumettre une nouvelle demande de vérification -->
<p><?= htmlspecialchars($messages['rejected_verification']) ?></p>

<form method="POST" enctype="multipart/form-data">
    <label><?= htmlspecialchars($messages['form_file_photo']) ?>:</label><br>
    <input type="file" name="photo_utilisateur" required><br><br>

    <label><?= htmlspecialchars($messages['form_file_identity']) ?>:</label><br>
    <input type="file" name="document_identite" required><br><br>

    <label><?= htmlspecialchars($messages['form_name']) ?>:</label><br>
    <input type="text" name="nom" required><br><br>

    <label><?= htmlspecialchars($messages['form_first_name']) ?>:</label><br>
    <input type="text" name="prenom" required><br><br>

    <label><?= htmlspecialchars($messages['form_nationality']) ?>:</label><br>
    <input type="text" name="nationalite" required><br><br>

    <label><?= htmlspecialchars($messages['form_residence']) ?>:</label><br>
    <input type="text" name="pays_residence" required><br><br>

    <label><?= htmlspecialchars($messages['form_birth_date']) ?>:</label><br>
    <input type="date" name="date_naissance" required><br><br>

    <label><?= htmlspecialchars($messages['form_gender']) ?>:</label><br>
    <select name="sexe" required>
        <option value="Homme"><?= htmlspecialchars('Homme') ?></option>
        <option value="Femme"><?= htmlspecialchars('Femme') ?></option>
        <option value="Autre"><?= htmlspecialchars('Autre') ?></option>
    </select><br><br>

    <label><?= htmlspecialchars($messages['form_place_of_birth']) ?>:</label><br>
    <input type="text" name="lieu_naissance" required><br><br>

    <button type="submit"><?= htmlspecialchars($messages['form_submit']) ?></button>
</form>

</body>
</html>
