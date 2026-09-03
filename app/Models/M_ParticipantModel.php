<?php

namespace App\Models;

use CodeIgniter\Model;

class M_ParticipantModel extends Model
{
    protected $table            = 'participants';
    protected $primaryKey       = 'id_participant';
    protected $allowedFields    = ['id_evenement', 'nom', 'prenom', 'structure', 'fonction', 'email', 'telephone', 'marge'];
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
    protected $createdField     = 'created_at';
}