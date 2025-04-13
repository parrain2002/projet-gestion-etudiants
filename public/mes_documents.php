<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'etudiant') {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Récupérer les documents
$stmt = $pdo->prepare("SELECT nom_fichier, chemin_fichier FROM documents WHERE user_id = ?");
$stmt->execute([$user_id]);
$documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Documents</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="mb-4">Mes Documents</h2>
        <ul class="list-group">
            <?php foreach ($documents as $doc): ?>
                <li class="list-group-item">
                    <a href="<?= htmlspecialchars($doc['chemin_fichier']) ?>" download>
                        <?= htmlspecialchars($doc['nom_fichier']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <a href="etudiants_dashboard.php" class="btn btn-secondary mt-3">Retour</a>
    </div>
</body>
</html>