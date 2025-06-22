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

        $upload_dir = "uploads/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $cv = '';
        if (!empty($_FILES["cv"]["name"])) {
            $cv = $upload_dir . time() . "_" . basename($_FILES["cv"]["name"]);
            move_uploaded_file($_FILES["cv"]["tmp_name"], $cv);
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
        html, body {
            height: 100%;
            margin: 0;
        }
        body {
            background:
                linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)),
                url('../images/image2.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            flex-direction: column;
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
        .message-container {
            margin-top: 100px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            padding: 0 15px;
        }
        .alert {
            border-radius: 12px;
            font-size: 1rem;
            padding: 15px 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(5px);
        }
        .alert-success {
            background-color: rgba(40, 167, 69, 0.9);
            color: #fff;
        }
        .alert-info {
            background-color: rgba(0, 123, 255, 0.85);
            color: #fff;
        }
        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding-top: 90px;
            padding-bottom: 50px;
        }
        .card-custom {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            width: 100%;
            max-width: 600px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .btn-save {
            background-color: #007bff;
            color: white;
            width: 100%;
            padding: 10px;
        }
        .footer {
            background: rgba(7, 147, 235, 0.8);
            color: white;
            text-align: center;
            padding: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="../Gestion_candidats/candidats.php" class="btn">🏠 Accueil</a>
        <h1>📋 Complétez votre profil</h1>
    </div>

    <div class="message-container">
        <?php if (!empty($message)): ?>
            <div class="alert alert-success text-center"><?php echo htmlspecialchars($message); ?></div>
        <?php elseif ($candidat_data): ?>
            <div class="alert alert-info text-center">ℹ️ Vous avez déjà complété votre profil. Aucune modification n’est autorisée.</div>
        <?php endif; ?>
    </div>

    <main>
        <?php if (!$candidat_data): ?>
        <div class="card card-custom">
            <form action="#" method="post" enctype="multipart/form-data">
                <label for="metier" class="form-label">Nom du métier :</label>
                <input type="text" name="metier" id="metier" class="form-control mb-3" required>

                <label for="cv" class="form-label">📄 CV :</label>
                <input type="file" name="cv" class="form-control mb-3" accept=".pdf,.doc,.docx">

                <label for="competences" class="form-label">🛠️ Compétences :</label>
                <input type="text" name="competences" class="form-control mb-3"
                       placeholder="Ex : Développement Web, Marketing..." required>

                <label for="niveau_etudes" class="form-label">🎓 Niveau d'études :</label>
                <select name="niveau_etudes" class="form-control mb-3" required>
                    <option value="">-- Sélectionnez --</option>
                    <option value="bac">🎓 Baccalauréat</option>
                    <option value="bac+2">📚 Bac +2</option>
                    <option value="bac+3">🎯 Bac +3</option>
                    <option value="bac+5">🏆 Bac +5</option>
                    <option value="doctorat">👨‍🎓 Doctorat</option>
                </select>

                <label for="experience" class="form-label">💼 Expérience :</label>
                <input type="text" name="experience" id="experience" class="form-control mb-3"
       placeholder="Ex : 1 an en gestion de projet, 5 ans en cybersécurité..." required>
                <button type="submit" class="btn btn-save">💾 Enregistrer</button>
            </form>
        </div>
        <?php endif; ?>
    </main>

    <div class="footer">
        🌟 &copy; 2025 GlobalJobs 🌟
    </div>
</body>
</html>