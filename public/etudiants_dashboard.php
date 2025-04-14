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
    <style>
        body {
            font-family: sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .dashboard-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
        }

        p {
            color: #555;
            margin-bottom: 15px;
        }

        h2 {
            color: #333;
            margin-top: 30px;
            margin-bottom: 10px;
            text-align: left;
        }

        ul {
            list-style: none;
            padding: 0;
            margin-bottom: 20px;
            text-align: left;
        }

        ul li {
            margin-bottom: 8px;
        }

        ul li a {
            color: #007bff;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        ul li a:hover {
            color: #0056b3;
        }

        .logout-link {
            display: block;
            margin-top: 30px;
            color: #e74c3c;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .logout-link:hover {
            color: #c0392b;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1>Bienvenue, <?php echo htmlspecialchars($etudiant['username']); ?> !</h1>
        <p>Email : <?php echo htmlspecialchars($etudiant['email']); ?></p>

        <h2>Mes Documents</h2>
        <ul>
            <li><a href="mes_documents.php">Voir mes documents</a></li>
            <li><a href="upload_documents.php">Ajouter un document</a></li>
        </ul>

        <h2>Mes Notes</h2>
        <p><a href="mes_notes.php">Voir mes notes</a></p>

        <p><a class="logout-link" href="logout.php">Déconnexion</a></p>
    </div>
</body>
</html>