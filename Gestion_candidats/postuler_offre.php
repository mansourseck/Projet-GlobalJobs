<?php
session_start();
require '../db.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "❌ Vous devez être connecté.";
    header("Location: ../login.php");
    exit();
}

$candidat_id = $_SESSION['user_id'];
$offre_id = $_GET['id'];

// Vérifier si le candidat a déjà postulé
$check_stmt = $conn->prepare("SELECT id FROM Candidature WHERE candidat_id = ? AND offre_id = ?");
$check_stmt->execute([$candidat_id, $offre_id]);
$candidature_existante = $check_stmt->fetch();

if ($candidature_existante) {
    $_SESSION['message'] = "⚠ Vous avez déjà postulé à cette offre.";
} else {
    // Enregistrer la candidature
    $stmt = $conn->prepare("INSERT INTO Candidature (candidat_id, offre_id) VALUES (?, ?)");
    $stmt->execute([$candidat_id, $offre_id]);
    $_SESSION['message'] = "✅ Votre candidature a été envoyée avec succès.";
}

// Retourner à la page des offres
header("Location: postuler.php");
exit();