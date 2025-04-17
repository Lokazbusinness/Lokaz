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
        'title' => "Paiement par Carte Bancaire",
        'summary' => "Résumé de l’abonnement",
        'feature' => "Fonctionnalités incluses",
        'price' => "Prix",
        'currency' => "Devise",
        'bank_info' => "Numéro de compte pour le dépôt",
        'enter_reference' => "Référence bancaire (reçue après dépôt)",
        'upload_proof' => "Télécharger une preuve de paiement (capture d'écran)",
        'pay_now' => "Valider le paiement",
        'back' => "Retour",
        'error_reference' => "La référence est requise.",
        'error_file' => "Veuillez télécharger une preuve de paiement.",
        'success' => "Paiement envoyé, en attente de validation.",
        'language' => "Langue"
    ],
    'en' => [
        'title' => "Bank Transfer Payment",
        'summary' => "Subscription Summary",
        'feature' => "Included Features",
        'price' => "Price",
        'currency' => "Currency",
        'bank_info' => "Bank account number for deposit",
        'enter_reference' => "Bank reference (received after deposit)",
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
];

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
        $methode = 'carte_bancaire';
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
            background-color: #f5f5f5;
        }

        h1 {
            color:rgb(18, 17, 19);
        }

        form {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            max-width: 600px;
            margin: auto;
            box-shadow: 0 0 10px rgba(92, 15, 31, 0.1);
        }

        input[type="text"], input[type="file"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0 20px 0;
        }

        button {
            background-color:rgb(68, 22, 37);
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


<h1><?= $t['title'] ?></h1>

<?php if ($error): ?>
    <p class="error"><?= $error ?></p>
<?php endif; ?>

<div class="summary">
    <p><strong><?= $t['summary'] ?> :</strong></p>
    <ul>
        <li><?= $t['feature'] ?>:✔️ Accès complet à toutes les fonctionnalités</li>
                                 ✔️ Nombre illimité d’annonces</li>
                                 ✔️ Mise en avant de vos services </li>
        <li><?= $t['price'] ?>: <?= $prix_converti ?> <?= $monnaie_utilisateur ?></li>
    </ul>
</div>

<p><strong><?= $t['bank_info'] ?>:</strong><br>
<strong>IBAN :</strong> TG0700100012345678901234567<br>
<strong>Nom :</strong> Mlle Votre Nom</p>

<form method="POST" enctype="multipart/form-data">
    <label><?= $t['enter_reference'] ?>:</label>
    <input type="text" name="reference" required>

    <label><?= $t['upload_proof'] ?>:</label>
    <input type="file" name="preuve" accept="image/*" required>

    <button type="submit"><?= $t['pay_now'] ?></button>
</form>

<br>
<a href="abonnements.php"><?= $t['back'] ?></a>

</body>
</html>
