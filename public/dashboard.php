<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord</title>
</head>
<body>
    <h2>Bienvenue <?= htmlspecialchars($_SESSION['user']['username']) ?> !</h2>
    <a href="logout.php">Déconnexion</a>
</body>
</html>
