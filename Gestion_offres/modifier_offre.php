<?php
require '../db.php';

if (!isset($_GET['id'])) {
    die("Erreur : ID manquant !");
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM offres WHERE id = ?");
$stmt->execute([$id]);
$offre = $stmt->fetch();

if (!$offre) {
    die("Erreur : Offre introuvable !");
}else{

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];
    $titre = $_POST["titre"];
    $description = $_POST["description"];
    $lieu = $_POST["lieu"];
    $secteur = $_POST["secteur"];

    $stmt = $conn->prepare("UPDATE offres SET titre = ?, description = ?, lieu = ?, secteur = ? WHERE id = ?");
    $stmt->execute([$titre, $description, $lieu, $secteur, $id]);

    header("Location: dashboard_offres.php?message=Offre modifiée avec succès");
    exit();
}
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier une offre</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
</head>
<body>
    <div class="container mt-5">
        <div class="card p-4 shadow-lg">
            <h3 class="text-center">Modifier l’offre</h3>
            <form action="#" method="POST">
                <input type="hidden" name="id" value="<?= $offre['id']; ?>">
                <div class="mb-3">
                    <label class="form-label">Titre du poste</label>
                    <input type="text" name="titre" class="form-control" required value="<?= htmlspecialchars($offre['titre']); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" required><?= htmlspecialchars($offre['description']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Lieu</label>
                    <input type="text" name="lieu" class="form-control" required value="<?= htmlspecialchars($offre['lieu']); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Secteur</label>
                    <input type="text" name="secteur" class="form-control" required value="<?= htmlspecialchars($offre['secteur']); ?>">
                </div>
                <button type="submit" class="btn btn-warning w-100">Modifier l’offre</button>
            </form>
        </div>
    </div>
    <div class="text-center mt-4">
    <a href="dashboard_offres.php" class="btn btn-secondary">⬅ Retour à la liste des offres</a>
</div>
</body>
</html>