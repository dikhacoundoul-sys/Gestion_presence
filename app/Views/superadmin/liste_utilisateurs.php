<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Utilisateurs - SuperAdmin</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Icons Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <div class="container my-5">
        
        <!-- En-tête -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-dark mb-0">Gestion des Utilisateurs</h2>
            <div class="d-flex gap-2">
                <a href="<?= base_url('admin/dashboard'); ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Retour au Dashboard
                </a>
                <!-- Bouton ouvrant la modale -->
                <button type="button" class="btn btn-success fw-semibold" data-bs-toggle="modal" data-bs-target="#modalCreerUtilisateur">
                    <i class="bi bi-person-plus me-1"></i> Ajouter un utilisateur
                </button>
            </div>
        </div>

        <!-- Message Flash de Succès -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-check-circle me-2"></i><?= session()->getFlashdata('success'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Tableau des Utilisateurs -->
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="py-3 px-4">#</th>
                                <th class="py-3">Nom & Prénom</th>
                                <th class="py-3">Email</th>
                                <th class="py-3">Téléphone</th>
                                <th class="py-3">Rôle</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($utilisateurs) && is_array($utilisateurs)): ?>
                                <?php foreach ($utilisateurs as $u): ?>
                                    <tr>
                                        <td class="px-4 fw-bold"><?= esc($u['id_admin']); ?></td>
                                        <td class="fw-semibold text-dark"><?= esc($u['nom']) . ' ' . esc($u['prenom']); ?></td>
                                        <td><?= esc($u['email']); ?></td>
                                        <td><?= esc($u['telephone']); ?></td>
                                        <td>
                                            <span class="badge <?= $u['role'] === 'superadmin' ? 'bg-danger' : 'bg-primary'; ?>">
                                                <?= strtoupper(esc($u['role'])); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Aucun utilisateur trouvé.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- Modale d'ajout d'utilisateur -->
    <div class="modal fade" id="modalCreerUtilisateur" tabindex="-1" aria-labelledby="modalUserLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="modalUserLabel">
                        <i class="bi bi-person-plus-fill me-2"></i>Créer un Utilisateur
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="<?= base_url('superadmin/enregistrer-utilisateur'); ?>" method="post">
                    <div class="modal-body p-4">
                        <?= csrf_field(); ?>

                        <!-- Affichage des erreurs de validation -->
                        <?php if (session()->getFlashdata('errors')): ?>
                            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                                <ul class="mb-0 ps-3">
                                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                        <li><?= esc($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-dark fw-semibold">Nom</label>
                                <input type="text" name="nom" value="<?= old('nom'); ?>" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-dark fw-semibold">Prénom</label>
                                <input type="text" name="prenom" value="<?= old('prenom'); ?>" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-dark fw-semibold">Email</label>
                                <input type="email" name="email" value="<?= old('email'); ?>" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-dark fw-semibold">Téléphone</label>
                                <input type="text" name="telephone" value="<?= old('telephone'); ?>" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-dark fw-semibold">Mot de passe</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-dark fw-semibold">Rôle</label>
                                <select name="role" class="form-select">
                                    <option value="admin" <?= old('role') === 'admin' ? 'selected' : ''; ?>>Administrateur Simple</option>
                                    <option value="superadmin" <?= old('role') === 'superadmin' ? 'selected' : ''; ?>>Super Administrateur</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary fw-semibold">
                            <i class="bi bi-check-lg me-1"></i> Enregistrer
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- Bundle JS Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script de réouverture automatique si la validation échoue -->
    <?php if (session()->getFlashdata('errors')): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var modalElement = document.getElementById('modalCreerUtilisateur');
            var modal = new bootstrap.Modal(modalElement);
            modal.show();
        });
    </script>
    <?php endif; ?>
</body>
</html>