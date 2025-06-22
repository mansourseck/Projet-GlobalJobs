<?php
session_start();
require '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entreprise = trim($_POST["entreprise"]);
    $email_entreprise = trim($_POST["email_entreprise"]); // Email professionnel de l'entreprise
    $secteur = trim($_POST["secteur"]);
    $adresse = trim($_POST["adresse"]);
    
    // Vérification des champs obligatoires
    if (empty($entreprise) || empty($email_entreprise) || empty($secteur) || empty($adresse)) {
        $_SESSION['message'] = "❌ Tous les champs doivent être remplis.";
        exit();
    }

    // Vérification du format de l'email entreprise
    if (!filter_var($email_entreprise, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = "❌ Email de l'entreprise invalide.";
        exit();
    }

    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['message'] = "❌ Vous devez être connecté en tant que recruteur.";
        exit();
    }

    $user_id = $_SESSION['user_id']; // ID utilisateur connecté

    // Vérifier si cet email d’entreprise est déjà utilisé
    $stmt = $conn->prepare("SELECT id FROM Recruteurs WHERE email_entreprise = ?");
    $stmt->execute([$email_entreprise]);
    if ($stmt->fetch()) {
        $_SESSION['message'] = "❌ Cet email entreprise est déjà utilisé.";
        exit();
    }

    // Insérer l’entreprise dans `Recruteurs`
    $stmt = $conn->prepare("INSERT INTO Recruteurs (user_id, entreprise, email_entreprise, secteur, adresse) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $entreprise, $email_entreprise, $secteur, $adresse]);

    $_SESSION['message'] = "✅ Entreprise enregistrée avec succès !";
    header("location: profil_recruteur.php");
    
}
?>