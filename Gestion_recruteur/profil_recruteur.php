<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>🏢 Inscription Recruteur - GlobalJobs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: 
                url('../images/image3.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
        }
        
        .header {
            position: fixed;
            width: 100%;
            background: rgba(51, 213, 231, 0.8);
            color: rgb(253, 251, 251);
            padding: 15px;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .header h1 {
            text-align: center;
            flex-grow: 1;
            margin: 0;
        }

        .header .btn {
            position: absolute;
            right: 60px;
            background-color: #007bff;
            color: white;
        }

        .footer {
            width: 100%;
            background: rgba(51, 213, 231, 0.8);
            color: black;
            text-align: center;
            padding: 15px;
            margin-top: 50px;
        }

        .card-main {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            margin: 0 auto;
        }

        .container-main {
            padding-top: 90px;
            padding-bottom: 20px;
            min-height: calc(100vh - 90px);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-back {
            background-color: #6c757d;
            color: white;
            padding: 10px 30px;
        }

        .alert {
            border-radius: 10px;
        }

        .form-control, select.form-control {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 12px;
        }

        .form-control:focus, select.form-control:focus {
            border-color: #33d5e7;
            box-shadow: 0 0 0 0.2rem rgba(51, 213, 231, 0.25);
        }

        .btn-primary {
            background-color: #33d5e7;
            border-color: #33d5e7;
            border-radius: 8px;
            padding: 12px;
        }

        .btn-primary:hover {
            background-color: #2bb8cc;
            border-color: #2bb8cc;
        }

        .btn-success {
            border-radius: 8px;
            padding: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="../index.php" class="btn">🏠 Accueil</a>
        <h1>🏢 Inscription Recruteur</h1>
    </div>

    <div class="container container-main">
        <div class="card-main w-100">
            <h2 class="text-center mb-4">🏢 Créer un compte entreprise</h2>
            <p class="text-center text-muted mb-4">Rejoignez GlobalJobs et publiez vos offres d'emploi</p>

            <!-- Affichage du message -->
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle"></i> <?= $_SESSION['message']; ?>
                </div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>

            <form id="inscriptionForm" action="traitement_inscription.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">🏢 Nom de l'entreprise</label>
                    <input type="text" name="entreprise" class="form-control" placeholder="Ex: TechCorp Solutions" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">📧 Email professionnel de l'entreprise</label>
                    <input type="email" name="email_entreprise" class="form-control" placeholder="contact@entreprise.com" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">🏭 Secteur d'activité</label>
                    <select name="secteur" class="form-control" required>
                        <option value="">-- Sélectionnez un secteur --</option>
                        <option value="Technologie">🖥️ Technologie</option>
                        <option value="Finance">💰 Finance</option>
                        <option value="Santé">🏥 Santé</option>
                        <option value="Éducation">🎓 Éducation</option>
                        <option value="Commerce">🛒 Commerce</option>
                        <option value="Construction">🏗️ Construction</option>
                        <option value="Transport">🚛 Transport</option>
                        <option value="Industrie">🏭 Industrie</option>
                        <option value="Agriculture">🌾 Agriculture</option>
                        <option value="Tourisme">✈️ Tourisme</option>
                        <option value="Communication">📢 Communication</option>
                        <option value="Consulting">💼 Consulting</option>
                        <option value="Immobilier">🏠 Immobilier</option>
                        <option value="Énergie">⚡ Énergie</option>
                        <option value="Agroalimentaire">🍽️ Agroalimentaire</option>
                        <option value="Autre">🔧 Autre</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">📍 Adresse de l'entreprise</label>
                    <input type="text" name="adresse" class="form-control" placeholder="123 Rue de l'Entreprise, Ville" required>
                </div>

                <button type="submit" class="btn btn-success w-100 mb-3" style="background-color: #007bff;">
                    ✅ Enregistrer l'entreprise
                </button>
            </form>

        </div>
    </div>

    <div class="footer">
        🌟 &copy; 2025 GlobalJobs 🌟 
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Vider les champs après soumission
        document.getElementById("inscriptionForm").addEventListener("submit", function() {
            setTimeout(() => {
                document.getElementById("inscriptionForm").reset();
            }, 500);
        });
    </script>
</body>
</html>