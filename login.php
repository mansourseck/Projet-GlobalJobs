<?php
session_start();
include 'db.php';

$message = "";

// Vérification des champs envoyés par le formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"] ?? '');
    $password = $_POST["password"] ?? '';

    if (!$email || !$password) {
        $message = "Veuillez remplir tous les champs.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Format d'email invalide.";
    } else {
        // Vérification de l'utilisateur dans la base de données
        $stmt = $conn->prepare("SELECT id, password, role, statut FROM users WHERE email = :email");
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if ($user['statut'] === 'bloquer') {
                $message = "Votre compte a été bloqué par l'administrateur. Connexion impossible.";
            } elseif (password_verify($password, $user["password"])) {
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["role"] = $user["role"];

                $table = (strcasecmp($user["role"], "candidat") === 0) ? "candidat" : "recruteurs";
                $stmt_check = $conn->prepare("SELECT * FROM $table WHERE user_id = :user_id");
                $stmt_check->bindParam(":user_id", $user["id"], PDO::PARAM_INT);
                $stmt_check->execute();
                $existing = $stmt_check->fetch(PDO::FETCH_ASSOC);

                header("Location: " . ($table === "candidat" ? "./Gestion_candidats/candidats.php" : "./Gestion_recruteur/recruteur"));
                exit();
            } else {
                $message = "Email ou mot de passe incorrect.";
            }
        } else {
            $message = "Email ou mot de passe incorrect.";
        }
    }
    $_SESSION["message"] = $message;
    header("Location: loginh.php");
    exit();
} 
?>