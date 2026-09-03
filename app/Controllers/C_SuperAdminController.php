<?php

namespace App\Controllers;

use App\Models\M_AdminModel;

class C_SuperAdminController extends BaseController
{
    // Page pour lister et ajouter des administrateurs
    public function utilisateurs()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'superadmin') {
            return redirect()->to('/admin/dashboard')->with('error', 'Accès réservé au SuperAdmin.');
        }
        $adminModel = new M_AdminModel();
        $data['utilisateurs'] = $adminModel->findAll();
        return view('superadmin/liste_utilisateurs', $data);
    }
    public function creer()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'superadmin') {
            return redirect()->to('/admin/dashboard');
        }
        // S'assurer que le fichier s'appelle bien app/Views/superadmin/creer_utilisateur.php
        return view('superadmin/creer_utilisateur');
    }
    // Création d'un nouvel administrateur par le SuperAdmin
    public function storeUtilisateur()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'superadmin') {
            return redirect()->to('/admin/dashboard');
        }
        $rules = [
            'nom'       => 'required|min_length[2]|max_length[30]',
            'prenom'    => 'required|min_length[2]|max_length[30]',
            'email'     => 'required|valid_email|is_unique[admins.email]',
            'telephone' => 'required|min_length[8]|max_length[20]',
            'password'  => 'required|min_length[6]',
            'role'      => 'required|in_list[admin,superadmin]'
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $adminModel = new M_AdminModel();
        $adminModel->insert([
            'nom'       => trim($this->request->getPost('nom')),
            'prenom'    => trim($this->request->getPost('prenom')),
            'email'     => strtolower(trim($this->request->getPost('email'))),
            'telephone' => trim($this->request->getPost('telephone')),
            'password'  => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'role'      => $this->request->getPost('role')
        ]);
        return redirect()->to('/superadmin/liste_utilisateurs')->with('success', 'Nouvel utilisateur créé avec succès !');
    }
}