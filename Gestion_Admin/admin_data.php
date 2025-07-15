<?php
session_start();
require "../db.php";
require_once "../mailer_config.php"; // INCLUS UNE SEULE FOIS

// if (!isset($_SESSION["admin_id"])) {
//     header("Location: admin_login.php");
//     exit();
// }

// --------- GESTION DU STATUT D'UNE OFFRE ----------
if (
    isset($_GET['update_id']) && 
    isset($_GET['new_status']) && 
    in_array($_GET['new_status'], ['Publier', 'Rejété'])
) {
    $update_id = (int)$_GET['update_id'];
    $new_status = $_GET['new_status'];

    if ($new_status === 'Publier') {
        // Validation immédiate
        $stmt_update = $conn->prepare("UPDATE offres SET statut = :statut WHERE id = :id");
        $stmt_update->execute([
            ':statut' => $new_status,
            ':id' => $update_id
        ]);

        // ENVOI EMAIL AU RECRUTEUR
        $stmt_info = $conn->prepare("
            SELECT o.titre, u.email, u.nom, u.prenom
            FROM offres o
            INNER JOIN recruteurs r ON o.recruteur_id = r.id
            INNER JOIN users u ON r.user_id = u.id
            WHERE o.id = ?
        ");
        $stmt_info->execute([$update_id]);
        $offre = $stmt_info->fetch(PDO::FETCH_ASSOC);

        if ($offre) {
            $subject = "Votre offre a été publiée !";
            $body = "Bonjour <b>{$offre['nom']} {$offre['prenom']}</b>,<br>
                Votre offre intitulée <b>{$offre['titre']}</b> a été <span style='color:green'>publiée</span> sur GlobalJobs.<br>
                Merci pour votre confiance.<br><br>- L'équipe GlobalJobs";
            // Appel de la fonction réutilisable :
            sendMail($offre['email'], $offre['nom'].' '.$offre['prenom'], $subject, $body);
        }

        header("Location: admin_dashboard.php?page=" . (isset($_GET['page']) ? (int)$_GET['page'] : 1));
        exit();
    } else {
        // Redirection vers la page de saisie de motif de rejet
        header("Location: rejet_cause.php?id=" . $update_id);
        exit();
    }
}
// ---------------------------------------------------

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Statistiques globales
$usersCount = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
$jobsCount = $conn->query("SELECT COUNT(*) FROM offres")->fetchColumn();
$applicationsCount = $conn->query("SELECT COUNT(*) FROM candidature")->fetchColumn();

// Utilisateurs avec pagination
$stmt_users = $conn->prepare("SELECT id, nom, prenom, telephone, adresse, email, role, statut FROM users LIMIT $limit OFFSET $offset");
$stmt_users->execute();
$users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);

// Offres avec jointure sur `recruteur`
$stmt_jobs = $conn->prepare("
    SELECT 
        o.id, 
        o.titre, 
        r.entreprise AS entreprise, 
        o.lieu, 
        o.type_contrat, 
        o.domain, 
        o.date_postee, 
        o.date_expire,
        o.statut,
        u.email,
        u.nom,
        u.prenom
    FROM offres o
    INNER JOIN recruteurs r ON o.recruteur_id = r.id
    INNER JOIN users u ON r.user_id = u.id
    LIMIT $limit OFFSET $offset
");
$stmt_jobs->execute();
$jobs = $stmt_jobs->fetchAll(PDO::FETCH_ASSOC);

// Candidatures
$stmt_apps = $conn->prepare("
    SELECT c.id, u.nom AS candidat, o.titre, c.statut, c.date_postulation 
    FROM candidature c
    INNER JOIN users u ON c.candidat_id = u.id
    INNER JOIN offres o ON c.offre_id = o.id
");
$stmt_apps->execute();
$applications = $stmt_apps->fetchAll(PDO::FETCH_ASSOC);

// ------- Statistiques détaillées par état -------

// Utilisateurs
$usersTotal = count($users);
$usersActifs = count(array_filter($users, fn($u) => $u['statut'] !== 'bloquer'));
$usersBloques = count(array_filter($users, fn($u) => $u['statut'] === 'bloquer'));

// Offres
$jobsTotal = count($jobs);
$jobsPublier = count(array_filter($jobs, fn($j) => $j['statut'] === 'Publier'));
$jobsAttente = count(array_filter($jobs, fn($j) => $j['statut'] === 'En attente'));
$jobsRejete = count(array_filter($jobs, fn($j) => $j['statut'] === 'Rejété'));

// Candidatures
$applicationsTotal = count($applications);
$applicationsAcceptees = count(array_filter($applications, fn($a) => strtolower($a['statut']) === 'accepté'));
$applicationsAttente = count(array_filter($applications, fn($a) => strtolower($a['statut']) === 'en attente'));
$applicationsRefusees = count(array_filter($applications, fn($a) => strtolower($a['statut']) === 'refusé'));
?>