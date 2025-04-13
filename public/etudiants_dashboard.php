<?php
session_start();
require_once '../config/database.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'etudiant') {
    header('Location: login.php');
    exit();
}

// Récupération des infos de l'étudiant connecté
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$etudiant = $stmt->fetch(PDO::FETCH_ASSOC);

// Vérifier si l'étudiant existe
if (!$etudiant) {
    echo "Étudiant introuvable.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Étudiant</title>
</head>
<body>
    <h1>Bienvenue, <?php echo htmlspecialchars($etudiant['username']); ?> !</h1>
    <p>Email : <?php echo htmlspecialchars($etudiant['email']); ?></p>

    <!-- Lien vers les documents -->
    <h2>Mes Documents</h2>
    <ul>
        <li><a href="mes_documents.php">Voir mes documents</a></li>
        <li><a href="upload_documents.php">Ajouter un document</a></li>
    </ul>

    <!-- Lien vers les notes -->
    <h2>Mes Notes</h2>
    <a href="mes_notes.php">Voir mes notes</a>

    <p><a href="logout.php">Déconnexion</a></p>
</body>
</html>

