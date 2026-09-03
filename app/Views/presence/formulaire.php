<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Émargement - <?= esc($evenement['titre']); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-4 mb-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white text-center">
            <h4>Émargement - <?= esc($evenement['titre']); ?></h4>
            <p class="mb-0"><?= esc($evenement['lieu']); ?> | <?= date('d/m/Y H:i', strtotime($evenement['date_evenement'])); ?></p>
        </div>
        <div class="card-body">
            
            <!-- Affichage des alertes d'erreurs -->
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('warning')): ?>
                <div class="alert alert-warning">
                    <?= session()->getFlashdata('warning'); ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error_gps')): ?>
                <div class="alert alert-danger">
                    <?= session()->getFlashdata('error_gps'); ?>
                </div>
            <?php endif; ?>

            <!-- Indicateur d'état du GPS -->
            <div id="gps-status" class="alert alert-info text-center">
                <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                Détection de votre position GPS en cours...
            </div>

            <form action="<?= base_url('presence/enregistrer'); ?>" method="post" id="presenceForm">
                <?= csrf_field(); ?>
                <input type="hidden" name="id_evenement" value="<?= $evenement['id_evenement']; ?>">
                <input type="hidden" name="latitude_participant" id="latitude_participant">
                <input type="hidden" name="longitude_participant" id="longitude_participant">

                <div class="mb-3">
                    <label for="nom" class="form-label">Nom *</label>
                    <input type="text" name="nom" id="nom" class="form-control" value="<?= old('nom'); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="prenom" class="form-label">Prénom *</label>
                    <input type="text" name="prenom" id="prenom" class="form-control" value="<?= old('prenom'); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="structure" class="form-label">Structure *</label>
                    <input type="text" name="structure" id="structure" class="form-control" value="<?= old('structure'); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="fonction" class="form-label">Fonction *</label>
                    <input type="text" name="fonction" id="fonction" class="form-control" value="<?= old('fonction'); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email *</label>
                    <input type="email" name="email" id="email" class="form-control" value="<?= old('email'); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="telephone" class="form-label">Téléphone *</label>
                    <input type="tel" name="telephone" id="telephone" class="form-control" value="<?= old('telephone'); ?>" required>
                </div>

                <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>

                <div class="form-group mb-4">
                    <label for="signature-pad" class="form-label fw-bold">Marge *</label>
                    <div class="border rounded bg-white p-2 text-center shadow-sm">
                        <canvas id="signature-pad" width="400" height="200" style="touch-action: none; cursor: crosshair; width: 100%; max-width: 500px; border: 1px dashed #ccc;"></canvas>
                    </div>
                    <input type="hidden" name="marge" id="signature_data" value="<?= old('marge') ?>">

                    <div class="mt-2 d-flex justify-content-between align-items-center">
                        <small class="text-muted">Dessinez votre signature au doigt ou à la souris dans le cadre ci-dessus.</small>
                        <button type="button" class="btn btn-sm btn-outline-danger" id="clear-btn">Effacer</button>
                    </div>
                </div>

                <button type="submit" id="btnSubmit" class="btn btn-success w-100" disabled>Valider ma présence</button>
            </form>

        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Signature Pad logic
    const canvas = document.getElementById('signature-pad');
    const signaturePad = new SignaturePad(canvas, {
        backgroundColor: 'rgba(255, 255, 255, 0)',
        penColor: 'rgb(0, 0, 0)'
    });
    const hiddenInput = document.getElementById('signature_data');
    const form = document.getElementById('presenceForm');
    const clearBtn = document.getElementById('clear-btn');

    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);
        signaturePad.clear();
    }
    window.addEventListener("resize", resizeCanvas);
    resizeCanvas();

    clearBtn.addEventListener('click', function() {
        signaturePad.clear();
        hiddenInput.value = '';
    });

    form.addEventListener('submit', function(e) {
        if (signaturePad.isEmpty()) {
            e.preventDefault();
            alert("Veuillez apposer votre signature avant de valider.");
            return false;
        }
        hiddenInput.value = signaturePad.toDataURL('image/png');
    });

    // Geolocation logic
    const gpsStatus = document.getElementById('gps-status');
    const btnSubmit = document.getElementById('btnSubmit');

    if ("geolocation" in navigator) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                document.getElementById('latitude_participant').value = position.coords.latitude;
                document.getElementById('longitude_participant').value = position.coords.longitude;
                gpsStatus.className = "alert alert-success text-center";
                gpsStatus.innerHTML = "Position GPS détectée avec succès.";
                btnSubmit.disabled = false;
            },
            function(error) {
                gpsStatus.className = "alert alert-danger text-center";
                gpsStatus.innerHTML = "Erreur GPS : Veuillez autoriser la géolocalisation sur votre téléphone pour pouvoir émarger.";
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    } else {
        gpsStatus.className = "alert alert-danger text-center";
        gpsStatus.innerHTML = "La géolocalisation n'est pas supportée par votre navigateur.";
    }
});
</script>
</body>
</html>