<?php

namespace App\Models;

use CodeIgniter\Model;

class M_AdminModel extends Model
{
    protected $table            = 'admins';
    protected $primaryKey       = 'id_admin';
    protected $allowedFields    = ['nom', 'prenom', 'email', 'telephone', 'password', 'role'];
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
    protected $createdField     = 'created_at';
}
