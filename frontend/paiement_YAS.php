<?php   
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}

require_once '../backend/db.php';

$lang = $_GET['lang'] ?? 'fr';

$traductions = [
    'fr' => [
        'title' => "Paiement par YAS ",
        'summary' => "Résumé de l’abonnement",
        'feature' => "Fonctionnalités incluses",
        'price' => "Prix",
        'currency' => "Devise",
        'moov_number' => "Numéro YAS pour le dépôt",
        'enter_reference' => "Référence YAS (reçue par SMS)",
        'upload_proof' => "Télécharger une preuve de paiement (capture d'écran)",
        'pay_now' => "Valider le paiement",
        'back' => "Retour",
        'error_reference' => "La référence est requise.",
        'error_file' => "Veuillez télécharger une preuve de paiement.",
        'success' => "Paiement envoyé, en attente de validation.",
        'language' => "Langue"
    ],
    'en' => [
        'title' => "Payment via YAS",
        'summary' => "Subscription Summary",
        'feature' => "Included Features",
        'price' => "Price",
        'currency' => "Currency",
        'moov_number' => "YAS number for deposit",
        'enter_reference' => "YAS Reference (SMS received)",
        'upload_proof' => "Upload payment proof (screenshot)",
        'pay_now' => "Confirm Payment",
        'back' => "Back",
        'error_reference' => "Reference is required.",
        'error_file' => "Please upload a payment proof.",
        'success' => "Payment sent, awaiting confirmation.",
        'language' => "Language"
    ]
];


$t = $traductions[$lang] ?? $traductions['fr'];
$user_id = $_SESSION['user_id'];

$query = $pdo->prepare("SELECT monnaie FROM users WHERE id = :id");
$query->execute(['id' => $user_id]);
$user = $query->fetch(PDO::FETCH_ASSOC);
$monnaie_utilisateur = $user['monnaie'] ?? 'XOF';

$taux_conversion = [ 
"XOF" => 1, "XAF" => 1, "DZD" => 0.22, "EGP" => 0.050, "MAD" => 0.017,
"TND" => 0.0048, "NGN" => 1.4, "GHS" => 0.015, "KES" => 0.27, "UGX" => 2.8,
"TZS" => 2.6, "ETB" => 0.089, "ZAR" => 0.031, "LYD" => 0.0071, "SDG" => 0.97,
"BWP" => 0.022, "MWK" => 0.90, "MZN" => 0.11, "NAD" => 0.031, "SCR" => 0.021,
"SLL" => 174, "SOS" => 0.92, "SZL" => 0.031, "CDF" => 4.5, "BIF" => 4.4,
"DJF" => 0.29, "GNF" => 143, "ERN" => 0.096, "LSL" => 0.031, "MGA" => 9.3,
"MRU" => 0.057, "RWF" => 1.8, "ZMW" => 0.12, "AOA" => 1.2
]; // (Garde le même tableau des taux que dans le fichier Moov

$prix_xof = 5500;
$taux = $taux_conversion[$monnaie_utilisateur] ?? 1;
$prix_converti = round($prix_xof * $taux, 2);

$error = ''; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reference = trim($_POST['reference'] ?? '');
    $preuve = $_FILES['preuve'] ?? null;

    if (empty($reference)) {
        $error = $t['error_reference'];
    } elseif (!$preuve || $preuve['error'] !== 0) {
        $error = $t['error_file'];
    } else {
        $methode = 'YAS';
        $upload_dir = '../uploads/preuves/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $filename = uniqid() . '_' . basename($preuve['name']);
        $target_file = $upload_dir . $filename;

        if (move_uploaded_file($preuve['tmp_name'], $target_file)) {
            $stmt = $pdo->prepare("INSERT INTO paiements (user_id, methode, reference, preuve_path, statut) VALUES (?, ?, ?, ?, 'en attente')");
            $stmt->execute([$user_id, $methode, $reference, $filename]);

            $date_activation = date('Y-m-d H:i:s');
            $date_expiration = date('Y-m-d H:i:s', strtotime('+30 days'));
            $duree = 30;

            $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM abonnements WHERE user_id = ?");
            $stmt_check->execute([$user_id]);
            $existe = $stmt_check->fetchColumn();

            if ($existe) {
                $stmt_update = $pdo->prepare("UPDATE abonnements SET date_activation = ?, date_expiration = ?, duree = ? WHERE user_id = ?");
                $stmt_update->execute([$date_activation, $date_expiration, $duree, $user_id]);
            } else {
                $stmt_insert = $pdo->prepare("INSERT INTO abonnements (user_id, date_activation, date_expiration, duree) VALUES (?, ?, ?, ?)");
                $stmt_insert->execute([$user_id, $date_activation, $date_expiration, $duree]);
            }

            $_SESSION['message'] = $t['success'];
            header("Location: confirmation.php");
            exit();
        }
    }
}

