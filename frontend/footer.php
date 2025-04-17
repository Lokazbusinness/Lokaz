<?php
include 'header.php';
?>
<section class="footer-container" style="padding: 40px; max-width: 900px; margin: auto; text-align: center; font-family: Arial, sans-serif;">
    <!-- Boutons de langue -->
    <div style="margin-bottom: 20px;">
        <button onclick="setLanguage('fr')" style="margin-right: 10px;">Français</button>
        <button onclick="setLanguage('en')">English</button>
    </div>

    <!-- Contenu en français -->
    <div id="footer-fr">
        <h2>Informations importantes</h2>
        <p>
            Ces informations vous permettent de mieux connaître notre plateforme, ses conditions d’utilisation, ainsi que notre politique de confidentialité.
            N’hésitez pas à les consulter pour toute question concernant l’utilisation de notre service.
        </p>
        <nav>
            <ul style="list-style: none; padding: 0; display: flex; justify-content: center; gap: 20px; margin-top: 20px;">
                <li><a href="à_propos.php" class="footer-link">À propos</a></li>
                <li><a href="contact.php" class="footer-link">Contact</a></li>
                <li><a href="terms.php" class="footer-link">Conditions d’utilisation</a></li>
                <li><a href="privacy.php" class="footer-link">Politique de confidentialité</a></li>
              
            </ul>
        </nav>
        <a href="dashboard.php" class="dashboard-item-back">retour</a>

    </div>

    <!-- Contenu en anglais -->
    <div id="footer-en" style="display: none;">
        <h2>Important Information</h2>
        <p>
            This section helps you better understand our platform, its terms of use, and our privacy policy.
            Feel free to consult them for any questions about using our service.
        </p>
        <nav>
            <ul style="list-style: none; padding: 0; display: flex; justify-content: center; gap: 20px; margin-top: 20px;">
                <li><a href="à_propos.php" class="footer-link">About</a></li>
                <li><a href="contact.php" class="footer-link">Contact</a></li>
                <li><a href="terms.php" class="footer-link">Terms of Use</a></li>
                <li><a href="privacy.php" class="footer-link">Privacy Policy</a></li>
            </ul>     
        </nav>
        <a href="dashboard.php" class="dashboard-item-back">back</a>

    </div>

    <script>
        function setLanguage(lang) {
            if (lang === 'fr') {
                document.getElementById('footer-fr').style.display = 'block';
                document.getElementById('footer-en').style.display = 'none';
            } else {
                document.getElementById('footer-fr').style.display = 'none';
                document.getElementById('footer-en').style.display = 'block';
            }
        }
    </script>
<style>
    body {
        background-color: #ffffff;
        color: rgb(103, 38, 59); /* Bordeaux plus doux pour le texte principal */
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
    }

    .footer-container {
        background-color: #ffffff;
        border: 1px solid rgb(84, 48, 58); /* Bordure bordeaux doux */
        border-radius: 8px;
        box-shadow: 0 0 8px rgba(119, 88, 96, 0.4); /* Ombre bordeaux légèrement transparente */
    }

    h2 {
        color: rgb(103, 30, 51); /* Bordeaux doux pour les titres */
        font-size: 22px;
        margin-bottom: 15px;
    }

    p {
        color: rgb(128, 0, 32); /* Bordeaux un peu plus profond pour le texte */
        font-size: 16px;
        line-height: 1.6;
        max-width: 700px;
        margin: 0 auto 20px auto;
    }

    button {
        background-color: rgb(120, 40, 60); /* Bordeaux modéré pour les boutons */
        color: #ffffff;
        border: none;
        padding: 10px 16px;
        font-size: 14px;
        border-radius: 4px;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    button:hover {
        background-color: rgb(139, 41, 61); /* Bordeaux plus clair au survol */
    }

    nav ul {
        display: flex;
        justify-content: center;
        padding: 0;
        list-style: none;
        gap: 25px;
        margin-top: 20px;
    }

    .footer-link {
        text-decoration: none;
        color: rgb(128, 0, 32); /* Bordeaux doux pour les liens */
        font-weight: bold;
        transition: color 0.3s;
    }

    .footer-link:hover {
        color: rgb(139, 41, 61); /* Bordeaux plus clair au survol */
        text-decoration: underline;
    }

    a {
        display: block;
        text-align: center;
        margin-top: 30px;
        color: rgb(128, 0, 32); /* Bordeaux doux pour les liens */
        font-weight: bold;
        text-decoration: none;
    }

    a:hover {
        color: rgb(139, 41, 61); /* Bordeaux plus clair au survol */
        text-decoration: underline;
    }

    /* Style du bouton Back/Retour */
    .dashboard-item-back {
        display: inline-block;
        background-color: rgb(128, 0, 32); /* Bordeaux doux pour le bouton retour */
        color: white;
        padding: 12px 24px;
        font-size: 16px;
        border: none;
        border-radius: 6px;
        text-decoration: none;
        text-align: center;
        transition: background-color 0.3s, transform 0.2s;
    }

    .dashboard-item-back:hover {
        background-color: rgb(139, 41, 61); /* Bordeaux plus clair au survol */
        transform: scale(1.05);
    }

    .dashboard-item-back:active {
        background-color: rgb(102, 0, 26); /* Bordeaux plus foncé au clic */
        transform: scale(1);
    }
</style>


