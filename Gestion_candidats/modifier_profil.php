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
    SELECT u.nom, u.prenom, u.email, u.telephone, u.adresse,
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

// Gérer le message de succès ou d'erreur d'upload
$upload_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = trim($_POST["nom"]);
    $prenom = trim($_POST["prenom"]);
    $email = trim($_POST["email"]);
    $telephone = trim($_POST["telephone"]);
    $adresse = trim($_POST["adresse"]);
    $competences = trim($_POST["competences"]);
    $experience = trim($_POST["experience"]);
    $niveau_etudes = trim($_POST["niveau_etudes"]);

    // Gestion du CV
    $cv = $candidat["cv"]; // Par défaut, garde l'ancien
    if (isset($_FILES["cv"]) && $_FILES["cv"]["error"] !== 4) { // 4 signifie pas de fichier envoyé
        if ($_FILES["cv"]["error"] === 0 && $_FILES["cv"]["size"] > 0) {
            $upload_dir = __DIR__ . "/../uploads/";
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $file_name = time() . "_" . basename($_FILES["cv"]["name"]);
            $dest_path = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES["cv"]["tmp_name"], $dest_path)) {
                $cv = "uploads/" . $file_name;
                $upload_message = "<div class='alert alert-success text-center mb-2'>✅ Nouveau CV uploadé avec succès !</div>";
            } else {
                $upload_message = "<div class='alert alert-danger text-center mb-2'>❌ Erreur lors de l'upload du CV.</div>";
            }
        } else {
            $upload_message = "<div class='alert alert-danger text-center mb-2'>❌ Erreur lors de l'upload. Fichier invalide.</div>";
        }
    }

    try {
        $conn->beginTransaction();

        // Mise à jour des informations de l'utilisateur
        $stmtUser = $conn->prepare("UPDATE users SET nom = ?, prenom = ?, email = ?, telephone = ?, adresse = ? WHERE id = ?");
        $stmtUser->execute([$nom, $prenom, $email, $telephone, $adresse, $user_id]);

        // Mise à jour des informations du candidat
        $stmtCandidat = $conn->prepare("UPDATE candidat SET competences = ?, experience = ?, niveau_etudes = ?, cv = ? WHERE user_id = ?");
        $stmtCandidat->execute([$competences, $experience, $niveau_etudes, $cv, $user_id]);

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
    <title>Modifier le profil - Candidat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f8fb;
            min-height: 100vh;
        }
        .header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
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
            color: #28a745;
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
            background-color: #28a745;
            border-color: #28a745;
        }
        .btn-primary:hover {
            background-color: #218838;
            border-color: #1e7e34;
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
        .cv-link {
            font-size: 0.97rem;
            color: #28a745;
            word-break: break-all;
        }
        .cv-info {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 10px;
            margin-top: 8px;
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
            <h1>💼 GlobalJobs - Espace Candidat</h1>
            <p>Bienvenue <?= htmlspecialchars($candidat["prenom"] . " " . $candidat["nom"]) ?> - Gérez votre profil professionnel</p>
        </div>
    </div>

    <div class="profile-card">
        <h2>✏️ Modifier mon profil candidat</h2>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-info text-center mb-3">
                <?= $_SESSION['message'] ?>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <?= $upload_message ?>

        <form method="POST" enctype="multipart/form-data" autocomplete="off">
            <!-- Informations personnelles -->
            <div class="section-title">👤 Informations personnelles</div>
            
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label for="nom" class="form-label">Nom :</label>
                    <input type="text" id="nom" name="nom" class="form-control" value="<?= htmlspecialchars($candidat["nom"] ?? '') ?>" required>
                </div>
                <div class="col-md-6 mb-2">
                    <label for="prenom" class="form-label">Prénom :</label>
                    <input type="text" id="prenom" name="prenom" class="form-control" value="<?= htmlspecialchars($candidat["prenom"] ?? '') ?>" required>
                </div>
            </div>
            
            <div class="mb-2">
                <label for="email" class="form-label">Email :</label>
                <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($candidat["email"] ?? '') ?>" required>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label for="telephone" class="form-label">Téléphone :</label>
                    <input type="text" id="telephone" name="telephone" class="form-control" value="<?= htmlspecialchars($candidat["telephone"] ?? '') ?>" required>
                </div>
                <div class="col-md-6 mb-2">
                    <label for="adresse" class="form-label">Adresse :</label>
                    <input type="text" id="adresse" name="adresse" class="form-control" value="<?= htmlspecialchars($candidat["adresse"] ?? '') ?>" required>
                </div>
            </div>

            <!-- Informations professionnelles -->
            <div class="section-title">🎓 Informations professionnelles</div>
            
            <div class="mb-2">
                <label for="niveau_etudes" class="form-label">Niveau d'études :</label>
                <input type="text" id="niveau_etudes" name="niveau_etudes" class="form-control" value="<?= htmlspecialchars($candidat["niveau_etudes"] ?? '') ?>" required>
            </div>
            
            <div class="mb-2">
                <label for="competences" class="form-label">Compétences :</label>
                <input type="text" id="competences" name="competences" class="form-control" value="<?= htmlspecialchars($candidat["competences"] ?? '') ?>" required>
                <small class="text-muted">Séparez vos compétences par des virgules (ex: PHP, JavaScript, MySQL)</small>
            </div>
            
            <div class="mb-3">
                <label for="experience" class="form-label">Expérience professionnelle :</label>
                <textarea id="experience" name="experience" class="form-control" rows="3" required><?= htmlspecialchars($candidat["experience"] ?? '') ?></textarea>
                <small class="text-muted">Décrivez brièvement votre expérience professionnelle</small>
            </div>

            <!-- CV -->
            <div class="section-title">📄 Curriculum Vitae</div>
            
            <div class="mb-3">
                <label for="cv" class="form-label">Télécharger un nouveau CV :</label>
                <input type="file" id="cv" name="cv" class="form-control" accept=".pdf,.doc,.docx">
                
                <?php if (!empty($candidat["cv"])): ?>
                    <div class="cv-info">
                        <div class="d-flex align-items-center">
                            <span class="me-2">📎</span>
                            <div>
                                <strong>CV actuel :</strong><br>
                                <a href="/projet_php_GlobalJobs/<?= htmlspecialchars($candidat["cv"]) ?>" target="_blank" class="cv-link">
                                    📄 Voir mon CV actuel
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <small class="text-muted">Formats acceptés : PDF, DOC, DOCX. Laissez vide pour conserver le CV actuel.</small>
            </div>

            <button type="submit" class="btn btn-primary">✅ Enregistrer les modifications</button>
        </form>

    </div>
</body>
</html>