// Ensuite, tu ajoutes la partie HTML comme dans le fichier Moov avec la variable $t utilisée.
?>


<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $t['title'] ?></title>
    <style>
    body {
        font-family: Arial, sans-serif;
        padding: 40px;
        background-color: #ffffff; /* Blanc */
    }

    h1 {
        color: #4a1c1c; /* Bordeaux brun foncé */
    }

    form {
        background-color: #ffffff; /* Blanc */
        padding: 30px;
        border-radius: 10px;
        max-width: 600px;
        margin: auto;
        box-shadow: 0 0 10px rgba(74, 28, 28, 0.1); /* Ombre bordeaux brun */
    }

    input[type="text"], input[type="file"] {
        width: 100%;
        padding: 10px;
        margin: 10px 0 20px 0;
    }

    button {
        background-color: #5c1e1e; /* Bordeaux brun */
        color: white;
        padding: 10px 25px;
        border: none;
        cursor: pointer;
        border-radius: 5px;
    }

    .lang-switch {
        text-align: right;
        margin-bottom: 20px;
    }

    .error {
        color: red;
    }

    .summary {
        margin-bottom: 20px;
    }

    .btn-verifier-identite {
        display: block;
        text-align: center;
        margin: 30px auto;
        background: #3d1a1a; /* Bordeaux brun foncé */
        color: #ffffff;
        padding: 10px 20px;
        border-radius: 5px;
        text-decoration: none;
        width: fit-content;
        font-weight: bold;
        transition: background 0.3s;
    }

    .btn-verifier-identite:hover {
        background: #2a1010; /* Bordeaux brun encore plus sombre */
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

<div class="language-switch">
  <a href="?lang=fr" class="lang-btn <?= ($_GET['lang'] ?? 'fr') === 'fr' ? 'active' : '' ?>">FR Français</a>
  <a href="?lang=en" class="lang-btn <?= ($_GET['lang'] ?? '') === 'en' ? 'active' : '' ?>">GB English</a>
</div>

<h2><?= $t['summary'] ?></h2>

<ul>
    <li>✔️ Accès complet à toutes les fonctionnalités</li>
    <li>✔️ Nombre illimité d’annonces</li>
    <li>✔️ Mise en avant de vos services</li>
</ul>

<p><strong><?= $t['price'] ?> :</strong> <?= $prix_converti ?> <?= $monnaie_utilisateur ?></p>

<hr>

<form method="post" enctype="multipart/form-data">
    <div class="form-group">
        <label><?= $t['moov_number'] ?> :</label>
        <p><strong>+228 71531764</strong></p> <!-- Remplace avec ton vrai numéro Moov -->
    </div>

    <div class="form-group">
        <label for="reference"><?= $t['enter_reference'] ?> :</label>
        <input type="text" name="reference" id="reference" required>
    </div>

    <div class="form-group">
        <label for="preuve"><?= $t['upload_proof'] ?> :</label>
        <input type="file" name="preuve" id="preuve" accept="image/*" required>
    </div>

    <?php if (!empty($error)): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <button class="btn" type="submit"><?= $t['pay_now'] ?></button>
</form>
<a href="abonnements.php" class="btn-verifier-identite">Back/Retour</a>

</body>
</html>