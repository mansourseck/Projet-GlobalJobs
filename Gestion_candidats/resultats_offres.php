<?php
session_start();
include '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $keyword = trim($_GET["keyword"]);
    $location = trim($_GET["location"]);

    try {
        // Requête avec LEFT JOIN pour inclure les offres sans recruteur associé
        $stmt = $conn->prepare("
            SELECT o.id, o.titre, o.description, o.lieu, o.secteur, 
                   COALESCE(r.entreprise, 'Non spécifié') AS entreprise
            FROM Offres o
            LEFT JOIN Recruteurs r ON o.recruteur_id = r.id
            WHERE (o.titre LIKE :keyword OR o.description LIKE :keyword) 
            AND o.lieu LIKE :location
        ");

        $keyword_param = "%$keyword%";
        $location_param = "%$location%";
        $stmt->bindParam(":keyword", $keyword_param, PDO::PARAM_STR);
        $stmt->bindParam(":location", $location_param, PDO::PARAM_STR);
        $stmt->execute();
        $offres = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("❌ Erreur SQL : " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Résultats de recherche</title>
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
            position: fixed;
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

        
        .card-custom {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            margin-bottom: 20px;
        }

        .container-main {
            padding-top: 90px;
            padding-bottom: 70px;
            min-height: calc(100vh - 140px);
        }

        .btn-details {
            background-color: #17a2b8;
            color: white;
            margin-right: 10px;
        }

        .btn-apply {
            background-color: #28a745;
            color: white;
        }

        .btn-back {
            background-color: #6c757d;
            color: white;
            padding: 10px 30px;
        }

        .no-results {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="offres.php" class="btn">🏠 Retour</a>
        <h1>📌 Résultats de recherche</h1>
    </div>

    <div class="container container-main">
        <?php if (!empty($offres)): ?>
            <div class="row">
                <?php foreach ($offres as $offre): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card-custom">
                            <h5>📝 <?= htmlspecialchars($offre["titre"]) ?></h5>
                            <p><strong>🏢 Entreprise :</strong> <?= htmlspecialchars($offre["entreprise"]) ?></p>
                            <p><strong>📍 Lieu :</strong> <?= htmlspecialchars($offre["lieu"]) ?></p>
                            <p><strong>🗂 Secteur :</strong> <?= htmlspecialchars($offre["secteur"]) ?></p>
                            <div class="alert alert-light">
                                <?= nl2br(htmlspecialchars(substr($offre["description"], 0, 150))) ?>...
                            </div>
                            <div class="d-flex gap-2 mt-3">
                                <a href="postuler_offre.php?id=<?= $offre['id'] ?>" class="btn btn-apply flex-fill">📩 Postuler</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-results">
                <h3>⚠️ Aucune offre trouvée</h3>
                <p class="text-muted">Essayez avec d'autres mots-clés ou une localisation différente.</p>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>