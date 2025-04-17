<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/Lokaz/backend/db.php'; 

// Détection de la langue
$lang = isset($_GET['lang']) && $_GET['lang'] === 'en' ? 'en' : 'fr';

// Traductions intégrées
$texts = [
    'fr' => [
        'title' => "Inscription Particulier",
        'nom_complet' => "Nom complet",
        'nom_utilisateur' => "pseudonyme",
        'email' => "Email",
        'mot_secret' => "Mot Secret",
        'mot_secret_nb' => "NB : Gardez précieusement ce mot secret et ne le partagez avec personne. Il vous servira pour vérifier votre identité lors de la réinitialisation du mot de passe et autres opérations sensibles.",
        'creer_mdp' => "Créer un mot de passe",
        'confirmer_mdp' => "Confirmer le mot de passe",
        'mismatch_mdp' => "❌ Les mots de passe ne correspondent pas.",
        'photo_profil' => "Photo de profil",
        'genre' => "Genre",
        'homme' => "Homme",
        'femme' => "Femme",
        'autre' => "Autre",
        'date_naissance' => "Date de naissance",
        'pays' => "Pays",
        'ville' => "Ville",
        'telephone' => "Téléphone",
        'monnaie' => "Choisissez votre devise",
        'bouton_inscription' => "S'inscrire",
        'toggle_password' => "Afficher les mots de passe",
        'changer_langue' => "English "
    ],
    'en' => [
        'title' => "Individual Sign Up",
        'nom_complet' => "Full Name",
        'nom_utilisateur'=> "pseudonyme",
        'email' => "Email",
        'mot_secret' => "Secret Word",
        'mot_secret_nb' => "NB: Keep this secret word safe and do not share it with anyone. It will be used to verify your identity for password reset and other sensitive operations.",
        'creer_mdp' => "Create Password",
        'confirmer_mdp' => "Confirm Password",
        'mismatch_mdp' => "❌ Passwords do not match.",
        'photo_profil' => "Profile Picture",
        'genre' => "Gender",
        'homme' => "Male",
        'femme' => "Female",
        'autre' => "Other",
        'date_naissance' => "Date of Birth",
        'pays' => "Country",
        'ville' => "City",
        'telephone' => "Phone",
        'monnaie' => "Choose your currency",
        'bouton_inscription' => "Sign Up",
        'toggle_password' => "Show Passwords",
        'changer_langue' => "Français"
    ]
];

$t = $texts[$lang];
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $t['title'] ?></title>
    <style>
/* Style global */
body {
    font-family: Arial, sans-serif;
    background-color: #fff;
    color: #4b1c1c; /* Texte général bordeaux foncé */
    margin: 0;
    padding: 0;
}

h2 {
    text-align: center;
    color: #721c24; /* Titre en bordeaux profond */
    margin-top: 30px;
    font-size: 28px;
}

