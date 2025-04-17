<?php
session_start();
require_once '../backend/db.php'; // Connexion à la base de données
include 'header.php';
// Définir la langue
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$lang = $_SESSION['lang'] ?? 'fr';

// Traductions
$translations = [
    'fr' => [
        'access_denied' => "❌ Accès interdit",
        'no_listing' => "⚠️ Erreur : aucune annonce spécifiée.",
        'listing_not_found' => "⚠️ Erreur : annonce introuvable.",
        'sending_to' => "📩 Vous envoyez un message à",
        'message_sent' => "✅ Message envoyé !",
        'empty_message' => "⚠️ Le message ne peut pas être vide.",
        'send_message' => "📩 Envoyer un message",
        'write_message' => "✍️ Écrivez votre message...",
        'send' => "📤 Envoyer",
        'view_messages' => "📂 Voir mes messages",
        'language' => "🌍 Langue :",
        'french' => "🇫🇷 Français",
        'english' => "🇬🇧 English",
        'unread_messages' => "📨 Messages non lus",
        'message_read' => "✅ Lu"
    ],
    'en' => [
        'access_denied' => "❌ Access denied",
        'no_listing' => "⚠️ Error: No listing specified.",
        'listing_not_found' => "⚠️ Error: Listing not found.",
        'sending_to' => "📩 You are sending a message to",
        'message_sent' => "✅ Message sent!",
        'empty_message' => "⚠️ The message cannot be empty.",
        'send_message' => "📩 Send a message",
        'write_message' => "✍️ Write your message...",
        'send' => "📤 Send",
        'view_messages' => "📂 View my messages",
        'language' => "🌍 Language:",
        'french' => "🇫🇷 French",
        'english' => "🇬🇧 English",
        'unread_messages' => "📨 Unread messages",
        'message_read' => "✅ Read"
    ]
];

// Vérifier la connexion utilisateur
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    die($translations[$lang]['access_denied']);
}

// Vérifier si l'annonce est spécifiée dans l'URL
if (!isset($_GET['annonce_id']) || empty($_GET['annonce_id'])) {
    die($translations[$lang]['no_listing']);
}

$annonce_id = intval($_GET['annonce_id']); // Sécuriser l'ID

// 🔎 Récupérer le propriétaire de l'annonce et son `nom_utilisateur`
$stmt = $pdo->prepare("SELECT a.utilisateur_id, u.nom_utilisateur FROM annonces a 
                       JOIN users u ON a.utilisateur_id = u.id 
                       WHERE a.id = ?");
$stmt->execute([$annonce_id]);
$annonce = $stmt->fetch();

if (!$annonce) {
    die($translations[$lang]['listing_not_found']);
}

$destinataire_id = $annonce['utilisateur_id'];
$nom_destinataire = $annonce['nom_utilisateur'];
$expediteur_id = $_SESSION['user_id'];

// Récupérer le nombre de messages non lus
$stmt = $pdo->prepare("SELECT COUNT(*) FROM messages WHERE destinataire_id = ? AND lu = 0");
$stmt->execute([$expediteur_id]);
$messages_non_lus = $stmt->fetchColumn();

// Marquer les messages comme lus après affichage
$pdo->prepare("UPDATE messages SET lu = 1 WHERE destinataire_id = ? AND expediteur_id = ?")
    ->execute([$expediteur_id, $destinataire_id]);

// 🔄 Générer les liens de changement de langue tout en conservant l'annonce
$current_url = $_SERVER['REQUEST_URI'];
$current_url = preg_replace('/(\?|&)lang=[^&]*/', '', $current_url);
$separator = (strpos($current_url, '?') === false) ? '?' : '&';

$lang_fr_url = $current_url . $separator . "lang=fr&annonce_id=" . urlencode($annonce_id);
$lang_en_url = $current_url . $separator . "lang=en&annonce_id=" . urlencode($annonce_id);

// Traitement du message
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $contenu = trim($_POST['contenu']);

    if (!empty($contenu)) {
        $stmt = $pdo->prepare("INSERT INTO messages (expediteur_id, destinataire_id, contenu, date_envoi, lu) VALUES (?, ?, ?, NOW(), 0)");
        $stmt->execute([$expediteur_id, $destinataire_id, $contenu]);
        echo "<p class='success'>" . $translations[$lang]['message_sent'] . "</p>";
    } else {
        echo "<p class='error'>" . $translations[$lang]['empty_message'] . "</p>";
    }
}
?>

