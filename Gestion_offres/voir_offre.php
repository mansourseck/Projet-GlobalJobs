<?php 
session_start();  
require '../db.php'; 
if (!isset($_GET['id'])) { die("⚠ Erreur : ID de l'offre non spécifié."); } 
$offre_id = $_GET['id']; 
$stmt = $conn->prepare("SELECT * FROM Offres WHERE id = ?"); 
$stmt->execute([$offre_id]); 
$offre = $stmt->fetch(PDO::FETCH_ASSOC); 
if (!$offre) { die("⚠ Erreur : Aucune offre trouvée."); } 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>📌 Voir Offre</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: url('../images/image3.jpg') no-repeat center center fixed; background-size: cover; min-height: 100vh; }
        .header { position: fixed; width: 100%; background: rgba(7, 147, 235, 0.8); color: white; padding: 15px; display: flex; justify-content: center; align-items: center; z-index: 1000; }
        .header h1 { text-align: center; flex-grow: 1; margin: 0; }
        .header .btn { position: absolute; right: 60px; background-color: #007bff; color: white; }
        .footer { width: 100%; background: rgba(7, 147, 235, 0.8); color: white; text-align: center; padding: 15px; margin-top: auto; }
        .card-main { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border-radius: 15px; padding: 25px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); }
        .container-main { padding-top: 90px; padding-bottom: 20px; min-height: calc(100vh - 140px); }
        .btn-back { background-color: #6c757d; color: white; padding: 10px 30px; }
        .alert-secondary { background: rgba(108, 117, 125, 0.1); border: 1px solid rgba(108, 117, 125, 0.3); color: #495057; backdrop-filter: blur(5px); }
    </style>
</head>
<body>
    <div class="header">
        <a href="dashboard_offres.php" class="btn">🏠 Accueil</a>
        <h1>📌 Détails de l'offre</h1>
    </div>
    <div class="container container-main">
        <h2 class="text-center mb-3" style="color: white;">🔍 Informations détaillées</h2>
        <div class="card-main">
            <h2 class="text-center mb-4">📌 Détails de l'Offre</h2>
            <h3 class="text-primary mb-3"><?= htmlspecialchars($offre["titre"]) ?></h3>
            <p><strong>📍 Lieu :</strong> <?= htmlspecialchars($offre["lieu"]) ?></p>
            <p><strong>🗂 Secteur :</strong> <?= htmlspecialchars($offre["secteur"]) ?></p>
            <p><strong>📅 Date postée :</strong> <?= htmlspecialchars($offre["date_postee"]) ?></p>
            <p><strong>📄 Description :</strong></p>
            <div class="alert alert-secondary"><?= nl2br(htmlspecialchars($offre["description"])) ?></div>
            <div class="d-flex justify-content-between mt-4">
                <a href="modifier_offre.php?id=<?= $offre['id'] ?>" class="btn btn-warning">✏️ Modifier</a>
                <a href="dashboard_offres.php" class="btn btn-back">⬅️ Retour</a>
            </div>
        </div>
    </div>
    <div class="footer">🌟 &copy; 2025 GlobalJobs 🌟 </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>