<?php
session_start();
require_once '../config/database.php';

if (empty($_POST['username']) || empty($_POST['password'])) {
    $_SESSION['login_error'] = "Identifiants invalides.";
    header('Location: login.php');
    exit();
}

$username = $_POST['username'];
$password = $_POST['password'];

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role']
        ];

        // Redirection basée sur le rôle avec des chemins absolus
        if ($user['role'] === 'admin') {
            header('Location: http://localhost/STUDENT-MANAGEMENT-APP/public/admin_dashboard.php');
        } else {
            header('Location: http://localhost/STUDENT-MANAGEMENT-APP/public/etudiants_dashboard.php');
        }
        exit();
    } else {
        $_SESSION['login_error'] = "Identifiants invalides.";
        header('Location: login.php');
        exit();
    }

} catch (PDOException $e) {
    $_SESSION['login_error'] = "Une erreur s'est produite. Veuillez réessayer plus tard.";
    header('Location: login.php');
    exit();
}
?>
