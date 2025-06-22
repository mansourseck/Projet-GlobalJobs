<?php
session_start();
require 'db.php';

if (!isset($_GET['token'])) {
    die("Lien invalide.");
}

$token = $_GET['token'];
$stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ?");
$stmt->execute([$token]);
$user = $stmt->fetch();

if (!$user) {
    die("Lien invalide ou expiré.");
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
            <form action="traitement_reinitialisation.php" method="POST">
                <input type="hidden" name="token" value="<?= $token; ?>">
                <div class="mb-3">
                    <label>Nouveau mot de passe</label>
                    <input type="password" name="mot_de_passe" class="form-control" required minlength="8">
                </div>
                <button type="submit" class="btn btn-primary w-100">Réinitialiser</button>
            </form>
        </div>
    </div>
</body>
</html>