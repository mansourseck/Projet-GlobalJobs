<?php
session_start();
require '../db.php';
require_once '../mailer_config.php'; // Fonction sendMail()

if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "❌ Vous devez être connecté.";
    header("Location: ../login.php");
    exit();
}

// 1. Récupérer le vrai id du candidat depuis la table candidat !
$stmt = $conn->prepare("SELECT id FROM candidat WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$candidat_infos = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$candidat_infos) {
    $_SESSION['message'] = "❌ Erreur : votre profil candidat est introuvable.";
    header("Location: postuler.php");
    exit();
}

$candidat_id = $candidat_infos['id'];
$offre_id = $_GET['id'];

// Vérifier si la candidature existe déjà
$check_stmt = $conn->prepare("SELECT id FROM Candidature WHERE candidat_id = ? AND offre_id = ?");
$check_stmt->execute([$candidat_id, $offre_id]);
$candidature_existante = $check_stmt->fetch();

if ($candidature_existante) {
    // Mise à jour (exemple : la date de postulation)
    $stmt = $conn->prepare("UPDATE Candidature SET date_postulation = NOW() WHERE id = ?");
    $stmt->execute([$candidature_existante['id']]);
    $_SESSION['message'] = "✅ Votre candidature a bien été actualisée.";
    $type_candidature = "actualisée";
} else {
    // Nouvelle candidature
    $stmt = $conn->prepare("INSERT INTO Candidature (candidat_id, offre_id, date_postulation) VALUES (?, ?, NOW())");
    $stmt->execute([$candidat_id, $offre_id]);
    $_SESSION['message'] = "✅ Votre candidature a été envoyée avec succès.";
    $type_candidature = "envoyée";
}

// --- Notifier le recruteur par mail ---
$sql = "SELECT u.email, u.nom, u.prenom, o.titre
        FROM offres o
        INNER JOIN recruteurs r ON o.recruteur_id = r.id
        INNER JOIN users u ON r.user_id = u.id
        WHERE o.id = ?";
$stmt_info = $conn->prepare($sql);
$stmt_info->execute([$offre_id]);
$recruteur = $stmt_info->fetch(PDO::FETCH_ASSOC);

// Récupérer le nom et l'email du candidat
$stmt_candidat = $conn->prepare("SELECT nom, prenom, email FROM users WHERE id = ?");
$stmt_candidat->execute([$_SESSION['user_id']]);
$candidat = $stmt_candidat->fetch(PDO::FETCH_ASSOC);

if ($recruteur && $candidat) {
    // Mail au recruteur
    $subject_recruteur = "Nouvelle candidature reçue pour votre offre \"{$recruteur['titre']}\"";
    $body_recruteur = "Bonjour <b>{$recruteur['prenom']} {$recruteur['nom']}</b>,<br>
        Le(la) candidat(e) <b>{$candidat['prenom']} {$candidat['nom']}</b> vient de postuler à votre offre <b>{$recruteur['titre']}</b>.<br>
        Merci de consulter votre tableau de bord pour voir la candidature.<br><br>
        - L'équipe GlobalJobs";
    sendMail($recruteur['email'], $recruteur['prenom'].' '.$recruteur['nom'], $subject_recruteur, $body_recruteur);

    // Mail au candidat
    $subject_candidat = "Confirmation : votre candidature a été $type_candidature";
    $body_candidat = "Bonjour <b>{$candidat['prenom']} {$candidat['nom']}</b>,<br>
        Votre candidature à l'offre <b>{$recruteur['titre']}</b> a bien été $type_candidature.<br>
        Vous serez notifié(e) en cas de réponse du recruteur.<br><br>
        - L'équipe GlobalJobs";
    sendMail($candidat['email'], $candidat['prenom'].' '.$candidat['nom'], $subject_candidat, $body_candidat);
}

// Retour à la liste des offres
header("Location: postuler.php");
exit();
?>