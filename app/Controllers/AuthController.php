<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

// Gère l'authentification : affichage du formulaire de connexion,
// vérification des identifiants, création/destruction de la session.
class AuthController extends Controller
{
    protected string $layout = 'auth'; // layout simplifié (sans menu admin)

    // Affiche le formulaire de connexion (GET /login)
    public function loginForm(): void
    {
        if (!empty($_SESSION['user_id'])) {
            $this->redirect('dashboard');
        }
        $lang  = $_SESSION['lang'] ?? 'fr';
        $isRtl = $lang === 'ar';
        $langSwitchBase = BASE_URL . '/?page=login&lang=';
        $this->view('auth/login', compact('lang', 'isRtl', 'langSwitchBase'));
    }

    // Traite la soumission du formulaire de connexion (POST /login)
    public function login(): void
    {
        if (!empty($_SESSION['user_id'])) {
            $this->redirect('dashboard');
        }

        $lang  = $_SESSION['lang'] ?? 'fr';
        $isRtl = $lang === 'ar';
        $langSwitchBase = BASE_URL . '/?page=login&lang=';
        $error = '';

        if ($this->isPost()) {
            $username = trim($this->input('username'));
            $password = trim($this->input('password'));

            if ($username && $password) {
                // Recherche l'utilisateur par son nom d'utilisateur
                $userModel = new User($this->db);
                $user      = $userModel->findByUsername($username);

                // Vérifie le mot de passe haché (bcrypt) avec password_verify()
                if ($user && password_verify($password, $user['password'])) {
                    // Authentification réussie : on stocke les infos en session
                    $_SESSION['user_id']   = $user['id'];
                    $_SESSION['username']  = $user['username'];
                    $_SESSION['user_role'] = $user['role'];
                    $_SESSION['user_nom']  = $user['nom'] . ' ' . $user['prenom'];
                    $this->redirect('dashboard');
                } else {
                    $error = t('error_invalid'); // identifiants incorrects
                }
            } else {
                $error = t('error_fields'); // champs manquants
            }
        }

        $this->view('auth/login', compact('lang', 'isRtl', 'langSwitchBase', 'error'));
    }

    // Déconnexion : détruit la session courante
    public function logout(): void
    {
        session_destroy();
        $this->layout = 'auth';
        $this->view('auth/logout', []);
    }
}
