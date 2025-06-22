<?php
session_start();
require '../db.php';

// Vérification de connexion
if (!isset($_SESSION['user_id']) || $_SESSION['statut'] !== 'Recruteur') {
    die("Accès refusé. Veuillez vous connecter en tant que recruteur.");
}

// Récupérer l'ID du recruteur depuis la table `recruteurs`
$stmt = $conn->prepare("SELECT id FROM recruteurs WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$recruteur = $stmt->fetch();

if (!$recruteur) {
    die("Erreur : Impossible de récupérer l'ID du recruteur.");
}

$recruteur_id = $recruteur['id']; // ID réel du recruteur

// Suppression AJAX après avoir obtenu le bon ID
if ($_POST['action'] ?? '' === 'supprimer') {
    $stmt = $conn->prepare("DELETE FROM offres WHERE id = ? AND recruteur_id = ?");
    $success = $stmt->execute([$_POST['id'], $recruteur_id]);
    echo json_encode(['success' => $success]);
    exit;
}

// Sélection des offres
$stmt = $conn->prepare("SELECT * FROM offres WHERE recruteur_id = ?");
$stmt->execute([$recruteur_id]);
$offres = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>📌 Mes offres publiées</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: url('../images/image3.jpg') no-repeat center center fixed; background-size: cover; min-height: 100vh; }
        .header { position: fixed; width: 100%; background: rgba(7, 147, 235, 0.8); color: white; padding: 15px; display: flex; justify-content: center; align-items: center; z-index: 1000; }
        .header h1 { text-align: center; flex-grow: 1; margin: 0; }
        .header .btn { position: absolute; right: 60px; background-color: #007bff; color: white; }
        .card-main { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border-radius: 15px; padding: 25px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); }
        .container-main { padding-top: 90px; padding-bottom: 20px; min-height: calc(100vh - 140px); }
        .table { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); }
        .table thead th { background: rgba(7, 147, 235, 0.9) !important; color: white; border: none; }
        .table tbody tr { background: rgba(255, 255, 255, 0.8); }
        .table tbody tr:hover { background: rgba(255, 255, 255, 0.95); }
        .fade-out { opacity: 0; transition: opacity 0.5s; }
    </style>
</head>
<body>
    <div class="header">
        <a href="../index.php" class="btn">🏠 Accueil</a>
        <h1>📌 Mes offres d'emploi</h1>
    </div>

    <div class="container container-main">
        <h2 class="text-center mb-3" style="color: white;">📋 Offres que j'ai publiées</h2>
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
                            <th>🗂 Secteur</th>
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
                                <td><?= htmlspecialchars($o["secteur"]) ?></td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="voir_offre.php?id=<?= $o['id'] ?>" class="btn btn-info btn-sm">🔎</a>
                                        <a href="modifier_offre.php?id=<?= $o['id'] ?>" class="btn btn-warning btn-sm">✏️</a>
                                        <button class="btn btn-danger btn-sm" onclick="del(<?= $o['id'] ?>)">🗑</button>
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
    </div>

    <script>
        function del(id) {
            if (!confirm('Supprimer cette offre ?')) return;

            fetch(location.href, {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `action=supprimer&id=${id}`
            })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    document.getElementById('r' + id).classList.add('fade-out');
                    setTimeout(() => location.reload(), 500);
                } else {
                    alert('Erreur de suppression');
                }
            });
        }
    </script>
</body>
</html>