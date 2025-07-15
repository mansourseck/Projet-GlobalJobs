<?php
require_once __DIR__ . '/vendor/autoload.php'; // adapte le chemin si besoin
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Envoie un email avec PHPMailer
 * @param string $toEmail      L'email du destinataire
 * @param string $toName       Le nom du destinataire
 * @param string $subject      Sujet du mail
 * @param string $body         Corps du mail (HTML autorisé)
 * @param string $altBody      Version texte brut (optionnel)
 * @return bool|string         true si OK, sinon message d'erreur
 */
function sendMail($toEmail, $toName, $subject, $body, $altBody = '') {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'mohamedseck163@gmail.com'; // <-- À adapter !
        $mail->Password   = 'saxj xpfe ucfc kioa'; 
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64'; 
        $mail->Timeout = 5;
        $mail->SMTPKeepAlive = true;
        //$mail->SMTPDebug = 2; // Pour voir les logs en cas de souci
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('mohamedseck163@gmail.com', 'GlobalJobs'); // À personnaliser
        $mail->addAddress($toEmail, $toName);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = $altBody ?: strip_tags($body);

        $mail->send();
        return true;
    } catch (Exception $e) {
        return $mail->ErrorInfo;
    }
}