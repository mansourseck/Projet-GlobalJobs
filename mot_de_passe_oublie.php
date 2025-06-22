<?php
session_start();
require 'db.php';

$message = ""; // Variable pour stocker le message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);

    // Vérifier si l'utilisateur existe
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        $message = "<div class='alert alert-danger'>Aucun compte trouvé avec cet email.</div>";
    } else {
        // Génération du token sécurisé
        $token = bin2hex(random_bytes(50));
        $stmt = $conn->prepare("UPDATE users SET reset_token = ? WHERE email = ?");
        $stmt->execute([$token, $email]);

        // Affichage du lien directement sur la page
        $reset_link = "http://localhost/projet_php_GlobalJobs/reinitialisation.php?token=" . $token;
        $message= "<div class='alert alert-danger'> compte trouvé .</div>";
        echo "<div class='alert alert-info'>Lien de réinitialisation : <a href='$reset_link'>$reset_link</a></div>";
        
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mot de passe oublié</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
     
        .header {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(1, 125, 214, 0.8);
            color: white;
            padding: 15px;
            text-align: center;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
        }

        /* Bouton "Accueil" en bleu */
        .header .btn {
            background-color:rgba(1,125, 214, 0.8);
            color: white;
            border: none;
        }
        .header h1 {
            flex: 1;
            text-align: center;
            margin-left: 90px;
        }
        .card{
        
           border-color: rgba(1,125, 214, 0.8);
           margin-bottom: 330px;
        }
        .alert{
            margin-top: 100px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reinitialisation</h1>
        <a href="index.php" class="btn btn-light">🏠 Accueil</a>
    </div>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow-lg" style="width: 600px;">
            <h2 class="text-center">Réinitialisation du mot de passe</h2>

            <!-- Affichage dynamique du message -->
            <? $message; ?>

            <form action="#" method="POST">
                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Envoyer le lien</button>
            </form>

        </div>
    </div>
</body>
</html>