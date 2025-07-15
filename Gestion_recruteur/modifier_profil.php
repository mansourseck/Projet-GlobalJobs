<?php
session_start();
require '../db.php';

// Vérifier si le recruteur est connecté
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "❌ Vous devez être connecté.";
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupérer les informations du recruteur
$stmt = $conn->prepare("
    SELECT u.nom, u.prenom, u.email, u.telephone, u.adresse,
           r.entreprise, r.secteur, r.adresse_entreprise
    FROM users u
    INNER JOIN recruteurs r ON u.id = r.user_id
    WHERE u.id = ?
");
$stmt->execute([$user_id]);
$recruteur = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$recruteur) {
    die("❌ Erreur : Aucune donnée trouvée pour cet utilisateur.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = trim($_POST["nom"]);
    $prenom = trim($_POST["prenom"]);
    $email = trim($_POST["email"]);
    $telephone = trim($_POST["telephone"]);
    $adresse = trim($_POST["adresse"]);
    $entreprise = trim($_POST["entreprise"]);
    $secteur = trim($_POST["secteur"]);
    $adresse_entreprise = trim($_POST["adresse_entreprise"]);

    try {
        $conn->beginTransaction();

        // Mise à jour des informations de l'utilisateur
        $stmtUser = $conn->prepare("UPDATE users SET nom = ?, prenom = ?, email = ?, telephone = ?, adresse = ? WHERE id = ?");
        $stmtUser->execute([$nom, $prenom, $email, $telephone, $adresse, $user_id]);

        // Mise à jour des informations du recruteur
        $stmtRecruteur = $conn->prepare("UPDATE recruteurs SET entreprise = ?, secteur = ?, adresse_entreprise = ? WHERE user_id = ?");
        $stmtRecruteur->execute([$entreprise, $secteur, $adresse_entreprise, $user_id]);

        $conn->commit();
        $_SESSION['message'] = "✅ Profil mis à jour avec succès.";
        header("Location: " . $_SERVER['PHP_SELF']);
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
    <title>Modifier le profil - Recruteur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: 
                url('../images/image3.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
        }
        .header {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            padding: 20px 0;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header h1 {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
            text-align: center;
        }
        .header p {
            font-size: 1.1rem;
            margin: 8px 0 0 0;
            text-align: center;
            opacity: 0.9;
        }
        .profile-card {
            max-width: 700px;
            min-width: 320px;
            margin: 0 auto 40px auto;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.07);
            padding: 32px 36px 20px 36px;
        }
        .profile-card h2 {
            font-size: 1.5rem;
            text-align: center;
            margin-bottom: 18px;
            font-weight: 700;
            color: #007bff;
        }
        .form-label {
            font-weight: 500;
        }
        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #495057;
            margin: 20px 0 10px 0;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 5px;
        }
        .btn-primary {
            width: 100%;
            border-radius: 8px;
            margin-top: 10px;
        }
        .btn-secondary {
            width: 100%;
            border-radius: 8px;
            margin-top: 8px;
        }
        .alert {
            border-radius: 8px;
            font-size: 1rem;
        }
        @media (max-width: 900px) {
            .profile-card {
                padding: 18px 4vw 12px 4vw;
            }
        }
        @media (max-width: 600px) {
            .profile-card {
                max-width: 98vw;
                padding: 10px 2vw;
            }
            .header h1 {
                font-size: 1.5rem;
            }
            .header p {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- En-tête -->
    <div class="header">
        <div class="container">
            <h1>🏢 GlobalJobs - Espace Recruteur</h1>
            <p>Bienvenue <?= htmlspecialchars($recruteur["prenom"] . " " . $recruteur["nom"]) ?> - <?= htmlspecialchars($recruteur["entreprise"]) ?></p>
        </div>
    </div>

    <div class="profile-card">
        <h2>✏️ Modifier mon profil recruteur</h2>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-info text-center mb-3">
                <?= $_SESSION['message'] ?>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <form method="POST" autocomplete="off">
            <!-- Informations personnelles -->
            <div class="section-title">👤 Informations personnelles</div>
            
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label for="nom" class="form-label">Nom :</label>
                    <input type="text" id="nom" name="nom" class="form-control" value="<?= htmlspecialchars($recruteur["nom"] ?? '') ?>" required>
                </div>
                <div class="col-md-6 mb-2">
                    <label for="prenom" class="form-label">Prénom :</label>
                    <input type="text" id="prenom" name="prenom" class="form-control" value="<?= htmlspecialchars($recruteur["prenom"] ?? '') ?>" required>
                </div>
            </div>
            
            <div class="mb-2">
                <label for="email" class="form-label">Email :</label>
                <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($recruteur["email"] ?? '') ?>" required>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label for="telephone" class="form-label">Téléphone :</label>
                    <input type="text" id="telephone" name="telephone" class="form-control" value="<?= htmlspecialchars($recruteur["telephone"] ?? '') ?>" required>
                </div>
                <div class="col-md-6 mb-2">
                    <label for="adresse" class="form-label">Adresse personnelle :</label>
                    <input type="text" id="adresse" name="adresse" class="form-control" value="<?= htmlspecialchars($recruteur["adresse"] ?? '') ?>" required>
                </div>
            </div>

            <!-- Informations entreprise -->
            <div class="section-title">🏢 Informations entreprise</div>
            
            <div class="mb-2">
                <label for="entreprise" class="form-label">Nom de l'entreprise :</label>
                <input type="text" id="entreprise" name="entreprise" class="form-control" value="<?= htmlspecialchars($recruteur["entreprise"] ?? '') ?>" required>
            </div>
            
            <div class="mb-2">
                <label for="secteur" class="form-label">Secteur d'activité :</label>
                <input type="text" id="secteur" name="secteur" class="form-control" value="<?= htmlspecialchars($recruteur["secteur"] ?? '') ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="adresse_entreprise" class="form-label">Adresse de l'entreprise :</label>
                <input type="text" id="adresse_entreprise" name="adresse_entreprise" class="form-control" value="<?= htmlspecialchars($recruteur["adresse_entreprise"] ?? '') ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">✅ Enregistrer les modifications</button>
        </form>
        
    </div>
</body>
</html>