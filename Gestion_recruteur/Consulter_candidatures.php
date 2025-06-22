<?php
session_start();
require '../db.php';

$recruteur_id = $_SESSION['user_id'];

// Récupérer tous les candidats
$sql = "SELECT u.id, u.nom, u.prenom, u.email, c.experience, c.niveau_etudes, c.competences, c.cv 
        FROM users u 
        INNER JOIN Candidat c ON u.id = c.user_id 
        WHERE u.statut = 'candidat'";
$tous_candidats = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// Récupérer l'ID du recruteur à partir de l'utilisateur connecté
$stmt = $conn->prepare("SELECT id FROM recruteurs WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$recruteur = $stmt->fetch();

if (!$recruteur) {
    die("❌ Erreur : recruteur introuvable.");
}

$recruteur_id = $recruteur['id'];
// Récupérer les postulants
$sqlPostulants = "SELECT u.id, u.nom, u.prenom, u.email, c.experience, c.niveau_etudes, c.competences, c.cv, ca.statut, ca.date_postulation 
                  FROM candidature ca
                  INNER JOIN candidat c ON ca.candidat_id = c.id
                  INNER JOIN users u ON c.user_id = u.id
                  INNER JOIN offres o ON ca.offre_id = o.id
                  WHERE o.recruteur_id = ?";
$stmt = $conn->prepare($sqlPostulants);
$stmt->execute([$recruteur_id]);
$postulants = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Liste des candidats - GlobalJobs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('../images/image3.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
        }

        .header {
            position: fixed;
            width: 100%;
            background: rgba(51, 213, 231, 0.8);
            color: white;
            padding: 15px;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .header h1 {
            flex-grow: 1;
            margin: 0;
            text-align: center;
        }

        .header .btn {
            position: absolute;
            right: 60px;
            background-color: #007bff;
            color: white;
        }

        .footer {
            width: 100%;
            background: rgba(51, 213, 231, 0.8);
            color: black;
            text-align: center;
            padding: 15px;
            margin-top: 50px;
        }

        .card-candidat {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            margin-bottom: 25px;
            position: relative;
        }

        .container-main {
            padding-top: 90px;
            padding-bottom: 20px;
            min-height: calc(100vh - 90px);
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 12px;
            background: rgba(255, 255, 255, 0.95);
        }

        .btn-info {
            background-color: #17a2b8;
            border-color: #17a2b8;
            border-radius: 8px;
            padding: 8px 16px;
        }

        .page-title,
        .search-container,
        .section-title {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            margin-bottom: 30px;
            text-align: center;
        }

        .candidat-title {
            color: #2c3e50;
            border-bottom: 2px solid #33d5e7;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }

        .info-row {
            margin-bottom: 8px;
        }

        .info-label {
            font-weight: 600;
            color: #34495e;
        }

        .postulant-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #28a745;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8em;
        }
    </style>
    <script>
        function filtrerCandidats() {
            var searchInput = document.getElementById("search").value.toLowerCase();
            var experienceFilter = document.getElementById("experienceFilter").value;
            var niveauFilter = document.getElementById("niveauFilter").value.toLowerCase();
            var cards = document.getElementsByClassName("candidat-card");
            var visibleCount = 0;

            for (var i = 0; i < cards.length; i++) {
                var competences = cards[i].dataset.competences.toLowerCase();
                var experience = parseInt(cards[i].dataset.experience) || 0;
                var niveau = cards[i].dataset.niveau.toLowerCase();
                var matchesSearch = searchInput === "" || competences.includes(searchInput);
                var matchesExperience = experienceFilter === "" || experience >= parseInt(experienceFilter);
                var matchesNiveau = niveauFilter === "" || niveau.includes(niveauFilter);
                var visible = matchesSearch && matchesExperience && matchesNiveau;

                cards[i].parentElement.style.display = visible ? "block" : "none";
                if (visible) visibleCount++;
            }
            updateResultCount(visibleCount);
        }

        function resetFiltres() {
            document.getElementById("search").value = "";
            document.getElementById("experienceFilter").value = "";
            document.getElementById("niveauFilter").value = "";
            filtrerCandidats();
        }

        function updateResultCount(count) {
            var resultElement = document.getElementById("resultCount");
            if (count === 0) {
                resultElement.innerHTML = "❌ Aucun candidat trouvé";
                resultElement.className = "ms-3 text-danger";
            } else {
                resultElement.innerHTML = "✅ " + count + " candidat" + (count > 1 ? "s" : "") + " trouvé" + (count > 1 ? "s" : "");
                resultElement.className = "ms-3 text-success";
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            filtrerCandidats();
        });
    </script>
</head>

<body>
    <div class="header">
        <a href="../Gestion_recruteur/recruteur.php" class="btn">🏠 Accueil</a>
        <h1>📌 Liste des candidats</h1>
    </div>

    <div class="container container-main">
        <div class="page-title">
            <h2>📌 Gestion des candidats</h2>
        </div>

        <div class="search-container">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <input type="text" id="search" class="form-control" placeholder="🔍 Rechercher par compétences" onkeyup="filtrerCandidats()">
                </div>
                <div class="col-md-4 mb-3">
                    <select id="experienceFilter" class="form-control" onchange="filtrerCandidats()">
                        <option value="">Toute expérience</option>
                        <option value="0">Débutant (0 ans)</option>
                        <option value="1">1+ ans</option>
                        <option value="2">2+ ans</option>
                        <option value="3">3+ ans</option>
                        <option value="5">5+ ans</option>
                        <option value="10">10+ ans</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <select id="niveauFilter" class="form-control" onchange="filtrerCandidats()">
                        <option value="">Tous les niveaux</option>
                        <option value="Bac">Baccalauréat</option>
                        <option value="Bac+2">Bac+2</option>
                        <option value="Bac+3">Bac+3 (Licence)</option>
                        <option value="Bac+5">Bac+5 (Master)</option>
                        <option value="Doctorat">Doctorat</option>
                    </select>
                </div>
            </div>
            <button type="button" class="btn btn-secondary" onclick="resetFiltres()">🔄 Réinitialiser</button>
            <span id="resultCount" class="ms-3"></span>
        </div>

        <div class="section-title">
            <h3 class="text-success">✅ Candidats ayant postulé</h3>
        </div>

        <div class="row">
            <?php if (empty($postulants)): ?>
                <div class="col-12">
                    <div class="card-candidat text-center">
                        <div class="alert alert-info">📭 Aucun postulant</div>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($postulants as $candidat): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card-candidat candidat-card" data-competences="<?= $candidat['competences'] ?>" data-experience="<?= $candidat['experience'] ?>" data-niveau="<?= $candidat['niveau_etudes'] ?>">
                            <div class="postulant-badge">✅ Postulant</div>
                            <h5 class="candidat-title">👤 <?= htmlspecialchars($candidat["nom"]) ?> <?= htmlspecialchars($candidat["prenom"]) ?></h5>
                            <div class="info-row"><span class="info-label">📧 Email :</span> <?= htmlspecialchars($candidat["email"]) ?></div>
                            <div class="info-row"><span class="info-label">💼 Expérience :</span> <?= htmlspecialchars($candidat["experience"]) ?> ans</div>
                            <div class="info-row"><span class="info-label">🎓 Niveau :</span> <?= htmlspecialchars($candidat["niveau_etudes"]) ?></div>
                            <div class="info-row"><span class="info-label">🛠️ Compétences :</span> <?= htmlspecialchars($candidat["competences"]) ?></div>
                            <div class="info-row"><span class="info-label">📅 Postulation :</span> <?= htmlspecialchars($candidat["date_postulation"]) ?></div>
                            <div class="info-row mb-3">
                                <span class="info-label">📊 Statut :</span>
                                <span class="badge <?= $candidat["statut"] == "Accepté" ? "bg-success" : ($candidat["statut"] == "Refusé" ? "bg-danger" : "bg-warning") ?>">
                                    <?= htmlspecialchars($candidat["statut"]) ?>
                                </span>
                            </div>
                            <a href="<?= $candidat['cv'] ?>" class="btn btn-info">📂 Voir CV</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <hr style="border: 2px solid rgba(51, 213, 231, 0.5); margin: 40px 0;">

        <div class="section-title">
            <h3 class="text-warning">📌 Tous les candidats</h3>
        </div>

        <div class="row">
            <?php if (empty($tous_candidats)): ?>
                <div class="col-12">
                    <div class="card-candidat text-center">
                        <div class="alert alert-info">📭 Aucun candidat</div>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($tous_candidats as $candidat): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card-candidat candidat-card" data-competences="<?= $candidat['competences'] ?>" data-experience="<?= $candidat['experience'] ?>" data-niveau="<?= $candidat['niveau_etudes'] ?>">
                            <h5 class="candidat-title">👤 <?= htmlspecialchars($candidat["nom"] ?? 'Nom inconnu') ?> <?= htmlspecialchars($candidat["prenom"] ?? '') ?></h5>
                            <div class="info-row"><span class="info-label">📧 Email :</span> <?= htmlspecialchars($candidat["email"] ?? 'Non fourni') ?></div>
                            <div class="info-row"><span class="info-label">💼 Expérience :</span> <?= htmlspecialchars($candidat["experience"] ?? '0') ?> ans</div>
                            <div class="info-row"><span class="info-label">🎓 Niveau :</span> <?= htmlspecialchars($candidat["niveau_etudes"] ?? 'Non spécifié') ?></div>
                            <div class="info-row mb-3"><span class="info-label">🛠️ Compétences :</span> <?= htmlspecialchars($candidat["competences"] ?? 'Non renseigné') ?></div>
                            <?php if (!empty($candidat['cv'])): ?>
                                <a class="btn btn-outline-primary w-100"
                                    href="../uploads <?= htmlspecialchars($candidat['cv']) ?>"
                                    target="_blank">
                                    📄 Voir le CV
                                </a>
                            <?php else: ?>
                                <div class="alert alert-warning text-center mt-2">Aucun CV disponible</div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>

    <div class="footer">
        🌟 &copy; 2025 GlobalJobs 🌟
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>