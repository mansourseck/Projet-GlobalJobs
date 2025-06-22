<?php
session_start();
require '../db.php';

// Vérifier si le candidat est connecté
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "❌ Vous devez être connecté.";
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];


$stmt = $conn->prepare("
    SELECT u.nom, u.prenom, u.email, u.telephone, 
           c.competences, c.experience, c.cv, c.niveau_etudes
    FROM users u
    INNER JOIN candidat c ON u.id = c.user_id
    WHERE u.id = ?
");
$stmt->execute([$user_id]);
$candidat = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$candidat) {
    die("❌ Erreur : Aucune donnée trouvée pour cet utilisateur.");
}


// Mise à jour du profil
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = trim($_POST["nom"]);
    $prenom = trim($_POST["prenom"]);
    $email = trim($_POST["email"]);
    $telephone = trim($_POST["telephone"]);
    $competences = trim($_POST["competences"]);
    $experience = trim($_POST["experience"]);
    $niveau_etudes = trim($_POST["niveau_etudes"]);

    try {
        $conn->beginTransaction();

        // Mise à jour des informations de l'utilisateur
        $stmtUser = $conn->prepare("UPDATE users SET nom = ?, prenom = ?, email = ?, telephone = ? WHERE id = ?");
        $stmtUser->execute([$nom, $prenom, $email, $telephone, $user_id]);

        // Mise à jour des informations du candidat
        $stmtCandidat = $conn->prepare("UPDATE candidat SET competences = ?, experience = ?, niveau_etudes = ? WHERE user_id = ?");
        $stmtCandidat->execute([$competences, $experience, $niveau_etudes, $user_id]);

        $conn->commit();
        $_SESSION['message'] = "✅ Profil mis à jour avec succès.";
        exit();
    } catch (PDOException $e) {
        $conn->rollBack();
        $_SESSION['message'] = "❌ Erreur lors de la mise à jour.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Modifier le profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('../images/image2.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container-card {
            width: 100%;
            max-width: 700px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(8px);
        }

        .form-label {
            font-weight: bold;
            color: #333;
        }

        .btn-primary {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border-radius: 8px;
        }

        .btn-secondary {
            width: 100%;
            padding: 8px;
            font-size: 14px;
            border-radius: 8px;
            background-color: #6c757d;
            color: white;
            border: none;
        }
    </style>
</head>

<body>

    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="container-card">
            <h2 class="text-center mb-4">✏️ Modifier le profil</h2>

            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-info text-center">
                    <?= $_SESSION['message'] ?>
                </div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label for="nom" class="form-label">Nom :</label>
                    <input type="text" id="nom" name="nom" class="form-control" value="<?= htmlspecialchars($candidat["nom"] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label for="prenom" class="form-label">Prénom :</label>
                    <input type="text" id="prenom" name="prenom" class="form-control" value="<?= htmlspecialchars($candidat["prenom"] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email :</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($candidat["email"] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label for="telephone" class="form-label">Téléphone :</label>
                    <input type="text" id="telephone" name="telephone" class="form-control" value="<?= htmlspecialchars($candidat["telephone"] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label for="competences" class="form-label">Compétences :</label>
                    <input type="text" id="competences" name="competences" class="form-control" value="<?= htmlspecialchars($candidat["competences"] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label for="experience" class="form-label">Expérience :</label>
                    <textarea id="experience" name="experience" class="form-control" required><?= htmlspecialchars($candidat["experience"] ?? '') ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="niveau_etudes" class="form-label">Niveau d’étude :</label>
                    <input type="text" id="niveau_etudes" name="niveau_etudes" class="form-control" value="<?= htmlspecialchars($candidat["niveau_etudes"] ?? '') ?>" required>
                </div>

                <button type="submit" class="btn btn-primary">✅ Enregistrer les modifications</button>
            </form>

            <div class="mt-4">
                <a href="candidats.php" class="btn btn-secondary">↩ Retour</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>