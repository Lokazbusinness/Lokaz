<?php
session_start();
ob_start(); // Active la mise en mémoire tampon pour éviter les erreurs d'affichage
require_once '../backend/db.php';
include 'header.php';

// Définir la langue par défaut
$lang = isset($_GET['lang']) && $_GET['lang'] === 'en' ? 'en' : 'fr';

// Traductions
$translations = [
    'fr' => [
        'title' => 'Vérification d\'identité',
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
        'verification_submitted' => 'Vous avez déjà soumis une demande de vérification.Si votre demande a été rejetéé',
        'error_files' => 'Erreur lors de l\'envoi des fichiers.',
        'rejected_verification' => 'Vous avez déjà soumis une demande de vérification d\'identité. Vous pouvez le <a href="verification_recheck.php?lang=<?php echo $lang; ?>">resoumettre ici</a>.'
    ],
    'en' => [
        'title' => 'Identity Verification',
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
        'verification_submitted' => 'You have already submitted a verification request. If your request was rejected, you can submit it again.',
        'error_files' => 'Error during file upload.',
        'rejected_verification' => 'You have already submitted an identity verification request. You can <a href="verification_recheck.php?lang=<?php echo $lang; ?>">resubmit it here</a>.'
    ]
];

$messages = $translations[$lang];

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    die("Accès refusé.");
}

$user_id = $_SESSION['user_id'];
$message = "";

// Vérifier si l'utilisateur a déjà soumis une demande
$stmt = $pdo->prepare("SELECT statut FROM verifications_identite WHERE user_id = ? ORDER BY date_soumission DESC LIMIT 1");
$stmt->execute([$user_id]);
$verification = $stmt->fetch();

if ($verification && $verification['statut'] !== 'rejetée') {
    // Si la vérification est déjà acceptée ou en attente, on empêche la soumission.
    $message = $messages['rejected_verification'];
} else {
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

        // Sauvegarde des infos
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
}
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($messages['title']) ?></title>
    <style>
    body {
        background-color: #fff8f8; /* Blanc rosé très clair */
        font-family: Arial, sans-serif;
        color: #5a2a27; /* Bordeaux brun pour le texte principal */
        margin: 0;
        padding: 40px;
    }

    h2 {
        text-align: center;
        color: #5a2a27; /* Bordeaux brun pour les titres */
    }

    .lang-switch {
        text-align: right;
        margin-bottom: 20px;
    }

    .lang-switch a {
        color: #4a1c1c; /* Bordeaux foncé pour les liens */
        text-decoration: none;
        margin: 0 5px;
        font-weight: bold;
    }

    .lang-switch a:hover {
        text-decoration: underline;
    }

    form {
        max-width: 600px;
        margin: auto;
        background-color: #ffffff;
        padding: 25px 30px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(90, 42, 39, 0.1);
    }

    label {
        display: block;
        margin-bottom: 5px;
        font-weight: 600;
        color: #5a2a27;
    }

    input[type="text"],
    input[type="file"],
    input[type="date"],
    select {
        width: 100%;
        padding: 10px;
        border: 1px solid #5a2a27; /* Bordeaux brun */
        border-radius: 6px;
        margin-bottom: 20px;
        background-color: #fff4f4; /* Fond très clair rosé */
        color: #4a1c1c;
    }

    button[type="submit"] {
        background-color: #5a2a27; /* Bordeaux brun pour le bouton */
        color: #ffffff;
        border: none;
        padding: 12px 20px;
        border-radius: 6px;
        font-size: 16px;
        cursor: pointer;
        width: 100%;
    }

    button[type="submit"]:hover {
        background-color: #3b1616; /* Bordeaux très foncé au survol */
    }

    .message {
        text-align: center;
        margin-bottom: 20px;
        padding: 10px;
        border-radius: 6px;
    }

    .message.success {
        background-color: #f3ffef; /* Fond vert très pâle */
        color: #3b662e; /* Vert foncé/brun naturel pour succès */
    }

    .message.error {
        background-color: #fff0f0; /* Fond rosé très pâle */
        color: #6b1212; /* Bordeaux brun foncé pour erreur */
    }

    p {
        max-width: 600px;
        margin: auto;
        margin-bottom: 20px;
        text-align: center;
        color: #4a1c1c; /* Bordeaux profond pour les paragraphes */
    }

    a {
        color: #4a1c1c; /* Bordeaux foncé pour les liens */
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
    <p><?= $message ?></p>
<?php endif; ?>

<?php if ($verification && $verification['statut'] !== 'rejetée'): ?>
    <p><?= htmlspecialchars($messages['verification_submitted']) ?></p>
<?php else: ?>
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
<?php endif; ?>

</body>
</html>
