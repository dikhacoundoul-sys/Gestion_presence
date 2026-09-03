<?php

namespace App\Controllers;

use App\Models\M_EvenementModel;
use App\Models\M_ParticipantModel;
use App\Models\M_PresenceModel;
use DateTime;

class C_PresenceController extends BaseController
{
    // Afficher le formulaire d'émargement au scan du QR Code
    public function emarger($codeQR = null)
    {
        $this->response->setHeader('ngrok-skip-browser-warning', true);
        if (empty($codeQR)) {
            return redirect()->to('/')->with('error', 'Code QR manquant.');
        }
        $evenementModel = new M_EvenementModel();
        $evenement      = $evenementModel->getByCodeQR($codeQR);
        if (!$evenement) {
            return redirect()->to('/')->with('error', 'Événement introuvable ou QR Code invalide.');
        }
        $dejaEmarge = session()->get('emarge_evt_' . $evenement['id_evenement']);
        $data = [
            'evenement'  => $evenement,
            'dejaEmarge' => $dejaEmarge
        ];
        return view('presence/formulaire', $data);
    }
    // Traiter la soumission du formulaire d'émargement
    public function enregistrer()
    {
        $participantModel = new M_ParticipantModel();
        $evenementModel   = new M_EvenementModel();
        $presenceModel    = new M_PresenceModel();
        // Règles de validation
        $rules = [
            'id_evenement'          => 'required|integer',
            'nom'                   => 'required|min_length[2]|max_length[50]',
            'prenom'                => 'required|min_length[2]|max_length[50]',
            'structure'             => 'required|min_length[2]|max_length[50]',
            'fonction'              => 'required|min_length[2]|max_length[50]',
            'email'                 => 'required|valid_email|max_length[100]',
            'telephone'             => 'required|min_length[8]|max_length[20]',
            'marge'                 => 'required',
            'latitude_participant'  => 'required|numeric',
            'longitude_participant' => 'required|numeric'
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $idEvenement = (int) $this->request->getPost('id_evenement');
        $email       = trim($this->request->getPost('email'));
        $telephone   = trim($this->request->getPost('telephone'));
        $userLat     = (float) $this->request->getPost('latitude_participant');
        $userLng     = (float) $this->request->getPost('longitude_participant');
        // Récupérer l'événement
        $evenement = $evenementModel->find($idEvenement);
        if (!$evenement) {
            return redirect()->to('/')->with('error', 'Événement inexistant.');
        }
        // --- CALCUL DU NUMÉRO DU JOUR ACTUEL ---
        $dateDebut = new DateTime(date('Y-m-d', strtotime($evenement['date_evenement'])));
        $dateAujourdhui = new DateTime(date('Y-m-d'));
        $interval = $dateDebut->diff($dateAujourdhui);
        $jourActuel = $interval->days + 1;
        // Contrôle sur la période de l'événement
        if ($dateAujourdhui < $dateDebut) {
            return redirect()->back()->withInput()->with('error', "L'événement n'a pas encore démarré.");
        }
        if ($jourActuel > (int)$evenement['duree_jours']) {
            return redirect()->back()->withInput()->with('error', "L'événement de {$evenement['duree_jours']} jour(s) est déjà terminé.");
        }
        // --- SÉCURITÉ GPS : VÉRIFICATION DE LA DISTANCE ---
        $evtLat      = (float) ($evenement['latitude_lieu'] ?? 0);
        $evtLng      = (float) ($evenement['longitude_lieu'] ?? 0);
        $margeMetres = (float) ($evenement['marge_tolerance_metres'] ?? 500);

        if ($evtLat == 0 || $evtLng == 0) {
            return redirect()->back()->withInput()->with('error_gps', 
                "Les coordonnées GPS de cet événement n'ont pas encore été configurées par l'administrateur."
            );
        }
        $distance = $this->calculerDistanceHaversine($userLat, $userLng, $evtLat, $evtLng);
        if ($distance > $margeMetres) {
            return redirect()->back()->withInput()->with('error_gps', 
                sprintf("Émargement refusé. Vous n'êtes pas dans le bon Lieu")
            );
        }
        // --- INSCRIPTION OU RÉCUPÉRATION DU PARTICIPANT ---
        $participant = $participantModel->where('id_evenement', $idEvenement)
                                         ->groupStart()
                                             ->where('email', $email)
                                             ->orWhere('telephone', $telephone)
                                         ->groupEnd()
                                         ->first();

        if (!$participant) {
            // Création du profil participant (Incrémentation automatique pour l'événement)
            $participantData = [
                'id_evenement' => $idEvenement,
                'nom'          => trim($this->request->getPost('nom')),
                'prenom'       => trim($this->request->getPost('prenom')),
                'structure'    => trim($this->request->getPost('structure')),
                'fonction'     => trim($this->request->getPost('fonction')),
                'email'        => $email,
                'telephone'    => $telephone,
                'marge'        => trim($this->request->getPost('marge'))
            ];
            $idParticipant = $participantModel->insert($participantData);
        } else {
            $idParticipant = $participant['id_participant'];
        }
        // --- CONTRÔLE ANTI-DOUBLON POUR LE JOUR ACTUEL ---
        $dejaEmargeAujourdhui = $presenceModel->where([
            'id_participant' => $idParticipant,
            'id_evenement'   => $idEvenement,
            'jour_numero'    => $jourActuel
        ])->first();
        if ($dejaEmargeAujourdhui) {
            return redirect()->back()->withInput()->with('warning', "Vous avez déjà émargé pour le Jour {$jourActuel}.");
        }
        // --- ENREGISTREMENT DE LA PRÉSENCE DU JOUR ---
        $presenceData = [
            'id_participant'       => $idParticipant,
            'id_evenement'         => $idEvenement,
            'jour_numero'          => $jourActuel,
            'date_presence'        => date('Y-m-d'),
            'heure_arrivee'        => date('Y-m-d H:i:s'),
            'latitude_participant' => $userLat,
            'longitude_participant'=> $userLng,
            'distance_metres'      => round($distance, 2)
        ];
        $presenceModel->insert($presenceData);
        session()->set('emarge_evt_' . $idEvenement . '_j' . $jourActuel, true);
        return redirect()->to('/presence/succes')->with('success', "Votre présence pour le Jour {$jourActuel} a été enregistrée avec succès.");
    }
    // Calcul Haversine (Mètres)
    private function calculerDistanceHaversine($lat1, $lon1, $lat2, $lon2)
    {
        $rayonTerre = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
             
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $rayonTerre * $c;
    }
    // Page de succès
    public function succes()
    {
        return view('presence/succes');
    }
}