<?php
require '../db.php';
require_once '../mailer_config.php'; // On inclut la fonction

if (!isset($_GET['id']) && !isset($_POST['id'])) {
    echo "Erreur : offre inconnue.";
    exit();
}

$offre_id = isset($_GET['id']) ? intval($_GET['id']) : intval($_POST['id']);

// Récupérer les infos de l'offre et du recruteur
$stmt = $conn->prepare("
    SELECT o.titre, u.email, u.nom, u.prenom
    FROM offres o
    INNER JOIN recruteurs r ON o.recruteur_id = r.id
    INNER JOIN users u ON r.user_id = u.id
    WHERE o.id = ?
");
$stmt->execute([$offre_id]);
$offre = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$offre) {
    echo "Offre non trouvée.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cause'])) {
    $cause = trim($_POST['cause']);

    // Met à jour le statut de l'offre
    $stmt_update = $conn->prepare("UPDATE offres SET statut = 'Rejété' WHERE id = ?");
    $stmt_update->execute([$offre_id]);

    // Envoi du mail via la fonction factorisée
    $subject = "Votre offre a été rejetée";
    $body = "
        Bonjour <b>" . htmlspecialchars($offre['nom'] . ' ' . $offre['prenom']) . "</b>,<br>
        Nous sommes désolés mais votre offre <b>" . htmlspecialchars($offre['titre']) . "</b> a été <span style='color:red'>rejetée</span>.<br>
        <b>Motif du rejet :</b> " . nl2br(htmlspecialchars($cause)) . "<br>
        Vous pouvez corriger et soumettre à nouveau.<br><br>
        - L'équipe GlobalJobs
    ";

    $result = sendMail($offre['email'], $offre['nom'] . ' ' . $offre['prenom'], $subject, $body);

    if ($result === true) {
        $msg = "Le recruteur a été informé du rejet de l'offre.";
    } else {
        $msg = "Erreur lors de l'envoi du mail : " . $result;
    }

    // Redirection ou affichage message
    echo "<div style='max-width:600px;margin:40px auto;font-family:sans-serif;'>
            <div class='alert alert-info'>$msg</div>
            <a href='admin_dashboard.php' class='btn btn-primary'>Retour au dashboard</a>
          </div>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Cause du rejet - GlobalJobs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .cause-box { max-width: 500px; margin: 60px auto; padding: 30px; background: #fff; border-radius: 10px; box-shadow:0 1px 10px #bbb; }
    </style>
</head>
<body>
    <div class="cause-box">
        <h4>Rejeter l'offre : <span class="text-primary"><?= htmlspecialchars($offre['titre']) ?></span></h4>
        <form method="post">
            <input type="hidden" name="id" value="<?= $offre_id ?>">
            <div class="mb-3">
                <label for="cause" class="form-label"><b>Motif du rejet à communiquer au recruteur :</b></label>
                <textarea class="form-control" id="cause" name="cause" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn btn-danger">Envoyer le motif</button>
            <a href="admin_dashboard.php" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
</body>
</html>