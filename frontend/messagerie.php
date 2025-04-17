<?php  
session_start();
ob_start(); // Active la mise en mémoire tampon pour éviter les erreurs d'affichage
require_once '../backend/db.php'; // Connexion à la base de données
include 'header.php';
// Vérification de l'authentification
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    die("❌ Accès interdit");
}

$user_id = $_SESSION['user_id'];
$lang = $_SESSION['lang'] ?? 'fr';

// 🔹 Gérer le changement de langue
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
    header("Location: messagerie.php");
    exit();
}

// 🔹 Traductions
$translations = [
    'fr' => ['messages' => 'Messages', 'envoyer' => 'Envoyer', 'aucune_conversation' => 'Aucune conversation', 'ecrire_message' => 'Écrire un message...', 'lu' => 'Lu','retour' => 'Retour aux annonces'],
    'en' => ['messages' => 'Messages', 'envoyer' => 'Send', 'aucune_conversation' => 'No conversations', 'ecrire_message' => 'Write a message...', 'lu' => 'Read','retour' => 'Back to listings'],
];



// Initialisation des variables
$contact_id = isset($_GET['contact_id']) ? intval($_GET['contact_id']) : null;
$messages = [];
$conversations = [];

// 🔹 Récupérer la liste des conversations
$query = "
    SELECT u.id, COALESCE(u.entreprise_nom, u.nom_utilisateur) AS nom_utilisateur,
           (SELECT COUNT(*) FROM messages WHERE expediteur_id = u.id AND destinataire_id = :user_id AND lu = FALSE) AS non_lus
    FROM users u
    JOIN messages m ON (m.expediteur_id = u.id OR m.destinataire_id = u.id)
    WHERE (m.expediteur_id = :user_id OR m.destinataire_id = :user_id)
      AND u.id != :user_id
    GROUP BY u.id, u.nom_utilisateur
    ORDER BY MAX(m.date_envoi) DESC
