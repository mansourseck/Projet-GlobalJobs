<?php
session_start();
$message = isset($_SESSION["message"]) ? $_SESSION["message"] : "";
unset($_SESSION["message"]); // Supprimer le message après affichage
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Arrière-plan */
        body {
            background: url(./images/image1.jpg) no-repeat center center fixed;
            background-size: cover;
        }

        /* Style du header */
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

        /* Style du footer */
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background: rgba(1, 125, 214, 0.8);
            color: white;
            text-align: center;
            padding: 10px;

        }

        /* Effet de flou sur la carte */
        .card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border-radius: 15px;
        }

        /* Ajustement du contenu pour éviter que le formulaire soit caché */
        /* Centrer le texte "Bienvenue sur GlobalJobs" */
        .header h1 {
            flex: 1;
            text-align: center;
            margin-left: 90px;
        }

        /* Assurer un bon centrage du contenu */
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding-top: 80px;
            /* Pour éviter que le formulaire soit caché par le header */
        }
    </style>
</head>

<body>
    <!-- Header avec bouton Accueil -->
    <div class="header">
        <h1>Bienvenue sur GlobalJobs</h1>
        <a href="index.php" class="btn btn-light">🏠 Accueil</a>
    </div>

    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow" style="width: 400px;">
            <h3 class="text-center">Connexion</h3>

            <?php if (!empty($message)): ?>
                <div class="alert alert-danger text-center"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">Email :</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mot de passe :</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Se connecter</button>
            </form>

            <p class="text-center mt-3">
                <a href="mot_de_passe_oublie.php">Mot de passe oublié ?</a>
            </p>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        &copy; 2025 GlobalJobs
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>