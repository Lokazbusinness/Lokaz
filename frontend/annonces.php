<?php
session_start();
ob_start(); // Active la mise en mémoire tampon pour éviter les erreurs d'affichage
require_once '../backend/db.php';
include 'header.php';
// Vérifie si l'utilisateur est connecté
$utilisateur_id = $_SESSION['utilisateur_id'] ?? null;
$language = $_GET['lang'] ?? 'fr';

// Traductions
$translations = [
    'fr' => [
        'title' => 'Annonces disponibles',
        'filter_city' => 'Ville *',
        'filter_category' => 'Catégorie',
        'filter_price' => 'Prix max *',
        'filter_keyword' => 'Nom de l\'objet: facultatif',
        'search_button' => 'Rechercher',
        'rent' => 'louer',
        'post_ad' => 'Publier une annonce',
        'no_listings' => 'Aucune annonce trouvée.',
        'posted_by' => 'Posté par',
        'rent_from' => 'Chez qui voulez-vous louer ?',
        'professional' => 'Professionnel',
        'individual' => 'Particulier',
        'unavailable' => 'Annonce indisponible',
        'identite_verifiee'=> 'loueur vérifié',
        'description_phrase' => 'Sur Lokaz, vous pouvez non seulement louer des objets, mais aussi proposer vos services ou rechercher des prestataires pour tous vos besoins. Que vous soyez un professionnel ou un particulier, Lokaz vous offre une plateforme de mise en relation simple et sécurisée.',
        'activate_listing' => 'Activer cette annonce'
    ],
    'en' => [
        'title' => 'Available Listings',
        'filter_city' => 'City *',
        'filter_category' => 'Category',
        'filter_price' => 'Max Price *',
        'filter_keyword' => 'Item Name: optional',
        'search_button' => 'Search',
        'rent' => 'rent',
        'post_ad' => 'Post an Ad',
        'no_listings' => 'No listings found.',
        'posted_by' => 'Posted by',
        'rent_from' => 'Who do you want to rent from?',
        'professional' => 'Professional',
        'individual' => 'Individual',
        'unavailable' => 'Unavailable listing',
        'identite_verifiee'=>'verified renter',
        'description_phrase' => 'On Lokaz, you can not only rent items, but also offer your services or find service providers for all your needs. Whether you are a professional or an individual, Lokaz offers you a simple and secure connection platform.',
        'activate_listing' => 'Activate this listing'
    ]
];
$t = $translations[$language];

// Récupération des filtres sécurisés
$ville = $_GET['ville'] ?? '';
$categorie = $_GET['categorie'] ?? '';
$prix_max = $_GET['prix_max'] ?? '';
$mot_cle = $_GET['mot_cle'] ?? '';
$type_location = $_GET['type_location'] ?? '';

$sql = "SELECT   
            annonces.*, 
            users.type, 
            users.photo_profil,
            abonnements.abonnement_actif,
            users.nom_utilisateur,
            users.entreprise_nom,
            users.identite_verifiee,
             (
                SELECT ROUND(AVG(note), 1)
                FROM avis
                WHERE avis.loueur_id = users.id
            ) AS moyenne_note,
            CASE 
                WHEN users.type = 'professionnel' THEN users.entreprise_nom 
                ELSE users.nom_utilisateur 
            END AS annonceur_nom 
        FROM annonces 
        JOIN users ON annonces.utilisateur_id = users.id
        LEFT JOIN abonnements ON users.id = abonnements.user_id 
        WHERE annonces.statut = 'actif'"; // Filtrer les annonces actives uniquement

$params = [];


if (!empty($ville)) {
    $sql .= " AND annonces.ville LIKE ?";
    $params[] = "%$ville%";
}

if (!empty($prix_max) && is_numeric($prix_max)) {
    $sql .= " AND annonces.prix <= ?";
    $params[] = $prix_max;
}

if (!empty($mot_cle)) {
    $sql .= " AND annonces.titre LIKE ?";
    $params[] = "%$mot_cle%";
} elseif (!empty($categorie)) {
    $sql .= " AND annonces.categorie = ?";
    $params[] = $categorie;
}

if (!empty($type_location)) {
    $sql .= " AND users.type = ?";
    $params[] = $type_location;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$annonces = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="<?= htmlspecialchars($language) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lokaz - <?= htmlspecialchars($t['title']) ?></title>
    <style> 
body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background-color: #ffffff; /* fond blanc */
    color: #1e1e1e; /* texte foncé neutre */
}

header {
    background-color: #5a2a27; /* Bordeaux brun */
    padding: 15px;
    text-align: right;
    border-bottom: 1px solid #8b3a48; /* Bordeaux plus clair */
}

