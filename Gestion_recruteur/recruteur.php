<?php
session_start();

// Vérification de l'authentification et du role
if (!isset($_SESSION["user_id"]) || strcasecmp($_SESSION["role"], "recruteur") !== 0) {
    header("Location: loginh.php");
    exit();
}

require '../db.php';

$user_id = $_SESSION["user_id"];

// Récupération des informations du recruteur
$stmt = $conn->prepare("SELECT prenom, nom FROM users WHERE id = :user_id");
$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$prenom_recruteur = $user["prenom"] ?? "Utilisateur";
$nom_recruteur = $user["nom"] ?? "Utilisateur";

// Traitement de la recherche avec filtres étendus
$search_results = [];
$search_term = $_GET['search'] ?? '';
$experience_filter = $_GET['experience'] ?? '';
$niveau_filter = $_GET['niveau'] ?? '';
$metier_filter = $_GET['metier'] ?? '';
$adresse_filter = $_GET['adresse'] ?? '';

if (!empty(trim($search_term)) || !empty($experience_filter) || !empty($niveau_filter) || !empty($metier_filter) || !empty($adresse_filter)) {
    $where_conditions = ["u.role = 'candidat'"];
    $params = [];

    // Recherche globale par terme
    if (!empty(trim($search_term))) {
        $search = "%" . trim($search_term) . "%";
        $where_conditions[] = "(p.competences LIKE :search OR p.metier LIKE :search_metier OR p.domaine LIKE :search_domaine OR u.adresse LIKE :search_adresse OR p.niveau_etudes LIKE :search_niveau)";
        $params[':search'] = $search;
        $params[':search_metier'] = $search;
        $params[':search_domaine'] = $search;
        $params[':search_adresse'] = $search;
        $params[':search_niveau'] = $search;
    }

    // Filtre spécifique par métier
    if (!empty($metier_filter)) {
        $where_conditions[] = "p.metier LIKE :metier";
        $params[':metier'] = "%" . $metier_filter . "%";
    }

    // Filtre spécifique par adresse
    if (!empty($adresse_filter)) {
        $where_conditions[] = "u.adresse LIKE :adresse";
        $params[':adresse'] = "%" . $adresse_filter . "%";
    }

    // Filtre par expérience
    if (!empty($experience_filter)) {
        $where_conditions[] = "p.experience LIKE :experience";
        $params[':experience'] = "%" . $experience_filter . "%";
    }

    // Filtre par niveau
    if (!empty($niveau_filter)) {
        $where_conditions[] = "p.niveau_etudes LIKE :niveau";
        $params[':niveau'] = "%" . $niveau_filter . "%";
    }

    $where_clause = implode(" AND ", $where_conditions);

    $stmt = $conn->prepare("
        SELECT DISTINCT u.id, u.prenom, u.nom, u.email, u.adresse,
               p.metier, p.competences, p.niveau_etudes, p.experience, p.domain
        FROM users u 
        LEFT JOIN candidat p ON u.id = p.user_id
        WHERE $where_clause
        ORDER BY u.prenom, u.nom
    ");

    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }

    $stmt->execute();
    $search_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$sql = "SELECT c.*, u.email 
        FROM candidat c
        JOIN users u ON c.user_id = u.id";
$stmt = $conn->prepare($sql);
$stmt->execute();
$candidats = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Si la requête échoue, $candidats sera un tableau vide
if ($candidats === false) {
    $candidats = [];
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Espace Recruteur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../Gestion_candidats/candidats.css">
    <link rel="stylesheet" href="recruteur_css.css">


</head>

<body>

    <div class="overlay">
        <header>
            <h1><i class="fa-solid fa-user-tie"></i> Espace Recruteur</h1>
            <div class="button-container">
                <a href="../index.php" class="btn btn-logout">Accueil</a>
                <a href="../logout.php" class="btn btn-logout">Déconnexion</a>
            </div>
        </header><br>

        <div class="welcome-section">
            <div class="welcome-content">
                <h2>Bienvenue, <?= htmlspecialchars($prenom_recruteur) . " " . htmlspecialchars($nom_recruteur); ?> ! 👋</h2>
                <p>Accédez à vos outils pour gérer vos offres et consulter les candidatures.</p>

                <div class="action-buttons">
                    <a href="modifier_profil.php" class="welcome-btn btn-primary-welcome">
                        <i class="fas fa-user-edit"></i>
                        Modifier le profil
                    </a>
                    <a href="profil_recruteur.php" class="welcome-btn btn-primary-welcome">
                        <i class="fas fa-plus-circle"></i>
                        Compléter profil
                    </a>
                    <a href="voir_candidatures.php" class="welcome-btn btn-primary-welcome">
                        <i class="fas fa-plus-circle"></i>
                        voir candidatures
                    </a>
                    <a href="../Gestion_offres/dashboard_offres.php" class="welcome-btn btn-primary-welcome">
                        <i class="fas fa-plus-circle"></i>
                        Gestion des offres
                    </a>
                </div>
            </div>
        </div>

        <!-- Barre de recherche moderne repositionnée -->
        <div class="container-fluid">
            <div class="search-container">
                <!-- Barre de recherche principale -->
                <form method="GET" id="searchForm">
                    <div class="search-row">
                        <!-- Champ de recherche global avec icône -->
                        <div class="search-input-container">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text"
                                name="search"
                                class="search-input"
                                placeholder="Rechercher par compétences, métier, domaine, adresse ou niveau d'études..."
                                value="<?= htmlspecialchars($search_term) ?>">
                        </div>

                        <!-- Bouton de recherche -->
                        <button type="submit" class="search-btn">
                            <i class="fas fa-search"></i>
                            Rechercher
                        </button>
                    </div>

                    <!-- Filtres spécifiques -->
                    <div class="search-row-secondary">
                        <!-- Filtre métier -->
                        <input type="text"
                            name="metier"
                            class="filter-select"
                            placeholder="Métier spécifique"
                            value="<?= htmlspecialchars($metier_filter) ?>"
                            style="border-radius: 25px; min-width: 150px;">

                        <!-- Filtre adresse -->
                        <input type="text"
                            name="adresse"
                            class="filter-select"
                            placeholder="Ville ou région"
                            value="<?= htmlspecialchars($adresse_filter) ?>"
                            style="border-radius: 25px; min-width: 150px;">

                        <!-- Filtre expérience -->
                        <select name="experience" class="filter-select">
                            <option value="">Toute expérience</option>
                            <option value="junior" <?= $experience_filter === 'junior' ? 'selected' : '' ?>>Junior (0-2 ans)</option>
                            <option value="intermediaire" <?= $experience_filter === 'intermediaire' ? 'selected' : '' ?>>Intermédiaire (2-5 ans)</option>
                            <option value="senior" <?= $experience_filter === 'senior' ? 'selected' : '' ?>>Senior (5+ ans)</option>
                            <option value="expert" <?= $experience_filter === 'expert' ? 'selected' : '' ?>>Expert (10+ ans)</option>
                        </select>

                        <!-- Filtre niveau d'études -->
                        <select name="niveau" class="filter-select">
                            <option value="">Tous les niveaux</option>
                            <option value="bac" <?= $niveau_filter === 'bac' ? 'selected' : '' ?>>Bac</option>
                            <option value="bac+2" <?= $niveau_filter === 'bac+2' ? 'selected' : '' ?>>Bac+2</option>
                            <option value="bac+3" <?= $niveau_filter === 'bac+3' ? 'selected' : '' ?>>Bac+3</option>
                            <option value="bac+5" <?= $niveau_filter === 'bac+5' ? 'selected' : '' ?>>Bac+5</option>
                            <option value="master" <?= $niveau_filter === 'master' ? 'selected' : '' ?>>Master</option>
                            <option value="doctorat" <?= $niveau_filter === 'doctorat' ? 'selected' : '' ?>>Doctorat</option>
                        </select>
                    </div>
                </form>

                <!-- Résumé des résultats -->
                <?php if (!empty($search_term) || !empty($experience_filter) || !empty($niveau_filter) || !empty($metier_filter) || !empty($adresse_filter)): ?>
                    <div class="results-summary">
                        <p class="results-text">
                            <i class="fas fa-check-circle"></i>
                            <span class="results-count"><?= count($search_results) ?> candidats trouvés</span>
                        </p>
                        <a href="<?= $_SERVER['PHP_SELF'] ?>" class="reinitialiser-btn">
                            <i class="fas fa-times"></i>
                            Réinitialiser
                        </a>
                    </div>

                    <!-- Résultats de recherche -->
                    <div class="results-section mt-4">
                        <?php if (empty($search_results)): ?>
                            <div class="no-results">
                                <div class="no-results-icon">
                                    <i class="fas fa-search"></i>
                                </div>
                                <h5>Aucun candidat trouvé</h5>
                                <p>Essayez avec d'autres critères de recherche</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($search_results as $candidat): ?>
                                <div class="result-card">
                                    <div class="candidate-name">
                                        <i class="fas fa-user"></i> <?= htmlspecialchars($candidat['prenom'] . ' ' . $candidat['nom']) ?>
                                    </div>
                                    <div class="candidate-info">
                                        <div class="info-item">
                                            <span class="info-label">Métier:</span> <?= htmlspecialchars($candidat['metier'] ?? 'Non spécifié') ?>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Domaine:</span> <?= htmlspecialchars($candidat['domain'] ?? 'Non spécifié') ?>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Adresse:</span> <?= htmlspecialchars($candidat['adresse'] ?? 'Non spécifiée') ?>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Expérience:</span> <?= htmlspecialchars($candidat['experience'] ?? 'Non spécifiée') ?>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Niveau:</span> <?= htmlspecialchars($candidat['niveau_etudes'] ?? 'Non spécifié') ?>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Compétences:</span> <?= htmlspecialchars($candidat['competences'] ?? 'Non spécifiées') ?>
                                        </div>
                                    </div>
                                    <div style="display: flex; justify-content: flex-end; width: 100%; margin-top: 30px;">
                                        <a href="contacter.php?to=<?= urlencode($candidat['email']) ?>" class="btn btn-primary rounded-pill">
                                            <i class="fas fa-envelope"></i> Contacter
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