<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Client;
use App\Models\AuditLog;

class ClientController extends Controller
{
    private Client   $clientModel;
    private AuditLog $auditModel;

    public function __construct(\PDO $db)
    {
        parent::__construct($db);
        $this->clientModel = new Client($db);
        $this->auditModel  = new AuditLog($db);
    }

    public function index(): void
    {
        $this->requireAuth();
        $search = trim($this->query('search'));
        $statut = $this->query('statut');
        $page   = max(1, (int)$this->query('p', 1));
        $result = $this->clientModel->paginatedList($search, $statut, 15, $page);
        $flash  = $this->getFlash();
        $this->view('clients/index', array_merge($result, compact('search', 'statut', 'flash')));
    }

    public function create(): void
    {
        $this->requireAuth();
        $errors = [];
        $data   = [
            'nom' => '', 'prenom' => '', 'email' => '', 'telephone' => '',
            'adresse' => '', 'ville' => '', 'cin' => '',
            'permis_numero' => '', 'permis_categorie' => 'B', 'permis_expiration' => '',
            'type_client' => 'particulier', 'entreprise' => '',
            'statut' => 'actif', 'notes' => '',
        ];

        if ($this->isPost()) {
            $data = array_merge($data, $_POST);
            if (!trim($data['nom']))    $errors[] = 'Le nom est obligatoire.';
            if (!trim($data['prenom'])) $errors[] = 'Le prénom est obligatoire.';

            if (!$errors) {
                $id = $this->clientModel->create([
                    'nom'               => $data['nom'],
                    'prenom'            => $data['prenom'],
                    'email'             => $data['email'],
                    'telephone'         => $data['telephone'],
                    'adresse'           => $data['adresse'],
                    'ville'             => $data['ville'],
                    'cin'               => $data['cin'],
                    'permis_numero'     => $data['permis_numero'],
                    'permis_categorie'  => $data['permis_categorie'],
                    'permis_expiration' => $data['permis_expiration'] ?: null,
                    'type_client'       => $data['type_client'],
                    'entreprise'        => $data['entreprise'],
                    'statut'            => $data['statut'],
                    'notes'             => $data['notes'],
                ]);
                $this->auditModel->log('Création client', 'clients', "{$data['nom']} {$data['prenom']} ajouté (ID:$id)");
                $this->flash('success', "Client {$data['nom']} {$data['prenom']} ajouté.");
                $this->redirect('clients');
            }
        }

        $this->view('clients/create', compact('errors', 'data'));
    }

    public function edit(): void
    {
        $this->requireAuth();
        $id     = (int)$this->query('id');
        $client = $this->clientModel->find($id);
        if (!$client) { $this->flash('danger', 'Client introuvable.'); $this->redirect('clients'); }

        $errors = [];
        $data   = $client;

        if ($this->isPost()) {
            $data = array_merge($data, $_POST);
            if (!trim($data['nom']))    $errors[] = 'Le nom est obligatoire.';
            if (!trim($data['prenom'])) $errors[] = 'Le prénom est obligatoire.';

            if (!$errors) {
                $this->clientModel->update($id, [
                    'nom'               => $data['nom'],
                    'prenom'            => $data['prenom'],
                    'email'             => $data['email'],
                    'telephone'         => $data['telephone'],
                    'adresse'           => $data['adresse'],
                    'ville'             => $data['ville'],
                    'cin'               => $data['cin'],
                    'permis_numero'     => $data['permis_numero'],
                    'permis_categorie'  => $data['permis_categorie'],
                    'permis_expiration' => $data['permis_expiration'] ?: null,
                    'type_client'       => $data['type_client'],
                    'entreprise'        => $data['entreprise'],
                    'statut'            => $data['statut'],
                    'notes'             => $data['notes'],
                ]);
                $this->auditModel->log('Modification client', 'clients', "{$data['nom']} {$data['prenom']} modifié");
                $this->flash('success', 'Client mis à jour.');
                $this->redirect('clients');
            }
        }

        $this->view('clients/edit', compact('errors', 'data', 'id'));
    }

    public function show(): void
    {
        $this->requireAuth();
        $id     = (int)$this->query('id');
        $client = $this->clientModel->find($id);
        if (!$client) { $this->flash('danger', 'Client introuvable.'); $this->redirect('clients'); }

        $stmt = $this->db->prepare(
            "SELECT r.*, v.marque, v.modele, v.numero FROM reservations r
             JOIN vehicles v ON v.id = r.vehicle_id
             WHERE r.client_id = ? ORDER BY r.date_debut DESC"
        );
        $stmt->execute([$id]);
        $reservations = $stmt->fetchAll();

        $this->view('clients/show', compact('client', 'reservations'));
    }

    public function delete(): void
    {
        $this->requireAuth();
        $id     = (int)$this->query('id');
        $client = $this->clientModel->find($id);
        if (!$client) { $this->flash('danger', 'Client introuvable.'); $this->redirect('clients'); }

        $this->clientModel->delete($id);
        $this->auditModel->log('Suppression client', 'clients', "{$client['nom']} {$client['prenom']} supprimé");
        $this->flash('success', "Client {$client['nom']} supprimé.");
        $this->redirect('clients');
    }
}
