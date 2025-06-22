<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_SESSION['nom'] = trim($_POST['nom']);
    $_SESSION['prenom'] = trim($_POST['prenom']);
    $_SESSION['statut'] = trim($_POST['statut']);
    $_SESSION['telephone'] = trim($_POST['telephone']); // Ajout du champ téléphone
    $_SESSION['message'] = ""; // Initialiser le message
    
    $email = trim($_POST['email']);
    $password = trim($_POST['password']); // NE PAS stocker en session pour la sécurité

    if (empty($_SESSION['nom']) || empty($_SESSION['prenom']) || empty($email) || empty($password) || empty($_SESSION['statut']) || empty($_SESSION['telephone'])) {
        $_SESSION['message'] = "<p class='text-danger'>Tous les champs sont obligatoires. Veuillez les remplir.</p>";
        header("Location: register.php");
        exit();
    } else {
        // Vérifier si l'email existe déjà
        $sql_check = "SELECT COUNT(*) FROM users WHERE email = :email";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bindParam(':email', $email);
        $stmt_check->execute();
        $count = $stmt_check->fetchColumn();

        if ($count > 0) {
            $_SESSION['message'] = "<p class='text-danger'>Cet email est déjà utilisé. Veuillez en choisir un autre.</p>";
            $_SESSION['email'] = ""; // Effacer uniquement le champ email
            header("Location: register.php");
            exit();
        } else {
            try {
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                $sql = "INSERT INTO users (nom, prenom, email, password, statut, telephone) VALUES (:nom, :prenom, :email, :password, :statut, :telephone)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':nom', $_SESSION['nom']);
                $stmt->bindParam(':prenom', $_SESSION['prenom']);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $hashed_password);
                $stmt->bindParam(':statut', $_SESSION['statut']);
                $stmt->bindParam(':telephone', $_SESSION['telephone']); // Enregistrement du téléphone

                if ($stmt->execute()) {
                    $_SESSION['message'] = "<p class='text-success'>Inscription réussie !</p>";
                    // Effacer les données enregistrées après une inscription réussie
                    unset($_SESSION['nom']);
                    unset($_SESSION['prenom']);
                    unset($_SESSION['statut']);
                    unset($_SESSION['telephone']);

                    header("Location: register.php");
                    exit();
                } else {
                    $_SESSION['message'] = "<p class='text-danger'>Erreur lors de l'inscription.</p>";
                    header("Location: register.php");
                    exit();
                }
            } catch (PDOException $e) {
                $_SESSION['message'] = "<p class='text-danger'>Erreur : " . $e->getMessage() . "</p>";
                header("Location: register.php");
                exit();
            }
        }
    }
}
?>