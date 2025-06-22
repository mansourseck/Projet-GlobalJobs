<?php
require "../db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $admin_code = trim($_POST["admin_code"]);

    $allowed_admin_code = "GJADMIN2025"; // Code requis pour créer un admin

    if ($admin_code !== $allowed_admin_code) {
        $message = "<p style='color: red;'>Code d’accès incorrect !</p>";
    } else {
        $stmt = $conn->prepare("SELECT idAdmin FROM admin WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            $message = "<p style='color: red;'>Email déjà utilisé !</p>";
        } else {
            $stmt = $conn->prepare("INSERT INTO admin (name, email, password, is_active) VALUES (?, ?, ?, 1)");
            
            if ($stmt->execute([$name, $email, $password])) {
                $message = "<p style='color: green;'>Compte administrateur créé avec succès !</p>";
            } else {
                $message = "<p style='color: red;'>Erreur lors de la création du compte.</p>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un compte Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow" style="width: 400px;">
            <h3 class="text-center">Créer un compte Admin</h3>
            <?= $message ?>
            <form action="#" method="POST">
                <div class="mb-3">
                    <label class="form-label">Nom :</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email :</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mot de passe :</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Code d’accès Admin :</label>
                    <input type="text" name="admin_code" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-danger w-100">Créer un compte</button>
            </form>
        </div>
    </div>
</body>
</html>