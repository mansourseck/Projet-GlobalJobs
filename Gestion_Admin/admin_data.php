<?php
session_start();
require "../db.php";

// if (!isset($_SESSION["admin_id"])) {
//     header("Location: admin_login.php");
//     exit();
// }

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Récupération des statistiques
$usersCount = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
$jobsCount = $conn->query("SELECT COUNT(*) FROM offres")->fetchColumn();
$applicationsCount = $conn->query("SELECT COUNT(*) FROM candidature")->fetchColumn();

// Récupération des utilisateurs avec pagination
$stmt_users = $conn->prepare("SELECT id, nom, email, statut FROM users LIMIT $limit OFFSET $offset");
$stmt_users->execute();
$users = $stmt_users->fetchAll();

// Récupération des offres avec jointure sur `recruteur`
$stmt_jobs = $conn->prepare("
    SELECT o.id, o.titre, r.entreprise AS entreprise, o.lieu, o.secteur, o.date_postee 
    FROM offres o 
    INNER JOIN recruteurs r ON o.recruteur_id = r.id
    LIMIT $limit OFFSET $offset
");
$stmt_jobs->execute();
$jobs = $stmt_jobs->fetchAll();

// Récupération des candidatures
$stmt_apps = $conn->prepare("
    SELECT c.id, u.nom AS candidat, o.titre, c.statut, c.date_postulation 
    FROM candidature c
    INNER JOIN users u ON c.candidat_id = u.id
    INNER JOIN offres o ON c.offre_id = o.id
");
$stmt_apps->execute();
$applications = $stmt_apps->fetchAll(PDO::FETCH_ASSOC);
?>