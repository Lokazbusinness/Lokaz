<?php
session_start();
require_once '../backend/db.php'; // adapte le chemin si besoin

$admin_email = "lokaz.business@gmail.com"; // Mets ton email admin ici
$admin_password = "200625"; // Défini un mot de passe fort ici

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($email === $admin_email && $password === $admin_password) {
        $_SESSION['email'] = $email;
        header('Location: admin_Homepage.php');
        exit;
    } else {
        $error = "Email ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Admin</title>
    <style>
        body { font-family: Arial; background-color: #f4f4f4; padding: 50px; }
        .container { max-width: 400px; margin: auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        input[type="email"], input[type="password"] { width: 100%; padding: 10px; margin: 10px 0; }
        button { width: 100%; padding: 10px; background: #333; color: white; border: none; }
        .error { color: red; margin-top: 10px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Connexion Administrateur</h2>
    <form method="post">
        <input type="email" name="email" placeholder="Email administrateur" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <button type="submit">Se connecter</button>
        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
    </form>
</div>
</body>
</html>
