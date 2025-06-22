<?php session_start();
// Vérification de l'authentification et du statut
if (!isset($_SESSION["user_id"]) || strcasecmp($_SESSION["statut"], "candidat") !== 0) {
    header("Location: loginh.php");
    exit();
}

require '../db.php';
$user_id = $_SESSION["user_id"];

// Préparation de la requête sécurisée
$stmt = $conn->prepare("SELECT prenom, nom, email, telephone FROM users WHERE id = :user_id");
$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
// Gestion des valeurs par défaut
$prenom_candidat = $user["prenom"] ?? "Utilisateur";
$nom_candidat = $user["nom"] ?? "Utilisateur";
$email_candidat = $user["email"] ?? "Non renseigné";
$tel_candidat = $user["telephone"] ?? "Non renseigné"; ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Espace Candidat</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="candidats.css">

</head>

<body>
    <div class="overlay">
        <header>
            <h1><i class="fa-solid fa-user-tie"></i> Espace Candidat</h1>
            <div class="button-container">
                <a href="../index.php" class="btn btn-logout">Accueil</a>
                <a href="logout.php" class="btn btn-logout">Déconnexion</a>
            </div>
        </header><br>
        <div class="welcome-section">
            <h2>Bienvenue, <?= htmlspecialchars($prenom_candidat) . " " . htmlspecialchars($nom_candidat); ?> ! 👋</h2>
            <p>Accédez à vos outils pour gérer votre profil et suivre vos candidatures.</p>
            <div class="profile-info">
                <div class="info-details">
                    <p><strong>Nom Complet :</strong> <?= htmlspecialchars($prenom_candidat) . " " . htmlspecialchars($nom_candidat); ?></p>
                    <p><strong>Email :</strong> <?= htmlspecialchars($email_candidat); ?></p>
                    <p><strong>Téléphone :</strong> <?= htmlspecialchars($tel_candidat); ?></p>
                </div>
                <div class="action-btn">
                    <a href="modifier_profil.php" class="btn btn-primary">Modifier le profil</a>
                </div>
            </div>

        </div>
        <div class="container mt-4">
            <div class="row g-4">
                <?php
                $features = [
                    ["icon" => "fa-user-pen", "title" => "Compléter votre profil", "desc" => "Ajoutez votre CV, vos compétences et votre expérience.", "link" => "profile_candidats.php"],
                    ["icon" => "fa-search", "title" => "Rechercher des offres", "desc" => "Trouvez des emplois correspondant à vos compétences.", "link" => "offres.php"],
                    ["icon" => "fa-paper-plane", "title" => "Postuler à une offre", "desc" => "Envoyez votre candidature aux offres qui vous intéressent.", "link" => "postuler.php"],
                    ["icon" => "fa-chart-line", "title" => "Suivre vos candidatures", "desc" => "Consultez le statut de vos candidatures.", "link" => "suivi_candidature.php"]
                ];

                foreach ($features as $feature) {
                    echo '<div class="col-md-6">
                    <div class="card card-custom text-center p-4 shadow">
                        <h5 class="card-title"><i class="fa-solid ' . $feature["icon"] . ' icon"></i> ' . $feature["title"] . '</h5>
                        <p class="card-text">' . $feature["desc"] . '</p>
                        <a href="' . $feature["link"] . '" class="btn btn-primary">Accéder</a>
                    </div>
                </div>';
                }
                ?>
            </div>
        </div>

        <footer>
            &copy; <?= date("Y"); ?> GlobalJobs. Tous droits réservés.
        </footer>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>