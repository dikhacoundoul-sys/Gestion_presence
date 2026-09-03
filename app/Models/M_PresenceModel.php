<?php

namespace App\Models;

use CodeIgniter\Model;

class M_PresenceModel extends Model
{
    protected $table            = 'presences';
    protected $primaryKey       = 'id_presence';
    protected $allowedFields    = ['id_participant', 'id_evenement', 'jour_numero', 'date_presence', 'heure_arrivee', 'latitude_participant', 'longitude_participant', 'distance_metres'];
    protected $returnType       = 'array';
    protected $useTimestamps    = false;

    // Récupérer les émargements d'un événement (avec jointure participant)
    public function getPresencesByEvenement($idEvenement, $jourNumero = null)
    {
        $builder = $this->db->table('presences pr')
            ->select('pr.*, p.nom, p.prenom, p.email, p.structure, p.fonction')
            ->join('participants p', 'p.id_participant = pr.id_participant')
            ->where('pr.id_evenement', $idEvenement);

        if ($jourNumero !== null) {
            $builder->where('pr.jour_numero', $jourNumero);
        }
        return $builder->orderBy('pr.heure_arrivee', 'DESC')->get()->getResultArray();
    }
}