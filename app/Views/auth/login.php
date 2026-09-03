<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Administration</title>
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #d1d5db; /* Fond gris clair comme la maquette */
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
        }

        .login-card {
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            max-width: 900px;
            width: 100%;
            border: none;
        }

        .login-img {
            background-image: url('<?= base_url("assets/images/presence.jpeg"); ?>');
            background-size: cover;
            background-position: center;
            min-height: 480px;
        }

        .login-form-container {
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            font-size: 1.4rem;
            color: #111827;
            margin-bottom: 25px;
        }

        .brand-logo i {
            color: #ef4444;
            font-size: 1.6rem;
        }

        .login-title {
            font-weight: 600;
            color: #111827;
            font-size: 1.3rem;
            margin-bottom: 25px;
        }

        .form-control {
            border: 1px solid #e5e7eb;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 0.95rem;
            background-color: #ffffff;
        }

        .form-control:focus {
            border-color: #000000;
            box-shadow: none;
        }

        .btn-black {
            background-color: #000000;
            color: #ffffff;
            font-weight: 600;
            padding: 12px;
            border-radius: 8px;
            border: none;
            transition: background-color 0.2s ease;
        }

        .btn-black:hover {
            background-color: #1f2937;
            color: #ffffff;
        }

        .login-footer-links {
            margin-top: 20px;
            font-size: 0.85rem;
            color: #6b7280;
        }

        .login-footer-links a {
            color: #3f51b5;
            text-decoration: none;
            font-weight: 500;
        }

        .login-footer-links a:hover {
            text-decoration: underline;
        }

        .terms-text {
            margin-top: 30px;
            font-size: 0.75rem;
            color: #9ca3af;
        }
    </style>
</head>
<body>

<div class="card login-card">
    <div class="row g-0">
        <!-- Colonne Gauche : Image -->
        <div class="col-md-6 d-none d-md-block login-img"></div>

        <!-- Colonne Droite : Formulaire -->
        <div class="col-md-6 login-form-container">
            
            <!-- Logo & En-tête -->
            <div class="brand-logo">
                <i class="bi bi-qr-code-scan"></i>
                <span>PresenceApp</span>
            </div>

            <h5 class="login-title">Connexion Administration</h5>

            <!-- Gestion des messages Flash -->
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger py-2 border-0 rounded-3 small mb-3">
                    <?= session()->getFlashdata('error'); ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger py-2 border-0 rounded-3 small mb-3">
                    <ul class="mb-0 ps-3">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Formulaire inchangé -->
            <form action="<?= base_url('auth/attemptLogin'); ?>" method="post">
                <?= csrf_field(); ?>

                <div class="mb-3">
                    <input type="email" name="email" id="email" class="form-control" value="<?= old('email'); ?>" placeholder="Adresse Email" required autofocus>
                </div>

                <div class="mb-3">
                    <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn btn-black w-100 mt-2">Se connecter</button>
            </form>
            <div class="terms-text">
                Conditions d'utilisation. Politique de confidentialité.
            </div>

        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>