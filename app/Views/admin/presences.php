<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Présences - Multi-jours</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container-fluid py-4 px-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2>Présences : <?= esc($evenement['titre']); ?></h2>
            <p class="text-muted mb-0">
                <?= esc($evenement['lieu']); ?> - Total : <strong><?= count($participants); ?></strong> participant(s) | 
                Durée : <strong><?= esc($evenement['duree_jours']); ?> jour(s)</strong>
            </p>
        </div>
        <div>
            <a href="<?= base_url('admin/dashboard'); ?>" class="btn btn-secondary">Retour</a>
            <a href="<?= base_url('admin/export/pdf/' . $evenement['id_evenement']); ?>" class="btn btn-danger">Export PDF</a>
            <a href="<?= base_url('admin/export/excel/' . $evenement['id_evenement']); ?>" class="btn btn-success">Export Excel</a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Structure</th>
                            <th>Fonction</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th class="text-center">Signature</th>
                            <!-- Colonnes dynamiques pour les jours -->
                            <?php for ($j = 1; $j <= $evenement['duree_jours']; $j++): ?>
                                <th class="text-center">Jour <?= $j; ?></th>
                            <?php endfor; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($participants)): ?>
                            <tr>
                                <td colspan="<?= 8 + $evenement['duree_jours']; ?>" class="text-center text-muted py-4">
                                    Aucun émargement pour le moment.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($participants as $index => $p): ?>
                            <tr>
                                <td><?= $index + 1; ?></td>
                                <td><?= esc($p['nom']); ?></td>
                                <td><?= esc($p['prenom']); ?></td>
                                <td><?= esc($p['structure']); ?></td>
                                <td><?= esc($p['fonction']); ?></td>
                                <td><?= esc($p['email']); ?></td>
                                <td><?= esc($p['telephone']); ?></td>
                                
                                <!-- Affichage de la signature -->
                                <td class="text-center">
                                    <?php if (!empty($p['marge']) && strstr($p['marge'], 'data:image')): ?>
                                        <img src="<?= $p['marge']; ?>" alt="Signature" style="max-height: 40px; border: 1px solid #ccc; background: #fff; padding: 2px; border-radius: 4px;">
                                    <?php else: ?>
                                        <span class="text-muted"><?= !empty($p['marge']) ? esc($p['marge']) : '-'; ?></span>
                                    <?php endif; ?>
                                </td>

                                <!-- Affichage de la présence jour par jour -->
                                <?php for ($j = 1; $j <= $evenement['duree_jours']; $j++): ?>
                                    <td class="text-center">
                                        <?php if (isset($tableauPresences[$p['id_participant']][$j])): ?>
                                            <span class="badge bg-success">
                                                <?= date('H:i', strtotime($tableauPresences[$p['id_participant']][$j])); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Absent</span>
                                        <?php endif; ?>
                                    </td>
                                <?php endfor; ?>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>