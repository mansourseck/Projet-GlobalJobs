<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "❌ Vous devez être connecté.";
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Vérifier si le profil du candidat est complet
    $stmt = $conn->prepare("SELECT * FROM candidat WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $profil = $stmt->fetch(PDO::FETCH_ASSOC);

    // Définis ici les champs obligatoires pour un profil "complet"
    $profil_complet = !empty($profil['experience'])
        && !empty($profil['metier'])
        && !empty($profil['competences'])
        && !empty($profil['cv']); // adapte selon tes champs

    // Seules les offres publiées (statut = 'Publier')
    $stmt = $conn->prepare("
        SELECT 
            o.id, o.titre, o.description, o.lieu, o.domain, o.type_contrat, o.date_postee, o.date_expire,  
            r.entreprise, r.secteur
        FROM offres o
        LEFT JOIN recruteurs r ON o.recruteur_id = r.id
        WHERE o.statut = 'Publier'
    ");
    $stmt->execute();
    $offres = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("❌ Erreur SQL : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>📌 Postuler à une offre</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background:
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

        .footer {
            margin-top: 50px;
            bottom: 0;
            width: 100%;
            background: rgba(7, 147, 235, 0.8);
            color: white;
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

        .card-custom {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            margin-bottom: 20px;
            height: 100%;
            display: flex;
            flex-direction: column;
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
    </style>
</head>

<body>
    <div class="header">
        <a href="candidats.php" class="btn">🏠 Accueil</a>
        <h1>📌 Postuler à une offre</h1>
    </div>

    <div class="container container-main">
        <h2 class="text-center mb-3" style="color: black;">🎯 Offres disponibles</h2>
        <p class="text-center text-muted mb-4" style="color: black;">Envoyez votre candidature aux offres qui vous intéressent.</p>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success text-center">
                <?= $_SESSION['message'] ?>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <div class="row">
            <?php foreach ($offres as $offre): ?>
                <div class="col-sm-6 col-md-4 mb-4">
                    <div class="card-custom">
                        <h5>📝 <?= htmlspecialchars($offre["titre"]) ?></h5>
                        <p><strong>🏢 Entreprise :</strong> <?= htmlspecialchars($offre["entreprise"]) ?></p>
                        <p><strong>📍 Lieu :</strong> <?= htmlspecialchars($offre["lieu"]) ?></p>
                        <p><strong>🗂 Secteur :</strong> <?= htmlspecialchars($offre["secteur"]) ?></p>
                        <p><strong>🌐 Domaine d'activité :</strong> <?= htmlspecialchars($offre["domain"]) ?></p>
                        <p><strong>📅 Date postée :</strong> <?= htmlspecialchars($offre["date_postee"]) ?></p>
                        <p><strong>⏳ Date d'expiration :</strong> <?= htmlspecialchars($offre["date_expire"]) ?></p>
                        <p><strong>📝 Type de contrat :</strong> <?= htmlspecialchars($offre["type_contrat"]) ?></p>
                        <div class="alert alert-light">
                            <?= nl2br(htmlspecialchars(substr($offre["description"], 0, 150))) ?>...
                        </div>
                        <div class="d-flex gap-2 mt-auto">
                            <?php if($profil_complet): ?>
                                <a href="postuler_offre.php?id=<?= $offre['id'] ?>" class="btn btn-apply flex-fill">📩 Postuler</a>
                            <?php else: ?>
                                <button class="btn btn-apply flex-fill" disabled title="Complétez d'abord votre profil">Profil incomplet</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if(!$profil_complet): ?>
            <div class="alert alert-warning text-center mt-4">
                ⚠️ Veuillez <a href="../Gestion_candidats/profile_candidats.php" class="alert-link">compléter votre profil</a> pour pouvoir postuler aux offres.
            </div>
        <?php endif; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>