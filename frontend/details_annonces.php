<?php
session_start();

if (!isset($_SESSION['utilisateur_id'])) {
    header("Location: connexion.php");
    exit();
}

require_once '../backend/db.php';
include 'header.php';

$utilisateur_id = $_SESSION['utilisateur_id'];
$utilisateur_nom = $_SESSION['user_name'] ?? 'Utilisateur';
$utilisateur_type = $_SESSION['user_type'] ?? 'inconnu';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID d'annonce non spécifié !");
}

$annonce_id = (int) $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM annonces WHERE id = :id");
$stmt->execute([':id' => $annonce_id]);
$annonce = $stmt->fetch(PDO::FETCH_ASSOC);

if ($annonce) {
    $utilisateur_id_annonce = $annonce['utilisateur_id'];

    $stmt = $pdo->prepare("SELECT type FROM users WHERE id = :id");
    $stmt->execute([':id' => $utilisateur_id_annonce]);
    $utilisateur_data = $stmt->fetch(PDO::FETCH_ASSOC);

    $utilisateur_type_annonce = $utilisateur_data['type'];

    $stmt = $pdo->prepare("SELECT id FROM users WHERE id = :id AND type = :type");
    $stmt->execute([':id' => $utilisateur_id_annonce, ':type' => $utilisateur_type_annonce]);
    $user_data = $stmt->fetch();
    $user_id = $user_data['id'];

    if ($annonce['utilisateur_id'] != $utilisateur_id) {
        $stmt = $pdo->prepare("INSERT INTO vues_annonces (annonce_id, utilisateur_id) VALUES (:annonce_id, :utilisateur_id)");
        $stmt->execute([':annonce_id' => $annonce_id, ':utilisateur_id' => $utilisateur_id]);
    }
}

$language = isset($_GET['lang']) ? $_GET['lang'] : 'fr';

$translations = [
    'fr' => [
        'title' => "Détails de l'annonce",
        'price' => 'Prix',
        'category' => 'Catégorie',
        'location' => 'Lieu',
        'description' => 'Description',
        'published_on' => 'Publié le',
        'back' => 'Retour aux annonces',
        'Laisser_avis'=>'donner un avis',
        'contacter_loueur' => 'Contacter le loueur',
        'comments_title' => 'Avis des utilisateurs'
    ],
    'en' => [
        'title' => 'Listing Details',
        'price' => 'Price',
        'category' => 'Category',
        'location' => 'Location',
        'description' => 'Description',
        'published_on' => 'Published on',
        'back' => 'Back to Listings',
        'Laisser_avis'=>'leave a review',
        'contacter_loueur' => 'Contact the renter',
        'comments_title' => 'User Reviews'
    ],
];

$t = $translations[$language];

$sql = "SELECT * FROM annonces WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$annonce_id]);
$annonce = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$annonce) {
    die("Annonce introuvable.");
}

$sql = "SELECT avis.commentaire, avis.note 
        FROM avis 
        JOIN users ON avis.utilisateur_id = users.id 
        WHERE avis.loueur_id = ? 
        ORDER BY avis.id DESC 
        LIMIT 10";
