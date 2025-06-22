<?php
session_start();
require_once __DIR__ . 'db.php';
require_once __DIR__ . 'mailer_config.php'; // Fichier de config PHPMailer

// Vérifier que le recruteur est connecté
if (!isset($_SESSION['users']) || $_SESSION['status'] !== 'recruteurs') {
    header("Location: loginh.php");
    exit();
}

// Vérification des paramètres POST
if (
    !isset($_POST['id']) ||
    !is_numeric($_POST['id']) ||
    !isset($_POST['statut']) ||
    !in_array($_POST['statut'], ['accepter', 'refuser'])
) {
    header("Location: ../Gestion_recruteur/communiquer_candidats.php?message=erreur");
    exit();
}

$id = intval($_POST['id']);
$nouveau_statut = $_POST['status'] === 'accepter' ? 'Accepté' : 'Refusé';

// Récupérer infos pour le message et l'e-mail
$stmt = $bdd->prepare("
    SELECT c.candidat_id, o.titre, ca.email, ca.prenom, ca.nom
    FROM candidature c
    JOIN offres o ON c.offre_id = o.id
    JOIN candidat ca ON c.andidat_id = ca.id
    WHERE c.id = ?
");
$stmt->execute([$id]);
$info = $stmt->fetch();

if (!$info) {
    header("Location: ../Gestion_recruteur/communiquer_candidats.php?message=candidature_introuvable");
    exit();
}

$id_candidat = $info['candidat_id'];
$titre_offre = $info['titre'];
$email_candidat = $info['email'];
$prenom_candidat = $info['prenom'] ?? '';
$nom_candidat = $info['nom'] ?? '';
$nom_complet = trim($prenom_candidat . " " . $nom_candidat);

// Préparer le message (interne et email)
if ($nouveau_statut === 'Accepté') {
    $contenu = "Félicitations, votre candidature à l'offre " . htmlspecialchars($titre_offre) . " a été acceptée.";
    $sujet_email = "Votre candidature a été acceptée !";
    $body_email = "<p>Bonjour $nom_complet,</p>
    <p>Félicitations, votre candidature à l’offre <strong>$titre_offre</strong> a été <b>acceptée</b> !<br>
    Nous vous contacterons prochainement pour la suite du processus.</p>
    <p>Cordialement,<br>L’équipe Recrutement IKBARA</p>";
} else {
    $contenu = "Nous sommes désolés, votre candidature à l'offre " . htmlspecialchars($titre_offre) . " a été refusée.";
    $sujet_email = "Votre candidature n'a pas été retenue";
    $body_email = "<p>Bonjour $nom_complet,</p>
    <p>Nous sommes désolés, votre candidature à l’offre <strong>$titre_offre</strong> n’a pas été retenue.<br>
    Nous vous souhaitons une bonne continuation.</p>
    <p>Cordialement,<br>L’équipe Recrutement IKBARA</p>";
}

// Insérer le message dans la table messages (messagerie interne)
$stmt_msg = $bdd->prepare("
    INSERT INTO messages (id_expediteur, id_destinataire, contenu, date_envoi)
    VALUES (?, ?, ?, NOW())
");
$stmt_msg->execute([
    $_SESSION['users']['id'], // id du recruteur
    $id_candidat,
    $contenu
]);

// Envoyer l'email externe au candidat (PHPMailer)
try {
    $mail = getMailer();
    $mail->addAddress($email_candidat, $nom_complet);
    $mail->Subject = $sujet_email;
    $mail->Body    = $body_email;
    $mail->AltBody = strip_tags(str_replace("<br>", "\n", $body_email));
    $mail->send();
} catch (Exception $e) {
    // Log ou alerte admin possible ici si besoin
    // error_log("Erreur PHPMailer : " . $mail->ErrorInfo);
}

// Mettre à jour le statut de la candidature
$stmt = $bdd->prepare("UPDATE candidatures SET statut = ? WHERE id = ?");
$stmt->execute([$nouveau_statut, $id]);

header("Location: ../Gestion_recruteur/communiquer_candidats.php?message=statut_maj");
exit();
?>