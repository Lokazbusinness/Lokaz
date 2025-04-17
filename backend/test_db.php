<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../backend/db.php';

try {
    $stmt = $pdo->query("SELECT 1");
    echo "Connexion réussie !";
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
