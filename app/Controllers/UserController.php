<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\AuditLog;

class UserController extends Controller
{
    private User     $userModel;
    private AuditLog $auditModel;

    public function __construct(\PDO $db)
    {
        parent::__construct($db);
        $this->userModel  = new User($db);
        $this->auditModel = new AuditLog($db);
    }

    public function index(): void
    {
        $this->requireAdmin();
        $users = $this->userModel->all('created_at DESC');
        $flash = $this->getFlash();
        $this->view('users/index', compact('users', 'flash'));
    }

    public function create(): void
    {
        $this->requireAdmin();
        $errors = [];
        $data   = ['nom'=>'','prenom'=>'','username'=>'','role'=>'operateur'];

        if ($this->isPost()) {
            $data = array_merge($data, $_POST);
            if (!trim($data['nom']))               $errors[] = t('err_nom_required');
            if (!trim($data['username']))          $errors[] = t('err_username_required');
            $pw = $this->input('password');
            if (!$pw)                              $errors[] = t('err_password_required');
            if ($pw && strlen($pw) < 6)            $errors[] = t('err_password_length');
            if (!$errors && $this->userModel->usernameExists($data['username'])) $errors[] = t('err_username_exists');

            if (!$errors) {
                $id = $this->userModel->create([
                    'nom'      => $data['nom'],
                    'prenom'   => $data['prenom'],
                    'username' => $data['username'],
                    'password' => password_hash($pw, PASSWORD_BCRYPT),
                    'role'     => $data['role'],
                ]);
                $this->auditModel->log('Création utilisateur', 'system', "User {$data['username']} créé");
                $this->flash('success', t('user_added'));
                $this->redirect('users');
            }
        }

        $this->view('users/create', compact('errors', 'data'));
    }

    public function edit(): void
    {
        $this->requireAdmin();
        $id   = (int)$this->query('id');
        $user = $this->userModel->find($id);
        if (!$user) { $this->flash('danger', t('user_not_found')); $this->redirect('users'); }

        $errors = [];
        $data   = $user;

        if ($this->isPost()) {
            $data = array_merge($data, $_POST);
            if (!trim($data['nom'])) $errors[] = t('err_nom_required');
            $pw = $this->input('password');
            if ($pw && strlen($pw) < 6) $errors[] = t('err_password_length');

            if (!$errors) {
                $fields = ['nom' => $data['nom'], 'prenom' => $data['prenom'], 'role' => $data['role']];
                if ($pw) $fields['password'] = password_hash($pw, PASSWORD_BCRYPT);
                $this->userModel->update($id, $fields);
                $this->auditModel->log('Modification utilisateur', 'system', "User {$user['username']} modifié");
                $this->flash('success', t('user_updated'));
                $this->redirect('users');
            }
        }

        $this->view('users/edit', compact('errors', 'data', 'id'));
    }

    public function delete(): void
    {
        $this->requireAdmin();
        $id = (int)$this->query('id');
        if ($id === (int)($_SESSION['user_id'] ?? 0)) {
            $this->flash('danger', t('cannot_delete_self'));
            $this->redirect('users');
        }
        $user = $this->userModel->find($id);
        if (!$user) { $this->flash('danger', t('user_not_found')); $this->redirect('users'); }

        $this->userModel->delete($id);
        $this->auditModel->log('Suppression utilisateur', 'system', "User {$user['username']} supprimé");
        $this->flash('success', t('user_deleted'));
        $this->redirect('users');
    }
}
