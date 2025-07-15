<?php
session_start();

// Vérification de l'authentification et du rôle
if (!isset($_SESSION["user_id"]) || strcasecmp($_SESSION["role"], "candidat") !== 0) {
    header("Location: loginh.php");
    exit();
}

require '../db.php';

$user_id = $_SESSION["user_id"];

// Récupération des informations du candidat
$stmt = $conn->prepare("SELECT prenom, nom, email, telephone FROM users WHERE id = :user_id");
$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$prenom_candidat = $user["prenom"] ?? "Utilisateur";
$nom_candidat = $user["nom"] ?? "Utilisateur";
$email_candidat = $user["email"] ?? "Non renseigné";
$tel_candidat = $user["telephone"] ?? "Non renseigné";

// Traitement de la recherche avec filtres étendus pour les offres
$search_results = [];
$search_term = $_GET['search'] ?? '';
$experience_filter = $_GET['experience'] ?? '';
$niveau_filter = $_GET['niveau'] ?? '';
$domain_filter = $_GET['domain'] ?? '';
$adresse_filter = $_GET['adresse'] ?? '';

if (!empty(trim($search_term)) || !empty($experience_filter) || !empty($niveau_filter) || !empty($domain_filter) || !empty($adresse_filter)) {
    $where_conditions = ["o.statut = 'Publier'"];
    $params = [];
    
    // Recherche globale par terme
    if (!empty(trim($search_term))) {
        $search = "%" . trim($search_term) . "%";
        $where_conditions[] = "(o.titre LIKE :search OR o.description LIKE :search_desc OR o.domain LIKE :search_domain OR o.lieu LIKE :search_lieu)";
        $params[':search'] = $search;
        $params[':search_desc'] = $search;
        $params[':search_domain'] = $search;
        $params[':search_lieu'] = $search;
    }
    
    // Filtre spécifique par domain
    if (!empty($domain_filter)) {
        $where_conditions[] = "o.domain LIKE :domain";
        $params[':domain'] = "%" . $domain_filter . "%";
    }
    
    // Filtre spécifique par lieu
    if (!empty($adresse_filter)) {
        $where_conditions[] = "o.lieu LIKE :lieu";
        $params[':lieu'] = "%" . $adresse_filter . "%";
    }
    
    // Filtre par expérience (champ non présent dans la table offres, donc ignoré !)
    // Filtre par niveau (champ non présent dans la table offres, donc ignoré !)
    // Si tu ajoutes ces champs à la table plus tard, tu pourras décommenter les lignes suivantes :
    /*
    if (!empty($experience_filter)) {
        $where_conditions[] = "o.experience_requise LIKE :experience";
        $params[':experience'] = "%" . $experience_filter . "%";
    }
    if (!empty($niveau_filter)) {
        $where_conditions[] = "o.niveau_etudes_requis LIKE :niveau";
        $params[':niveau'] = "%" . $niveau_filter . "%";
    }
    */

    $where_clause = implode(" AND ", $where_conditions);

    $stmt = $conn->prepare("
        SELECT o.id, o.titre, o.description, o.domain, o.lieu, 
               o.type_contrat, o.date_postee,
               u.nom as entreprise_nom, u.email as entreprise_email
        FROM offres o 
        LEFT JOIN users u ON o.recruteur_id = u.id
        WHERE $where_clause
        ORDER BY o.titre DESC
    ");
    
    foreach($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    
    $stmt->execute();
    $search_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Espace Candidat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../Gestion_candidats/candidats.css">
    
    <style>
        .welcome-section {
            background: url('../images/image5.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: rgba(242, 243, 245, 0.8);
            padding: 70px 0;
            border-radius: 20px;
            margin: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            position: relative;
            overflow: hidden;
        }
        .welcome-section::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.1);
            z-index: 1;
        }
        .welcome-content {
            position: relative;
            z-index: 2;
            text-align: center;
            padding: 0 20px;
        }
        .welcome-section h2 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .welcome-section p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
            opacity: 0.95;
        }
        .action-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 30px;
        }
        .welcome-btn {
            padding: 14px 30px;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            backdrop-filter: blur(10px);
            min-width: 180px;
            justify-content: center;
        }
        .btn-primary-welcome {
            background: rgba(255, 255, 255, 0.9);
            color: #007bff;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
        .btn-primary-welcome:hover {
            background: white;
            color: #0056b3;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }
        .search-container {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin: 30px auto;
            max-width: 1200px;
        }
        .search-row, .search-row-secondary {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .search-row-secondary {
            padding-top: 15px;
            border-top: 1px solid #e9ecef;
        }
        .search-input-container {
            flex: 1;
            min-width: 300px;
            position: relative;
        }
        .search-input {
            width: 100%;
            padding: 12px 20px 12px 45px;
            border: 2px solid #e9ecef;
            border-radius: 50px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        .search-input:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
            background: white;
        }
        .search-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            font-size: 16px;
        }
        .filter-select {
            padding: 12px 20px;
            border: 2px solid #e9ecef;
            border-radius: 50px;
            background: #f8f9fa;
            font-size: 14px;
            min-width: 180px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .search-btn {
            background: #6c757d;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 50px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .search-btn:hover {
            background: #5a6268;
            transform: translateY(-1px);
            color: white;
        }
        .results-summary {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 20px;
            padding: 15px 20px;
            background: #e8f5e8;
            border-radius: 50px;
            border: 1px solid #d4edda;
        }
        .results-text {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #155724;
            font-weight: 500;
            margin: 0;
        }
        .results-count {
            background: #28a745;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
        }
        .reinitialiser-btn {
            background: #6c757d;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 25px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        .reinitialiser-btn:hover {
            background: #5a6268;
            color: white;
            text-decoration: none;
        }
        .result-card {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .result-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .offer-title {
            color: #007bff;
            font-weight: 600;
            font-size: 18px;
            margin-bottom: 10px;
        }
        .offer-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 10px;
            margin-bottom: 15px;
        }
        .info-item {
            color: #6c757d;
            font-size: 14px;
        }
        .info-label {
            font-weight: 600;
            color: #495057;
        }
        .contact-btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 25px;
            font-size: 14px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
        }
        .contact-btn:hover {
            background: #0056b3;
            color: white;
            transform: translateY(-1px);
            text-decoration: none;
        }
        @media (max-width: 768px) {
            .welcome-section h2 { font-size: 2rem; }
            .welcome-section p { font-size: 1rem; }
            .action-buttons { flex-direction: column; align-items: center; }
            .welcome-btn { min-width: 250px; }
            .search-row, .search-row-secondary { flex-direction: column; align-items: stretch; }
            .search-input-container { min-width: auto; }
            .filter-select { min-width: auto; }
            .results-summary { flex-direction: column; gap: 10px; text-align: center; }
            .offer-info { grid-template-columns: 1fr; }
        }
        .no-results {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }
        .no-results-icon {
            font-size: 48px;
            color: #dee2e6;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<div class="overlay">
    <header>
        <h1><i class="fa-solid fa-user-tie"></i> Espace Candidat</h1>
        <div class="button-container">
            <a href="../index.php" class="btn btn-logout">Accueil</a>
            <a href="../logout.php" class="btn btn-logout">Déconnexion</a>
        </div>
    </header><br>
    
    <div class="welcome-section">
        <div class="welcome-content">
            <h2>Bienvenue, <?= htmlspecialchars($prenom_candidat) . " " . htmlspecialchars($nom_candidat); ?> ! 👋</h2>
            <p>Accédez à vos outils pour gérer votre profil et suivre vos candidatures.</p>
            <div class="action-buttons">
                <a href="modifier_profil.php" class="welcome-btn btn-primary-welcome">
                    <i class="fas fa-user-edit"></i>
                    Modifier le profil
                </a>
                <a href="profile_candidats.php" class="welcome-btn btn-primary-welcome">
                    <i class="fas fa-plus-circle"></i>
                    Compléter profil
                </a>
                <a href="suivi_candidature.php" class="welcome-btn btn-primary-welcome">
                    <i class="fas fa-plus-circle"></i>
                    Suivi des candidatures
                </a>
                <a href="postuler.php" class="welcome-btn btn-primary-welcome">
                    <i class="fas fa-plus-circle"></i>
                    Postuler à une offre
                </a>
            </div>
        </div>
    </div>

    <!-- Barre de recherche moderne repositionnée -->
    <div class="container-fluid">
        <div class="search-container">
            <form method="GET" id="searchForm">
                <div class="search-row">
                    <div class="search-input-container">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" 
                               name="search" 
                               class="search-input" 
                               placeholder="Rechercher par titre, domaine, lieu ou description..."
                               value="<?= htmlspecialchars($search_term) ?>">
                    </div>
                    <button type="submit" class="search-btn">
                        <i class="fas fa-search"></i>
                        Rechercher
                    </button>
                </div>
                <div class="search-row-secondary">
                    <input type="text" 
                           name="domain" 
                           class="filter-select" 
                           placeholder="Domaine spécifique"
                           value="<?= htmlspecialchars($domain_filter) ?>"
                           style="border-radius: 25px; min-width: 150px;">
                    <input type="text" 
                           name="adresse" 
                           class="filter-select" 
                           placeholder="Ville ou région"
                           value="<?= htmlspecialchars($adresse_filter) ?>"
                           style="border-radius: 25px; min-width: 150px;">
                </div>
            </form>
            <?php if (!empty($search_term) || !empty($domain_filter) || !empty($adresse_filter)): ?>
                <div class="results-summary">
                    <p class="results-text">
                        <i class="fas fa-check-circle"></i>
                        <span class="results-count"><?= count($search_results) ?> offres trouvées</span>
                    </p>
                    <a href="<?= $_SERVER['PHP_SELF'] ?>" class="reinitialiser-btn">
                        <i class="fas fa-times"></i>
                        Réinitialiser
                    </a>
                </div>
                <div class="results-section mt-4">
                    <?php if (empty($search_results)): ?>
                        <div class="no-results">
                            <div class="no-results-icon">
                                <i class="fas fa-search"></i>
                            </div>
                            <h5>Aucune offre trouvée</h5>
                            <p>Essayez avec d'autres critères de recherche.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($search_results as $offre): ?>
                            <div class="result-card">
                                <div class="offer-title">
                                    <i class="fas fa-briefcase"></i> <?= htmlspecialchars($offre['titre']) ?>
                                </div>
                                <div class="offer-info">
                                    <div class="info-item">
                                        <span class="info-label">Entreprise:</span> <?= htmlspecialchars($offre['entreprise_nom'] ?? 'Non spécifiée') ?>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Domaine:</span> <?= htmlspecialchars($offre['domain'] ?? 'Non spécifié') ?>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Lieu:</span> <?= htmlspecialchars($offre['lieu'] ?? 'Non spécifié') ?>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Type de contrat:</span> <?= htmlspecialchars($offre['type_contrat'] ?? 'Non spécifié') ?>
                                    </div>
                                </div>
                                <div class="info-item" style="margin-bottom: 15px;">
                                    <span class="info-label">Description:</span> 
                                    <?= htmlspecialchars(substr($offre['description'], 0, 150)) ?><?= strlen($offre['description']) > 150 ? '...' : '' ?>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="info-item">
                                        <span class="info-label">Publié le:</span> <?= date('d/m/Y', strtotime($offre['date_postee'])) ?>
                                    </span>
                                    <a href="postuler.php?offre_id=<?= $offre['id'] ?>" class="contact-btn">
                                        <i class="fas fa-paper-plane"></i>
                                        Postuler
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <footer>
        &copy; <?= date("Y"); ?> GlobalJobs. Tous droits réservés.
    </footer>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Animation au focus des champs
    document.querySelectorAll('.search-input, .filter-select').forEach(element => {
        element.addEventListener('focus', function() {
            this.style.transform = 'scale(1.02)';
        });
        element.addEventListener('blur', function() {
            this.style.transform = 'scale(1)';
        });
    });
</script>
</body>
</html>