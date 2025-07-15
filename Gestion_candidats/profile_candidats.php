<?php
session_start();
include '../db.php';

$message = '';
$candidat_data = null;

if (isset($_SESSION["user_id"])) {
    $user_id = $_SESSION["user_id"];

    $stmt_get = $conn->prepare("SELECT * FROM candidat WHERE user_id = :user_id");
    $stmt_get->bindParam(":user_id", $user_id, PDO::PARAM_INT);
    $stmt_get->execute();
    $candidat_data = $stmt_get->fetch(PDO::FETCH_ASSOC);

    if ($_SERVER["REQUEST_METHOD"] == "POST" && !$candidat_data) {
        $metier = trim($_POST["metier"]);
        $competences = trim($_POST["competences"]);
        $niveau_etudes = trim($_POST["niveau_etudes"]);
        $experience = trim($_POST["experience"]);

        $upload_dir = __DIR__ . "/../uploads/"; // Dossier uploads à la racine du projet
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $cv = '';
        if (!empty($_FILES["cv"]["name"])) {
            $file_name = time() . "_" . basename($_FILES["cv"]["name"]);
            $dest_path = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES["cv"]["tmp_name"], $dest_path)) {
                $cv = "uploads/" . $file_name; // Chemin relatif à la racine du site
            } else {
                echo "❌ Erreur lors de l'upload du CV. Vérifiez les droits du dossier uploads !";
                exit;
            }
        }

        $stmt_insert = $conn->prepare("INSERT INTO candidat (user_id, metier, cv, competences, niveau_etudes, experience) VALUES (:user_id, :metier, :cv, :competences, :niveau_etudes, :experience)");
        $stmt_insert->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $stmt_insert->bindParam(":metier", $metier);
        $stmt_insert->bindParam(":cv", $cv);
        $stmt_insert->bindParam(":competences", $competences);
        $stmt_insert->bindParam(":niveau_etudes", $niveau_etudes);
        $stmt_insert->bindParam(":experience", $experience);
        $stmt_insert->execute();

        $message = "✅ Profil enregistré avec succès !";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Compléter votre profil</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)),
                        url('../images/image2.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
        }
        
        .header {
            position: fixed;
            width: 100%;
            background: rgba(7, 147, 235, 0.8);
            color: white;
            padding: 15px;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        
        .header h1 {
            margin: 0;
            flex-grow: 1;
            text-align: center;
        }
        
        .header .btn {
            position: absolute;
            right: 60px;
            background-color: #007bff;
            color: white;
        }
        
        .container-main {
            padding-top: 90px;
            padding-bottom: 20px;
            min-height: calc(100vh - 90px);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .split-container {
            display: flex;
            max-width: 1000px;
            width: 100%;
            gap: 30px;
            align-items: stretch;
        }
        
        .form-section, .info-section {
            flex: 1;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 35px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        }
        
        .info-section {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .alert {
            border-radius: 12px;
            margin-bottom: 25px;
        }
        
        .alert-success {
            background-color: rgba(40, 167, 69, 0.9);
            color: #fff;
        }
        
        .alert-info {
            background-color: rgba(7, 147, 235, 0.85);
            color: #fff;
        }
        
        .form-control, select.form-control {
            border-radius: 8px;
            padding: 12px;
            border: 1px solid #ddd;
        }
        
        .form-control:focus, select.form-control:focus {
            border-color: #0793eb;
            box-shadow: 0 0 0 0.2rem rgba(7, 147, 235, 0.25);
        }
        
        .btn-save {
            background-color: #007bff;
            border-color: #007bff;
            color: white;
            width: 100%;
            padding: 12px;
            border-radius: 8px;
        }
        
        .btn-save:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        
        .info-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: #2d3748;
        }
        
        .tips {
            list-style: none;
            padding: 0;
        }
        
        .tips li {
            margin-bottom: 10px;
            display: flex;
            align-items: flex-start;
            color: #444;
        }
        
        .tips li::before {
            content: '💡';
            margin-right: 10px;
            margin-top: 2px;
        }
        
        .footer {
            background: rgba(7, 147, 235, 0.8);
            color: white;
            text-align: center;
            padding: 15px;
        }
        
        @media (max-width: 768px) {
            .split-container {
                flex-direction: column;
                gap: 20px;
            }
            .form-section, .info-section {
                padding: 25px;
            }
            .header .btn {
                right: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="../Gestion_candidats/candidats.php" class="btn">🏠 Accueil</a>
        <h1>📋 Complétez votre profil</h1>
    </div>

    <div class="container container-main">
        <div class="split-container">
            <!-- Section Formulaire -->
            <div class="form-section">
                <?php if (!empty($message)): ?>
                    <div class="alert alert-success text-center"><?php echo htmlspecialchars($message); ?></div>
                <?php elseif ($candidat_data): ?>
                    <div class="alert alert-info text-center">ℹ️ Vous avez déjà complété votre profil. Aucune modification n'est autorisée.</div>
                <?php endif; ?>

                <?php if (!$candidat_data): ?>
                <form action="#" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="metier" class="form-label">👨‍💼 Nom du métier :</label>
                        <input type="text" name="metier" id="metier" class="form-control" placeholder="Ex: Développeur Web" required>
                    </div>

                    <div class="mb-3">
                        <label for="cv" class="form-label">📄 CV :</label>
                        <input type="file" name="cv" class="form-control" accept=".pdf,.doc,.docx">
                    </div>

                    <div class="mb-3">
                        <label for="competences" class="form-label">🛠️ Compétences :</label>
                        <input type="text" name="competences" class="form-control" 
                               placeholder="Ex : PHP, JavaScript, Marketing..." required>
                    </div>

                    <div class="mb-3">
                        <label for="niveau_etudes" class="form-label">🎓 Niveau d'études :</label>
                        <select name="niveau_etudes" class="form-control" required>
                            <option value="">-- Sélectionnez --</option>
                            <option value="bac">🎓 Baccalauréat</option>
                            <option value="bac+2">📚 Bac +2</option>
                            <option value="bac+3">🎯 Bac +3</option>
                            <option value="bac+5">🏆 Bac +5</option>
                            <option value="doctorat">👨‍🎓 Doctorat</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="experience" class="form-label">💼 Expérience :</label>
                        <input type="text" name="experience" id="experience" class="form-control"
                               placeholder="Ex : 2 ans en développement web..." required>
                    </div>

                    <button type="submit" class="btn btn-save">💾 Enregistrer mon profil</button>
                </form>
                <?php endif; ?>
            </div>

            <!-- Section Information -->
            <div class="info-section">
                <h2 class="info-title">Conseils pour votre profil</h2>
                <p style="color: #666; margin-bottom: 25px;">Un profil complet augmente vos chances d'être recruté par les meilleures entreprises.</p>
                
                <ul class="tips">
                    <li>Soyez précis dans la description de votre métier</li>
                    <li>Uploadez un CV récent et bien formaté</li>
                    <li>Listez vos compétences principales séparées par des virgules</li>
                    <li>Mentionnez vos années d'expérience concrètes</li>
                    <li>Votre profil sera visible par les recruteurs</li>
                    <li>Les informations ne pourront plus être modifiées</li>
                </ul>

                <div style="background: rgba(7, 147, 235, 0.1); border-left: 4px solid #0793eb; padding: 15px; margin-top: 30px; border-radius: 8px;">
                    <strong style="color: #0793eb;">Bon à savoir :</strong> Les profils complets reçoivent 3x plus de propositions d'emploi.
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        🌟 &copy; 2025 GlobalJobs 🌟
    </div>
</body>
</html>