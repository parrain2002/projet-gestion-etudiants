<?php
require_once '../config/database.php';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Hachage des mots de passe</title>
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

        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        p {
            color: green;
            margin-bottom: 10px;
        }

        .final-message {
            color: #007bff;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Hachage des mots de passe</h2>
        <?php
        // Récupérer tous les utilisateurs
        $stmt = $pdo->query("SELECT id, password FROM users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($users as $user) {
            $id = $user['id'];
            $plain_password = $user['password'];

            // Hacher le mot de passe
            $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

            // Mettre à jour le mot de passe dans la base de données
            $update = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
            $update->execute(['password' => $hashed_password, 'id' => $id]);

            echo "<p>Mot de passe de l'utilisateur ID $id haché avec succès.</p>";
        }

        echo "<p class='final-message'>Tous les mots de passe ont été hachés.</p>";
        ?>
    </div>
</body>
</html>