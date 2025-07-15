<?php
session_start();
require '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entreprise = trim($_POST["entreprise"]);
    $secteur = trim($_POST["secteur"]);
    $adresse_entreprise = trim($_POST["adresse_entreprise"]);
    
    // Vérification des champs obligatoires
    if (empty($entreprise) || empty($secteur) || empty($adresse_entreprise)) {
        $_SESSION['message'] = "❌ Tous les champs doivent être remplis.";
        exit();
    }

    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['message'] = "❌ Vous devez être connecté en tant que recruteur.";
        exit();
    }

    $user_id = $_SESSION['user_id']; // ID utilisateur connecté

    // Vérifier si cet d’entreprise est déjà utilisé
    $stmt = $conn->prepare("SELECT id FROM Recruteurs WHERE entreprise = ?");
    $stmt->execute([$entreprise]);
    if ($stmt->fetch()) {
        $_SESSION['message'] = "❌ Cet entreprise est déjà utilisé.";
        exit();
    }

    // Insérer l’entreprise dans `Recruteurs`
    $stmt = $conn->prepare("INSERT INTO Recruteurs (user_id, entreprise,secteur, adresse_entreprise) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $entreprise, $secteur, $adresse_entreprise]);

    $_SESSION['message'] = "✅ Entreprise enregistrée avec succès !";
    header("location: profil_recruteur.php");
    
}
?>