<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'C_Auth::login');
$routes->get('login', 'C_Auth::login');
$routes->post('auth/attemptLogin', 'C_Auth::attemptLogin');
$routes->get('reset-superadmin', 'C_Auth::resetSuperAdmin');
$routes->get('logout', 'C_Auth::logout');
$routes->get('presence/emarger/(:segment)', 'C_PresenceController::emarger/$1');
$routes->post('presence/enregistrer', 'C_PresenceController::enregistrer');
$routes->get('presence/succes', 'C_PresenceController::succes');
$routes->get('admin/dashboard', 'C_AdminController::dashboard');
$routes->post('admin/evenement/store', 'C_AdminController::storeEvenement');
$routes->get('admin/evenement/edit/(:num)', 'C_AdminController::editEvenement/$1');
$routes->post('admin/evenement/update/(:num)', 'C_AdminController::updateEvenement/$1');
$routes->get('admin/evenement/delete/(:num)', 'C_AdminController::deleteEvenement/$1');
$routes->get('admin/evenement/qrcode/(:num)', 'C_AdminController::showQrCode/$1');
$routes->get('admin/presences/(:num)', 'C_AdminController::voirPresences/$1');
$routes->get('admin/export/pdf/(:num)', 'C_AdminController::exportPDF/$1');
$routes->get('admin/export/excel/(:num)', 'C_AdminController::exportExcel/$1');
$routes->group('superadmin', ['filter' => 'superAdminFilter'], function($routes) {
    $routes->get('utilisateurs', 'C_SuperAdminController::utilisateurs');
    $routes->get('creer_utilisateur', 'C_SuperAdminController::creer');
    $routes->post('enregistrer-utilisateur', 'C_SuperAdminController::storeUtilisateur');
});
$routes->get('profil', 'C_ProfilController::index');
$routes->get('profil/changer-password', 'C_ProfilController::changerPassword');
$routes->post('profil/update-password', 'C_ProfilController::updatePassword');