<?php
session_start();
require_once '../config/database.php';

// Génération d'un token CSRF si pas encore défini
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$username = ''; // par défaut vide

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Vérifier le token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['login_error'] = "Requête invalide (CSRF détecté).";
        header('Location: login.php');
        exit;
    }

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Rediriger selon le rôle
            if ($user['role'] === 'admin') {
                header('Location: admin__dashboard.php');
            } else {
                header('Location: etudiants_dashboard.php');
            }
            exit;
        } else {
            $_SESSION['login_error'] = "Identifiants invalides.";
        }
    } catch (PDOException $e) {
        $_SESSION['login_error'] = "Erreur de connexion : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>

    <?php
    if (isset($_SESSION['login_error'])) {
        echo '<p style="color:red;">' . htmlspecialchars($_SESSION['login_error']) . '</p>';
        unset($_SESSION['login_error']);
    }
    ?>

    <form method="post" action="login.php">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

        <label for="username">Nom d'utilisateur:</label>
        <input type="text" id="username" name="username" required value="<?= htmlspecialchars($username) ?>"><br><br>

        <label for="password">Mot de passe:</label>
        <input type="password" id="password" name="password" required><br><br>

        <input type="submit" value="Se connecter">
    </form>
</body>
</html>
