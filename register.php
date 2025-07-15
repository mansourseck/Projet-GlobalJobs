<?php
session_start();
require 'header.html';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inscription - GlobalJobs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Arrière-plan */
        body {
            background-color: white;
            min-height: 100vh;
        }

        /* Style de la carte d'inscription */
        .card-custom {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 3px solid rgba(1, 125, 214, 0.8);
            box-shadow: 0 8px 32px rgba(1, 125, 214, 0.2);
            padding: 40px;
            width: 100%;
        }

        /* Container principal pour le layout divisé */
        .container-center {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 140px);
            padding-top: 90px;
            padding-bottom: 50px;
        }

        /* Section texte de présentation */
        .presentation-section {
            padding: 40px;
            height: fit-content;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .presentation-section h2 {
            color: rgba(1, 125, 214, 1);
            margin-bottom: 20px;
            font-size: 2.2rem;
            font-weight: bold;
        }

        .presentation-section p {
            line-height: 1.6;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }

        .feature-list {
            list-style: none;
            padding-left: 0;
            margin-top: 20px;
        }

        .feature-list li {
            margin-bottom: 12px;
            padding-left: 30px;
            position: relative;
            font-size: 1rem;
        }

        .feature-list li:before {
            content: "🚀";
            position: absolute;
            left: 0;
            font-size: 1.2rem;
        }

        .btn-save {
            background-color: rgba(1, 125, 214, 1);
            color: white;
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            font-size: 1.1rem;
        }

        .btn-save:hover {
            background-color: rgba(1, 100, 180, 1);
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 5px;
            color: #333;
        }

        .form-control {
            margin-bottom: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 10px;
        }

        .form-control:focus {
            border-color: rgba(1, 125, 214, 0.8);
            box-shadow: 0 0 0 0.2rem rgba(1, 125, 214, 0.25);
        }

        .login-link {
            text-decoration: none;
            color: rgba(1, 125, 214, 1);
            font-weight: 500;
        }

        .login-link:hover {
            color: rgba(1, 100, 180, 1);
            text-decoration: underline;
        }

        .welcome-badge {
            background: linear-gradient(135deg, rgba(1, 125, 214, 0.1), rgba(1, 125, 214, 0.05));
            border: 2px solid rgba(1, 125, 214, 0.3);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
            text-align: center;
        }

        .welcome-badge h3 {
            color: rgba(1, 125, 214, 1);
            margin-bottom: 10px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container-center {
                flex-direction: column;
                padding: 100px 15px 80px;
            }
            
            .presentation-section {
                margin-top: 30px;
                padding: 20px;
            }
            
            .card-custom {
                padding: 25px;
            }
        }
    </style>
</head>

<body>
    <div class="container container-center">
        <div class="row w-100">
            <!-- Section Inscription (Gauche) -->
            <div class="col-lg-6 d-flex justify-content-center align-items-center">
                <div class="col-md-10">
                    <div class="card-custom">
                        <div class="text-center mb-4">
                            <h3 style="color: rgba(1, 125, 214, 1);">📝 Créer votre compte</h3>
                            <p class="text-muted">Rejoignez la communauté GlobalJobs</p>
                        </div>

                        <?php
                        // Display messages if any
                        if (isset($_SESSION['message'])) {
                            echo '<div class="alert alert-danger text-center">' . $_SESSION['message'] . '</div>';
                            unset($_SESSION['message']);
                        }
                        ?>

                        <form action="register_process.php" method="POST">
                            <label for="nom" class="form-label">👤 Nom :</label>
                            <input type="text" name="nom" class="form-control" required
                                value="<?php echo isset($_SESSION['nom']) ? htmlspecialchars($_SESSION['nom']) : ''; ?>"
                                placeholder="Votre nom de famille">

                            <label for="prenom" class="form-label">👤 Prénom :</label>
                            <input type="text" name="prenom" class="form-control" required
                                value="<?php echo isset($_SESSION['prenom']) ? htmlspecialchars($_SESSION['prenom']) : ''; ?>"
                                placeholder="Votre prénom">

                            <label for="email" class="form-label">📧 Email :</label>
                            <input type="email" name="email" class="form-control" required
                                value=""
                                placeholder="votre.email@exemple.com">

                            <label for="password" class="form-label">🔒 Mot de passe :</label>
                            <input type="password" name="password" class="form-control" required
                                placeholder="Créez un mot de passe sécurisé">

                            <label for="adresse" class="form-label">📍 Adresse :</label>
                            <input type="text" name="adresse" class="form-control" required
                                value="<?php echo isset($_SESSION['adresse']) ? htmlspecialchars($_SESSION['adresse']) : ''; ?>"
                                placeholder="Votre adresse complète">

                            <label for="telephone" class="form-label">📞 Téléphone :</label>
                            <input type="tel" name="telephone" class="form-control" required
                                placeholder="Ex : +221 77 123 45 67">

                            <label for="role" class="form-label">🎯 Rôle :</label>
                            <select name="role" class="form-control" required>
                                <option value="">-- Sélectionnez votre rôle --</option>
                                <option value="Candidat" <?php echo (isset($_SESSION['role']) && $_SESSION['role'] == "Candidat") ? 'selected' : ''; ?>>👨‍💼 Candidat</option>
                                <option value="Recruteur" <?php echo (isset($_SESSION['role']) && $_SESSION['role'] == "Recruteur") ? 'selected' : ''; ?>>🏢 Recruteur</option>
                            </select>

                            <button type="submit" class="btn btn-save">✅ S'inscrire</button>
                        </form>

                        <p class="mt-3 text-center">
                            Déjà inscrit ?
                            <a href="loginh.php" class="login-link">🔑 Se connecter</a>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Section Présentation (Droite) -->
            <div class="col-lg-6 d-flex align-items-center">
                <div class="presentation-section">
                    <div class="welcome-badge">
                        <h3>🌟 Bienvenue dans l'aventure !</h3>
                        <p class="mb-0">Votre futur emploi vous attend sur GlobalJobs</p>
                    </div>

                    <h2>Pourquoi s'inscrire ?</h2>
                    <p>
                        En créant votre compte GlobalJobs, vous accédez à un écosystème professionnel 
                        complet qui vous accompagne dans votre réussite.
                    </p>

                    <ul class="feature-list">
                        <li>Profil professionnel optimisé pour attirer les recruteurs</li>
                        <li>Candidatures simplifiées en un clic</li>
                        <li>Alertes personnalisées selon vos critères</li>
                        <li>Suivi en temps réel de vos candidatures</li>
                        <li>Accès aux entreprises partenaires exclusives</li>
                        <li>Conseils carrière et coaching personnalisé</li>
                        <li>Réseau professionnel étendu</li>
                    </ul>

                    <div style="background: linear-gradient(135deg, rgba(1, 125, 214, 0.1), rgba(1, 125, 214, 0.05)); 
                                border-left: 4px solid rgba(1, 125, 214, 1); 
                                padding: 15px; 
                                border-radius: 8px; 
                                margin-top: 25px;">
                        <p style="margin: 0; font-weight: 500; color: rgba(1, 125, 214, 1);">
                            💡 <strong>Astuce :</strong> Un profil complet augmente vos chances d'être contacté de 70% !
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>

<?php require 'footer.html'; ?>