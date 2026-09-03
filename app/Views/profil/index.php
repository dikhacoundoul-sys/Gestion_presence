<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Profil</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light py-5">
<div class="container" style="max-width: 600px;">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success'); ?></div>
    <?php endif; ?>
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white font-weight-bold">Mon Profil</div>
        <div class="card-body">
            <p><strong>Prénom :</strong> <?= esc(session()->get('prenom')); ?></p>
            <p><strong>Nom :</strong> <?= esc(session()->get('nom')); ?></p>
            <p><strong>Rôle :</strong> <?= strtoupper(esc(session()->get('role'))); ?></p>
            <hr>
            <a href="<?= base_url('admin/dashboard'); ?>" class="btn btn-secondary">Retour au Dashboard</a>
            <a href="<?= base_url('profil/changer-password'); ?>" class="btn btn-warning">Changer le mot de passe</a>
        </div>
    </div>
</div>
</body>
</html>