<?php
session_start();
require '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['statut'] !== 'Recruteur') {
    $_SESSION['message'] = "❌ Vous devez être connecté comme recruteur.";
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Étape 1 : récupérer l'ID du recruteur depuis la table recruteurs
$stmt = $conn->prepare("SELECT id FROM recruteurs WHERE user_id = ?");
$stmt->execute([$user_id]);
$recruteur = $stmt->fetch();

if (!$recruteur) {
    die("❌ Erreur : recruteur introuvable.");
}

$recruteur_id = $recruteur['id'];

// Étape 2 : récupérer les candidatures reçues
$stmt = $conn->prepare("SELECT ca.id, u.nom, u.prenom, c.competences, ca.date_postulation, ca.statut, o.titre, r.entreprise
                        FROM candidature ca
                        INNER JOIN candidat c ON ca.candidat_id = c.id
                        INNER JOIN users u ON c.user_id = u.id
                        INNER JOIN offres o ON ca.offre_id = o.id
                        INNER JOIN recruteurs r ON o.recruteur_id = r.id
                        WHERE o.recruteur_id = ?");
$stmt->execute([$recruteur_id]);
$candidatures = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Messages en fonction du statut
$messages_statut = [
    "En attente" => "📌 Votre candidature est toujours en attente. Nous vous tiendrons informé.",
    "Accepté" => "🎉 Félicitations ! Votre candidature a été acceptée. Nous vous contacterons bientôt.",
    "Refusé" => "❌ Nous sommes désolés, votre candidature n'a pas été retenue cette fois-ci."
];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>📌 Gérer les candidatures - GlobalJobs</title>
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

        .card-candidature {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            margin-bottom: 25px;
            border: 1px solid rgba(51, 213, 231, 0.3);
        }

        .container-main {
            padding-top: 90px;
            padding-bottom: 20px;
            min-height: calc(100vh - 90px);
        }

        .btn-back {
            background-color: #6c757d;
            color: white;
            padding: 10px 30px;
            border-radius: 8px;
        }

        .form-select {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 10px;
        }

        .form-select:focus {
            border-color: #33d5e7;
            box-shadow: 0 0 0 0.2rem rgba(51, 213, 231, 0.25);
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
            border-radius: 8px;
            padding: 10px;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        .badge {
            font-size: 0.9em;
            padding: 6px 12px;
        }

        .alert {
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .alert-dismissible .btn-close {
            padding: 0.75rem 1rem;
        }

        .candidature-title {
            color: #2c3e50;
            border-bottom: 2px solid #33d5e7;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }

        .info-row {
            margin-bottom: 8px;
        }

        .info-label {
            font-weight: 600;
            color: #34495e;
        }

        .page-title {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            margin-bottom: 30px;
            text-align: center;
        }

        .no-candidatures {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .back-button-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="recruteurs.php" class="btn">🏠 Accueil</a>
        <h1>📌 Gérer les candidatures</h1>
    </div>

    <div class="container container-main">
        <div class="page-title">
            <h2 class="mb-3">📌 Gestion des candidatures</h2>
            <p class="text-muted mb-0">Consultez et gérez toutes les candidatures reçues pour vos offres</p>
        </div>

        <!-- Affichage du message de confirmation -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
                <i class="fas fa-check-circle"></i> <?= $_SESSION['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <?php if (empty($candidatures)): ?>
            <div class="no-candidatures">
                <div class="alert alert-info">
                    <h4>📭 Aucune candidature</h4>
                    <p>Vous n'avez pas encore reçu de candidatures pour vos offres.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($candidatures as $candidature): ?>
                    <div class="col-md-5 mb-3">
                        <div class="card-candidature">
                            <h5 class="candidature-title">📝 <?= htmlspecialchars($candidature["titre"]) ?></h5>
                            
                            <div class="info-row">
                                <span class="info-label">🏢 Entreprise :</span> 
                                <?= htmlspecialchars($candidature["entreprise"]) ?>
                            </div>
                            
                            <div class="info-row">
                                <span class="info-label">👤 Candidat :</span> 
                                <?= htmlspecialchars($candidature["nom"]) ?> <?= htmlspecialchars($candidature["prenom"]) ?>
                            </div>
                            
                            <div class="info-row">
                                <span class="info-label">🛠️ Compétences :</span> 
                                <?= htmlspecialchars($candidature["competences"]) ?>
                            </div>
                            
                            <div class="info-row">
                                <span class="info-label">📅 Date :</span> 
                                <?= htmlspecialchars($candidature["date_postulation"]) ?>
                            </div>
                            
                            <div class="info-row mb-3">
                                <span class="info-label">📊 Statut :</span> 
                                <span class="badge <?= $candidature["statut"] == "Accepté" ? "bg-success" : ($candidature["statut"] == "Refusé" ? "bg-danger" : "bg-warning") ?>">
                                    <?= htmlspecialchars($candidature["statut"]) ?>
                                </span>
                            </div>

                            <!-- Message automatique en fonction du statut -->
                            <div class="alert <?= $candidature["statut"] == "Accepté" ? "alert-success" : ($candidature["statut"] == "Refusé" ? "alert-danger" : "alert-warning") ?>">
                                <strong>📩 Message au candidat :</strong><br>
                                <?= $messages_statut[$candidature["statut"]] ?>
                            </div>

                            <!-- Formulaire de mise à jour du statut -->
                            <form action="modifier_statut.php" method="POST">
                                <input type="hidden" name="candidature_id" value="<?= $candidature['id'] ?>">
                                <div class="mb-2">
                                    <label class="form-label info-label">🔄 Changer le statut :</label>
                                    <select name="statut" class="form-select">
                                        <option value="En attente" <?= $candidature["statut"] == "En attente" ? "selected" : "" ?>>⏳ En attente</option>
                                        <option value="Accepté" <?= $candidature["statut"] == "Accepté" ? "selected" : "" ?>>✅ Accepté</option>
                                        <option value="Refusé" <?= $candidature["statut"] == "Refusé" ? "selected" : "" ?>>❌ Refusé</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-success w-100">🔄 Mettre à jour le statut</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="footer">
        🌟 &copy; 2025 GlobalJobs 🌟
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>