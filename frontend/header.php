<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include $_SERVER['DOCUMENT_ROOT'] . '/Lokaz/backend/db.php';
$photo = "../images/default.png"; // Image de profil par défaut
$nom_affichage = "Utilisateur"; // Nom affiché par défaut
$type_utilisateur = ""; // Type de compte (Professionnel ou Particulier)

// Vérifier si l'utilisateur est connecté
if (isset($_SESSION['user_id']) && isset($pdo)) {
    $user_id = $_SESSION['user_id'];

    // Requête pour récupérer les infos de l'utilisateur
    $stmt = $pdo->prepare("SELECT nom_utilisateur, entreprise_nom, photo_profil, type FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['type_compte'] = $user['type'];

        if ($user['type'] === 'particulier / Individual') {
            $_SESSION['nom_utilisateur'] = $user['nom_utilisateur'];
            $nom_affichage = $user['nom_utilisateur'];
        } else {
            $_SESSION['entreprise_nom'] = $user['entreprise_nom'];
            $nom_affichage = $user['entreprise_nom'];
        }

        $type_utilisateur = ucfirst($user['type']);

        if (!empty($user['photo_profil'])) {
            $photo = "../uploads/" . htmlspecialchars($user['photo_profil']);
        }
    }
}


?>

<!DOCTYPE html>
<html lang="<?= htmlspecialchars($langue_code); ?>"> 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lokaz</title>
    <style>
/* STYLE GLOBAL HEADER - Lokaz */
body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background-color: #fff; /* fond blanc */
    color: #333;
}

/* Barre du haut */
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #fff; /* fond blanc */
    padding: 15px 30px;
    box-shadow: 0 2px 8px rgba(114, 47, 55, 0.08); /* bordeaux très léger */
    position: sticky;
    top: 0;
    z-index: 1000;
}

.logo {
    font-size: 26px;
    font-weight: bold;
    color: #6A1B1B; /* bordeaux foncé */
    letter-spacing: 1px;
}

.nav-links {
    display: flex;
    align-items: center;
    gap: 16px;
}

a {
    text-decoration: none;
    color: #6A1B1B; /* bordeaux */
    font-weight: 500;
    transition: color 0.2s ease;
}

a:hover {
    color:rgb(14, 1, 6); /* rouge vif */
}

/* Boutons de navigation */
.btn {
    background-color: #6A1B1B; /* bordeaux foncé */
    color: white;
    padding: 9px 14px;
    border: none;
    cursor: pointer;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    transition: background-color 0.3s ease;
}

.btn:hover {
    background-color: #8B0000; /* bordeaux plus foncé */
}

/* Nouveau bouton "Nous connaître" */
.btn-nous-connaitre {
    background-color: #6A1B1B; /* bordeaux foncé */
    color: white;
    padding: 10px 18px;
    border: none;
    cursor: pointer;
    border-radius: 6px;
    font-size: 15px;
    font-weight: 600;
    transition: background-color 0.3s ease;
}

.btn-nous-connaitre:hover {
    background-color: #8B0000; /* bordeaux plus foncé */
}

/* Messages badge */
.btn span {
    background-color:rgb(26, 3, 14); /* rouge vif */
    color: white;
    border-radius: 50%;
    padding: 2px 7px;
    margin-left: 4px;
    font-size: 12px;
    font-weight: bold;
}

/* Profil utilisateur */
.profile {
    position: relative;
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
}

.profile img {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #b26b6b; /* bord bordeaux clair */
}

.profile-name {
    font-size: 15px;
    font-weight: 600;
    color: #6A1B1B; /* bordeaux */
}

.profile-type {
    font-size: 13px;
    font-weight: bold;
    <?php if ($type_utilisateur === "Professionnel") : ?>
        color: #cc0000; /* rouge vif */
    <?php else : ?>
        color: #007acc;
    <?php endif; ?>
}

/* Menu déroulant profil */
.profile-menu {
    display: none;
    position: absolute;
    top: 52px;
    right: 0;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
    min-width: 170px;
    padding: 8px 0;
    z-index: 500;
}

.profile-menu a {
    display: block;
    padding: 10px 15px;
    text-decoration: none;
    color: #6A1B1B; /* bordeaux */
    font-size: 14px;
    transition: background-color 0.2s ease;
}

.profile-menu a:hover {
    background: #fbecec; /* rose pâle pour hover */
}
</style>


</head>
<body>

<!-- Barre du haut -->
<header class="header">
    <div class="logo">Lokaz</div>

    <div class="nav-links">
        <a href="dashboard.php" class="btn">Mon espace / My space</a>
        <a href="deconnexion.php" class="btn">Se déconnecter / Logout</a>
        <a href="footer.php"class="btn">Nous connaître/ to Know us</a>
        <?php
        // Récupérer le nombre de messages non lus
        $message_count = 0;
        if (isset($_SESSION['user_id'])) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM messages WHERE destinataire_id = ? AND lu = 0");
            $stmt->execute([$_SESSION['user_id']]);
            $message_count = $stmt->fetchColumn();
        }
        ?>
        <a href="messagerie.php" class="btn">
            📩 Messages <?php if ($message_count > 0) : ?>
                <span style="background-color: red; color: white; border-radius: 50%; padding: 2px 6px; font-size: 14px;">
                    <?= $message_count; ?>
                </span>
            <?php endif; ?>
        </a>

        <!-- Profil utilisateur avec menu déroulant au clic -->
        <div class="profile" id="profile">
            <img src="<?= $photo ?>" alt="Photo de profil" id="profile-btn">
            <span class="profile-name"><?= htmlspecialchars($nom_affichage) ?></span>
            <span class="profile-type"><?= htmlspecialchars($type_utilisateur) ?></span>
            <div class="profile-menu" id="profile-menu">
                <a href="<?= $photo ?>" target="_blank">Voir la photo / see the photo</a>
                <a href="profil.php">Changer la photo / change the photo </a>
                <a href="footer.php">nous connaitre / to know us</a>
            </div>
        </div>
    </div>
</header>

<script>
    // Gestion du menu déroulant au clic
    document.addEventListener("DOMContentLoaded", function () {
        const profileBtn = document.getElementById("profile-btn");
        const profileMenu = document.getElementById("profile-menu");

        profileBtn.addEventListener("click", function (event) {
            event.stopPropagation(); // Empêche la fermeture immédiate
            profileMenu.style.display = profileMenu.style.display === "block" ? "none" : "block";
        });

        // Fermer le menu si on clique ailleurs
        document.addEventListener("click", function (event) {
            if (!profileBtn.contains(event.target) && !profileMenu.contains(event.target)) {
                profileMenu.style.display = "none";
            }
        });
    });
</script>

</body>
</html>

