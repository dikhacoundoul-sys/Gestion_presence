<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier l'événement</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-5" style="max-width: 600px;">
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-primary text-white py-3">
            <h5 class="mb-0 fw-bold">Modifier l'événement</h5>
        </div>
        <div class="card-body p-4">

            <!-- Affichage des erreurs de validation -->
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger rounded-3">
                    <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('admin/updateEvenement/' . $evenement['id_evenement']); ?>" method="post">
                <?= csrf_field(); ?>

                <!-- Titre de l'événement -->
                <div class="mb-3">
                    <label for="titre" class="form-label fw-semibold">Titre de l'événement</label>
                    <input type="text" name="titre" id="titre" class="form-control" 
                           value="<?= old('titre', $evenement['titre']); ?>" required>
                </div>

                <!-- Date de début et Durée en jours -->
                <div class="row g-3 mb-3">
                    <div class="col-md-7">
                        <label for="date_evenement" class="form-label fw-semibold">Date de début</label>
                        <input type="date" name="date_evenement" id="date_evenement" class="form-control" 
                               value="<?= old('date_evenement', $evenement['date_evenement']); ?>" required>
                    </div>
                    <div class="col-md-5">
                        <label for="duree_jours" class="form-label fw-semibold">Durée (en jours)</label>
                        <input type="number" name="duree_jours" id="duree_jours" class="form-control" 
                               min="1" max="30" value="<?= old('duree_jours', $evenement['duree_jours'] ?? 1); ?>" required>
                    </div>
                </div>

                <!-- Lieu -->
                <div class="mb-4">
                    <label for="lieu" class="form-label fw-semibold">Lieu / Emplacement</label>
                    <input type="text" name="lieu" id="lieu" class="form-control" 
                           value="<?= old('lieu', $evenement['lieu']); ?>" required>
                </div>

                <!-- Boutons d'action -->
                <div class="d-flex justify-content-end gap-2">
                    <a href="<?= base_url('admin/dashboard'); ?>" class="btn btn-outline-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                </div>
            </form>

        </div>
    </div>
</div>
</body>
</html>