/* Bouton de changement de langue */
button:not([type="submit"]) {
    position: absolute;
    top: 20px;
    right: 20px;
    background-color: #8b3a48; /* Bordeaux moyen */
    color: white;
    padding: 10px 18px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button:not([type="submit"]):hover {
    background-color: #721c24; /* Bordeaux foncé */
}

/* Conteneur du formulaire */
form {
    max-width: 800px;
    margin: 50px auto;
    background-color: #fceeee; /* Fond rose très pâle pour rester dans le thème */
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 0 15px rgba(114, 28, 36, 0.2); /* ombre bordeaux */
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
}

/* Labels */
form label {
    width: 100%;
    color: #5c1f26; /* Bordeaux sombre */
    font-weight: bold;
    margin: 10px 0 5px;
}

/* Champs de saisie */
form input,
form select {
    width: calc(50% - 10px);
    padding: 10px;
    border: 2px solid #a05c5c; /* Bordure bordeaux doux */
    background-color: #fff;
    color: #4b1c1c;
    border-radius: 4px;
    margin-bottom: 20px;
}

/* Pleine largeur si nécessaire */
form input[type="file"],
form input[type="date"],
form select[multiple],
form textarea {
    width: 100%;
}

/* Bouton de soumission */
form button[type="submit"] {
    width: 100%;
    background-color: #721c24; /* Bordeaux foncé */
    color: #fff;
    padding: 14px;
    font-size: 16px;
    border: none;
    border-radius: 6px;
    margin-top: 10px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    align-self: flex-end;
}

/* Effet au survol */
form button[type="submit"]:hover {
    background-color: #8b3a48; /* Bordeaux plus clair */
}

/* Liens dans le formulaire */
form a {
    display: inline-block;
    margin-top: 15px;
    color: #8b3a48; /* Bordeaux doux */
    text-decoration: none;
    font-weight: bold;
}

form a:hover {
    color: #721c24; /* Bordeaux plus foncé */
}

/* Bouton spécial */
.btn-verifier-identite {
    display: inline-block;
    background-color: #8b3a48; /* Bordeaux moyen */
    color: white;
    padding: 12px 24px;
    font-size: 16px;
    border: none;
    border-radius: 6px;
    text-decoration: none;
    text-align: center;
    transition: background-color 0.3s, transform 0.2s;
}

.btn-verifier-identite:hover {
    background-color: #721c24; /* Bordeaux foncé */
    transform: scale(1.05);
}

.btn-verifier-identite:active {
    background-color: #4b1c1c; /* Bordeaux très foncé */
    transform: scale(1);
}

/* Responsive */
@media (max-width: 768px) {
    form input,
    form select {
        width: 100%;
        margin-right: 0;
    }
}
</style>

</head>
<body>

    <button onclick="switchLanguage()"><?= $t['changer_langue'] ?></button>

    <h2><?= $t['title'] ?></h2>

    <form action="../backend/traitement_inscription.php" method="POST" enctype="multipart/form-data">
        <label for="nom_complet"><?= $t['nom_complet'] ?> :</label>
        <input type="text" id="nom_complet" name="nom_complet" required><br>

        <label for="nom_utilisateur"><?= $t['nom_utilisateur'] ?> :</label>
        <input type="text" name="nom_utilisateur" id="nom_utilisateur" required>


        <label for="email"><?= $t['email'] ?> :</label>
        <input type="email" id="email" name="email" placeholder="Utilisez un email fonctionnel" required><br>

        <label for="mot_secret"><?= $t['mot_secret'] ?> :</label>
        <input type="text" id="mot_secret" name="mot_secret" required>
        <p><strong><?= $t['mot_secret_nb'] ?></strong></p>

        <label for="mot_de_passe"><?= $t['creer_mdp'] ?> :</label>
        <input type="password" id="mot_de_passe" name="mot_de_passe" required><br>

        <label for="confirmer_mot_de_passe"><?= $t['confirmer_mdp'] ?> :</label>
        <input type="password" id="confirmer_mot_de_passe" name="confirmer_mot_de_passe" required>
        <p class="error-message" id="errorMessage"><?= $t['mismatch_mdp'] ?></p>

        <input type="checkbox" id="togglePassword"> <span><?= $t['toggle_password'] ?></span><br>

        <label for="photo_profil"><?= $t['photo_profil'] ?> :</label>
        <input type="file" id="photo_profil" name="photo_profil"><br>

        <label for="genre"><?= $t['genre'] ?> :</label>
        <select name="genre" id="genre" required>
            <option value="Homme"><?= $t['homme'] ?></option>
            <option value="Femme"><?= $t['femme'] ?></option>
            <option value="Autre"><?= $t['autre'] ?></option>
        </select><br>

        <label for="date_naissance"><?= $t['date_naissance'] ?> :</label>
        <input type="date" id="date_naissance" name="date_naissance" required><br>

        <label for="pays"><?= $t['pays'] ?> :</label>
        <input type="text" id="pays" name="pays" required><br>

        <label for="ville"><?= $t['ville'] ?> :</label>
        <input type="text" id="ville" name="ville" required><br>

        <label for="telephone"><?= $t['telephone'] ?> :</label>
        <input type="tel" id="telephone" name="telephone" placeholder="avec l'indicatif / with callsign. Ex:+228 96295595" required><br>

        <label for="monnaie"><?= $t['monnaie'] ?> :</label>
        <select name="monnaie" required>
        <option value="XOF">West African CFA Franc (XOF - UEMOA) / Franc CFA Ouest-Africain</option>
    <option value="XAF">Central African CFA Franc (XAF - CEMAC) / Franc CFA Centrafricain</option>
    <option value="DZD">Algerian Dinar (DZD) / Dinar Algérien</option>
    <option value="EGP">Egyptian Pound (EGP) / Livre Égyptienne</option>
    <option value="MAD">Moroccan Dirham (MAD) / Dirham Marocain</option>
    <option value="TND">Tunisian Dinar (TND) / Dinar Tunisien</option>
    <option value="NGN">Nigerian Naira (NGN) / Naira Nigérian</option>
    <option value="GHS">Ghanaian Cedi (GHS) / Cedi Ghanéen</option>
    <option value="KES">Kenyan Shilling (KES) / Shilling Kényan</option>
    <option value="UGX">Ugandan Shilling (UGX) / Shilling Ougandais</option>
    <option value="TZS">Tanzanian Shilling (TZS) / Shilling Tanzanien</option>
    <option value="ETB">Ethiopian Birr (ETB) / Birr Éthiopien</option>
    <option value="ZAR">South African Rand (ZAR) / Rand Sud-Africain</option>
    <option value="LYD">Libyan Dinar (LYD) / Dinar Libyen</option>
    <option value="SDG">Sudanese Pound (SDG) / Livre Soudanaise</option>
    <option value="BWP">Botswana Pula (BWP) / Pula Botswanais</option>
    <option value="MWK">Malawian Kwacha (MWK) / Kwacha Malawien</option>
    <option value="MZN">Mozambican Metical (MZN) / Metical Mozambicain</option>
    <option value="NAD">Namibian Dollar (NAD) / Dollar Namibien</option>
    <option value="SCR">Seychellois Rupee (SCR) / Roupie Seychelloise</option>
    <option value="SLL">Sierra Leonean Leone (SLL) / Leone Sierra-Léonais</option>
    <option value="SOS">Somali Shilling (SOS) / Shilling Somalien</option>
    <option value="SZL">Eswatini Lilangeni (SZL) / Lilangeni Eswatinien</option>
    <option value="CDF">Congolese Franc (CDF) / Franc Congolais</option>
    <option value="BIF">Burundian Franc (BIF) / Franc Burundais</option>
    <option value="DJF">Djiboutian Franc (DJF) / Franc Djiboutien</option>
    <option value="GNF">Guinean Franc (GNF) / Franc Guinéen</option>
    <option value="ERN">Eritrean Nakfa (ERN) / Nakfa Érythréen</option>
    <option value="LSL">Lesotho Loti (LSL) / Loti Lésothien</option>
    <option value="MGA">Malagasy Ariary (MGA) / Ariary Malgache</option>
    <option value="MRU">Mauritanian Ouguiya (MRU) / Ouguiya Mauritanien</option>
    <option value="RWF">Rwandan Franc (RWF) / Franc Rwandais</option>
    <option value="ZMW">Zambian Kwacha (ZMW) / Kwacha Zambien</option>
    <option value="AOA">Angolan Kwanza (AOA) / Kwanza Angolais</option>
    </select>
        <button type="submit"><?= $t['bouton_inscription'] ?></button>
    </form>

    <script>
        document.getElementById("togglePassword").addEventListener("change", function() {
            let passwordField = document.getElementById("mot_de_passe");
            let confirmPasswordField = document.getElementById("confirmer_mot_de_passe");
            let type = this.checked ? "text" : "password";
            passwordField.type = type;
            confirmPasswordField.type = type;
        });

        document.getElementById("confirmer_mot_de_passe").addEventListener("input", function() {
            let password = document.getElementById("mot_de_passe").value;
            let confirmPassword = this.value;
            let errorMessage = document.getElementById("errorMessage");

            if (password !== confirmPassword) {
                errorMessage.style.display = "block";
            } else {
                errorMessage.style.display = "none";
            }
        });

        function switchLanguage() {
            let newLang = "<?= $lang ?>" === "fr" ? "en" : "fr";
            window.location.href = "?lang=" + newLang;
        }
    </script>
<a href="choix_inscription.php" class="btn-verifier-identite">Back/Retour</a>

</body>
</html>

