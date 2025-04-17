<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = 'localhost:3307';
$username = "root";
$password = "";
$dbname = "lokaz";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Vérifie si une session est déjà active avant de l'initialiser
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>