<div class="container">
    <div class="header">
        <h2><?= $translations[$lang]['send_message'] ?></h2>
        <div class="language-switcher">
            <a href="<?= $lang_fr_url ?>" class="btn"><?= $translations[$lang]['french'] ?></a>
            <a href="<?= $lang_en_url ?>" class="btn"><?= $translations[$lang]['english'] ?></a>
        </div>
    </div>

    <p class="recipient"><?= $translations[$lang]['sending_to'] ?> <b><?= $nom_destinataire ?></b></p>

    <form method="post" class="message-form">
        <textarea name="contenu" placeholder="<?= $translations[$lang]['write_message'] ?>" required></textarea>
        <button type="submit"><?= $translations[$lang]['send'] ?></button>
    </form>

    <div class="message-status">
        <a href="messagerie.php?annonce_id=<?= urlencode($annonce_id) ?>" class="btn">
            <?= $translations[$lang]['view_messages'] ?>
            <?php if ($messages_non_lus > 0): ?>
                <span class="badge"><?= $messages_non_lus ?></span>
            <?php endif; ?>
        </a>
    </div>
</div>

<style>
body {
    background-color: #ffffff;
    color: #4b1e1e; /* Texte principal en bordeaux brun foncé */
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 20px;
}

.container {
    background-color: #ffffff; /* Fond blanc */
    border: 2px solid #4b1e1e; /* Bordure bordeaux brun */
    max-width: 700px;
    margin: 0 auto;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 0 10px #aaaaaa;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 2px solid #4b1e1e; /* Bordure bordeaux brun */
    padding-bottom: 15px;
    margin-bottom: 20px;
}

h2 {
    color: #4b1e1e; /* Titre en bordeaux brun */
    margin: 0;
}

.language-switcher .btn {
    background-color: #4b1e1e; /* Bouton bordeaux brun */
    color: #ffffff;
    padding: 8px 16px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: bold;
    margin-left: 8px;
    border: 1px solid #4b1e1e; /* Bordure bordeaux brun */
    transition: background-color 0.3s;
}

.language-switcher .btn:hover {
    background-color: #3a1414; /* Survol plus foncé */
}

.recipient {
    background-color: #f5f5f5;
    padding: 10px;
    border-left: 4px solid #4b1e1e; /* Bordure en bordeaux brun */
    font-size: 16px;
    margin-bottom: 20px;
}

form.message-form {
    display: flex;
    flex-direction: column;
}

form.message-form textarea {
    height: 120px;
    padding: 12px;
    border: 2px solid #4b1e1e; /* Bordure en bordeaux brun */
    border-radius: 6px;
    resize: vertical;
    background-color: #ffffff;
    color: #4b1e1e; /* Texte en bordeaux brun */
    font-size: 15px;
    margin-bottom: 15px;
}

form.message-form button {
    background-color: #4b1e1e; /* Bouton en bordeaux brun */
    color: white;
    border: none;
    padding: 12px;
    border-radius: 6px;
    font-size: 15px;
    cursor: pointer;
    transition: background-color 0.3s;
}

form.message-form button:hover {
    background-color: #3a1414; /* Survol bordeaux brun foncé */
}

.message-status {
    margin-top: 20px;
    text-align: center;
}

.message-status .btn {
    background-color: #4b1e1e; /* Bouton bordeaux brun */
    color: #ffffff;
    padding: 10px 18px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: bold;
    display: inline-block;
    border: 1px solid #4b1e1e; /* Bordure bordeaux brun */
}

.message-status .btn:hover {
    background-color: #3a1414; /* Survol bordeaux brun foncé */
}

.badge {
    background-color: #4b1e1e; /* Badge bordeaux brun */
    color: #ffffff;
    font-weight: bold;
    padding: 4px 8px;
    border-radius: 50%;
    margin-left: 6px;
    font-size: 13px;
}

.success {
    background-color: #f0fff0;
    color: #4b1e1e; /* Texte en bordeaux brun */
    padding: 10px;
    border-left: 4px solid rgb(76, 175, 80); /* Bordure verte pour succès */
    margin-bottom: 20px;
}

.error {
    background-color: #fff0f0;
    color: #4b1e1e; /* Texte en bordeaux brun */
    padding: 10px;
    border-left: 4px solid rgb(244, 67, 54); /* Bordure rouge pour erreur */
    margin-bottom: 20px;
}
</style>

