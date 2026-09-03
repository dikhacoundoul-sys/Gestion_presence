<?php

namespace App\Controllers;

use App\Models\M_EvenementModel;
use App\Models\M_ParticipantModel;
use App\Models\M_PresenceModel;

class C_AdminController extends BaseController
{
   public function dashboard()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
        $evenementModel = new M_EvenementModel();
        $db = \Config\Database::connect();
        //Récupération de la liste complète des événements
        $evenements = $evenementModel->findAll();

        $chartLabels = [];
        $chartData   = [];
        // Calcul des statistiques pour le graphique
        // On part de la table 'evenements' pour inclure aussi les événements sans émargement (0 présence)
        $builder = $db->table('evenements e')
                    ->select('e.titre, COUNT(p.id_presence) as total')
                    ->join('presences p', 'p.id_evenement = e.id_evenement', 'left')
                    ->groupBy('e.id_evenement');
        $stats = $builder->get()->getResultArray();
        foreach ($stats as $row) {
            $chartLabels[] = $row['titre'];
            $chartData[]   = (int)$row['total'];
        }
        // Envoi des données à la vue
        $data = [
            'evenements'  => $evenements,
            'chartLabels' => $chartLabels,
            'chartData'   => $chartData
        ];
        return view('admin/dashboard', $data);
    }
    // Traitement de la création d'un événement avec durée en jours
    public function storeEvenement()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
        $rules = [
            'titre'          => 'required|min_length[3]|max_length[40]',
            'date_evenement' => 'required',
            'duree_jours'    => 'required|integer|greater_than[0]',
            'lieu'           => 'required|max_length[50]',
            'latitude_lieu'  => 'permit_empty|decimal',
            'longitude_lieu' => 'permit_empty|decimal'
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $codeUnique = 'EVT-' . strtoupper(bin2hex(random_bytes(4))) . '-' . time();
        $evenementModel = new M_EvenementModel();
        // Récupération des valeurs GPS (null si non remplies)
        $lat = $this->request->getPost('latitude_lieu');
        $lng = $this->request->getPost('longitude_lieu');
        $evenementModel->insert([
            'titre'          => $this->request->getPost('titre'),
            'date_evenement' => $this->request->getPost('date_evenement'),
            'duree_jours'    => (int) $this->request->getPost('duree_jours'),
            'lieu'           => $this->request->getPost('lieu'),
            'latitude_lieu'  => (!empty($lat) && is_numeric($lat)) ? (float)$lat : null,
            'longitude_lieu' => (!empty($lng) && is_numeric($lng)) ? (float)$lng : null,
            'marge_tolerance_metres'=> 500, 
            'code_qr'        => $codeUnique,
            'id_admin'       => $session->get('id_admin')
        ]);
        return redirect()->to('/admin/dashboard')->with('success', 'Événement créé avec succès.');
    }
    // Formulaire de modification
    public function editEvenement($idEvenement)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
        $evenementModel = new M_EvenementModel();
        $evenement = $evenementModel->find($idEvenement);
        if (!$evenement) {
            return redirect()->to('/admin/dashboard')->with('errors', ['Événement introuvable.']);
        }
        if ($session->get('role') !== 'superadmin' && $evenement['id_admin'] != $session->get('id_admin')) {
            return redirect()->to('/admin/dashboard')->with('errors', ['Accès non autorisé.']);
        }
        $data['evenement'] = $evenement;
        return view('admin/edit_evenement', $data);
    }
    // Mise à jour d'un événement
    public function updateEvenement($idEvenement)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
        $evenementModel = new M_EvenementModel();
        $evenement = $evenementModel->find($idEvenement);
        if (!$evenement) {
            return redirect()->to('/admin/dashboard')->with('errors', ['Événement introuvable.']);
        }
        if ($session->get('role') !== 'superadmin' && $evenement['id_admin'] != $session->get('id_admin')) {
            return redirect()->to('/admin/dashboard')->with('errors', ['Accès non autorisé.']);
        }
        $rules = [
            'titre'          => 'required|min_length[3]|max_length[40]',
            'date_evenement' => 'required',
            'duree_jours'    => 'required|integer|greater_than[0]',
            'lieu'           => 'required|max_length[50]'
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $evenementModel->update($idEvenement, [
            'titre'          => $this->request->getPost('titre'),
            'date_evenement' => $this->request->getPost('date_evenement'),
            'duree_jours'    => (int) $this->request->getPost('duree_jours'),
            'lieu'           => $this->request->getPost('lieu')
        ]);
        return redirect()->to('/admin/dashboard')->with('success', 'Événement mis à jour avec succès.');
    }
    // Suppression d'un événement
    public function deleteEvenement($idEvenement)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
        $evenementModel = new M_EvenementModel();
        $evenement = $evenementModel->find($idEvenement);
        if (!$evenement) {
            return redirect()->to('/admin/dashboard')->with('errors', ['Événement introuvable.']);
        }
        if ($session->get('role') !== 'superadmin' && $evenement['id_admin'] != $session->get('id_admin')) {
            return redirect()->to('/admin/dashboard')->with('errors', ['Accès non autorisé.']);
        }
        $evenementModel->delete($idEvenement);
        return redirect()->to('/admin/dashboard')->with('success', 'Événement supprimé avec succès.');
    }
    // Consultation multi-jours des présences
    public function voirPresences($idEvenement)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
        $evenementModel   = new M_EvenementModel();
        $participantModel = new M_ParticipantModel();
        $presenceModel    = new M_PresenceModel();

        $evenement = $evenementModel->find($idEvenement);
        if (!$evenement) {
            return redirect()->to('/admin/dashboard')->with('error', 'Événement introuvable.');
        }
        // Récupérer les participants inscrits à cet événement
        $participants = $participantModel->where('id_evenement', $idEvenement)->findAll();
        // Récupérer tous les émargements
        $presencesBrutes = $presenceModel->where('id_evenement', $idEvenement)->findAll();
        // Organiser les présences sous la forme [id_participant][jour_numero] = heure_arrivee
        $tableauPresences = [];
        foreach ($presencesBrutes as $p) {
            $tableauPresences[$p['id_participant']][$p['jour_numero']] = $p['heure_arrivee'];
        }
        $data = [
            'evenement'        => $evenement,
            'participants'     => $participants,
            'tableauPresences' => $tableauPresences
        ];
        return view('admin/presences', $data);
    }
    // Exportation en rapport PDF Multi-jours
    public function exportPDF($idEvenement)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
        $evenementModel   = new M_EvenementModel();
        $participantModel = new M_ParticipantModel();
        $presenceModel    = new M_PresenceModel();
        $evenement = $evenementModel->find($idEvenement);
        if (!$evenement) {
            return redirect()->to('/admin/dashboard');
        }
        $participants = $participantModel->where('id_evenement', $idEvenement)->findAll();
        $presencesBrutes = $presenceModel->where('id_evenement', $idEvenement)->findAll();
        $tableauPresences = [];
        foreach ($presencesBrutes as $p) {
            $tableauPresences[$p['id_participant']][$p['jour_numero']] = $p['heure_arrivee'];
        }
        $data = [
            'evenement'        => $evenement,
            'participants'     => $participants,
            'tableauPresences' => $tableauPresences
        ];
        $html = view('admin/pdf_rapport', $data);
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape'); // Format paysage recommandé pour afficher plusieurs jours
        $dompdf->render();
        $fileName = 'Rapport_Presence_' . preg_replace('/[^A-Za-z0-9\-]/', '_', $evenement['titre']) . '.pdf';
        $dompdf->stream($fileName, ["Attachment" => true]);
        exit();
    }
    // Exportation en CSV/Excel avec colonnes dynamiques par jour
    public function exportExcel($idEvenement)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
        $evenementModel   = new M_EvenementModel();
        $participantModel = new M_ParticipantModel();
        $presenceModel    = new M_PresenceModel();
        $evenement = $evenementModel->find($idEvenement);
        if (!$evenement) {
            return redirect()->to('/admin/dashboard');
        }
        $participants = $participantModel->where('id_evenement', $idEvenement)->findAll();
        $presencesBrutes = $presenceModel->where('id_evenement', $idEvenement)->findAll();
        $tableauPresences = [];
        foreach ($presencesBrutes as $p) {
            $tableauPresences[$p['id_participant']][$p['jour_numero']] = $p['heure_arrivee'];
        }
        $fileName = 'Presences_' . preg_replace('/[^A-Za-z0-9\-]/', '_', $evenement['titre']) . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$fileName");
        header("Content-Type: application/csv; charset=UTF-8");
        $file = fopen('php://output', 'w');
        fputs($file, "\xEF\xBB\xBF"); // UTF-8 BOM pour Excel
        // En-têtes fixes
        $headers = ['N°', 'Nom', 'Prénom', 'Email', 'Téléphone', 'Structure', 'Fonction'];
        // En-têtes dynamiques (Jour 1, Jour 2, etc.)
        for ($j = 1; $j <= $evenement['duree_jours']; $j++) {
            $headers[] = 'Jour ' . $j;
        }
        fputcsv($file, $headers);
        // Données des lignes
        foreach ($participants as $index => $p) {
            $row = [
                $index + 1,
                $p['nom'],
                $p['prenom'],
                $p['email'],
                $p['telephone'],
                $p['structure'],
                $p['fonction']
            ];
            // Remplissage des statuts de présence par jour
            for ($j = 1; $j <= $evenement['duree_jours']; $j++) {
                if (isset($tableauPresences[$p['id_participant']][$j])) {
                    $row[] = date('H:i:s - d/m/Y', strtotime($tableauPresences[$p['id_participant']][$j]));
                } else {
                    $row[] = 'Absent';
                }
            }
            fputcsv($file, $row);
        }
        fclose($file);
        exit();
    }
    public function showQrCode($id_evenement)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
        $evenementModel = new M_EvenementModel();
        $evenement = $evenementModel->find($id_evenement);
        if (!$evenement) {
            return redirect()->to('/admin/dashboard')->with('error', 'Événement introuvable.');
        }
        return view('admin/qrcode_fullscreen', ['evenement' => $evenement]);
    }
}