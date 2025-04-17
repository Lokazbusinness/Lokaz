<?php 
session_start();
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'fr';
}
$lang = $_SESSION['lang'];

// Traductions intégrées
$translations = [
    'fr' => [
        'title' => "Inscription Professionnelle",
        'company_name' => "Nom de l'entreprise :",
        'email' => "Email :",
        'phone' => "Téléphone :",
        'country' => "Pays :",
        'city' => "Ville :",
        'secret_word' => "Mot secret :",
        'secret_note' => "NB : Gardez précieusement ce mot secret et ne le partagez avec personne. Il vous servira pour vérifier votre identité lors de la réinitialisation du mot de passe et autres opérations sensibles.",
        'password' => "Créer un mot de passe :",
        'confirm_password' => "Confirmer le mot de passe :",
        'currency' => "Choisissez votre monnaie :",
        'profile_pic' => "Photo de profil :",
        'register' => "S'inscrire",
        'show_password' => "Afficher le mot de passe",
        'language' => "Langue :"
    ],
    'en' => [
        'title' => "Professional Registration",
        'company_name' => "Company Name:",
        'email' => "Email:",
        'phone' => "Phone:",
        'country' => "Country:",
        'city' => "City:",
        'secret_word' => "Secret Word:",
        'secret_note' => "NB: Keep this secret word safe and do not share it with anyone. It will be used to verify your identity when resetting your password and other sensitive operations.",
        'password' => "Create a password:",
        'confirm_password' => "Confirm password:",
        'currency' => "Choose your currency:",
        'profile_pic' => "Profile Picture:",
        'register' => "Register",
        'show_password' => "Show password",
        'language' => "Language:"
    ]
];

// Gestion du changement de langue
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
    header("Location: inscriptionpro.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $translations[$lang]['title'] ?></title>
    <style>
/* Style global */
body {
    font-family: Arial, sans-serif;
    background-color: #fff;
    color: #4b1c1c; /* Texte principal en bordeaux foncé */
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
    background-color: #721c24; /* Bordeaux plus foncé */
}

/* Conteneur du formulaire */
form {
    max-width: 800px;
    margin: 50px auto;
    background-color: #fceeee; /* Fond rosé pâle */
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 0 15px rgba(114, 28, 36, 0.2); /* Ombre bordeaux */
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
    border: 2px solid #a05c5c; /* Bordure bordeaux clair */
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
    background-color: #8b3a48; /* Bordeaux moyen */
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

    <script>
        function checkPasswordMatch() {
            let password = document.getElementById("password").value;
            let confirmPassword = document.getElementById("confirm_password").value;
            let errorText = document.getElementById("password_error");

            if (password !== confirmPassword) {
                errorText.style.display = "block";
            } else {
                errorText.style.display = "none";
            }
        }

        function togglePasswordVisibility() {
            let password = document.getElementById("password");
            let confirmPassword = document.getElementById("confirm_password");
            let checkBox = document.getElementById("show_password");

            if (checkBox.checked) {
                password.type = "text";
                confirmPassword.type = "text";
            } else {
                password.type = "password";
                confirmPassword.type = "password";
            }
        }
    </script>
</head>
<body>

    <!-- Boutons de changement de langue -->
    <div>
        <span><?= $translations[$lang]['language'] ?></span>
        <div class="language-links">
  <a href="?lang=fr" class="lang-link active">FR Français</a>
  <a href="?lang=en" class="lang-link">GB English</a>
</div>

    <h2><?= $translations[$lang]['title'] ?></h2>
    <form method="post" action="../backend/traitement_inscriptionpro.php" enctype="multipart/form-data">
        <label><?= $translations[$lang]['company_name'] ?></label>
        <input type="text" name="entreprise_nom" required><br>

        <label><?= $translations[$lang]['email'] ?></label>
        <input type="email" name="email" required><br>

        <label><?= $translations[$lang]['phone'] ?></label>
        <input type="tel" id="telephone" name="telephone" placeholder="avec l'indicatif / with callsign. Ex:+228 96295595" required><br>

        <label><?= $translations[$lang]['country'] ?></label>
        <input type="text" name="pays" required><br>

        <label><?= $translations[$lang]['city'] ?></label>
        <input type="text" name="ville" required><br>

        <label><?= $translations[$lang]['secret_word'] ?></label>
        <input type="text" name="mot_secret" required><br>
        <small><?= $translations[$lang]['secret_note'] ?></small><br>

        <label><?= $translations[$lang]['password'] ?></label>
        <input type="password" id="password" name="mot_de_passe" required onkeyup="checkPasswordMatch()"><br>

        <label><?= $translations[$lang]['confirm_password'] ?></label>
        <input type="password" id="confirm_password" name="conf_mot_de_passe" required onkeyup="checkPasswordMatch()"><br>
        <span id="password_error" style="color: red; display: none;">Les mots de passe ne correspondent pas./Passwords do not match.</span><br>

        <input type="checkbox" id="show_password" onclick="togglePasswordVisibility()"> <?= $translations[$lang]['show_password'] ?><br>

        <label><?= $translations[$lang]['currency'] ?></label>
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

        <label><?= $translations[$lang]['profile_pic'] ?></label>
        <input type="file" name="photo_profil" accept="image/*" required><br>

        <button type="submit"><?= $translations[$lang]['register'] ?></button>
    </form>
    <a href="choix_inscription.php" class="btn-verifier-identite">Back/Retour</a>

</body>
</html>
