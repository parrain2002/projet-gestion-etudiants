<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'etudiant') {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';

// Générer un token CSRF si absent
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['document'])) {
    // Vérifier le CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $message = "Échec de la vérification du jeton CSRF.";
    } else {
        $file = $_FILES['document'];
        $maxSize = 2 * 1024 * 1024; // 2 Mo
        $allowedMime = ['application/pdf', 'image/jpeg', 'image/png'];

        if ($file['error'] === UPLOAD_ERR_OK && $file['size'] <= $maxSize) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($file['tmp_name']);

            if (in_array($mime, $allowedMime)) {
                $uploadDir = '../uploads/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $filename = basename($file['name']);
                $chemin = $uploadDir . uniqid() . '_' . $filename;

                if (move_uploaded_file($file['tmp_name'], $chemin)) {
                    $stmt = $pdo->prepare("INSERT INTO documents (etudiant_id, nom_fichier, chemin) VALUES (?, ?, ?)");
                    $stmt->execute([$user_id, $filename, $chemin]);
                    $message = "Document uploadé avec succès.";
                } else {
                    $message = "Erreur lors du déplacement du fichier.";
                }
            } else {
                $message = "Type de fichier non autorisé.";
            }
        } else {
            $message = "Fichier trop volumineux ou erreur d'upload.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Upload Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="mb-4">Ajouter un Document</h2>
        <?php if ($message): ?>
            <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <div class="mb-3">
                <label for="document" class="form-label">Choisir un fichier (PDF, JPG, PNG, max 2Mo)</label>
                <input type="file" name="document" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Uploader</button>
            <a href="etudiants_dashboard.php" class="btn btn-secondary">Retour</a>
        </form>
    </div>
</body>
</html>

