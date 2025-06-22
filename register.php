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
        body {
            background: url(./images/image2.jpg) no-repeat center center fixed;
            background-size: cover;
        }
        
        .card-custom {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            margin: 0 auto;
        }

        .container-center {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 140px);
            padding-top: 90px;
            padding-bottom: 50px;
        }

        .btn-save {
            background-color: #007bff;
            color: white;
            width: 100%;
            padding: 10px;
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 5px;
        }

        .form-control {
            margin-bottom: 15px;
        }

        .login-link {
            text-decoration: none;
            color: #007bff;
            font-weight: 500;
        }

        .login-link:hover {
            color: #0056b3;
            text-decoration: underline;
        }
    </style>
</head>
<body>
  

    <div class="container container-center">
        <div class="col-md-8">
            <div class="card card-custom">
                <?php
                // Display messages if any
                if (isset($_SESSION['message'])) {
                    echo $_SESSION['message'];
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

                    <label for="telephone" class="form-label">📞 Téléphone :</label>
                    <input type="tel" name="telephone" class="form-control" required
                           placeholder="Ex : +221 77 123 45 67">

                    <label for="statut" class="form-label">🎯 Statut :</label>
                    <select name="statut" class="form-control" required>
                        <option value="">-- Sélectionnez votre statut --</option>
                        <option value="Candidat" <?php echo (isset($_SESSION['statut']) && $_SESSION['statut'] == "Candidat") ? 'selected' : ''; ?>>👨‍💼 Candidat</option>
                        <option value="Recruteur" <?php echo (isset($_SESSION['statut']) && $_SESSION['statut'] == "Recruteur") ? 'selected' : ''; ?>>🏢 Recruteur</option>
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

   
</body>
</html>

<?php require 'footer.html'; ?>