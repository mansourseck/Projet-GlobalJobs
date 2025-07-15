<?php
require '../db.php'; // Mets le bon chemin si besoin

// Sécurisation de l'id (GET)
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die("Candidat introuvable.");
}

$sql = "SELECT u.prenom, u.nom, u.email, u.telephone, c.metier, c.experience, c.competences, c.niveau_etudes
        FROM users u
        INNER JOIN candidat c ON u.id = c.user_id
        WHERE u.id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$id]);
$candidat = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$candidat) {
    die("Candidat introuvable.");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil de <?= htmlspecialchars($candidat['prenom'] . ' ' . $candidat['nom']) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-lg">
                <div class="card-body">
                    <h3 class="card-title text-primary mb-3"><?= htmlspecialchars($candidat['prenom'] . ' ' . $candidat['nom']) ?></h3>
                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item"><strong>Email :</strong> <?= htmlspecialchars($candidat['email']) ?></li>
                        <li class="list-group-item"><strong>Téléphone :</strong> <?= htmlspecialchars($candidat['telephone'] ?? 'Non renseigné') ?></li>
                        <li class="list-group-item"><strong>Métier :</strong> <?= htmlspecialchars($candidat['metier'] ?? 'Non précisé') ?></li>
                        <li class="list-group-item"><strong>Expérience :</strong> <?= htmlspecialchars($candidat['experience'] ?? 'Non précisée') ?></li>
                        <li class="list-group-item"><strong>Niveau d'études :</strong> <?= htmlspecialchars($candidat['niveau_etudes'] ?? 'Non précisé') ?></li>
                        <li class="list-group-item"><strong>Compétences :</strong> <?= htmlspecialchars($candidat['competences'] ?? 'Non précisées') ?></li>
                    </ul>
                    <a href="profils_candidats.php" class="btn btn-secondary">Retour</a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>