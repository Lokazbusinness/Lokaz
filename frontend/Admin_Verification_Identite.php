<?php 
session_start();
require_once '../backend/db.php';

// Sécurité : accès uniquement pour l'admin
$admin_email = "lokaz.business@gmail.com";
if (!isset($_SESSION['email']) || $_SESSION['email'] !== $admin_email) {
    die("Accès refusé.");
}

// Traitement action (valider ou rejeter)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $verif_id = $_POST['verif_id'];
    $action = $_POST['action']; // 'valider' ou 'rejeter'

    // Récupération de l'utilisateur concerné
    $stmt = $pdo->prepare("SELECT user_id FROM verifications_identite WHERE id = ?");
    $stmt->execute([$verif_id]);
    $user_id = $stmt->fetchColumn();

    if ($user_id) {
        if ($action === 'valider') {
            $statut = 'valide';
            $identite_verifiee = 1;
            $contenu = "Votre identité a été vérifiée avec succès. / Your identity has been successfully verified.";
        } else {
            $statut = 'rejete';
            $identite_verifiee = 0;
            $contenu = "Votre vérification d'identité a été rejetée. Vous pouvez la resoumettre en accédant à la page de vérification d'identité dans votre espace . 
                       / Your identity verification has been rejected. You can resubmit it by accessing the identity verification page in your space. ";
        }

        // Mise à jour du statut de la vérification
        $stmt = $pdo->prepare("UPDATE verifications_identite SET statut = ? WHERE id = ?");
        $stmt->execute([$statut, $verif_id]);

        // Mise à jour de la table users
        $stmt = $pdo->prepare("UPDATE users SET identite_verifiee = ? WHERE id = ?");
        $stmt->execute([$identite_verifiee, $user_id]);

        // Envoi d'un message à l'utilisateur
        $stmt = $pdo->prepare("INSERT INTO messages (expediteur_id, destinataire_id, contenu, date_envoi) VALUES (?, ?, ?, NOW())");
        $stmt->execute([6, $user_id, $contenu]); // 0 = admin système
    }
}

// Recherche des demandes de vérification
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Récupération des demandes de vérification avec recherche
// Récupération des demandes de vérification avec affichage conditionnel du nom
$query = "
    SELECT 
        v.*, 
        u.email, 
        u.nom_complet, 
        u.entreprise_nom,
        CASE 
            WHEN u.entreprise_nom IS NOT NULL AND u.entreprise_nom != '' THEN u.entreprise_nom 
            ELSE u.nom_complet 
        END AS nom_affiche
    FROM verifications_identite v 
    JOIN users u ON v.user_id = u.id 
    ORDER BY v.date_soumission DESC
";

$stmt = $pdo->prepare($query);
$stmt->execute();
$verifications = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Vérifications d'identité</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ccc; padding: 10px; }
        th { background-color: #f2f2f2; }
        img { max-width: 100px; max-height: 100px; }
        .btn { padding: 6px 10px; border: none; cursor: pointer; }
        .valider { background-color: green; color: white; }
        .rejeter { background-color: red; color: white; }
        .search-bar { margin-bottom: 20px; }
    </style>
</head>
<body>

<h2>Demandes de vérification d'identité</h2>

<!-- Barre de recherche -->
<form method="GET" class="search-bar">
    <input type="text" name="search" placeholder="Rechercher par nom ou email" value="<?= htmlspecialchars($search) ?>">
    <button type="submit">Rechercher</button>
</form>

<table>
    <tr>
        <th>Utilisateur</th>
        <th>Email</th>
        <th>Photo</th>
        <th>Document</th>
        <th>Infos</th>
        <th>Date de Soumission</th>
        <th>Statut</th>
        <th>Action</th>
    </tr>
    <?php foreach ($verifications as $verif): ?>
        <tr>
        <td>
                <?php 
                // Afficher le nom en fonction de la présence de "entreprise_nom"
                if (!empty($verif['entreprise_nom'])) {
                    echo htmlspecialchars($verif['entreprise_nom']);
                } else {
                    echo htmlspecialchars($verif['nom_complet']);
                }
                ?>
            </td>
            <td><?= htmlspecialchars($verif['email']) ?></td>
            <td>
                <a href="<?= $verif['photo_utilisateur'] ?>" target="_blank">
                    <img src="<?= $verif['photo_utilisateur'] ?>">
                </a>
            </td>
            <td>
                <a href="<?= $verif['document_identite'] ?>" target="_blank">
                    <img src="<?= $verif['document_identite'] ?>">
                </a>
            </td>
            <td>
                <strong>Nom :</strong> <?= htmlspecialchars($verif['nom']) ?><br>
                <strong>Prénom :</strong> <?= htmlspecialchars($verif['prenom']) ?><br>
                <strong>Nationalité :</strong> <?= htmlspecialchars($verif['nationalite']) ?><br>
                <strong>Pays de résidence :</strong> <?= htmlspecialchars($verif['pays_residence']) ?><br>
                <strong>Date de naissance :</strong> <?= htmlspecialchars($verif['date_naissance']) ?><br>
                <strong>Sexe :</strong> <?= htmlspecialchars($verif['sexe']) ?><br>
                <strong>Lieu de naissance :</strong> <?= htmlspecialchars($verif['lieu_naissance']) ?>
            </td>
            <td><?= htmlspecialchars($verif['date_soumission']) ?></td>
            <td><?= htmlspecialchars($verif['statut']) ?></td>
            <td>
                <form method="POST" style="display:inline-block">
                    <input type="hidden" name="verif_id" value="<?= $verif['id'] ?>">
                    <input type="hidden" name="action" value="valider">
                    <button class="btn valider" type="submit">Valider</button>
                </form>
                <form method="POST" style="display:inline-block">
                    <input type="hidden" name="verif_id" value="<?= $verif['id'] ?>">
                    <input type="hidden" name="action" value="rejeter">
                    <button class="btn rejeter" type="submit">Rejeter</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
