<?php  
session_start();
require_once '../backend/db.php';

// Sécurité : seul toi as accès
$admin_email = "lokaz.business@gmail.com"; 
if (!isset($_SESSION['email']) || $_SESSION['email'] !== $admin_email) {
    die("Accès refusé.");
}

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paiement_id = $_POST['paiement_id'];
    $action = $_POST['action'];

    if ($action === 'valider') {
        $statut = 'validé';
        $abonnement_actif = 1;
    } elseif ($action === 'rejeter') {
        $statut = 'rejeté';
        $abonnement_actif = 0;
    }

    $stmt = $pdo->prepare("UPDATE paiements SET statut = ? WHERE id = ?");
    $stmt->execute([$statut, $paiement_id]);

    $stmt = $pdo->prepare("SELECT user_id FROM paiements WHERE id = ?");
    $stmt->execute([$paiement_id]);
    $user_id = $stmt->fetchColumn();

    if ($user_id) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM abonnements WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $row_count = $stmt->fetchColumn();

        if ($row_count > 0) {
            $stmt = $pdo->prepare("UPDATE abonnements SET abonnement_actif = ? WHERE user_id = ?");
            $stmt->execute([$abonnement_actif, $user_id]);
        }

        if ($statut === 'validé') {
            $date_activation = date('Y-m-d H:i:s');
            $date_expiration = date('Y-m-d H:i:s', strtotime('+30 days'));
            $duree = 30;

            $stmt = $pdo->prepare("UPDATE abonnements SET abonnement_actif = ?, date_activation = ?, date_expiration = ?, duree = ? WHERE user_id = ?");
            $stmt->execute([$abonnement_actif, $date_activation, $date_expiration, $duree, $user_id]);

            $contenu_message = "salut, votre paiement a été validé avec succès. Votre abonnement est maintenant actif jusqu'au $date_expiration.
                                pour nous contacter envoyez un message à l'adresse de notre service client lokaz.business@gmail.com ou envoyez nous un message via ce chat. /
                                Hi, your payment has been successfully validated. Your subscription is now active until $date_expiration.
                                to contact us, send an e-mail to our customer service address lokaz.business@gmail.com or contact us via this chat.";
        } else {
            $contenu_message = "salut, malheureusement votre paiement a été rejeté. Veuillez vérifier si le paiement a bien été effectué sur le numéro mobile ou numéro compte bancaire  fourni sur le site.
                                En cas d'erreur, contacter notre service client lokaz.business@gmail.com ou envoyez nous un message via ce chat. /
                                Hi, unfortunately your payment has been rejected. Please check if the payment has been made on the mobile number or bank account number provided on the site.
                                In case of error, contact our customer service lokaz.business@gmail.com or send us a message via this chat.";
        }

        $admin_id = 6;
        $stmt = $pdo->prepare("INSERT INTO messages (expediteur_id, destinataire_id, contenu, date_envoi, lu) VALUES (?, ?, ?, NOW(), 0)");
        $stmt->execute([$admin_id, $user_id, $contenu_message]);
    }
}

// Récupérer tous les paiements avec info utilisateur
$requete = $pdo->query("
    SELECT 
        p.*, 
        CASE 
            WHEN u.entreprise_nom IS NOT NULL AND u.entreprise_nom != '' THEN u.entreprise_nom 
            ELSE u.nom_complet 
        END AS nom_affiche
    FROM paiements p
    JOIN users u ON p.user_id = u.id
    ORDER BY p.created_at DESC
");

$paiements = $requete->fetchAll();

// Grouper les paiements par mois
$paiements_par_mois = [];

foreach ($paiements as $paiement) {
    $mois = date('F Y', strtotime($paiement['created_at'])); // Ex : "April 2025"
    if (!isset($paiements_par_mois[$mois])) {
        $paiements_par_mois[$mois] = [];
    }
    $paiements_par_mois[$mois][] = $paiement;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Paiements</title>
    <style>
        body { font-family: Arial; margin: 30px; }
        table { border-collapse: collapse; width: 100%; margin-top: 10px; margin-bottom: 40px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        img { max-width: 100px; max-height: 100px; }
        .btn { padding: 8px 12px; border: none; border-radius: 5px; cursor: pointer; }
        .valider { background-color: green; color: white; }
        .rejeter { background-color: red; color: white; }
        .deconnexion { background-color: #333; color: white; text-decoration: none; padding: 8px 12px; border-radius: 5px; float: right; }
        #searchInput { padding: 10px; width: 100%; margin-bottom: 20px; border: 1px solid #ccc; border-radius: 5px; }
        h2.mois { margin-top: 60px; color: #333; }
    </style>
</head>
<body>

<a href="admin_deconnexion.php" class="deconnexion">Déconnexion</a>
<h1>Paiements (par mois)</h1>

<input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Rechercher un utilisateur, une référence, un statut...">

<?php foreach ($paiements_par_mois as $mois => $paiements_du_mois): ?>
    <h2 class="mois"><?= htmlspecialchars($mois) ?></h2>
    <table class="paiementTable">
        <thead>
            <tr>
                <th>Utilisateur</th>
                <th>Méthode</th>
                <th>Référence / Hash</th>
                <th>Preuve</th>
                <th>Statut</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($paiements_du_mois as $paiement): ?>
                <tr>
                    <td><?= htmlspecialchars($paiement['nom_affiche']) ?></td>
                    <td><?= htmlspecialchars($paiement['methode']) ?></td>
                    <td><?= htmlspecialchars($paiement['reference']) ?></td>
                    <td>
                        <?php if (!empty($paiement['preuve'])): ?>
                            <img src="../<?= htmlspecialchars($paiement['preuve']) ?>" alt="Preuve">
                        <?php else: ?>
                            Aucune
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($paiement['statut']) ?></td>
                    <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($paiement['created_at']))) ?></td>
                    <td>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="paiement_id" value="<?= $paiement['id'] ?>">
                            <button type="submit" name="action" value="valider" class="btn valider">Valider</button>
                            <button type="submit" name="action" value="rejeter" class="btn rejeter">Rejeter</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endforeach; ?>

<script>
function searchTable() {
    const input = document.getElementById("searchInput");
    const filter = input.value.toLowerCase();
    const tables = document.querySelectorAll(".paiementTable");

    tables.forEach(table => {
        const rows = table.querySelectorAll("tbody tr");
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(filter) ? "" : "none";
        });
    });
}
</script>

</body>
</html>






