<?php
require '/wamp64/www/projet_php_GlobalJobs/db.php';
session_start();    
$titre = $_POST["titre"];
$description = $_POST["description"];
$lieu = $_POST["lieu"];
$secteur = $_POST["secteur"];
$user_id = $_SESSION["user_id"]; // ID de la table users

// ✅ Récupérer l'ID du recruteur depuis la table recruteurs
$sql_recruteur = "SELECT id FROM recruteurs WHERE user_id = ?";
$stmt_recruteur = $conn->prepare($sql_recruteur);
$stmt_recruteur->execute([$user_id]);
$recruteur = $stmt_recruteur->fetch();

if ($recruteur) {
    $recruteur_id = $recruteur['id']; // ✅ Maintenant c'est le bon ID
    
    $sql = "INSERT INTO offres (recruteur_id, titre, description, lieu, secteur) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$recruteur_id, $titre, $description, $lieu, $secteur]);
} else {
    echo "Erreur : Recruteur non trouvé";
}

header("Location: dashboard_offres.php"); // Redirection vers le tableau de bord
exit();
?>