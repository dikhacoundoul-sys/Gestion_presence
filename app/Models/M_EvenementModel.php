<?php

namespace App\Models;

use CodeIgniter\Model;

class M_EvenementModel extends Model
{
    protected $table            = 'evenements';
    protected $primaryKey       = 'id_evenement';
    protected $allowedFields    = ['titre', 'date_evenement', 'duree_jours', 'lieu', 'latitude_lieu', 'longitude_lieu', 'marge_tolerance_metres', 'code_qr', 'id_admin'];
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
    protected $createdField     = 'created_at';

    // Méthode helper pour récupérer un événement via son code QR unique
    public function getByCodeQR($codeQR)
    {
        return $this->where('code_qr', $codeQR)->first();
    }
}