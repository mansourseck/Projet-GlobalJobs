<?php
session_start();
require '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['message'] = "❌ Vous devez être connecté.";
    } else {
        $candidature_id = $_POST['candidature_id'];
        $nouveau_statut = $_POST['statut'];

        // Vérifier que le statut est valide
        $statuts_valides = ["En attente", "Accepté", "Refusé"];
        if (!in_array($nouveau_statut, $statuts_valides)) {
            $_SESSION['message'] = "⚠ Statut invalide.";
        } else {
            // Mettre à jour le statut
            $stmt = $conn->prepare("UPDATE Candidature SET statut = ? WHERE id = ?");
            $stmt->execute([$nouveau_statut, $candidature_id]);

            $_SESSION['message'] = "✅ Statut mis à jour avec succès.";
        }
    }
}
header("Location: communiquer_candidats.php");
exit();