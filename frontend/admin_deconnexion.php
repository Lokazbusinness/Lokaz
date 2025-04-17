<?php
session_start();
session_unset();
session_destroy();
header("Location: admin_connexion.php"); // ou vers la page de connexion
exit();
