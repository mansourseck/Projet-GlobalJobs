<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // ou le chemin adapté vers PHPMailer

function envoyerMailCandidat($destinataire, $nomComplet, $titreOffre, $statut) {
    $messages = [
        "Accepté" => "🎉 Félicitations $nomComplet ! Votre candidature pour le poste de <strong>$titreOffre</strong> a été acceptée. Nous vous contacterons prochainement.",
        "Refusé" => "Bonjour $nomComplet, nous vous remercions pour votre candidature au poste de <strong>$titreOffre</strong>. Après étude, nous ne pourrons pas donner suite cette fois-ci.",
        "En attente" => "Bonjour $nomComplet, votre candidature au poste de <strong>$titreOffre</strong> est toujours en cours d'examen. Merci de votre patience."
    ];

    $message = $messages[$statut] ?? "Votre candidature a été mise à jour.";

    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.exemple.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'mohamedseck163@gmail.com';
        $mail->Password   = 'saxj xpfe ucfc kioa';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;
        $mail->setFrom('mohamedseck163@gmail.com', 'GlobalJobs');
        $mail->addAddress($destinataire, $nomComplet);

        $mail->isHTML(true);
        $mail->Subject = "📝 Mise à jour de votre candidature - $titreOffre";
        $mail->Body    = $message;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Erreur PHPMailer : " . $mail->ErrorInfo);
        return false;
    }
}