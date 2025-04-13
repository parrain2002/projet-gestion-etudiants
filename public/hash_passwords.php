<?php
require_once '../config/database.php';

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

    echo "Mot de passe de l'utilisateur ID $id haché avec succès.<br>";
}

echo "Tous les mots de passe ont été hachés.";
?>