$stmt = $pdo->prepare($sql);
$stmt->execute([$annonce['utilisateur_id']]);
$commentaires = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($language) ?>">
<head>
    <meta charset="UTF-8">
    <title>Lokaz - <?= htmlspecialchars($t['title']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
body {
    font-family: Arial, sans-serif;
    margin: 20px;
    padding: 0;
    background-color: #ffffff;
    color: #4b1e1e; /* Texte principal en bordeaux brun */
}

.container {
    max-width: 1000px;
    margin: 20px auto;
    background: #ffffff; /* Fond blanc */
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0px 0px 10px #999999;
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
}

h1 {
    text-align: center;
    color: #4b1e1e; /* Titre en bordeaux brun */
    width: 100%;
    font-size: 24px;
}

img {
    width: 45%;
    height: auto;
    border-radius: 8px;
    border: 2px solid #4b1e1e; /* Bordure en bordeaux brun */
}

.details {
    width: 50%;
    padding-left: 20px;
}

.details p {
    font-size: 16px;
    margin: 10px 0;
    color: #4b1e1e; /* Texte en bordeaux brun */
}

.btn {
    display: inline-block;
    padding: 10px 15px;
    margin: 5px;
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
    transition: background-color 0.3s;
}

.btn-contact {
    background-color: #4b1e1e; /* Bouton contact en bordeaux brun */
    color: #ffffff;
}

.btn-contact:hover {
    background-color: #3a1414; /* Survol en bordeaux brun foncé */
}

.btn-back {
    background-color: #4b1e1e; /* Bouton retour en bordeaux brun */
    color: #ffffff;
}

.btn-back:hover {
    background-color: #3a1414; /* Survol en bordeaux brun foncé */
}

.avis-section {
    margin-top: 40px;
    overflow: hidden;
    height: 150px;
    position: relative;
    background-color: #f5f5f5; /* Fond clair légèrement ivoire */
    border: 1px solid #a6a6a6;
    border-radius: 6px;
    padding: 10px;
}

.avis-wrapper {
    display: flex;
    flex-direction: column;
    animation: scrollAvis 20s linear infinite;
}

.avis-wrapper.paused {
    animation-play-state: paused;
}

.avis {
    padding: 10px;
    border-bottom: 1px solid #999999;
    background-color: #ffffff; /* Fond des avis en blanc */
    color: #4b1e1e; /* Texte des avis en bordeaux brun */
    cursor: pointer;
}

@keyframes scrollAvis {
    0% { transform: translateY(100%); }
    100% { transform: translateY(-100%); }
}
.btn-laisser-avis {
    background-color: #4b1e1e; /* Bordeaux brun */
    color: #ffffff; /* Texte blanc */
    padding: 10px 20px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: bold;
    display: inline-block;
    border: 1px solid #4b1e1e;
    transition: background-color 0.3s, transform 0.2s;
}

.btn-laisser-avis:hover {
    background-color: #3a1414; /* Bordeaux brun foncé au survol */
    transform: scale(1.05);
}

.btn-laisser-avis:active {
    background-color: #290b0b; /* Encore plus foncé au clic */
    transform: scale(1);
}

</style>


</head>
<body>

<header>
    <p>
        <a href="?id=<?= $annonce_id ?>&lang=fr" <?= $language == 'fr' ? 'style="font-weight:bold;"' : '' ?>>Français</a> |
        <a href="?id=<?= $annonce_id ?>&lang=en" <?= $language == 'en' ? 'style="font-weight:bold;"' : '' ?>>English</a>
    </p>
</header>
<div class="container">
    <h1><?= htmlspecialchars($t['title']) ?></h1>

    <?php if (!empty($annonce['image_path'])) : ?>
    <img src="../uploads/<?= htmlspecialchars($annonce['image_path']) ?>" alt="<?= htmlspecialchars($annonce['titre']) ?>">
    <p>.</p>
<?php endif; ?>

    <div class="details">
        <p><strong><?= $annonce['prix'] . ' ' . htmlspecialchars($annonce['monnaie']) ?> /jour/day</strong></p>
        <p><strong><?= $t['category'] ?> :</strong> <?= htmlspecialchars($annonce['categorie']) ?></p>
        <p><strong><?= htmlspecialchars($t['location']) ?>:</strong> <?= htmlspecialchars($annonce['ville']) ?>, <?= htmlspecialchars($annonce['pays']) ?></p>
        <p><strong><?= $t['description'] ?> :</strong><br><?= nl2br(htmlspecialchars($annonce['description'])) ?></p>
        <p><strong><?= $t['published_on'] ?> :</strong> <?= date('d/m/Y', strtotime($annonce['date_publication'])) ?></p>

        <a class="btn btn-contact" href="contacter_loueur.php?annonce_id=<?= $annonce['id'] ?>"><?= htmlspecialchars($t['contacter_loueur']) ?></a>
        <a href="laisser_avis.php?loueur_id=<?= $annonce['utilisateur_id'] ?>" class="btn-laisser-avis">
    <?= htmlspecialchars($t['laisser_avis'] ?? 'Laisser un avis / Leave a review') ?>
</a>
<a href="annonces.php?id=<?= $annonce['utilisateur_id'] ?>" class="btn"><?= $t['back'] ?></a>


    <div class="avis-section">
        <h2><?= $t['comments_title'] ?></h2>
        <div class="avis-wrapper" id="avisWrapper">
            <?php foreach ($commentaires as $avis): ?>
                <div class="avis">
                    <strong>Note :</strong> <?= htmlspecialchars($avis['note']) ?>/5<br>
                    <?= nl2br(htmlspecialchars($avis['commentaire'])) ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
    const avisWrapper = document.getElementById('avisWrapper');
    let pauseTimeout;

    avisWrapper.addEventListener('click', function (event) {
        event.stopPropagation();
        avisWrapper.classList.add('paused');
        if (pauseTimeout) clearTimeout(pauseTimeout);
        pauseTimeout = setTimeout(() => {
            avisWrapper.classList.remove('paused');
        }, 5000);
    });

    document.addEventListener('click', function () {
        avisWrapper.classList.remove('paused');
        clearTimeout(pauseTimeout);
    });
</script>

</body>
</html>