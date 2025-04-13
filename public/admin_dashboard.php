<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once '../config/database.php'; // adapte le chemin selon ton arborescence

// Vérifie si l'utilisateur est bien un admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Suppression d’un utilisateur
if (isset($_GET['delete_id'])) {
    $deleteId = intval($_GET['delete_id']); // Assurez-vous que l'ID est un entier
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
    $stmt->execute(['id' => $deleteId]);
    $message = "Utilisateur supprimé avec succès.";
}

// Ajout d’un utilisateur
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, created_at) VALUES (:username, :email, :password, :role, NOW())");
    $stmt->execute([
        'username' => $username,
        'email' => $email,
        'password' => $password,
        'role' => $role
    ]);
    $message = "Utilisateur ajouté avec succès.";
}

// Récupère tous les utilisateurs
$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
</head>
<body>
    <h2>Bienvenue, <?php echo htmlspecialchars($_SESSION['username']); ?> (Admin)</h2>

    <?php if (isset($message)): ?>
        <p style="color: green;"><?php echo $message; ?></p>
    <?php endif; ?>

    <h3>Liste des utilisateurs</h3>
    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Nom d'utilisateur</th>
            <th>Email</th>
            <th>Rôle</th>
            <th>Créé le</th>
            <th>Action</th>
        </tr>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['id']) ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['role']) ?></td>
                <td><?= htmlspecialchars($user['created_at']) ?></td>
                <td>
                    <a href="admin_dashboard.php?delete_id=<?= $user['id'] ?>" onclick="return confirm('Supprimer cet utilisateur ?')">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h3>Ajouter un utilisateur</h3>
    <form method="POST" action="">
        <label>Nom d'utilisateur:</label><br>
        <input type="text" name="username" required><br>

        <label>Email:</label><br>
        <input type="email" name="email" required><br>

        <label>Mot de passe:</label><br>
        <input type="password" name="password" required><br>

        <label>Rôle:</label><br>
        <select name="role" required>
            <option value="etudiant">Étudiant</option>
            <option value="admin">Admin</option>
        </select><br><br>

        <input type="submit" name="add_user" value="Ajouter">
    </form>

    <br><a href="logout.php">Se déconnecter</a>
</body>
</html>
