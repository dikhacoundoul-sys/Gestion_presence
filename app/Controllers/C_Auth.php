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
    $email = strtolower(trim($this->request->getPost('email')));
    $password = (string) $this->request->getPost('password');

    try {
        $adminModel = new \App\Models\M_AdminModel();
        $admin = $adminModel->where('email', $email)->first();
    } catch (\Throwable $e) {
        // Enregistre l'erreur réelle dans les logs de Render
        log_message('error', 'Erreur BDD Login: ' . $e->getMessage());
        
        // Retourne un message propre à l'utilisateur
        return redirect()->back()->withInput()->with('error', 'Erreur de connexion à la base de données : ' . $e->getMessage());
    }

    // S'assurer que l'utilisateur existe
    if (!$admin) {
        return redirect()->back()->withInput()->with('error', 'Cet email n\'existe pas en BDD.');
    }

    // Vérification explicite du hash
    if (password_verify($password, $admin['password'])) {
        session()->set([
            'id_admin'   => $admin['id_admin'] ?? $admin['id'],
            'nom'        => $admin['nom'],
            'prenom'     => $admin['prenom'],
            'email'      => $admin['email'],
            'role'       => $admin['role'],
            'isLoggedIn' => true
            ]);
            return redirect()->to('/admin/dashboard');
        }

        return redirect()->back()->withInput()->with('error', 'Mot de passe incorrect.');
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