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

        .container-main {
            padding-top: 90px;
            padding-bottom: 20px;
            min-height: calc(100vh - 90px);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .split-container {
            display: flex;
            max-width: 1200px;
            width: 100%;
            gap: 30px;
            align-items: stretch;
        }

        .form-section {
            flex: 1;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        }

        .info-section {
            flex: 1;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: #2d3748;
            text-align: center;
        }

        .form-subtitle {
            color: #666;
            margin-bottom: 30px;
            text-align: center;
            font-size: 1rem;
        }

        .info-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: #2d3748;
        }

        .info-text {
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 25px;
            color: #555;
        }

        .features {
            list-style: none;
            padding: 0;
        }

        .features li {
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            font-size: 1rem;
            color: #444;
        }

        .features li::before {
            content: '✨';
            margin-right: 10px;
            font-size: 1.1rem;
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
            background-color: #007bff;
            border-color: #007bff;
            border-radius: 8px;
            padding: 12px;
        }

        .btn-success:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .stats-highlight {
            background: rgba(51, 213, 231, 0.1);
            border-left: 4px solid #33d5e7;
            padding: 15px;
            margin-top: 30px;
            border-radius: 8px;
        }

        .stats-highlight strong {
            color: #33d5e7;
        }

        @media (max-width: 768px) {
            .split-container {
                flex-direction: column;
                gap: 20px;
            }

            .form-section, .info-section {
                padding: 30px 25px;
            }

            .form-title, .info-title {
                font-size: 1.5rem;
            }

            .header .btn {
                right: 20px;
                padding: 8px 15px;
                font-size: 0.9rem;
            }
        }

        /* Animation d'entrée */
        .form-section {
            animation: slideInLeft 0.6s ease-out;
        }

        .info-section {
            animation: slideInRight 0.6s ease-out;
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="../index.php" class="btn">🏠 Accueil</a>
        <h1>🏢 Inscription Recruteur</h1>
    </div>

    <div class="container container-main">
        <div class="split-container">
            <!-- Section Formulaire -->
            <div class="form-section">
                <h2 class="form-title">🏢 Créer un compte entreprise</h2>
                <p class="form-subtitle">Rejoignez GlobalJobs et publiez vos offres d'emploi</p>

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
                        <input type="text" name="adresse_entreprise" class="form-control" placeholder="123 Rue de l'Entreprise, Ville" required>
                    </div>

                    <button type="submit" class="btn btn-success w-100 mb-3">
                        ✅ Enregistrer l'entreprise
                    </button>
                </form>
            </div>

            <!-- Section Information -->
            <div class="info-section">
                <h2 class="info-title">Pourquoi choisir GlobalJobs ?</h2>
                <p class="info-text">Rejoignez des milliers d'entreprises qui font confiance à GlobalJobs pour recruter les meilleurs talents dans le monde entier.</p>
                
                <ul class="features">
                    <li>Outils de gestion des candidatures performants</li>
                    <li>Publication d'offres d'emploi illimitées</li>
                    <li>Interface moderne et intuitive</li>
                    <li>Support client dédié et réactif</li>
                    <li>Analyses détaillées de vos recrutements</li>
                    <li>Processus de recrutement simplifié</li>
                </ul>

                <div class="stats-highlight">
                    <strong>Beaucoup d'entreprises</strong> utilisent déjà GlobalJobs pour leurs recrutements et ont trouvé leurs talents idéaux.
                </div>
            </div>
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

        // Effet de focus amélioré sur les inputs
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.style.transform = 'scale(1.02)';
                this.style.transition = 'transform 0.2s ease';
            });

            input.addEventListener('blur', function() {
                this.style.transform = 'scale(1)';
            });
        });
    </script>
</body>
</html>