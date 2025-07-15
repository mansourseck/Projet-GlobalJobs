<?php
require '/wamp64/www/projet_php_GlobalJobs/db.php';
require_once '/wamp64/www/projet_php_GlobalJobs/mailer_config.php';
session_start();  

// Récupération des données du formulaire
$titre        = $_POST["titre"];
$description  = $_POST["description"];
$lieu         = $_POST["lieu"];
$domain       = $_POST["domain"];
$date_expire  = $_POST["date_expire"];
$type_contrat = $_POST["type_contrat"];
$user_id      = $_SESSION["user_id"]; // ID de la table users

// Récupérer l'ID du recruteur
$sql_recruteur = "SELECT id, entreprise FROM recruteurs WHERE user_id = ?";
$stmt_recruteur = $conn->prepare($sql_recruteur);
$stmt_recruteur->execute([$user_id]);
$recruteur = $stmt_recruteur->fetch();

if ($recruteur) {
    $recruteur_id = $recruteur['id'];
    $entreprise   = $recruteur['entreprise'];

    // Récupérer le nom du recruteur
    $sql_user = "SELECT nom, prenom FROM users WHERE id = ?";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->execute([$user_id]);
    $user = $stmt_user->fetch();

    $nom_recruteur = $user['nom'] . ' ' . $user['prenom'];

    // Insertion de l'offre dans la base
    $sql = "INSERT INTO offres (recruteur_id, titre, description, lieu, domain, date_expire, type_contrat)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$recruteur_id, $titre, $description, $lieu, $domain, $date_expire, $type_contrat]);

    // Envoi de mail à l'admin (appel de la fonction factorisée)
    $subject = "Nouvelle offre a valider : $titre";
    $body = "
        Bonjour,<br><br>
        <b>$nom_recruteur</b> de l'entreprise <b>$entreprise</b> vient de publier une nouvelle offre : <b>$titre</b>.<br>
        Merci de consulter votre tableau de bord pour valider ou consulter les détails.<br><br>
        - GlobalJobs
    ";
    // Appelle la fonction (tu peux changer l'adresse destinataire si besoin)
    sendMail('perso6199@gmail.com', 'Admin', $subject, $body);

    header("Location: dashboard_offres.php");
    exit();
} else {
    echo "Erreur : Recruteur non trouvé";
    exit();
}
?>