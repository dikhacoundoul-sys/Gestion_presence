<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scanner pour émarger - <?= esc($evenement['titre']); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        body, html {
            height: 100%;
            background-color: #0f172a;
            color: #ffffff;
        }
        .qrcode-container img, .qrcode-container canvas {
            margin: 0 auto;
            border: 12px solid #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center text-center">
<div class="container p-4">
    <h1 class="display-4 fw-bold text-warning mb-2"><?= esc($evenement['titre']); ?></h1>
    <p class="fs-4 text-light mb-4">
        <?= esc($evenement['lieu']); ?> &nbsp;|&nbsp; 
        <?= date('d/m/Y à H:i', strtotime($evenement['date_evenement'])); ?>
    </p>
    <div class="my-4 d-flex justify-content-center">
        <div id="qrcode-display" class="qrcode-container"></div>
    </div>
    <p class="fs-3 mt-4 text-info fw-semibold">
        Scannez ce QR Code avec votre téléphone pour enregistrer votre présence
    </p>
    <button onclick="window.close()" class="btn btn-outline-light mt-3">Fermer la fenêtre</button>
</div>
<script>
    new QRCode(document.getElementById("qrcode-display"), {
        text: "<?= base_url('presence/emarger/' . $evenement['code_qr']); ?>",
        width: 320,
        height: 320
    });
</script>
</body>
</html>