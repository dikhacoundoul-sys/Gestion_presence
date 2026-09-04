<?php

namespace App\Controllers;

use App\Models\M_AdminModel;

class C_Auth extends BaseController
{
    public function login()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/admin/dashboard');
        }
        return view('auth/login');
    }
    public function attemptLogin()
    {
        $email    = strtolower(trim($this->request->getPost('email') ?? ''));
        $password = (string) ($this->request->getPost('password') ?? '');

        $adminModel = new M_AdminModel();
        $admin      = $adminModel->where('email', $email)->first();
        if (! $admin) {
            return redirect()->back()->with('error', 'Identifiants invalides');
        }
        if (! password_verify($password, $admin['password'])) {
            return redirect()->back()->with('error', 'Identifiants invalides');
        }
        session()->set([
            'admin_id'   => $admin['id'],
            'email'      => $admin['email'],
            'isLoggedIn' => true,
        ]);
        return redirect()->to('/admin/dashboard');
    }
    public function resetSuperAdmin()
    {
        $adminModel = new M_AdminModel();

        $email = 'amadouba@education.sn'; 
        $nouveauPassword = 'Admin@2026!';
        $admin = $adminModel->where('email', strtolower(trim($email)))->first();
        if (!$admin) {
            return "Erreur : L'email '$email' n'existe pas dans la table admins.";
        }
        $hash = password_hash($nouveauPassword, PASSWORD_BCRYPT);
        $adminModel->update($admin['id_admin'], [
            'password' => $hash,
            'role'     => 'superadmin'
        ]);
        return "Succès ! Le mot de passe pour <b>{$email}</b> a été réinitialisé à : <b>{$nouveauPassword}</b>";
    }
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}