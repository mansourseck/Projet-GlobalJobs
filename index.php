
<?php require 'db.php'?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.84.0">
    <title>Product example · Bootstrap v5.0</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/product/">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  

    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>

    
    <!-- Custom styles for this template -->
    <link href="product.css" rel="stylesheet">
  </head>
  <body>
  <?php require'header.html';?>
<main>
<style>
        .carousel-item {
            height: 500px;
        }
        .carousel-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .carousel-caption {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            background: rgba(0, 0, 0, 0.6); /* Cadre semi-transparent */
            padding: 20px;
            border-radius: 10px;
        }
        .carousel-caption h1, .carousel-caption p {
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
        }
    </style>


<section id="heroCarousel" class="carousel slide container-fluid" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
    </div>
    <div class="carousel-inner">
        <div class="carousel-item active" data-bs-interval="5000">
            <img src="images/image2.jpg" alt="Image 1">
            <div class="carousel-caption">
                <h1 class="display-4 fw-bold">La Meilleure Plateforme De Recrutement</h1>
                <p class="lead">Nous sommes les meilleurs car nous vous comprenons.</p>
                <a class="btn btn-light btn-lg" href="#">Commencer Maintenant</a>
            </div>
        </div>
        <div class="carousel-item" data-bs-interval="5000">
            <img src="images/image5.jpg" alt="Image 2">
            <div class="carousel-caption">
                <h1 class="display-4 fw-bold">Trouvez le job parfait</h1>
                <p class="lead">Accédez aux meilleures opportunités.</p>
                <a class="btn btn-light btn-lg" href="#">Explorer les offres</a>
            </div>
        </div>
        <div class="carousel-item" data-bs-interval="5000">
            <img src="images/image4.jpg" alt="Image 3">
            <div class="carousel-caption">
                <h1 class="display-4 fw-bold">Recrutez les meilleurs talents</h1>
                <p class="lead">Des milliers de professionnels disponibles.</p>
                <a class="btn btn-light btn-lg" href="#">Voir les profils</a>
            </div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</section>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


  <section class="container my-5">
    <div class="row g-4">
      <!-- Section Offres d'emploi -->
      <div class="col-md-6">
  <div class="bg-white text-dark p-5 rounded shadow">
    <h2 class="display-5 text-primary">Offres d'emploi</h2>
    <p class="lead">Trouvez votre opportunité idéale parmi nos nombreuses offres.</p>
    <ul class="list-unstyled">
      <?php
      $stmt = $conn->query("SELECT titre, lieu FROM offres ORDER BY id DESC LIMIT 5");
      foreach ($stmt as $offre) {
        echo "<li><a href='#' class='text-dark fw-bold'>{$offre['titre']} - {$offre['lieu']}</a></li>";
      }
      ?>
    </ul>
    <a class="btn btn-outline-primary mt-3" href="#">Voir toutes les offres</a>
  </div>
</div>

      <!-- Section Profils disponibles -->
      <div class="col-md-6">
  <div class="bg-primary text-white p-5 rounded shadow">
    <h2 class="display-5">Profils disponibles</h2>
    <p class="lead">Accédez aux meilleurs talents prêts à rejoindre votre entreprise.</p>
    <ul class="list-unstyled">
      <?php
      $stmt = $conn->query("SELECT metier, experience FROM candidat ORDER BY id DESC LIMIT 5");
      foreach ($stmt as $profil) {
        echo "<li><a href='#' class='text-light fw-bold'>{$profil['metier']} - {$profil['experience']} </a></li>";
      }
      ?>
    </ul>
    <a class="btn btn-outline-light mt-3" href="#">Voir tous les profils</a>
  </div>
</div>
    </div>
  </section>

  <section class="container-fluid py-5 bg-light">
  <div class="container">
    <h2 class="text-center fw-bold text-primary mb-4">👥 Profils de candidats</h2>
    <div class="row">
      <!-- Profil 1 -->
      <div class="col-md-4">
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title">Amina Diallo</h5>
            <p class="card-text">Développeuse Web | 5 ans d'expérience</p>
            <p class="card-text text-muted">Compétences : HTML, CSS, JavaScript, PHP</p>
            <a href="#" class="btn btn-primary">Voir le profil</a>
          </div>
        </div>
      </div>
      <!-- Profil 2 -->
      <div class="col-md-4">
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title">Moussa Ndiaye</h5>
            <p class="card-text">Data Analyst | 3 ans d'expérience</p>
            <p class="card-text text-muted">Compétences : Python, SQL, Power BI</p>
            <a href="#" class="btn btn-primary">Voir le profil</a>
          </div>
        </div>
      </div>
      <!-- Profil 3 -->
      <div class="col-md-4">
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title">Fatou Sy</h5>
            <p class="card-text">Marketing Digital | 4 ans d'expérience</p>
            <p class="card-text text-muted">Compétences : SEO, SEM, Social Media</p>
            <a href="#" class="btn btn-primary">Voir le profil</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="container-fluid py-5 bg-white">
  <div class="container">
    <h2 class="text-center fw-bold text-success mb-4">💼 Offres d'emploi</h2>
    <div class="row">
      <!-- Offre 1 -->
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title">Développeur Front-end</h5>
            <p class="card-text">Entreprise : Tech Solutions</p>
            <p class="card-text text-muted">Lieu : Dakar, Sénégal</p>
            <p class="card-text">Expérience requise : 3 ans</p>
            <a href="#" class="btn btn-success">Postuler</a>
          </div>
        </div>
      </div>
      <!-- Offre 2 -->
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title">Analyste Financier</h5>
            <p class="card-text">Entreprise : FinancePro</p>
            <p class="card-text text-muted">Lieu : Dakar, Sénégal</p>
            <p class="card-text">Expérience requise : 5 ans</p>
            <a href="#" class="btn btn-success">Postuler</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
</main>
<?php require'footer.html';?>
    <script src="/docs/5.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  </body>
</html>
