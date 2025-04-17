<?php  
session_start(); // Démarre la session
// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur_id'])) {
    header("Location: connexion.php");
    exit();
}

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang']; // Stocke la langue choisie
}

require_once '../backend/db.php';
include 'header.php';

$utilisateur_id = $_SESSION['utilisateur_id'];
$language = isset($_GET['lang']) ? $_GET['lang'] : 'fr';

// Traductions
$translations = [
    'fr' => [
        'title' => 'Mes annonces',
        'no_listings' => 'Vous n\'avez publié aucune annonce.',
        'listing_views' => 'Nombre de vues',
        'activate' => 'Activer',
        'deactivate' => 'Désactiver',
        'edit' => 'Modifier',
        'delete' => 'Supprimer',
        'back'=>'retour aux annonces',
        'view_details' => 'Voir les détails'
    ],
    'en' => [
        'title' => 'My Listings',
        'no_listings' => 'You have not posted any listings.',
        'listing_views' => 'Number of Views',
        'activate' => 'Activate',
        'deactivate' => 'Deactivate',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'back' => 'back',
         'view_details' => 'View Details'
    ],
];

$t = $translations[$language];

$sql = "SELECT  
            annonces.id, 
            annonces.titre, 
            annonces.image_path, 
            annonces.date_publication, 
            abonnements.abonnement_actif, 
            COUNT(vues_annonces.annonce_id) AS nombre_vues
        FROM annonces 
        LEFT JOIN vues_annonces ON annonces.id = vues_annonces.annonce_id
        INNER JOIN users ON annonces.utilisateur_id = users.id
        INNER JOIN abonnements ON abonnements.user_id = users.id
        WHERE annonces.utilisateur_id = :utilisateur_id
        GROUP BY 
            annonces.id, 
            annonces.titre, 
            annonces.image_path, 
            annonces.date_publication, 
            abonnements.abonnement_actif
        ORDER BY annonces.date_publication DESC";


$stmt = $pdo->prepare($sql);
$stmt->execute([':utilisateur_id' => $utilisateur_id]);
$annonces = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="<?= htmlspecialchars($language) ?>">

<a href="annonces.php?lang=<?= $language ?>" class="btn btn-back"><?= htmlspecialchars($t['back']) ?></a>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lokaz - <?= htmlspecialchars($t['title']) ?></title>
    <style> 
/* Conteneur des annonces */
.annonces-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 15px;
}

/* Annonce */
.annonce {
    border: 1px solid #c7a7a7;
    padding: 15px;
    border-radius: 8px;
    background: #ffffff;
    text-align: center;
}

.annonce img {
    max-width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 5px;
}

/* Annonce inactive */
.annonce-inactive {
    background-color: #b88d8d; /* Bordeaux grisé */
    color: white;
    font-weight: bold;
    border: 2px solid #800000;
}

/* Boutons */
.btn {
    display: inline-block;
    padding: 8px;
    margin: 5px;
    border-radius: 5px;
    font-weight: bold;
    text-decoration: none;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

/* Couleurs boutons */
.btn-edit {
    background-color: #8b3a3a; /* Bordeaux */
    color: white;
}

.btn-edit:hover {
    background-color: #a94444; /* Bordeaux clair */
}

.btn-delete {
    background-color: #7b2d2d; /* Bordeaux foncé */
    color: white;
}

.btn-delete:hover {
    background-color: #a33232;
}

.btn-activate {
    background-color: #6f1d1b;
    color: white;
}

.btn-activate:hover {
    background-color: #a0332e;
}

.btn-deactivate {
    background-color: #f0e0e0;
    color: #800000;
}

.btn-deactivate:hover {
    background-color: #e3caca;
    color: #5a0f0f;
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

<header>
<div class="language-switch">
  <a href="?lang=fr" class="lang-btn <?= ($_GET['lang'] ?? 'fr') === 'fr' ? 'active' : '' ?>">FR Français</a>
  <a href="?lang=en" class="lang-btn <?= ($_GET['lang'] ?? '') === 'en' ? 'active' : '' ?>">GB English</a>
</div>
</header>

<h1><?= htmlspecialchars($t['title']) ?></h1>

<section class="annonces-container">
    <?php if (empty($annonces)) : ?>
        <p><?= htmlspecialchars($t['no_listings']) ?></p>
    <?php else : ?>
        <?php foreach ($annonces as $annonce) : 
            $image_path = !empty($annonce['image_path']) ? "uploads/" . htmlspecialchars($annonce['image_path']) : "assets/default-image.jpg";
            $is_active = $annonce['abonnement_actif'] == 1;
        ?>
           <div class="annonce <?= $is_active ? '' : 'annonce-inactive' ?>">
    <img src="<?= htmlspecialchars($annonce['image_path']) ?>" alt="Image de l'annonce">
    <h3><?= htmlspecialchars($annonce['titre']) ?></h3>
    <p><?= htmlspecialchars($t['listing_views']) ?>: <?= $annonce['nombre_vues'] ?></p>

    <!-- Conteneur des boutons sous l'annonce -->
    <div class="annonce-buttons">
        <a href="details_annonces.php?id=<?= $annonce['id'] ?>" class="btn btn-edit"><?= htmlspecialchars($t['view_details']) ?></a>
        <a href="modifier_annonce.php?id=<?= $annonce['id'] ?>" class="btn btn-edit"><?= htmlspecialchars($t['edit']) ?></a>
        <a href="supprimer_annonce.php?id=<?= $annonce['id'] ?>" class="btn btn-delete" onclick="return confirm('Êtes-vous sûr ?');"><?= htmlspecialchars($t['delete']) ?></a>
        <a href="desactiver_annonce.php?id=<?= $annonce['id'] ?>" class="btn btn-deactivate"><?= htmlspecialchars($t['deactivate']) ?></a>
        <a href="activer_annonce.php?id=<?= $annonce['id'] ?>" class="btn btn-activate"><?= htmlspecialchars($t['activate']) ?></a>
    </div>
</div>
<?php endforeach; // ✅ Fermeture de la boucle ?>
<?php endif; // ✅ Fermeture de la condition ?>
</section>
</body>
</html>