<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller
{
    protected string $layout = 'auth';

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
                $userModel = new User($this->db);
                $user      = $userModel->findByUsername($username);

                if ($user && password_verify($password, $user['password'])) {
                    $_SESSION['user_id']   = $user['id'];
                    $_SESSION['username']  = $user['username'];
                    $_SESSION['user_role'] = $user['role'];
                    $_SESSION['user_nom']  = $user['nom'] . ' ' . $user['prenom'];
                    $this->redirect('dashboard');
                } else {
                    $error = t('error_invalid');
                }
            } else {
                $error = t('error_fields');
            }
        }

        $this->view('auth/login', compact('lang', 'isRtl', 'langSwitchBase', 'error'));
    }

    public function logout(): void
    {
        session_destroy();
        $this->layout = 'auth';
        $this->view('auth/logout', []);
    }
}
