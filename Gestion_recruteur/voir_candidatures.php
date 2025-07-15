<?php
session_start();
require '../db.php';
require_once '../mailer_config.php';

// Vérification de session et rôle
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) != 'recruteur') {
    header("Location: ../login.php");
    exit();
}

// Récupérer l'id du recruteur (table recruteurs) pour l'utilisateur connecté
$stmt = $conn->prepare("SELECT id, entreprise FROM recruteurs WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$recruteur = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$recruteur) {
    echo "Erreur : recruteur introuvable.";
    exit();
}
$recruteur_id = $recruteur['id'];

// Traitement acceptation/refus
if (isset($_GET['action'], $_GET['candidature_id'])) {
    $action = $_GET['action'];
    $candidature_id = (int)$_GET['candidature_id'];

    // Récupérer la candidature + infos pour le mail
    $stmt = $conn->prepare(
        "SELECT c.id, c.statut, u.email, u.nom, u.prenom, o.titre 
         FROM candidature c
         INNER JOIN candidat ca ON c.candidat_id = ca.id
         INNER JOIN users u ON ca.user_id = u.id
         INNER JOIN offres o ON c.offre_id = o.id
         WHERE c.id = ? AND o.recruteur_id = ?"
    );
    $stmt->execute([$candidature_id, $recruteur_id]);
    $candidature = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($candidature) {
        if ($action === 'accepter') {
        $new_statut = 'Accepté';
        $msg = "La candidature a été acceptée avec succès.";
        $subject = "Félicitations, votre candidature a été retenue !";
        $body = "Bonjour {$candidature['prenom']} {$candidature['nom']},<br>
        Nous avons le plaisir de vous informer que votre candidature pour le poste <b>{$candidature['titre']}</b> a été <b>acceptée</b>.<br>
        Nous vous contacterons prochainement pour la suite du processus.<br>
        <br>
        Cordialement,<br>
        L'équipe GlobalJobs";
    } elseif ($action === 'refuser') {
        $new_statut = 'Refusé';
        $msg = "La candidature a été refusée.";
        $subject = "Retour sur votre candidature";
        $body = "Bonjour {$candidature['prenom']} {$candidature['nom']},<br>
        Après étude attentive de votre dossier pour le poste <b>{$candidature['titre']}</b>, nous sommes au regret de vous informer que votre candidature n'a pas été retenue.<br>
        Nous vous remercions pour l’intérêt que vous avez porté à cette offre et vous souhaitons bonne continuation dans vos recherches.<br>
        <br>
        L'équipe GlobalJobs";
    } else {
        // Sécurité si action inattendue
        header("Location: voir_candidatures.php");
        exit();
    }


        // Mettre à jour le statut
        $stmt = $conn->prepare("UPDATE candidature SET statut = ? WHERE id = ?");
        $stmt->execute([$new_statut, $candidature_id]);

        // Envoyer le mail au candidat
        sendMail($candidature['email'], $candidature['prenom'] . ' ' . $candidature['nom'], $subject, $body);

        $_SESSION['message'] = $msg;
        header("Location: voir_candidatures.php");
        exit();
    }
}

