<?php
session_start();
require "../db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    $stmt = $conn->prepare("SELECT idAdmin, password FROM admin WHERE email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin["password"])) {
            header("Location: Admin_dashboard.php");
    } else {
        $message = "<p style='color: red;'>Identifiants incorrects.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow" style="width: 400px;">
            <h3 class="text-center">Connexion Admin</h3>

            <?= !empty($message) ? "<div class='alert alert-danger text-center'>$message</div>" : "" ?>

            <form action="admin_login.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">Email Admin :</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mot de passe :</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-danger w-100">Se connecter</button>
            </form>
        </div>
    </div>
</body>
</html>