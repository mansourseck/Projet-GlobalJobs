<?php
session_start();
require '../db.php';

// Vérification de connexion
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Recruteur') {
    die("Accès refusé. Veuillez vous connecter en tant que recruteur.");
}

// Récupérer l'ID du recruteur depuis la table `recruteurs`
$stmt = $conn->prepare("SELECT id FROM recruteurs WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$recruteur = $stmt->fetch();

if (!$recruteur) {
    die("Erreur : Impossible de récupérer l'ID du recruteur.");
}

$recruteur_id = $recruteur['id'];

// Sélection des offres valides, validées par l'admin et non expirées
$stmt = $conn->prepare("SELECT * FROM offres WHERE recruteur_id = ? AND statut = 'publier' AND date_expire >= CURDATE()");
$stmt->execute([$recruteur_id]);
$offres = $stmt->fetchAll();

// Offres en attente de validation
$stmt2 = $conn->prepare("SELECT * FROM offres WHERE recruteur_id = ? AND statut = 'En attente'");
$stmt2->execute([$recruteur_id]);
$offres_en_attente = $stmt2->fetchAll();

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>📌 Mes offres publiées</title>
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
            ;
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

        .card-main {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .container-main {
            padding-top: 90px;
            padding-bottom: 20px;
            min-height: calc(100vh - 140px);
        }

        .table {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }

        .table thead th {
            background: rgba(7, 147, 235, 0.9) !important;
            color: white;
            border: none;
        }

        .table tbody tr {
            background: rgba(255, 255, 255, 0.8);
        }

        .table tbody tr:hover {
            background: rgba(255, 255, 255, 0.95);
        }

        .fade-out {
            opacity: 0;
            transition: opacity 0.5s;
        }
    </style>
</head>

<body>
    <div class="header">
        <a href="../index.php" class="btn">🏠 Accueil</a>
        <h1>📌 Mes offres d'emploi</h1>
    </div>

    <div class="container container-main">
        <h2 class="text-center mb-3" style="color: white;">📋 Offres déja publiées</h2>
        <div id="msg"></div>

        <div class="card-main">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">Mes offres</h3>
                <a href="ajoute_offre.php" class="btn btn-success">➕ Nouvelle offre</a>
            </div>

            <?php if ($offres): ?>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>📝 Titre</th>
                            <th>📄 Description</th>
                            <th>📍 Lieu</th>
                            <th> Domaine</th>
                            <th>📅 Publiée le</th>
                            <th>⏳ Expire le</th>
                            <th> Contrat</th>
                            <th>⚙️ Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($offres as $i => $o): ?>
                            <tr id="r<?= $o['id'] ?>">
                                <td><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($o["titre"]) ?></td>
                                <td><?= htmlspecialchars(substr($o["description"], 0, 100)) ?>...</td>
                                <td><?= htmlspecialchars($o["lieu"]) ?></td>
                                <td><?= htmlspecialchars($o["domain"]) ?></td>
                                <td><?= htmlspecialchars($o["date_postee"]) ?></td>
                                <td><?= htmlspecialchars($o["date_expire"]) ?></td>
                                <td><?= htmlspecialchars($o["type_contrat"]) ?></td>

                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="voir_offre.php?id=<?= $o['id'] ?>" class="btn btn-info btn-sm">🔎</a>
                                        <a href="modifier_offre.php?id=<?= $o['id'] ?>" class="btn btn-warning btn-sm">✏️</a>

                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-warning text-center">⚠️ Vous n'avez publié aucune offre.</div>
            <?php endif; ?>
        </div>

        <h2 class="text-center mb-4 mt-5" style="color: white;">🕓 Offres en attente de publication</h2>

        <div class="card-main">
            <?php if ($offres_en_attente): ?>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>📝 Titre</th>
                            <th>📄 Description</th>
                            <th>📍 Lieu</th>
                            <th> Domaine</th>
                            <th>📅 Créée le</th>
                            <th>⏳ Expire le</th>
                            <th> Contrat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($offres_en_attente as $i => $o): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($o["titre"]) ?></td>
                                <td><?= htmlspecialchars(substr($o["description"], 0, 100)) ?>...</td>
                                <td><?= htmlspecialchars($o["lieu"]) ?></td>
                                <td><?= htmlspecialchars($o["domain"]) ?></td>
                                <td><?= htmlspecialchars($o["date_postee"]) ?></td>
                                <td><?= htmlspecialchars($o["date_expire"]) ?></td>
                                <td><?= htmlspecialchars($o["type_contrat"]) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-info text-center">Aucune offre en attente de validation.</div>
            <?php endif; ?>
        </div>
    </div>

</body>

</html>