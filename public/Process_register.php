<?php
session_start();
require_once '../config/database.php';

// Vérifie si les champs sont remplis
if (!isset($_POST['username'], $_POST['email'], $_POST['password'], $_POST['confirm_password'])) {
    $_SESSION['register_error'] = "Please fill in all fields.";
    header('Location: register.php');
    exit;
}

$username = trim($_POST['username']);
$email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
if (!$email) {
    $_SESSION['register_error'] = "Invalid email address.";
    header('Location: register.php');
    exit;
}

$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

// Vérifie si les mots de passe correspondent
if ($password !== $confirm_password) {
    $_SESSION['register_error'] = "Passwords do not match.";
    header('Location: register.php');
    exit;
}

// Vérifie si l'utilisateur ou l'email existe déjà
try {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
    $stmt->execute([
        ':username' => $username,
        ':email' => $email
    ]);

    if ($stmt->fetch()) {
        $_SESSION['register_error'] = "Username or email already taken.";
        header('Location: register.php');
        exit;
    }

    // Hashe le mot de passe
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Définir le rôle par défaut
    $role = 'etudiant'; // Par défaut, les nouveaux utilisateurs sont des étudiants

    // Insère le nouvel utilisateur avec le rôle
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)");
    $stmt->execute([
        ':username' => $username,
        ':email' => $email,
        ':password' => $hashed_password,
        ':role' => $role
    ]);

    $_SESSION['register_success'] = "Account created successfully. You can now login.";
    header('Location: login.php');
    exit;

} catch (PDOException $e) {
    $_SESSION['register_error'] = "An error occurred. Please try again later.";
    // Pour debug (à désactiver en prod) : $_SESSION['register_error'] = $e->getMessage();
    header('Location: register.php');
    exit;
}
?>
