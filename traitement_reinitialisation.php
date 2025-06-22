<?php
session_start();
require 'db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['token'], $_POST['mot_de_passe'])) {
        $message = "Données invalides.";
    } else {
        $token = $_POST['token'];
        $new_password = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);

        $stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ?");
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if (!$user) {
            $message = "Lien invalide ou expiré.";
        } else {
            $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL WHERE id = ?");
            $stmt->execute([$new_password, $user['id']]);
            $message = "Mot de passe réinitialisé avec succès.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouveau mot de passe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow-lg" style="width: 400px;">
            <h2 class="text-center">Nouveau mot de passe</h2>
            <?php if ($message): ?>
                <div class="alert alert-info text-center"><?= $message; ?></div>
            <?php endif; ?>
            <form action="" method="POST">
                <input type="hidden" name="token" value="<?= $_GET['token'] ?? ''; ?>">
            </form>
            <?php if ($message === "Mot de passe réinitialisé avec succès."): ?>
                <a href="loginh.php" class="btn btn-success w-100 mt-3">Se connecter</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>