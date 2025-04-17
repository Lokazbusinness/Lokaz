<?php  
// Inclure le fichier de connexion à la base de données
require_once '../backend/db.php';

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Récupérer les données du formulaire et les sécuriser
    $nom_complet = trim($_POST['nom_complet']);
    $nom_utilisateur = trim($_POST['nom_utilisateur']);
    $email = trim($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];
    $mot_secret = trim($_POST['mot_secret']); // ✅ Ajout du mot secret
    $genre = $_POST['genre'] ?? '';
    $date_naissance = trim($_POST['date_naissance']);
    $pays = trim($_POST['pays']);
    $ville = trim($_POST['ville']);
    $telephone = trim($_POST['telephone']);
    $monnaie = trim($_POST['monnaie']);
    $photo_profil = NULL; // Par défaut, pas de photo

    if (!empty($_FILES['photo_profil']['name'])) {
        // Vérifie que le dossier existe et le crée s'il n'existe pas
        $dossier_upload = __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR;

        if (!file_exists($dossier_upload)) {
            mkdir($dossier_upload, 0777, true);
        }

        // Récupérer le nom du fichier avec un préfixe unique (timestamp)
        $nom_fichier = time() . "_" . basename($_FILES["photo_profil"]["name"]);
        $chemin_complet = $dossier_upload . $nom_fichier;

        // Vérification de l'extension du fichier
        $extension = pathinfo($nom_fichier, PATHINFO_EXTENSION);
        $extensions_autorisees = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array(strtolower($extension), $extensions_autorisees)) {
            echo "Erreur : Seuls les fichiers images (jpg, jpeg, png, gif) sont autorisés.";
            exit();
        }

        // Vérifier la taille du fichier
        if ($_FILES["photo_profil"]["size"] > 5 * 1024 * 1024) {
            echo "Erreur : Le fichier est trop volumineux. La taille maximale autorisée est 5 Mo.";
            exit();
        }

        // Déplacer l'image vers le dossier
        if (move_uploaded_file($_FILES["photo_profil"]["tmp_name"], $chemin_complet)) {
            $photo_profil = $nom_fichier;
        } else {
            echo "Erreur lors du téléchargement de l'image.";
            exit();
        }
    }

    // Vérifier si l'email, le nom complet, le numéro de téléphone ou le nom d'utilisateur existent déjà 
$sql = "SELECT id FROM users WHERE email = :email OR nom_complet = :nom_complet OR telephone = :telephone OR nom_utilisateur = :nom_utilisateur";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":email", $email, PDO::PARAM_STR);
$stmt->bindValue(":nom_complet", $nom_complet, PDO::PARAM_STR);
$stmt->bindValue(":telephone", $telephone, PDO::PARAM_STR);
$stmt->bindValue(":nom_utilisateur", $nom_utilisateur, PDO::PARAM_STR);
$stmt->execute();


    if ($stmt->rowCount() > 0) {
        echo "Erreur : Cet email,pseudonyme,numéro de téléphone ou nom est déjà utilisé./Error: This email, username, phone number or name is already in use.";
        exit();
    } else {
        // Hachage du mot de passe
        $mot_de_passe_hache = password_hash($mot_de_passe, PASSWORD_DEFAULT);

        // ✅ Ajout de la colonne mot_secret dans la requête d'insertion
        $sql = "INSERT INTO users (nom_complet, nom_utilisateur, email, mot_de_passe, mot_secret, genre, date_naissance, photo_profil, pays, ville, telephone, monnaie)
                VALUES (:nom_complet,:nom_utilisateur, :email, :mot_de_passe, :mot_secret, :genre, :date_naissance, :photo_profil, :pays, :ville, :telephone, :monnaie)";

        // Préparer et exécuter la requête d'insertion
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":nom_complet", $nom_complet, PDO::PARAM_STR);
        $stmt->bindValue(":nom_utilisateur", $nom_utilisateur, PDO::PARAM_STR);
        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
        $stmt->bindValue(":mot_de_passe", $mot_de_passe_hache, PDO::PARAM_STR);
        $stmt->bindValue(":mot_secret", $mot_secret, PDO::PARAM_STR);
        $stmt->bindValue(":genre", $genre, PDO::PARAM_STR);
        $stmt->bindValue(":date_naissance", $date_naissance, PDO::PARAM_STR);
        $stmt->bindValue(":photo_profil", $photo_profil, PDO::PARAM_STR);
        $stmt->bindValue(":pays", $pays, PDO::PARAM_STR);
        $stmt->bindValue(":ville", $ville, PDO::PARAM_STR);
        $stmt->bindValue(":telephone", $telephone, PDO::PARAM_STR);
        $stmt->bindValue(":monnaie", $monnaie, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo "Inscription réussie !";
            header("Location: ../frontend/connexion.php");
            exit();
        } else {
            echo "Erreur lors de l'inscription : " . implode(" ", $stmt->errorInfo());
        }
    }
}
?>



