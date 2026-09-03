<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport de Présences - <?= esc($evenement['titre']); ?></title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #333;
        }
        .header {
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        .header p {
            margin: 2px 0;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 6px 4px;
            text-align: left;
            vertical-align: middle;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
        }
        .text-center {
            text-align: center;
        }
        .badge-present {
            color: #155724;
            background-color: #d4edda;
            padding: 3px 5px;
            border-radius: 3px;
            font-weight: bold;
        }
        .badge-absent {
            color: #721c24;
            background-color: #f8d7da;
            padding: 3px 5px;
            border-radius: 3px;
        }
        .signature-img {
            max-height: 30px;
            max-width: 80px;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 8px;
            color: #777;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Rapport d'émargement - <?= esc($evenement['titre']); ?></h1>
        <p><strong>Lieu :</strong> <?= esc($evenement['lieu']); ?> | <strong>Date de début :</strong> <?= date('d/m/Y', strtotime($evenement['date_evenement'])); ?> | <strong>Durée :</strong> <?= esc($evenement['duree_jours']); ?> jour(s)</p>
        <p><strong>Total des inscrits/présents :</strong> <?= count($participants); ?> participant(s)</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 25px;">#</th>
                <th>Nom & Prénom</th>
                <th>Structure / Fonction</th>
                <th>Email & Téléphone</th>
                <th class="text-center" style="width: 70px;">Signature</th>
                <!-- Colonnes dynamiques des jours -->
                <?php for ($j = 1; $j <= $evenement['duree_jours']; $j++): ?>
                    <th class="text-center">Jour <?= $j; ?></th>
                <?php endfor; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($participants)): ?>
                <?php foreach ($participants as $index => $p): ?>
                <tr>
                    <td class="text-center"><?= $index + 1; ?></td>
                    <td><strong><?= esc($p['nom']) . ' ' . esc($p['prenom']); ?></strong></td>
                    <td>
                        <?= esc($p['structure']); ?><br>
                        <small style="color: #666;"><?= esc($p['fonction']); ?></small>
                    </td>
                    <td>
                        <?= esc($p['email']); ?><br>
                        <small style="color: #666;"><?= esc($p['telephone']); ?></small>
                    </td>
                    <td class="text-center">
                        <?php if (!empty($p['marge']) && strstr($p['marge'], 'data:image')): ?>
                            <img src="<?= $p['marge']; ?>" class="signature-img" alt="Sig">
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>

                    <!-- Vérification présence multi-jours -->
                    <?php for ($j = 1; $j <= $evenement['duree_jours']; $j++): ?>
                        <td class="text-center">
                            <?php if (isset($tableauPresences[$p['id_participant']][$j])): ?>
                                <span class="badge-present">
                                    <?= date('H:i', strtotime($tableauPresences[$p['id_participant']][$j])); ?>
                                </span>
                            <?php else: ?>
                                <span class="badge-absent">Absent</span>
                            <?php endif; ?>
                        </td>
                    <?php endfor; ?>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="<?= 5 + $evenement['duree_jours']; ?>" class="text-center" style="padding: 20px;">
                        Aucun participant n'a été enregistré pour cet événement.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        Généré le <?= date('d/m/Y à H:i'); ?> - Système de Gestion de Présence
    </div>

</body>
</html>