<?php
session_start();
require '../vendor/autoload.php'; // Assure-toi que le chemin est bon
require '../db.php'; // Ton fichier de connexion PDO

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Récupération de l'email expéditeur depuis la session ou la base
$from = '';
if (!empty($_SESSION['user_email'])) {
    $from = $_SESSION['user_email'];
} elseif (!empty($_SESSION['user_id'])) {
    // Récupère l'email depuis la table users
    $stmt = $conn->prepare('SELECT email FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $from = $stmt->fetchColumn();
    if ($from) {
        $_SESSION['user_email'] = $from; // Garde-le en session pour les prochaines fois
    }
}

// Redirige vers la connexion si pas d'utilisateur
if (empty($from)) {
    header('Location: ../login.php');
    exit;
}

$to = isset($_GET['to']) ? $_GET['to'] : '';
$success = false;
$error = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $to = $_POST['to'];
    $from = $_POST['from'];
    $message = $_POST['message'];

    $mail = new PHPMailer(true);

    try {
        // Config SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username   = 'mohamedseck163@gmail.com'; // <-- À adapter !
        $mail->Password   = 'saxj xpfe ucfc kioa'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom($from);
        $mail->addAddress($to);
        $mail->Subject = "Contact depuis GlobalJobs";
        $mail->Body = $message;

        $mail->send();
        $success = true;
    } catch (Exception $e) {
        $error = $mail->ErrorInfo;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Contacter le candidat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .contact-form { max-width: 480px; margin: 40px auto; padding:32px; background:#fff; border-radius:12px; box-shadow:0 2px 16px #0002; }
        .form-label { font-weight:bold; }
    </style>
</head>
<body>
    <div class="contact-form">
        <h4><i class="fas fa-envelope"></i> Contacter le candidat</h4>
        <?php if ($success): ?>
            <div class="alert alert-success">Votre message a été envoyé avec succès !</div>
        <?php elseif ($error): ?>
            <div class="alert alert-danger">Erreur lors de l'envoi : <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if (!$success): ?>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Email du destinataire</label>
                <input type="email" class="form-control" name="to" value="<?= htmlspecialchars($to) ?>" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">Votre email (expéditeur)</label>
                <input type="email" class="form-control" name="from" value="<?= htmlspecialchars($from) ?>" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">Message</label>
                <textarea class="form-control" name="message" rows="5" required placeholder="Votre message..."></textarea>
            </div>
            <button class="btn btn-primary w-100"><i class="fas fa-paper-plane"></i> Envoyer</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>