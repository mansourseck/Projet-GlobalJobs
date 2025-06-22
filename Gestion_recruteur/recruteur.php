<?php
session_start();

// Vérification de l'authentification et du statut
if (!isset($_SESSION["user_id"]) || strcasecmp($_SESSION["statut"], "recruteur") !== 0) {
    header("Location: loginh.php");
    exit();
}

require '../db.php';

$user_id = $_SESSION["user_id"];

// Récupération des informations du recruteur
$stmt = $conn->prepare("SELECT prenom, nom, email, telephone FROM users WHERE id = :user_id");
$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
// Gestion des valeurs par défaut
$prenom_recruteur = $user["prenom"] ?? "Utilisateur";
$nom_recruteur = $user["nom"] ?? "Utilisateur";
$email_recruteur = $user["email"] ?? "Non renseigné";
$tel_recruteur = $user["telephone"] ?? "Non renseigné"; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Espace Recruteur</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../Gestion_candidats/candidats.css">
</head>
<body>

<div class="overlay">
    <header>
    <h1><i class="fa-solid fa-user-tie"></i> Espace Recruteur</h1>
    <div class="button-container">
        <a href="../index.php" class="btn btn-logout">Accueil</a>
        <a href="logout.php" class="btn btn-logout">Déconnexion</a>
    </div>
</header><br>
    <div class="welcome-section">
        <h2>Bienvenue, <?= htmlspecialchars($prenom_recruteur) . " " . htmlspecialchars($nom_recruteur); ?> ! 👋</h2>
        <p>Accédez à vos outils pour gérer vos offres et consulter les recruteurures.</p>

        <div class="profile-info">
            <div class="info-details">
                <p><strong>Nom Complet :</strong> <?= htmlspecialchars($prenom_recruteur) . " " . htmlspecialchars($nom_recruteur); ?></p>
                    <p><strong>Email :</strong> <?= htmlspecialchars($email_recruteur); ?></p>
                    <p><strong>Téléphone :</strong> <?= htmlspecialchars($tel_recruteur); ?></p>
            </div>
            <div class="action-btn">
                <a href="profil_recruteur.php" class="btn btn-primary">Modifier le profil</a>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <div class="row g-4">
            <?php
            $features = [
                ["icon" => "fa-file-alt", "title" => "Gestion offre", "desc" => "Ajoutez vos offres pour attirer des talents.", "link" => "../Gestion_offres/dashboard_offres.php"],
                ["icon" => "fa-users", "title" => "Consulter les candidatures reçues", "desc" => "Analysez les recruteurures et sélectionnez les meilleurs profils.", "link" => "Consulter_candidatures.php"],
                ["icon" => "fa-building", "title" => "Gérer votre profil", "desc" => "Mettez à jour vos informations et celles de votre entreprise.", "link" => "profil_recruteur.php"],
                ["icon" => "fa-comments", "title" => "Communiquer avec les candidats", "desc" => "Repondre au candidats postuler.", "link" => "communiquer_candidats.php"]
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