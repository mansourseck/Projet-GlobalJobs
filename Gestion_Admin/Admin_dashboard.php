<?php require 'admin_data.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - GlobalJobs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: 
                linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)),
                url('../images/image3.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            font-family: 'Segoe UI', sans-serif;
        }
        
        .header {
            
            width: 100%;
            background: rgba(7, 147, 235, 0.8);
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
        }

        .header h4 {
            margin: 0;
            flex-grow: 1;
        }

        .glass {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .sidebar {
            position: fixed;
            top: 70px;
            left: 0;
            width: 250px;
            height: calc(100vh - 70px);
            overflow-y: auto;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 0 15px 0 0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 20px;
        }

        .main-content {
            margin-left: 250px;
            margin-top: 70px;
            padding: 20px;
        }

        .nav-link {
            color: #333;
            margin-bottom: 5px;
            border-radius: 8px;
            padding: 10px 15px;
        }

        .nav-link:hover, .nav-link.active {
            background: rgba(7, 147, 235, 0.8);
            color: white;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background: rgba(7, 147, 235, 0.8);
            color: white;
            text-align: center;
            padding: 15px;
            z-index: 1000;
        }

        .table {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            overflow: hidden;
        }

        .btn {
            border-radius: 8px;
        }

        .btn-light {
            background-color: #007bff;
            color: white;
            border: none;
        }

        .btn-light:hover {
            background-color: #0056b3;
            color: white;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid rgba(0,0,0,0.1);
            background: rgba(255, 255, 255, 0.9);
        }

        .section {
            margin-bottom: 100px;
        }

        h5, h6 {
            color: #333;
        }

        .text-center h3 {
            color: #007bff;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="header">
        <h4><i class="fas fa-shield-alt me-2"></i>GlobalJobs Admin</h4>
        <a href="logout.php" class="btn btn-light">Déconnexion</a>
    </div>

    <nav class="sidebar">
        <h6 class="text-center mb-3">Panel Admin</h6>
        <ul class="nav nav-pills flex-column">
            <li><a class="nav-link active" href="#" onclick="showSection('dashboard')"><i class="fas fa-chart-pie"></i> Dashboard</a></li>
            <li><a class="nav-link" href="#" onclick="showSection('users')"><i class="fas fa-users"></i> Utilisateurs</a></li>
            <li><a class="nav-link" href="#" onclick="showSection('jobs')"><i class="fas fa-briefcase"></i> Offres</a></li>
            <li><a class="nav-link" href="#" onclick="showSection('applications')"><i class="fas fa-file-alt"></i> Candidatures</a></li>
        </ul>
    </nav>

    <div class="main-content">
        <div id="dashboard" class="section">
            <div class="glass p-3 mb-3">
                <h5>🎯 Bienvenue Admin</h5>
                <p class="mb-0">Gérez votre plateforme GlobalJobs</p>
            </div>

            <div class="row">
                <div class="col-md-4"><div class="glass text-center"><h3><?= $usersCount ?></h3><p>Utilisateurs inscrits</p></div></div>
                <div class="col-md-4"><div class="glass text-center"><h3><?= $jobsCount ?></h3><p>Offres disponibles</p></div></div>
                <div class="col-md-4"><div class="glass text-center"><h3><?= $applicationsCount ?></h3><p>Candidatures soumises</p></div></div>
            </div>
        </div>

        <!-- Utilisateurs -->
        <div id="users" class="section" style="display:none;">
            <div class="glass">
                <h5><i class="fas fa-users me-2"></i> Utilisateurs</h5>
                <input type="text" class="form-control w-50 mb-3" placeholder="Rechercher..." onkeyup="filterTable(this.value, 'usersTable')">
                <table class="table table-light table-bordered" id="usersTable">
                    <thead><tr><th>ID</th><th>Nom</th><th>Email</th><th>Statut</th><th>Actions</th></tr></thead>
                    <tbody><?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['id']) ?></td>
                            <td><?= htmlspecialchars($user['nom']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><span class="badge bg-<?= $user['statut'] == 'Candidat' ? 'success' : 'danger' ?>"><?= htmlspecialchars($user['statut']) ?></span></td>
                            <td>
                                <a href="toggle_user.php?id=<?= $user['id'] ?>&action=activate" class="btn btn-success btn-sm">Activer</a>
                                <a href="toggle_user.php?id=<?= $user['id'] ?>&action=block" class="btn btn-danger btn-sm">Bloquer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?></tbody>
                </table>
            </div>
        </div>

        <!-- Offres -->
        <div id="jobs" class="section" style="display:none;">
            <div class="glass">
                <h5><i class="fas fa-briefcase me-2"></i> Offres d'emploi</h5>
                <table class="table table-light table-bordered">
                    <thead><tr><th>ID</th><th>Titre</th><th>Entreprise</th><th>Lieu</th><th>Statut</th></tr></thead>
                    <tbody><?php foreach ($jobs as $job): ?><tr>
                        <td><?= htmlspecialchars($job['id']) ?></td>
                        <td><?= htmlspecialchars($job['titre']) ?></td>
                        <td><?= htmlspecialchars($job['entreprise']) ?></td>
                        <td><?= htmlspecialchars($job['lieu']) ?></td>
                        <td><?= htmlspecialchars($job['secteur']) ?></td>
                    </tr><?php endforeach; ?></tbody>
                </table>
            </div>
        </div>

        <!-- Candidatures -->
        <div id="applications" class="section" style="display:none;">
            <div class="glass">
                <h5><i class="fas fa-file-alt me-2"></i> Candidatures</h5>
                <input type="text" class="form-control w-50 mb-3" placeholder="Rechercher..." onkeyup="filterTable(this.value, 'applicationsTable')">

                <table class="table table-light table-bordered" id="applicationsTable">
                    <thead class="table-dark">
                        <tr><th>ID</th><th>Candidat</th><th>Offre</th><th>Statut</th></tr>
                    </thead>
                    <tbody><?php foreach ($applications as $app): ?>
                        <tr>
                            <td><?= htmlspecialchars($app['id']) ?></td>
                            <td><?= htmlspecialchars($app['candidat']) ?></td>
                            <td><?= htmlspecialchars($app['titre']) ?></td>
                            <td><span class="badge bg-<?= ($app['statut'] === 'acceptée') ? 'success' : (($app['statut'] === 'en attente') ? 'warning' : 'danger') ?>"><?= htmlspecialchars($app['statut']) ?></span></td>
                        </tr>
                    <?php endforeach; ?></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="footer">
        🌟 &copy; 2025 GlobalJobs 🌟
    </div>

    <script> 
        function showSection(sectionId) { 
            document.querySelectorAll('.section').forEach(s => s.style.display = 'none'); 
            document.getElementById(sectionId).style.display = 'block'; 
        } 
    </script>

</body>
</html>