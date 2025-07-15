<?php require 'admin_data.php'; ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - GlobalJobs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="header">
        <h4><i class="fas fa-shield-alt"></i>GlobalJobs Admin</h4>
        <a href="../logout.php" class="btn-logout">
            <i class="fas fa-sign-out-alt"></i>Déconnexion
        </a>
    </div>

    <nav class="sidebar">
        <h6>Panel Admin</h6>
        <ul class="nav nav-pills flex-column">
            <li><a class="nav-link active" href="#" onclick="showSection('dashboard', event)">
                    <i class="fas fa-chart-pie"></i> Dashboard
                </a></li>
            <li><a class="nav-link" href="#" onclick="showSection('users', event)">
                    <i class="fas fa-users"></i> Utilisateurs
                </a></li>
            <li><a class="nav-link" href="#" onclick="showSection('jobs', event)">
                    <i class="fas fa-briefcase"></i> Offres
                </a></li>
            <li><a class="nav-link" href="#" onclick="showSection('applications', event)">
                    <i class="fas fa-file-alt"></i> Candidatures
                </a></li>
        </ul>
    </nav>

    <div class="main-content">

        <!-- DASHBOARD -->
        <div id="dashboard" class="section">
            <div class="glass welcome-section">
                <h5>🎯 Bienvenue Admin</h5>
                <p>
                    Ce tableau de bord vous donne un aperçu global de l'activité de la plateforme GlobalJobs.
                </p>
            </div>
            <br><br>

            <!-- EXPLICATIONS -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="glass p-3">
                        <b>Utilisateurs :</b>
                        <p>
                            Nombre total d'utilisateurs inscrits, avec distinction entre les comptes actifs et ceux bloqués.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="glass p-3">
                        <b>Offres d'emploi :</b>
                        <p>
                            Statistiques sur toutes les offres publiées, en attente de validation, ou rejetées par l'administration.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="glass p-3">
                        <b>Candidatures :</b>
                        <p>
                            Suivi du nombre de candidatures envoyées par les utilisateurs, avec leur statut (acceptées, en attente, refusées).
                        </p>
                    </div>
                </div>
            </div>

            <!-- GRAPHIQUES SECTEURS -->
            <div class="row mb-4">
                <div class="col-md-4 text-center">
                    <canvas id="usersPie"></canvas>
                    <div class="chart-caption" style="color: white;">Répartition utilisateurs</div>
                </div>
                <div class="col-md-4 text-center">
                    <canvas id="jobsPie"></canvas>
                    <div class="chart-caption" style="color: white;">Répartition offres</div>
                </div>
                <div class="col-md-4 text-center">
                    <canvas id="appsPie"></canvas>
                    <div class="chart-caption" style="color: white;">Répartitioncandidatures</div>
                </div>
            </div>
             <br>
            <!-- TABLE DE RÉPARTITION -->
            <div class="glass">
                <h5>Table de répartition</h5>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle text-center">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Total</th>
                                <th class="bg-success text-white">Actifs / Publiées / Acceptées</th>
                                <th class="bg-warning text-dark">En attente</th>
                                <th class="bg-danger text-white">Bloqués / Rejetées / Refusées</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th>Utilisateurs</th>
                                <td><?= $usersTotal ?></td>
                                <td><?= $usersActifs ?></td>
                                <td>—</td>
                                <td><?= $usersBloques ?></td>
                            </tr>
                            <tr>
                                <th>Offres</th>
                                <td><?= $jobsTotal ?></td>
                                <td><?= $jobsPublier ?></td>
                                <td><?= $jobsAttente ?></td>
                                <td><?= $jobsRejete ?></td>
                            </tr>
                            <tr>
                                <th>Candidatures</th>
                                <td><?= $applicationsTotal ?></td>
                                <td><?= $applicationsAcceptees ?></td>
                                <td><?= $applicationsAttente ?></td>
                                <td><?= $applicationsRefusees ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Utilisateurs -->
        <div id="users" class="section" style="display:none;">
            <div class="glass">
                <h5 class="section-header">
                    <i class="fas fa-users"></i> Utilisateurs
                </h5>
                <p class="text-secondary mb-3">
                    Retrouvez ici la liste de tous les utilisateurs inscrits sur la plateforme. 
                    Vous pouvez rechercher un utilisateur, vérifier son statut (actif ou bloqué) et gérer ses droits d'accès.
                </p>
                <div class="row mb-3">
                    <div class="col">
                        <span class="badge bg-primary">Total : <?= $usersTotal ?></span>
                        <span class="badge bg-success">Actifs : <?= $usersActifs ?></span>
                        <span class="badge bg-danger">Bloqués : <?= $usersBloques ?></span>
                    </div>
                </div>
                <input type="text" class="form-control w-50 mb-3" placeholder="Rechercher..." onkeyup="filterTable(this.value, 'usersTable')">

                <div class="table-container">
                    <table class="table table-bordered" id="usersTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Téléphone</th>
                                <th>Adresse</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['id']) ?></td>
                                    <td><?= htmlspecialchars($user['nom']) ?></td>
                                    <td><?= htmlspecialchars($user['prenom']) ?></td>
                                    <td><?= htmlspecialchars($user['telephone']) ?></td>
                                    <td><?= htmlspecialchars($user['adresse']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td><?= htmlspecialchars($user['role']) ?></td>
                                    <td class="status-cell">
                                        <span class="badge bg-<?= $user['statut'] === 'bloquer' ? 'danger' : 'success' ?>">
                                            <?= htmlspecialchars($user['statut']) ?>
                                        </span>
                                    </td>
                                    <td class="action-buttons">
                                        <?php if ($user['statut'] === 'bloquer'): ?>
                                            <a href="action.php?id=<?= $user['id'] ?>&action=unblock" class="btn btn-success btn-sm">Activer</a>
                                        <?php else: ?>
                                            <a href="action.php?id=<?= $user['id'] ?>&action=block" class="btn btn-danger btn-sm">Bloquer</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Offres -->
        <div id="jobs" class="section" style="display:none;">
            <div class="glass">
                <h5 class="section-header">
                    <i class="fas fa-briefcase"></i> Offres d'emploi
                </h5>
                <p class="text-secondary mb-3">
                    Consultez ici toutes les offres d'emploi publiées ou en attente de validation.<br>
                    Vous pouvez contrôler le statut des offres, les valider ou les refuser, et accéder aux détails de chaque poste proposé sur la plateforme.
                </p>
                <div class="row mb-3">
                    <div class="col">
                        <span class="badge bg-primary">Total : <?= $jobsTotal ?></span>
                        <span class="badge bg-success">Publiées : <?= $jobsPublier ?></span>
                        <span class="badge bg-warning text-dark">En attente : <?= $jobsAttente ?></span>
                        <span class="badge bg-danger">Rejetées : <?= $jobsRejete ?></span>
                    </div>
                </div>
                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Titre</th>
                                    <th>Entreprise</th>
                                    <th>Recruteur</th>
                                    <th>Domaine</th>
                                    <th>Type</th>
                                    <th>Lieu</th>
                                    <th>Expire le</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($jobs as $job): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($job['id']) ?></td>
                                        <td><strong><?= htmlspecialchars($job['titre']) ?></strong></td>
                                        <td><?= htmlspecialchars($job['entreprise']) ?></td>
                                        <td>
                                            <small class="text-muted"><?= htmlspecialchars($job['email']) ?></small>
                                        </td>
                                        <td><?= htmlspecialchars($job['domain']) ?></td>
                                        <td><?= htmlspecialchars($job['type_contrat']) ?></td>
                                        <td><?= htmlspecialchars($job['lieu']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($job['date_expire'])) ?></td>
                                        <td class="status-cell">
                                            <?php
                                            $badge = "secondary";
                                            if ($job['statut'] === 'Publier') $badge = "success";
                                            elseif ($job['statut'] === 'En attente') $badge = "warning";
                                            elseif ($job['statut'] === 'Rejété') $badge = "danger";
                                            ?>
                                            <span class="badge bg-<?= $badge ?>" id="status-<?= $job['id'] ?>">
                                                <?= htmlspecialchars($job['statut']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="action-buttons" id="actions-<?= $job['id'] ?>">
                                                <?php if ($job['statut'] === 'En attente'): ?>
                                                    <a href="admin_dashboard.php?update_id=<?= $job['id'] ?>&new_status=Publier&page=<?= $page ?>" class="btn btn-success btn-sm">✅ Valider</a>
                                                    <a href="admin_dashboard.php?update_id=<?= $job['id'] ?>&new_status=Rejété&page=<?= $page ?>" class="btn btn-danger btn-sm">❌ Refuser</a>
                                                <?php else: ?>
                                                    <span class="text-muted">—</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Candidatures -->
        <div id="applications" class="section" style="display:none;">
            <div class="glass">
                <h5 class="section-header">
                    <i class="fas fa-file-alt"></i> Candidatures
                </h5>
                <p class="text-secondary mb-3">
                    Retrouvez ici toutes les candidatures envoyées par les utilisateurs.<br>
                    Vous pouvez suivre le statut de chaque candidature, filtrer les résultats et gérer le traitement des candidatures (acceptation, refus, etc.).
                </p>
                <div class="row mb-3">
                    <div class="col">
                        <span class="badge bg-primary">Total : <?= $applicationsTotal ?></span>
                        <span class="badge bg-success">Acceptées : <?= $applicationsAcceptees ?></span>
                        <span class="badge bg-warning text-dark">En attente : <?= $applicationsAttente ?></span>
                        <span class="badge bg-danger">Refusées : <?= $applicationsRefusees ?></span>
                    </div>
                </div>
                <input type="text" class="form-control w-50 mb-3" placeholder="Rechercher..." onkeyup="filterTable(this.value, 'applicationsTable')">

                <div class="table-container">
                    <table class="table table-bordered" id="applicationsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Candidat</th>
                                <th>Offre</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($applications as $app): ?>
                                <tr>
                                    <td><?= htmlspecialchars($app['id']) ?></td>
                                    <td><?= htmlspecialchars($app['candidat']) ?></td>
                                    <td><?= htmlspecialchars($app['titre']) ?></td>
                                    <td class="status-cell"><span class="badge bg-<?= ($app['statut'] === 'acceptée') ? 'success' : (($app['statut'] === 'en attente') ? 'warning' : 'danger') ?>"><?= htmlspecialchars($app['statut']) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        🌟 &copy; 2025 GlobalJobs 🌟
    </div>

    <script>
        function showSection(sectionId, event) {
            document.querySelectorAll('.section').forEach(function(sec) {
                sec.style.display = 'none';
            });
            document.getElementById(sectionId).style.display = 'block';

            document.querySelectorAll('.nav-link').forEach(function(link) {
                link.classList.remove('active');
            });
            if (event) event.target.classList.add('active');
        }

        function filterTable(searchValue, tableId) {
            const table = document.getElementById(tableId);
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                const cells = row.getElementsByTagName('td');
                let found = false;

                for (let j = 0; j < cells.length; j++) {
                    if (cells[j].textContent.toLowerCase().includes(searchValue.toLowerCase())) {
                        found = true;
                        break;
                    }
                }

                row.style.display = found ? '' : 'none';
            }
        }

        // Chart JS pour dashboard avec légende blanche et police plus grande
new Chart(document.getElementById('usersPie'), {
    type: 'pie',
    data: {
        labels: ['Actifs', 'Bloqués'],
        datasets: [{
            data: [<?= $usersActifs ?>, <?= $usersBloques ?>],
            backgroundColor: ['#198754', '#dc3545']
        }]
    },
    options: {
        plugins: {
            legend: {
                labels: {
                    color: '#fff', // écriture blanche
                    font: {
                        size: 12   // taille de police
                    }
                }
            }
        }
    }
});

new Chart(document.getElementById('jobsPie'), {
    type: 'pie',
    data: {
        labels: ['Publiées', 'En attente', 'Rejetées'],
        datasets: [{
            data: [<?= $jobsPublier ?>, <?= $jobsAttente ?>, <?= $jobsRejete ?>],
            backgroundColor: ['#198754', '#ffc107', '#dc3545']
        }]
    },
    options: {
        plugins: {
            legend: {
                labels: {
                    color: '#fff',
                    font: {
                        size: 12
                    }
                }
            }
        }
    }
});

new Chart(document.getElementById('appsPie'), {
    type: 'pie',
    data: {
        labels: ['Acceptées', 'En attente', 'Refusées'],
        datasets: [{
            data: [<?= $applicationsAcceptees ?>, <?= $applicationsAttente ?>, <?= $applicationsRefusees ?>],
            backgroundColor: ['#198754', '#ffc107', '#dc3545']
        }]
    },
    options: {
        plugins: {
            legend: {
                labels: {
                    color: '#fff',
                    font: {
                        size: 12
                    }
                }
            }
        }
    }
});
    </script>
</body>

</html>