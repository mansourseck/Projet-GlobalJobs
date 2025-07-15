<?php 
session_start(); 
require '../db.php';  

if (!isset($_SESSION['user_id'])) {     
    $_SESSION['message'] = "❌ Vous devez être connecté.";     
    header("Location: ../login.php");     
    exit(); 
}  

$candidat_id = $_SESSION['user_id']; 


// Récupérer toutes les candidatures du candidat avec LEFT JOIN pour éviter les pertes
$stmt = $conn->prepare("SELECT ca.id, 
                              COALESCE(o.titre, 'Titre non disponible') as titre, 
                              ca.statut, 
                              ca.date_postulation, 
                              COALESCE(r.entreprise, 'Entreprise non disponible') as entreprise,
                              ca.offre_id                         
                        FROM Candidature ca                         
                        LEFT JOIN Offres o ON ca.offre_id = o.id                         
                        LEFT JOIN Recruteurs r ON o.recruteur_id = r.id                         
                        WHERE ca.candidat_id = ?
                        ORDER BY ca.date_postulation DESC"); 
$stmt->execute([$candidat_id]); 
$candidatures = $stmt->fetchAll(PDO::FETCH_ASSOC);  

// Définition des messages automatiques en fonction du statut 
$messages_statut = [     
    "En attente" => "📌 Votre candidature est toujours en attente. Nous vous tiendrons informé.",     
    "Accepté" => "🎉 Félicitations ! Votre candidature a été acceptée. Nous vous contacterons bientôt.",     
    "Refusé" => "❌ Nous sommes désolés, votre candidature n'a pas été retenue cette fois-ci.",
]; 
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Suivi des candidatures</title>
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

        .footer {
            bottom: 0;
            width: 100%;
            background: rgba(7, 147, 235, 0.8);
            color: white;
            text-align: center;
            padding: 15px;
            z-index: 1000;
        }

        .container-main {
            padding-top: 90px;
            padding-bottom: 90px;
            min-height: 100vh;
        }

        .candidature-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.15);
            border-left: 4px solid #007bff;
        }

        .candidature-card.accepte { border-left-color: #28a745; }
        .candidature-card.refuse { border-left-color: #dc3545; }
        .candidature-card.en-attente { border-left-color: #ffc107; }

        .btn-back {
            background-color: #6c757d;
            color: white;
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            margin-top: 20px;
        }

        .btn-back:hover {
            background-color: #5a6268;
            color: white;
            text-decoration: none;
        }

        .btn-supprimer {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 0.9em;
            transition: all 0.3s;
        }

        .btn-supprimer:hover {
            background-color: #c82333;
            color: white;
        }

        .no-candidatures {
            text-align: center;
            padding: 40px;
            background: rgba(255, 193, 7, 0.1);
            border-radius: 12px;
            border: 2px dashed #ffc107;
        }

        .message-success {
            background: rgba(40, 167, 69, 0.1);
            border: 1px solid rgba(40, 167, 69, 0.3);
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        .message-error {
            background: rgba(220, 53, 69, 0.1);
            border: 1px solid rgba(220, 53, 69, 0.3);
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        .card-actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="candidats.php" class="btn">🏠 Accueil</a>
        <h1>📌 Suivi de vos candidatures</h1>
    </div>

    <div class="container container-main">
        <!-- Affichage des messages -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="<?= strpos($_SESSION['message'], '✅') !== false ? 'message-success' : 'message-error' ?>">
                <?= $_SESSION['message'] ?>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <?php if (empty($candidatures)): ?>
            <div class="no-candidatures">
                <h4>⚠️ Aucune candidature trouvée</h4>
                <p class="text-muted">Vous n'avez encore postulé à aucune offre. Commencez dès maintenant votre recherche d'emploi !</p>
            </div>
        <?php else: ?>
            <div class="alert alert-info mb-4">
                📊 <strong>Total :</strong> <?= count($candidatures) ?> candidature(s) trouvée(s)
            </div>
            <div class="row">
                <?php foreach ($candidatures as $candidature): ?>
                    <div class="col-md-6">
                        <div class="candidature-card <?= strtolower(str_replace(' ', '-', $candidature['statut'])) ?>">
                            <h5 class="mb-3">📝 <?= htmlspecialchars($candidature["titre"]) ?></h5>
                            <div class="mb-2"><strong>🏢 Entreprise :</strong> <?= htmlspecialchars($candidature["entreprise"]) ?></div>
                            <div class="mb-2"><strong>📅 Date :</strong> <?= htmlspecialchars($candidature["date_postulation"]) ?></div>
                            <div class="mb-3">
                                <strong>📊 Statut :</strong> 
                                <span class="badge <?= $candidature["statut"] == "Accepté" ? "bg-success" : ($candidature["statut"] == "Refusé" ? "bg-danger" : "bg-warning text-dark") ?>">
                                    <?= htmlspecialchars($candidature["statut"]) ?>
                                </span>
                            </div>
                            <?php 
                            $message_key = $candidature["statut"];
                            $message = isset($messages_statut[$message_key]) ? $messages_statut[$message_key] : "📩 Statut en cours de traitement.";
                            ?>
                            <div class="<?= $candidature["statut"] == "Accepté" ? "alert-success" : ($candidature["statut"] == "Refusé" ? "alert-danger" : "alert-warning") ?>" style="border-radius: 8px; margin-top: 15px; padding: 12px;">
                                <strong>📩 Message :</strong> <?= $message ?>
                            </div>

                            <!-- Actions de la candidature -->
                            <div class="card-actions">
                                <form method="POST" style="display: inline;" onsubmit="return confirm('⚠️ Êtes-vous sûr de vouloir supprimer cette candidature ? Cette action est irréversible.');">
                                    <input type="hidden" name="candidature_id" value="<?= $candidature['id'] ?>">
                                </form>
                            </div>
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