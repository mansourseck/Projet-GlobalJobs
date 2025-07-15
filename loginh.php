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
            background-color: white;
            min-height: 100vh;
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
            background-color: rgba(1, 125, 214, 0.8);
            color: white;
            border: none;
        }

        /* Style du footer */
        .footer {
            bottom: 0;
            width: 100%;
            background: rgba(1, 125, 214, 0.8);
            color: white;
            text-align: center;
            padding: 10px;
        }

        /* Effet de flou sur la carte */
        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            border: 3px solid rgba(1, 125, 214, 0.8);
            box-shadow: 0 8px 32px rgba(1, 125, 214, 0.2);
        }

        /* Centrer le texte "Bienvenue sur GlobalJobs" */
        .header h1 {
            flex: 1;
            text-align: center;
            margin-left: 90px;
        }

        /* Container principal pour le layout divisé */
        .main-container {
            padding-top: 80px;
            padding-bottom: 60px;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        /* Section texte de présentation */
        .presentation-section {
            padding: 30px;
            height: fit-content;
        }

        .presentation-section h2 {
            color: rgba(1, 125, 214, 1);
            margin-bottom: 20px;
        }

        .presentation-section p {
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .feature-list {
            list-style: none;
            padding-left: 0;
        }

        .feature-list li {
            margin-bottom: 10px;
            padding-left: 25px;
            position: relative;
        }

        .feature-list li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: rgba(1, 125, 214, 1);
            font-weight: bold;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-container {
                flex-direction: column;
                padding: 100px 15px 80px;
            }

            .presentation-section {
                margin-top: 20px;
            }
        }
    </style>
</head>

<body>
    <!-- Header avec bouton Accueil -->
    <div class="header">
        <h1>Bienvenue sur GlobalJobs</h1>
        <a href="index.php" class="btn btn-light">🏠 Accueil</a>
    </div>

    <div class="container main-container">
        <div class="row w-100">
            <!-- Section Connexion (Gauche) -->
            <div class="col-lg-6 d-flex justify-content-center align-items-center">
                <div class="card p-4 shadow" style="width: 100%; max-width: 400px;">
                    <h3 class="text-center mb-4">Connexion</h3>

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

            <!-- Section Présentation (Droite) -->
            <div class="col-lg-6 d-flex align-items-center">
                <div class="presentation-section">
                    <h2>Votre Carrière Vous Attend</h2>
                    <p>
                        GlobalJobs est votre plateforme de référence pour trouver l'emploi de vos rêves.
                        Nous connectons les talents avec les meilleures opportunités professionnelles
                        à travers le monde.
                    </p>

                    <h4 style="color: rgba(1, 125, 214, 1); margin-top: 25px; margin-bottom: 15px;">
                        Pourquoi choisir GlobalJobs ?
                    </h4>

                    <ul class="feature-list">
                        <li>Accès à des milliers d'offres d'emploi actualisées quotidiennement</li>
                        <li>Outils de recherche avancés pour trouver l'emploi parfait</li>
                        <li>Profil professionnel personnalisable</li>
                        <li>Notifications en temps réel des nouvelles opportunités</li>
                        <li>Support dédié pour vous accompagner dans votre recherche</li>
                        <li>Interface intuitive et moderne</li>
                    </ul>

                    <p style="margin-top: 25px; font-weight: 500; color: rgba(1, 125, 214, 1);">
                        Rejoignez des milliers de professionnels qui ont déjà trouvé leur emploi idéal avec GlobalJobs !
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        &copy; 2025 GlobalJobs
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>