header a {
    color: #ffffff; /* Blanc pour les liens */
    margin: 0 8px;
    text-decoration: none;
    font-weight: bold;
}

h1 {
    text-align: center;
    color: #5a2a27; /* Bordeaux brun */
    margin-top: 20px;
}

.filter-section {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 15px;
    margin: 25px auto;
    padding: 15px;
    background-color: #ffffff;
    border: 1px solid #8b3a48; /* Bordeaux plus clair */
    border-radius: 10px;
    max-width: 900px;
}

.filter-section input,
.filter-section select {
    padding: 10px;
    border: 1px solid #5a2a27; /* Bordeaux brun */
    border-radius: 8px;
    width: 180px;
    font-size: 14px;
    background-color: #ffffff;
    color: #1e1e1e;
}

.annonces-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    padding: 20px;
}

.annonce {
    background-color: #ffffff;
    border: 1px solidrgb(82, 25, 25);
    border-radius: 12px;
    padding: 15px;
    text-align: center;
    box-shadow: 0 2px 6px rgba(22, 6, 6, 0.05);
    position: relative;
    transition: transform 0.2s ease;
}

.annonce:hover {
    transform: scale(1.01);
}

.annonce img.annonce-image {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 10px;
}

.annonceur-info {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-top: 10px;
}

.profile-pic {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    object-fit: cover;
    border: 1px solidrgb(59, 22, 22);
}

.verification-status {
    font-style: italic;
    color: #2a8a2e;
    font-size: 14px;
}
.annonce-inactive {
    background-color: rgba(190, 167, 176, 0.5); /* Bordeaux clair transparent */
    color: #444;
    border: 2px dashed #7a3f3f; /* Bordeaux brun pour la bordure */
}

.btn-rent, .btn-activate, .submit {
    display: inline-block;
    padding: 10px 14px;
    margin-top: 10px;
    font-weight: bold;
    border-radius: 6px;
    text-decoration: none;
    transition: background-color 0.3s ease;
}

.btn-rent {
    background-color: #5a2a27; /* Bordeaux brun */
    color: #fff;
}

.btn-rent:hover {
    background-color: #3e1f1b; /* Bordeaux plus foncé */
}

.btn-activate {
    background-color:rgb(94, 31, 31);
    color: #fff;
}

.btn-activate:hover {
    background-color: #5a2a27; /* Bordeaux brun */
}

.btn-submit {
    background-color: #5a2a27; /* Bordeaux brun */
    color: #fff;
    border: 1px solid #8b3a48; /* Bordeaux plus clair */
}

.btn-recherche:hover {
    background-color: #3e1f1b; /* Bordeaux plus foncé */
}

.presentation-message {
    font-style: italic;
    font-size: 15px;
    color: #ffffff;
    background-color:rgb(105, 52, 48); /* Bordeaux brun */
    padding: 12px 20px;
    text-align: center;
    margin: 0;
    border-bottom: 2px solid #704b4b;
}
.filter-section button {
    padding: 10px 16px;
    border: none;
    border-radius: 8px;
    background-color: #5a2a27; /* Bordeaux brun */
    color: #ffffff;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.filter-section button:hover {
    background-color: #8b3a48; /* Bordeaux plus clair au survol */
    transform: scale(1.03);
}
.filter-section button:active,
.filter-section input[type="submit"]:active {
    background-color: #8b3a48; /* Bordeaux plus clair */
    transform: scale(0.98);
}
.language-links {
  display: flex;
  gap: 10px;
  justify-content: center;
  margin-top: 20px;
}

.language-links a.lang-link {
  padding: 10px 20px;
  border-radius: 30px;
  border: 2px solid #5b1a18; /* bordeaux-brun */
  background-color: white;
  color: #5b1a18; /* texte bordeaux-brun */
  font-weight: bold;
  text-decoration: none;
  transition: all 0.3s ease;
}

.language-links a.lang-link.active {
  background-color: #5b1a18; /* fond bordeaux-brun */
  color: white;
  border: 2px solid transparent;
}

.language-links a.lang-link:hover:not(.active) {
  background-color: #f5e6e6; /* léger fond rosé au survol */
}


</style>
</head>
<body>

<header>
<div class="language-links">
  <a href="?lang=fr" class="lang-link active">FR Français</a>
  <a href="?lang=en" class="lang-link">GB English</a>
</div>
    <p><a href="publier_annonce.php?lang=<?= htmlspecialchars($language) ?>"><?= htmlspecialchars($t['post_ad']) ?></a></p>
    <p class="presentation-message"><?= htmlspecialchars($t['description_phrase']) ?></p>

