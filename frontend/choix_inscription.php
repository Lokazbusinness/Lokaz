<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choix d'inscription</title>
    <style>  
/* Style de base */
body {
    font-family: Arial, sans-serif;
    background-color: #ffffff; /* Fond blanc */
    color: #3b1d1d; /* Bordeaux brun très foncé pour le texte */
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: 100vh;
    text-align: center;
}

/* Titre */
h2 {
    color: #4b1e1e; /* Bordeaux brun foncé */
    font-size: 24px;
    margin-bottom: 20px;
    padding-top: 20px;
}

/* Boutons */
a {
    display: block;
    margin-top: 10px;
}

button {
    background-color: #5e2a2a; /* Bordeaux chocolat/brun foncé */
    color: #ffffff;
    border: none;
    padding: 12px 24px;
    font-size: 16px;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s;
}

button:hover {
    background-color: #3a1616; /* Bordeaux brun encore plus foncé */
}

/* Style du bouton Back/Retour */
.btn-verifier-identite {
    display: inline-block;
    background-color: #4b1e1e; /* Bordeaux brun profond */
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
    background-color: #3a1414; /* Très foncé au survol */
    transform: scale(1.05);
}

.btn-verifier-identite:active {
    background-color: #2a0e0e; /* Bordeaux brun presque noir lors du clic */
    transform: scale(1);
}
</style>

</head>
<body>
    <h2>Choisissez votre type de compte / Choose your account type </h2>
    <a href="inscription.php">
        <button> Inscription Particulier / Individual registration</button>
    </a>
    <a href="inscriptionpro.php">
        <button>Inscription Professionnel / Inscription Professionnel</button>
    </a>
</body>
<a href="Homepage.php" class="btn-verifier-identite">Back/Retour</a>
</html>
