<?php

namespace App\Controllers;

use App\Models\M_AdminModel;

class C_ProfilController extends BaseController
{
    public function index()
    {
        return view('profil/index');
    }
    public function changerPassword()
    {
        return view('profil/changer_password');
    }
    public function updatePassword()
    {
        $session = session();
        $idUtilisateur = $session->get('id_admin'); // Adaptez la clé de session selon votre code
        $rules = [
            'old_password' => 'required',
            'new_password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[new_password]'
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $model = new M_AdminModel();
        $user = $model->find($idUtilisateur);
        // Vérification de l'ancien mot de passe
        if (!password_verify($this->request->getPost('old_password'), $user['password'])) {
            return redirect()->back()->with('errors', ['L\'ancien mot de passe est incorrect.']);
        }
        // Mise à jour avec le nouveau mot de passe haché
        $model->update($idUtilisateur, [
            'password' => password_hash($this->request->getPost('new_password'), PASSWORD_BCRYPT)
        ]);
        return redirect()->to(base_url('profil'))->with('success', 'Mot de passe modifié avec succès !');
    }
}