</header>

<h1><?= htmlspecialchars($t['title']) ?></h1>
<form method="GET" class="filter-section">
    <input type="hidden" name="lang" value="<?= htmlspecialchars($language) ?>">
    <input type="text" name="ville" placeholder="<?= htmlspecialchars($t['filter_city']) ?>" value="<?= htmlspecialchars($ville) ?>" required>
    <input type="number" name="prix_max" placeholder="<?= htmlspecialchars($t['filter_price']) ?>" value="<?= htmlspecialchars($prix_max) ?>" min="0" required>
    <input type="text" name="mot_cle" placeholder="<?= htmlspecialchars($t['filter_keyword']) ?>" value="<?= htmlspecialchars($mot_cle) ?>">
    <select name="categorie">
    <option value=""><?= htmlspecialchars($t['filter_category']) ?></option>
    <option value="immobilier">Immobilier</option>
    <option value="voitures">Automobile</option>
    <option value="électronique">Électronique</option>
    <option value="mode">Mode</option>
    <option value="bebe">Pour bébé</option>
    <option value="evenementiel">Événementiels</option>
    <option value="maison">Maison</option>
    <option value="services">Services</option>
</select>
  <select name="type_location">
        <option value=""><?= htmlspecialchars($t['rent_from']) ?></option>
        <option value="professionnel"><?= htmlspecialchars($t['professional']) ?></option>
        <option value="particulier"><?= htmlspecialchars($t['individual']) ?></option>
    </select>
    <button type="submit"><?= htmlspecialchars($t['search_button']) ?></button>
</form>
<div class="annonces-container">
    <?php if (empty($annonces)): ?>
        <p><?= htmlspecialchars($t['no_listings']) ?></p>
    <?php else: ?>
        <?php foreach ($annonces as $annonce): ?>
            <div class="annonce <?= !$annonce['abonnement_actif'] ? 'annonce-inactive' : '' ?>">
                <!-- Image de l'annonce -->
                <img src="<?= htmlspecialchars($annonce['image_path']) ?>" alt="Image de l'annonce" class="annonce-image">
                <!-- Titre de l'annonce -->
                <h3><?= htmlspecialchars($annonce['titre']) ?></h3>
                  
                <div class="annonceur-info">
    <!-- Photo de profil -->
    <img src="<?= !empty($annonce['photo_profil']) ? "../uploads/" . htmlspecialchars($annonce['photo_profil']) : "../images/default.png"; ?>" 
         alt="Photo de profil" class="profile-pic">

   <!-- Informations de l'annonceur -->
   <div>
        <span class="nom-annonceur">
            <?= htmlspecialchars($annonce['type'] === 'professionnel' ? $annonce['entreprise_nom'] : $annonce['nom_utilisateur']) ?>
        </span>
        <br>
        <small class="type-compte">
    <?= $annonce['type'] === 'professionnel' ? $t['professional'] : $t['individual'] ?>
    <?php if ($annonce['identite_verifiee']) : ?>
    <span class="verification-status"><?= htmlspecialchars($t['identite_verifiee']) ?></span>
<?php endif; ?>
</small>
    </div>
</div>
<?php if ($annonce['moyenne_note']) : ?>
    <div class="rating">
        <?php 
        $note = round($annonce['moyenne_note']);
        for ($i = 1; $i <= 5; $i++) {
            echo $i <= $note ? '⭐' : '☆';
        }
        ?>
        <small>(<?= $annonce['moyenne_note'] ?>/5)</small>
    </div>
<?php else : ?>
    <div class="rating">
        <small>Pas encore noté</small>
    </div>
<?php endif; ?>

                <!-- Description de l'annonce -->
                <p><?= htmlspecialchars($annonce['description']) ?></p>

                <!-- Prix -->
                <p><strong><?= $annonce['prix'] . ' ' . htmlspecialchars($annonce['monnaie']) ?> /jour/day</strong></p>

                <!-- Boutons d'action -->
                <?php if (!$annonce['abonnement_actif']): ?>
                    <?php if ($annonce['utilisateur_id'] == $utilisateur_id): ?>
                        <a href="abonnements.php" class="btn-activate"><?= htmlspecialchars($t['activate_listing']) ?></a>
                    <?php else: ?>
                        <p><?= htmlspecialchars($t['unavailable']) ?></p>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="details_annonces.php?id=<?= $annonce['id'] ?>" class="btn-rent"><?= htmlspecialchars($t['rent']) ?></a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html> 













