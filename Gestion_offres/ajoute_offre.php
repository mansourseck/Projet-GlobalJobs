<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>📝 Publier une nouvelle offre</title>
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
            color:rgb(253, 251, 251);
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
            bottom: 0;
            width: 100%;
            background: rgba(51, 213, 231, 0.8);
            color: black;
            text-align: center;
            padding: 15px;
            z-index: 1000;
        }

        .card-main {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .container-main {
            padding-top: 90px;
            padding-bottom: 70px;
            min-height: calc(100vh - 140px);
        }

        .btn-back {
            background-color: #6c757d;
            color: white;
            padding: 10px 30px;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="recruteurs.php" class="btn">🏠 Accueil</a>
        <h1>📝 Publier une nouvelle offre</h1>
    </div>

    <div class="container container-main">
        <h2 class="text-center mb-3" style="color: black;">📝 Nouvelle offre d'emploi</h2>
        <p class="text-center text-muted mb-4" style="color: #ddd;">Remplissez les informations pour publier votre offre.</p>

        <div class="card-main">
            <h3 class="text-center mb-4">Publier une nouvelle offre</h3>
            <form action="traitement_offres.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">📝 Titre du poste</label>
                    <input type="text" name="titre" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">📄 Description</label>
                    <textarea name="description" class="form-control" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">📍 Lieu</label>
                    <input type="text" name="lieu" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">🗂 Secteur</label>
                    <input type="text" name="secteur" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success w-100">📩 Publier l'offre</button>
            </form>
        </div>
    <div class="text-center mt-4">
            <a href="dashboard_offres.php" class="btn btn-secondary">↩ Retour à la table des offres</a>
        </div> 
    </div>

    <div class="footer">
        🌟 &copy; 2025 GlobalJobs 🌟
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>