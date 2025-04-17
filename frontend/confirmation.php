<?php
session_start();
$message = $_SESSION['message'] ?? "Merci, votre demande a été enregistrée. / Thank you, your request has been registered. ";
unset($_SESSION['message']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Confirmation de Paiement / Payment Confirmation</title>
    <style>
       <style>
body {
    font-family: Arial, sans-serif;
    text-align: center;
    margin-top: 100px;
    background-color: #d1d1d1; /* Gris clair mais pas trop doux */
    color: #444444; /* Gris foncé pour le texte */
}

h1 {
    color: #28a745; /* Vert pour le succès */
    font-size: 2.5em;
    font-weight: bold;
}

p {
    font-size: 1.2em;
    color: #555555; /* Gris moyen pour le texte */
    line-height: 1.5;
    margin-top: 20px;
}

a {
    display: inline-block;
    margin-top: 30px;
    text-decoration: none;
    color:rgb(34, 36, 39); /* Bleu clair pour les liens */
    font-size: 1.1em;
    border: 2px solidrgb(23, 24, 26); /* Bleu clair pour les bordures */
    padding: 10px 20px;
    border-radius: 5px;
    transition: background-color 0.3s, color 0.3s;
}

a:hover {
    background-color:rgb(39, 41, 43); /* Bleu clair au survol */
    color: white;
}
</style>

    </style>
</head>
<body>

<h1>✔️ <?= htmlspecialchars($message) ?></h1>
<p>Notre équipe va examiner votre paiement, si il est correct votre abonnement sera activé dans les 24H. Dans le cas ou il est incorrecte (dépot effectuer sur un numéro mobile ou bancaire autre que celui fourni sur le site) votre paiement sera rejeté. / 
Our team will examine your payment, and if it is correct, your subscription will be activated within 24 hours. If it is incorrect (deposit made on a mobile or bank number other than that provided on the site) your payment will be rejected.
</p>
<a href="dashboard.php">Retour au dashboard./Back to dashboard.</a>

</body>
</html>
