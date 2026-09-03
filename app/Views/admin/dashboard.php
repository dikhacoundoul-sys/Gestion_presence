<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Gestion Présences</title>
    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Icon Font Stylesheet -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <!-- QRCodeJS & Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Script Leaflet.js -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        body {
            font-family: 'Heebo', sans-serif;
            background-color: #f4f7fa;
            color: #6c757d;
            overflow-x: hidden;
        }
        .sidebar {
            width: 260px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #ffffff;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }
        .sidebar-brand {
            background-color: #3f51b5;
            color: #ffffff;
            padding: 20px;
            text-align: center;
            font-size: 1.4rem;
            font-weight: 700;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .sidebar-user {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #eef2f6;
        }
        .user-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: #3f51b5;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin: 0 auto 10px auto;
        }
        .sidebar-menu {
            padding: 15px 0;
            overflow-y: auto;
            flex-grow: 1;
        }
        .menu-header {
            padding: 10px 25px 5px 25px;
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 700;
            color: #3f51b5;
            letter-spacing: 0.5px;
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 12px 25px;
            color: #5b6b79;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .sidebar-link i {
            font-size: 1.2rem;
            margin-right: 12px;
        }
        .sidebar-link:hover {
            color: #3f51b5;
            background-color: #f8f9fa;
        }
        .sidebar-link.active {
            color: #ffffff;
            background-color: #3f51b5;
            border-radius: 0 25px 25px 0;
            margin-right: 15px;
        }
        .main-content {
            margin-left: 260px;
            padding: 30px;
        }
        .card-custom {
            background: #ffffff;
            border-radius: 10px;
            border: none;
            box-shadow: 0 0 15px rgba(0,0,0,0.03);
        }
        .suggestions-list {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 1050;
            background: #ffffff;
            border: 1px solid #ced4da;
            border-radius: 0 0 6px 6px;
            max-height: 200px;
            overflow-y: auto;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .suggestion-item {
            padding: 8px 12px;
            cursor: pointer;
            font-size: 0.875rem;
            color: #333;
        }
        .suggestion-item:hover {
            background-color: #f0f4ff;
            color: #3f51b5;
        }
        
        /* Styles personnalisés pour le conteneur du graphique défilable */
        .chart-scroll-container {
            max-height: 380px;
            overflow-y: auto;
            overflow-x: hidden;
            padding-right: 5px;
        }
        .chart-scroll-container::-webkit-scrollbar {
            width: 6px;
        }
        .chart-scroll-container::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 3px;
        }
    </style>
</head>
<body>

<aside class="sidebar">
    <a href="<?= base_url('admin/dashboard'); ?>" class="sidebar-brand">
        <i class="bi bi-qr-code-scan"></i>
        <span>PresenceApp</span>
    </a>
    <div class="sidebar-user">
        <div class="user-avatar">
            <i class="bi bi-person"></i>
        </div>
        <h6 class="mb-0 fw-bold text-dark"><?= esc(session()->get('prenom')) . ' ' . esc(session()->get('nom')); ?></h6>
        <small class="text-muted d-block"><?= ucfirst(esc(session()->get('role') ?? 'admin')); ?></small>
    </div>
    <div class="sidebar-menu">
        <div class="menu-header">Navigation</div>
        <a href="<?= base_url('admin/dashboard'); ?>" class="sidebar-link active">
            <i class="bi bi-house-door"></i> Dashboard
        </a>
        <?php if (session()->get('role') === 'superadmin'): ?>
            <div class="menu-header mt-3">Administration</div>
            <a href="<?= base_url('superadmin/utilisateurs'); ?>" class="sidebar-link">
                <i class="bi bi-people"></i> Utilisateurs
            </a>
        <?php endif; ?>
        <div class="menu-header mt-3">Mon Compte</div>
        <a href="<?= base_url('profil'); ?>" class="sidebar-link">
            <i class="bi bi-person-gear"></i> Mon Profil
        </a>
        <a href="<?= base_url('profil/changer-password'); ?>" class="sidebar-link">
            <i class="bi bi-shield-lock"></i> Mot de passe
        </a>
        <a href="<?= base_url('logout'); ?>" class="sidebar-link text-danger mt-2">
            <i class="bi bi-box-arrow-right"></i> Déconnexion
        </a>
    </div>
</aside>

<main class="main-content">
    <div class="container-fluid">

        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold text-dark mb-1">Tableau de Bord</h4>
                <p class="text-muted small mb-0">Aperçu général de la participation et gestion des événements.</p>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary fw-semibold" data-bs-toggle="modal" data-bs-target="#modalCreerEvenement">
                    <i class="bi bi-plus-lg me-1"></i> Nouveau Événement
                </button>
                <a href="#liste-evenements" class="btn btn-outline-secondary fw-semibold">
                    <i class="bi bi-list-task me-1"></i> Voir la Liste
                </a>
            </div>
        </div>
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="bi bi-check-circle me-2"></i><?= session()->getFlashdata('success'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                <ul class="mb-0 ps-3">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error); ?></li>
                <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- SECTION GRAPHIQUE REDESSINÉE (HORIZONTALE + DÉFILABLE) -->
        <div class="card-custom p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark mb-0">
                    <i class="bi bi-bar-chart-line text-primary me-2"></i>Statistiques de Présence
                </h5>
                <span class="badge bg-light text-secondary border">Défilement actif</span>
            </div>
            
            <div class="chart-scroll-container">
                <div id="chartContainer" style="position: relative; max-width: 450px;">
                    <canvas id="presenceChart"></canvas>
                </div>
            </div>
        </div>

        <div class="card-custom p-4" id="liste-evenements">
            <h5 class="mb-4 fw-bold text-dark"><i class="bi bi-list-task text-primary me-2"></i>Liste des Événements</h5>
            <div class="table-responsive">
                <table class="table align-middle table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3">Titre</th>
                            <th class="py-3">Date</th>
                            <th class="py-3">Lieu</th>
                            <th class="py-3 text-center">QR Code</th>
                            <th class="py-3 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($evenements) && is_array($evenements)): ?>
                            <?php foreach ($evenements as $evt): ?>
                            <tr>
                                <td class="fw-bold text-dark"><?= esc($evt['titre']); ?></td>
                                <td><i class="bi bi-clock me-1 text-muted"></i><?= date('d/m/Y H:i', strtotime($evt['date_evenement'])); ?></td>
                                <td><i class="bi bi-geo-alt me-1 text-muted"></i><?= esc($evt['lieu']); ?></td>
                                <td class="text-center">
                                    <div class="d-inline-block p-1 bg-light border rounded">
                                        <div id="qrcode-<?= $evt['id_evenement']; ?>"></div>
                                    </div>
                                    <script>
                                        new QRCode(document.getElementById("qrcode-<?= $evt['id_evenement']; ?>"), {
                                            text: "<?= base_url('presence/emarger/' . $evt['code_qr']); ?>",
                                            width: 45,
                                            height: 45
                                        });
                                    </script>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group gap-2" role="group">
                                        <a href="<?= base_url('admin/presences/' . $evt['id_evenement']); ?>" class="btn btn-sm btn-primary" title="Voir Présences">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="<?= base_url('admin/evenement/qrcode/' . $evt['id_evenement']); ?>" target="_blank" class="btn btn-sm btn-dark" title="Mode Écran">
                                            <i class="bi bi-qr-code"></i>
                                        </a>
                                        <a href="<?= base_url('admin/evenement/edit/' . $evt['id_evenement']); ?>" class="btn btn-sm btn-warning text-dark" title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="<?= base_url('admin/evenement/delete/' . $evt['id_evenement']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet événement ?');" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Aucun événement enregistré.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</main>

<!-- Modal Créer un Événement -->
<div class="modal fade" id="modalCreerEvenement" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="modalLabel"><i class="bi bi-plus-circle me-2"></i>Créer un Événement</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('admin/evenement/store'); ?>" method="post">
                <div class="modal-body p-4">
                    <?= csrf_field(); ?>
                    <input type="hidden" name="latitude_lieu" id="latitude_lieu" value="<?= old('latitude_lieu'); ?>">
                    <input type="hidden" name="longitude_lieu" id="longitude_lieu" value="<?= old('longitude_lieu'); ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-semibold">Titre</label>
                            <input type="text" name="titre" value="<?= old('titre'); ?>" class="form-control bg-light" required>
                        </div>
                        <div class="col-md-6">
                            <label for="date_evenement" class="form-label text-dark fw-semibold">Date et Heure</label>
                            <input type="datetime-local" name="date_evenement" id="date_evenement" value="<?= old('date_evenement'); ?>" class="form-control bg-light" required>
                        </div>
                        <div class="col-md-6">
                            <label for="duree_jours" class="form-label text-dark fw-semibold">Durée (jours)</label>
                            <input type="number" name="duree_jours" id="duree_jours" class="form-control bg-light" min="1" max="30" value="<?= old('duree_jours', 1); ?>" required>
                        </div>
                        <div class="col-md-6 position-relative">
                            <label class="form-label text-dark fw-semibold">Lieu (Recherche automatique GPS)</label>
                            <div class="input-group">
                                <input type="text" name="lieu" id="lieu" value="<?= old('lieu'); ?>" class="form-control bg-light" autocomplete="off" required>
                                <button type="button" class="btn btn-outline-primary" id="btnSearchLieu" onclick="rechercherLieuGeocode()">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                            <div id="suggestions" class="suggestions-list d-none"></div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label text-dark fw-semibold">Ajuster la position sur la carte (Glisser le marqueur)</label>
                        <div id="map" style="height: 300px; width: 100%; border-radius: 8px; border: 1px solid #ccc;"></div>
                    </div>

                    <div class="mt-3">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="capturerPositionAdmin()">
                            <i class="bi bi-geo-alt-fill"></i> Détecter automatiquement ma position GPS
                        </button>
                        <small id="gps-status" class="ms-2 fw-semibold"></small>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary fw-semibold"><i class="bi bi-check-lg me-1"></i> Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
let map, marker;
let debounceTimer;
const defaultLat = 14.6937;
const defaultLng = -17.4441;

function updateCoordinates(lat, lng) {
    document.getElementById('latitude_lieu').value = parseFloat(lat).toFixed(6);
    document.getElementById('longitude_lieu').value = parseFloat(lng).toFixed(6);
}

function initMap(lat, lng) {
    if (!map) {
        map = L.map('map').setView([lat, lng], 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        marker = L.marker([lat, lng], { draggable: true }).addTo(map);

        marker.on('dragend', function () {
            const position = marker.getLatLng();
            updateCoordinates(position.lat, position.lng);
        });

        map.on('click', function (e) {
            marker.setLatLng(e.latlng);
            updateCoordinates(e.latlng.lat, e.latlng.lng);
        });
    } else {
        map.setView([lat, lng], 14);
        marker.setLatLng([lat, lng]);
    }
    updateCoordinates(lat, lng);
}

function rechercherLieuGeocode() {
    const query = document.getElementById('lieu').value.trim();
    const suggestionsContainer = document.getElementById('suggestions');
    const status = document.getElementById('gps-status');

    if (query.length < 3) {
        suggestionsContainer.classList.add('d-none');
        return;
    }

    status.className = "ms-2 text-primary fw-semibold";
    status.textContent = "Recherche d'adresse en cours...";

    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5`)
        .then(response => response.json())
        .then(data => {
            suggestionsContainer.innerHTML = '';
            if (data.length > 0) {
                const topResult = data[0];
                const lat = parseFloat(topResult.lat);
                const lon = parseFloat(topResult.lon);

                initMap(lat, lon);
                status.className = "ms-2 text-success fw-semibold";
                status.textContent = "✓ Position mise à jour sur la carte";

                data.forEach(item => {
                    const div = document.createElement('div');
                    div.className = 'suggestion-item';
                    div.textContent = item.display_name;
                    div.onclick = function () {
                        document.getElementById('lieu').value = item.display_name.split(',')[0];
                        initMap(parseFloat(item.lat), parseFloat(item.lon));
                        suggestionsContainer.classList.add('d-none');
                    };
                    suggestionsContainer.appendChild(div);
                });
                suggestionsContainer.classList.remove('d-none');
            } else {
                status.className = "ms-2 text-danger fw-semibold";
                status.textContent = "Aucun emplacement trouvé.";
                suggestionsContainer.classList.add('d-none');
            }
        })
        .catch(() => {
            status.className = "ms-2 text-danger fw-semibold";
            status.textContent = "Erreur de connexion au service de carte.";
        });
}

document.getElementById('lieu').addEventListener('input', function () {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(rechercherLieuGeocode, 600);
});

document.addEventListener('click', function (e) {
    const suggestionsContainer = document.getElementById('suggestions');
    const inputLieu = document.getElementById('lieu');
    if (e.target !== inputLieu && !suggestionsContainer.contains(e.target)) {
        suggestionsContainer.classList.add('d-none');
    }
});

function capturerPositionAdmin() {
    const status = document.getElementById('gps-status');
    const inputLieu = document.getElementById('lieu');
    if ("geolocation" in navigator) {
        status.className = "ms-2 text-primary fw-semibold";
        status.textContent = "Détection GPS en cours...";
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                initMap(lat, lng);

                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                    .then(response => response.json())
                    .then(data => {
                        const nomLieu = data?.address?.suburb || data?.address?.city || data?.address?.town || data?.address?.village || "Lieu détecté";
                        inputLieu.value = nomLieu;
                        status.className = "ms-2 text-success fw-semibold";
                        status.textContent = `✓ Détecté : ${nomLieu}`;
                    })
                    .catch(() => {
                        status.className = "ms-2 text-success fw-semibold";
                        status.textContent = `✓ GPS capturé (${lat.toFixed(4)}, ${lng.toFixed(4)})`;
                    });
            },
            function() {
                status.className = "ms-2 text-danger fw-semibold";
                status.textContent = "Erreur : Accès GPS refusé.";
            },
            { enableHighAccuracy: true }
        );
    } else {
        status.className = "ms-2 text-danger fw-semibold";
        status.textContent = "GPS non supporté.";
    }
}

document.addEventListener("DOMContentLoaded", function () {
    if ("geolocation" in navigator) {
        navigator.geolocation.getCurrentPosition(
            function (position) {
                initMap(position.coords.latitude, position.coords.longitude);
            },
            function () {
                initMap(defaultLat, defaultLng);
            }
        );
    } else {
        initMap(defaultLat, defaultLng);
    }

    // JS DE CHART.JS MISE À JOUR POUR LE FORMAT HORIZONTAL DÉFILABLE
    const labels = <?= json_encode($chartLabels ?? ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim']); ?>;
    const dataValues = <?= json_encode($chartData ?? [12, 19, 3, 5, 2, 8, 15]); ?>;

    const maxDataValue = Math.max(...dataValues, 1);
    // Ajustement dynamique de la hauteur en fonction du nombre d'éléments
    const minPixelHeight = 36; 
    const calculatedHeight = Math.max(300, labels.length * minPixelHeight);
    document.getElementById('chartContainer').style.height = calculatedHeight + 'px';

    const ctx = document.getElementById('presenceChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Nombre de Participants',
                data: dataValues,
                backgroundColor: 'rgba(63, 81, 181, 0.75)', // Couleur originale #3f51b5 avec transparence
                borderColor: '#3f51b5',
                borderWidth: 1,
                borderRadius: 4,
                barThickness: 16
            }]
        },
        options: {
            indexAxis: 'y', // Passage en barres horizontales
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: { color: '#eef2f6', drawBorder: false },
                    ticks: {
                        stepSize: 1,
                        font: { family: 'Heebo', size: 11 },
                        color: '#94a3b8'
                    }
                },
                y: {
                    grid: { display: false },
                    ticks: {
                        font: { family: 'Heebo', size: 12, weight: '500' },
                        color: '#5b6b79'
                    }
                }
            }
        }
    });
});

const modalElement = document.getElementById('modalCreerEvenement');
if (modalElement) {
    modalElement.addEventListener('shown.bs.modal', function () {
        if (map) {
            map.invalidateSize();
        }
    });
}
</script>
</body>
</html>