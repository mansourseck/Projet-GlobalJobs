<?php require 'header.html'; ?>
<?php
require_once 'mailer_config.php'; // adapte le chemin si besoin

$msg = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom     = htmlspecialchars($_POST['nom']);
    $email   = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    $toEmail = 'mseck3999@gmail.com'; // à adapter !
    $toName  = 'Administrateur GlobalJobs';
    $subject = "Message depuis le formulaire de contact GlobalJobs";
    $body    = "<h2>Nouveau message de contact</h2>
                <p><strong>Nom :</strong> $nom</p>
                <p><strong>Email :</strong> $email</p>
                <p><strong>Message :</strong><br>$message</p>";

    $result = sendMail($toEmail, $toName, $subject, $body);

    if ($result === true) {
        $msg = "<div class='alert alert-success mt-3'>Merci $nom, votre message a bien été envoyé !</div>";
    } else {
        $msg = "<div class='alert alert-danger mt-3'>Erreur lors de l'envoi du message : $result</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/product/">
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <title>contact</title>
</head>
<body>
<div class="container py-5">
  <div class="row justify-content-center align-items-center">
    <!-- Formulaire de contact -->
    <div class="col-lg-6 mb-4">
      <div class="card border-0 shadow-lg">
        <div class="card-body p-5">
          <h2 class="mb-4 text-success fw-bold text-center">Contactez-nous</h2>
          <form method="post" action="">
            <div class="mb-3">
              <label for="nom" class="form-label">Nom</label>
              <input type="text" class="form-control" id="nom" name="nom" required>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Adresse email</label>
              <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
              <label for="message" class="form-label">Message</label>
              <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
            </div>
            <div class="d-grid">
              <button type="submit" class="btn btn-primary btn-lg">Envoyer</button>
            </div>
          </form>
          <?php if(!empty($msg)) echo $msg; ?>
        </div>
      </div>
    </div>
    <!-- Texte à droite -->
    <div class="col-lg-6 mb-4">
      <div class="p-4 ps-lg-5">
        <h3 class="fw-bold mb-3 text-primary">Notre équipe à votre écoute</h3>
        <p class="text-secondary mb-4">
          Notre équipe vous accompagne pour toute question, suggestion ou collaboration.<br>
          N'hésitez pas à nous écrire ou à nous contacter directement sur WhatsApp pour une réponse rapide.
        </p>
        <ul class="list-unstyled mb-4">
          <li class="mb-2"><strong><i class="bi bi-envelope-fill text-primary"></i> Email :</strong> admin@globals.com</li>
          <li class="mb-2"><strong><i class="bi bi-geo-alt-fill text-primary"></i> Adresse :</strong> Dakar, Sénégal</li>
          <li class="mb-2"><strong><i class="bi bi-whatsapp text-success"></i> WhatsApp :</strong> 
            <a href="https://wa.me/221772285452" target="_blank" class="text-success fw-bold" style="text-decoration: none;">
              +221 77 123 45 67
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-whatsapp" viewBox="0 0 16 16">
                <path d="M13.601 2.326A7.013 7.013 0 0 0 8.004 0C3.58 0 0 3.58 0 8c0 1.389.363 2.735 1.05 3.922L.017 16l4.175-1.092A7.002 7.002 0 0 0 8.004 16c4.423 0 8.004-3.58 8.004-8 0-1.796-.587-3.474-1.601-4.726Zm-5.597 12.02c-1.14 0-2.263-.298-3.237-.862l-.232-.138-2.477.648.663-2.414-.151-.247A6.032 6.032 0 0 1 1 8c0-3.86 3.14-7 7.004-7 1.872 0 3.63.728 4.951 2.05A6.968 6.968 0 0 1 15 8c0 3.86-3.14 7-7.004 7Zm3.866-5.245c-.211-.106-1.246-.613-1.439-.683-.192-.07-.332-.106-.473.106-.14.211-.543.682-.666.823-.123.14-.246.158-.457.053-.211-.106-.89-.327-1.693-1.043-.626-.558-1.049-1.246-1.173-1.456-.123-.211-.013-.324.093-.429.096-.095.211-.246.316-.369.106-.123.141-.211.211-.352.07-.14.035-.264-.018-.37-.053-.106-.473-1.14-.647-1.561-.17-.409-.344-.353-.473-.36l-.405-.007c-.14 0-.37.053-.563.264-.193.211-.737.721-.737 1.757 0 1.036.754 2.037.858 2.179.106.14 1.483 2.267 3.596 3.091.503.174.895.277 1.201.354.504.128.963.11 1.326.067.405.048 1.246-.509 1.422-1.002.176-.492.176-.913.123-.999-.053-.085-.193-.14-.404-.246Z"/>
              </svg>
            </a>
          </li>
        </ul>
        <a href="https://wa.me/221772285452" target="_blank" class="btn btn-success btn-lg">
          <i class="bi bi-whatsapp"></i> Discuter via WhatsApp
        </a>
      </div>
    </div>
  </div>
</div>
</body>
</html>
<?php require 'footer.html'; ?>