<?php 
session_start(); 
if (!isset($_SESSION["user_id"]) || strcasecmp($_SESSION["role"], "candidat") !== 0) {     
    header("Location: loginh.php");     
    exit(); 
} 
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rechercher des offres</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: 
                linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)),
                url('../images/image2.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
        }
        
        .header {
        
            width: 100%;
            background: rgba(7, 147, 235, 0.8);
            color: white;
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
            background: rgba(7, 147, 235, 0.8);
            color: white;
            text-align: center;
            padding: 15px;
            z-index: 1000;
        }

        .card-custom {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            width: 100%;
            max-width: 600px;
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

        .btn-search {
            background-color: #007bff;
            color: white;
            width: 100%;
            padding: 10px;
        }

        .btn-back {
            background-color: #6c757d;
            color: white;
            width: 100%;
            padding: 10px;
        }

        .btn-search:hover {
            background-color: #0056b3;
        }

        .btn-back:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
<div class="header">
    <a href="../Gestion_candidats/candidats.php" class="btn">🏠 Accueil</a>
    <h1>🔍 Rechercher des offres</h1>
</div>

    <div class="container container-center">
        <div class="col-md-8">
            <div class="card card-custom">
                <p class="text-center text-muted mb-4">Entrez un mot-clé et une localisation pour découvrir des opportunités qui vous correspondent.</p>

                <form action="resultats_offres.php" method="get">
                    <label for="keyword" class="form-label">🏷️ Mot-clé :</label>
                    <input type="text" name="keyword" class="form-control mb-3" 
                           placeholder="Ex : Développeur, Marketing, Designer..." 
                           value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>">

                    <label for="location" class="form-label">📍 Localisation :</label>
                    <input type="text" name="location" class="form-control mb-4" 
                           placeholder="Ex : Dakar, Paris, Remote..." 
                           value="<?php echo isset($_GET['location']) ? htmlspecialchars($_GET['location']) : ''; ?>">

                    <button type="submit" class="btn btn-search mb-2">🔍 Rechercher des offres</button>
                   
                </form>
            </div>
        </div>
    </div>

    <div class="footer">
        🌟 &copy; 2025 GlobalJobs 🌟
    </div>
</body>
</html>