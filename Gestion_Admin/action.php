<?php
require_once '../db.php';
session_start();

if (!isset($_GET['id']) || !isset($_GET['action'])) {
    header('Location: admin_dashboard.php?error=missing_params');
    exit();
}

$user_id = intval($_GET['id']);
$action = $_GET['action'];

// On adapte le statut selon l'action
if ($action === 'block') {
    $new_statut = 'bloquer';
} elseif ($action === 'unblock') {
    $new_statut = 'activer'; // Ou 'Candidat', adapte si besoin
} else {
    header('Location: admin_dashboard.php?error=invalid_action');
    exit();
}

// Mise à jour du statut
$stmt = $conn->prepare("UPDATE users SET statut = ? WHERE id = ?");
if ($stmt->execute([$new_statut, $user_id])) {
    header('Location: admin_dashboard.php?success=statut_updated');
    exit();
} else {
    header('Location: admin_dashboard.php?error=update_failed');
    exit();
}
?>