// Récupérer toutes les candidatures reçues pour ce recruteur avec infos utiles
$stmt = $conn->prepare(
    "SELECT c.id, c.date_postulation, c.statut,
            u.nom AS nom_candidat, u.prenom AS prenom_candidat, u.email, 
            ca.competences, ca.cv, ca.metier, ca.experience,
            o.titre AS titre_offre, r.entreprise
     FROM candidature c
     INNER JOIN candidat ca ON c.candidat_id = ca.id
     INNER JOIN users u ON ca.user_id = u.id
     INNER JOIN offres o ON c.offre_id = o.id
     INNER JOIN recruteurs r ON o.recruteur_id = r.id
     WHERE o.recruteur_id = ? AND c.statut = 'En attente'
     ORDER BY c.date_postulation DESC"
);
$stmt->execute([$recruteur_id]);
$candidatures = $stmt->fetchAll(PDO::FETCH_ASSOC);


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
            background: url('../images/image3.jpg') no-repeat center center fixed;
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
            padding: 18px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.17);
            margin-bottom: 20px;
            border: 1px solid rgba(51, 213, 231, 0.3);
            min-height: 320px;
        }

        .container-main {
            padding-top: 90px;
            padding-bottom: 20px;
            min-height: calc(100vh - 90px);
        }

        .form-select {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 8px;
        }

        .form-select:focus {
            border-color: #33d5e7;
            box-shadow: 0 0 0 0.15rem rgba(51, 213, 231, 0.19);
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
            border-radius: 8px;
            padding: 8px;
            font-size: 0.97em;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        .btn-danger {
            border-radius: 8px;
            padding: 8px;
            font-size: 0.97em;
        }

        .badge {
            font-size: 0.9em;
            padding: 6px 12px;
        }

        .alert {
            border-radius: 8px;
            margin-bottom: 13px;
            padding: 8px 12px;
            font-size: 0.97em;
        }

        .alert-dismissible .btn-close {
            padding: 0.75rem 1rem;
        }

        .candidature-title {
            color: #2c3e50;
            border-bottom: 2px solid #33d5e7;
            padding-bottom: 8px;
            margin-bottom: 10px;
            font-size: 1.13em;
        }

        .info-row {
            margin-bottom: 7px;
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
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.17);
            margin-bottom: 30px;
            text-align: center;
        }

        .no-candidatures {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.17);
            text-align: center;
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

        <!-- Message de confirmation -->
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
                    <div class="col-md-4 mb-3">
                        <div class="card-candidature">
                            <h5 class="candidature-title">📝 <?= htmlspecialchars($candidature["titre_offre"]) ?></h5>
                            <div class="info-row">
                                <span class="info-label">🏢 Entreprise :</span>
                                <?= htmlspecialchars($candidature["entreprise"]) ?>
                            </div>
                            <div class="info-row">
                                <span class="info-label">👤 Candidat :</span>
                                <?= htmlspecialchars($candidature["prenom_candidat"]) ?> <?= htmlspecialchars($candidature["nom_candidat"]) ?>
                            </div>
                            <div class="info-row">
                                <span class="info-label">🧑‍💼 Métier :</span>
                                <?= htmlspecialchars($candidature["metier"]) ?>
                            </div>
                            <div class="info-row">
                                <span class="info-label">📈 Expérience :</span>
                                <?= htmlspecialchars($candidature["experience"]) ?>
                            </div>
                            <div class="info-row">
                                <span class="info-label">🛠️ Compétences :</span>
                                <?= htmlspecialchars($candidature["competences"]) ?>
                            </div>
                            <?php if (!empty($candidature["cv"])): ?>
                                <div class="info-row">
                                    <span class="info-label">📄 CV :</span>
                                <a href="/projet_php_GlobalJobs/<?= htmlspecialchars($candidature["cv"]) ?>" target="_blank" class="btn btn-sm btn-outline-primary ms-2">Voir le CV</a>                                </div>
                            <?php endif; ?>
                            <div class="info-row">
                                <span class="info-label">📅 Date :</span>
                                <?= htmlspecialchars($candidature["date_postulation"]) ?>
                            </div>
                            <div class="info-row mb-2">
                                <span class="info-label">📊 Statut :</span>
                                <span class="badge <?= $candidature["statut"] == "Accepté" ? "bg-success" : ($candidature["statut"] == "Refusé" ? "bg-danger" : "bg-warning") ?>">
                                    <?= htmlspecialchars($candidature["statut"]) ?>
                                </span>
                            </div>
                            <?php if ($candidature["statut"] == "En attente"): ?>
                                <div class="d-flex gap-2">
                                    <a href="?action=accepter&candidature_id=<?= $candidature['id'] ?>" class="btn btn-success w-50"
                                        onclick="return confirm('Confirmer l\'acceptation de cette candidature ?');">Accepter</a>
                                    <a href="?action=refuser&candidature_id=<?= $candidature['id'] ?>" class="btn btn-danger w-50"
                                        onclick="return confirm('Confirmer le refus de cette candidature ?');">Refuser</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="footer">🌟 &copy; 2025 GlobalJobs 🌟</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>