";
$stmt = $pdo->prepare($query);
$stmt->execute(['user_id' => $user_id]);
$conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 🔹 Récupération des messages si un contact est sélectionné
if ($contact_id) {
    $query = "
        SELECT m.*, 
               COALESCE(u.entreprise_nom, u.nom_utilisateur) AS expediteur_nom
        FROM messages m
        JOIN users u ON m.expediteur_id = u.id
        WHERE (m.expediteur_id = :user_id AND m.destinataire_id = :contact_id)
           OR (m.expediteur_id = :contact_id AND m.destinataire_id = :user_id)
        ORDER BY m.date_envoi ASC
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['user_id' => $user_id, 'contact_id' => $contact_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Marquer les messages comme lus
    $stmt = $pdo->prepare("UPDATE messages SET lu = TRUE WHERE destinataire_id = :user_id AND expediteur_id = :contact_id AND lu = FALSE");
    $stmt->execute(['user_id' => $user_id, 'contact_id' => $contact_id]);
}

// 🔹 Envoi d'un message
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['contenu']) && $contact_id) {
    $contenu = trim($_POST['contenu']);
    if (!empty($contenu)) {
        $stmt = $pdo->prepare("INSERT INTO messages (expediteur_id, destinataire_id, contenu, date_envoi, lu) VALUES (?, ?, ?, NOW(), FALSE)");
        $stmt->execute([$user_id, $contact_id, $contenu]);
        header("Location: messagerie.php?contact_id=$contact_id");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $translations[$lang]['messages'] ?></title>
    <style>
body {
    font-family: Arial, sans-serif;
    background-color: #ffffff;
    color: #4a1c1c; /* Bordeaux foncé */
    margin: 0;
}

.container {
    display: flex;
    width: 80%;
    margin: auto;
    height: 90vh;
    background-color: #fffafa; /* Blanc léger */
    border: 2px solid #5a2a27;
    box-shadow: 0 0 10px #5a2a27;
}

.contacts {
    width: 30%;
    background: #5a2a27; /* Bordeaux brun */
    padding: 20px;
    overflow-y: auto;
    border-right: 2px solid #3e1919;
    color: #ffffff;
}

.contacts h3 {
    margin-bottom: 20px;
    color: #ffffff;
}

.contacts ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.contacts li {
    margin-bottom: 10px;
}

.contacts a {
    text-decoration: none;
    color: #ffffff;
    padding: 8px 12px;
    display: block;
    border-radius: 6px;
    background-color: #8b3a48; /* Bordeaux clair */
    transition: background 0.3s;
}

.contacts a:hover {
    background-color: #3e1919;
    color: #ffffff;
}

.chat {
    width: 70%;
    display: flex;
    flex-direction: column;
    background-color: #ffffff;
}

.chat-box {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
    background: #fffafa;
    border-bottom: 2px solid #5a2a27;
}

.chat-message {
    padding: 10px 14px;
    border-radius: 10px;
    margin-bottom: 10px;
    max-width: 80%;
    font-size: 15px;
    line-height: 1.5;
    word-wrap: break-word;
}

.sent {
    background-color: #8b3a48; /* Bordeaux clair */
    color: #ffffff;
    align-self: flex-end;
    text-align: right;
}

.received {
    background-color: #fce9ec; /* Bordeaux très très clair */
    color: #5a2a27;
    align-self: flex-start;
    text-align: left;
}

.chat-footer {
    display: flex;
    padding: 12px;
    background: #fcf1f3;
    border-top: 2px solid #5a2a27;
}

textarea {
    flex: 1;
    border: 1px solid #8b3a48;
    padding: 10px;
    border-radius: 5px;
    background-color: #ffffff;
    color: #5a2a27;
    resize: none;
    font-size: 14px;
}

button {
    background: #5a2a27;
    color: #ffffff;
    border: none;
    padding: 10px 15px;
    margin-left: 10px;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.3s;
}

button:hover {
    background: #3e1919;
}

.lang-switch {
    top: 10px;
    right: 20px;
    font-size: 20px;
}

.lang-switch a {
    color: #5a2a27;
    text-decoration: none;
    margin-left: 10px;
}

.lang-switch a:hover {
    text-decoration: underline;
    color: #8b3a48;
}

.unread {
    color: #b30000;
    font-weight: bold;
}

/* 🔹 Bouton retour aux annonces */
.retour {
    display: inline-block;
    margin: 15px 20px;
    padding: 10px 15px;
    background-color: #5a2a27;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    transition: background 0.3s;
}

.retour:hover {
    background-color: #3e1919;
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
<div class="language-switch">
  <a href="?lang=fr" class="lang-btn <?= ($_GET['lang'] ?? 'fr') === 'fr' ? 'active' : '' ?>">FR Français</a>
  <a href="?lang=en" class="lang-btn <?= ($_GET['lang'] ?? '') === 'en' ? 'active' : '' ?>">GB English</a>
</div>

<body>


<div class="container">
    <!-- 🔹 Liste des conversations -->
    <div class="contacts">
        <h3>📨 <?= $translations[$lang]['messages'] ?></h3>
        <?php if (!empty($conversations)): ?>
            <ul>
                <?php foreach ($conversations as $conv): ?>
                    <li>
                        <a href="messagerie.php?contact_id=<?= $conv['id'] ?>">
                            <?= htmlspecialchars($conv['nom_utilisateur']) ?> 
                            <?php if (!empty($conv['non_lus'])): ?>
                                <span class="unread">(<?= $conv['non_lus'] ?>)</span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p><?= $translations[$lang]['aucune_conversation'] ?></p>
        <?php endif; ?>
    </div>

    <!-- 🔹 Boîte de chat -->
    <div class="chat">
        <div class="chat-box">
            <?php if (!empty($messages)): ?>
                <?php foreach ($messages as $msg): ?>
                    <div class="chat-message <?= ($msg['expediteur_id'] == $user_id) ? 'sent' : 'received' ?>">
                        <b><?= htmlspecialchars($msg['expediteur_nom']) ?></b>: <?= nl2br(htmlspecialchars($msg['contenu'])) ?>
                        <br><small>🕒 <?= $msg['date_envoi'] ?> <?= $msg['lu'] ? '✅ ' . $translations[$lang]['lu'] : '' ?></small>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p><?= $translations[$lang]['aucune_conversation'] ?></p>
            <?php endif; ?>
        </div>


        <!-- 🔹 Zone de saisie pour répondre -->
        <form method="POST" class="chat-footer">
            <textarea name="contenu" placeholder="<?= $translations[$lang]['ecrire_message'] ?>" required></textarea>
            <button type="submit"><?= $translations[$lang]['envoyer'] ?></button>
        </form>
    </div>
</div>

<a href="annonces.php" class="retour"><?= $translations[$lang]['retour'] ?></a>

</body>
</html>






