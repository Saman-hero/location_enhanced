<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Incident;
use App\Models\Vehicle;
use App\Models\Client;
use App\Models\AuditLog;

class IncidentController extends Controller
{
    private Incident $incidentModel;
    private Vehicle  $vehicleModel;
    private Client   $clientModel;
    private AuditLog $auditModel;

    public function __construct(\PDO $db)
    {
        parent::__construct($db);
        $this->incidentModel = new Incident($db);
        $this->vehicleModel  = new Vehicle($db);
        $this->clientModel   = new Client($db);
        $this->auditModel    = new AuditLog($db);
    }

    public function index(): void
    {
        $this->requireAuth();
        $search = trim($this->query('search'));
        $statut = $this->query('statut');
        $page   = max(1, (int)$this->query('p', 1));
        $result = $this->incidentModel->paginatedList($search, $statut, 15, $page);
        $flash  = $this->getFlash();
        $this->view('incidents/index', array_merge($result, compact('search', 'statut', 'flash')));
    }

    public function create(): void
    {
        $this->requireAuth();
        $errors   = [];
        $vehicles = $this->db->query("SELECT id,numero,marque,modele FROM vehicles ORDER BY marque")->fetchAll();
        $clients  = $this->db->query("SELECT id,nom,prenom FROM clients ORDER BY nom")->fetchAll();
        $data     = ['vehicle_id'=>'','client_id'=>'','type'=>'accident','description'=>'',
                     'cout_reparation'=>'','prise_en_charge'=>'client',
                     'date_sinistre'=>date('Y-m-d'),'statut'=>'ouvert'];

        if ($this->isPost()) {
            $data = array_merge($data, $_POST);
            if (!$data['vehicle_id']) $errors[] = t('err_select_vehicle');

            if (!$errors) {
                $ref = 'SIN-' . date('Y') . '-' . str_pad(random_int(1,9999),4,'0',STR_PAD_LEFT);
                $id  = $this->incidentModel->create([
                    'reference'       => $ref,
                    'vehicle_id'      => $data['vehicle_id'],
                    'client_id'       => $data['client_id'] ?: null,
                    'type'            => $data['type'],
                    'description'     => $data['description'],
                    'cout_reparation' => $data['cout_reparation'] ?: null,
                    'prise_en_charge' => $data['prise_en_charge'],
                    'date_sinistre'   => $data['date_sinistre'],
                    'statut'          => $data['statut'],
                ]);
                $this->auditModel->log('Création sinistre', 'incidents', "Sinistre $ref créé (ID:$id)");
                $this->flash('success', t('incident_added'));
                $this->redirect('incidents');
            }
        }

        $this->view('incidents/create', compact('errors', 'data', 'vehicles', 'clients'));
    }

    public function edit(): void
    {
        $this->requireAuth();
        $id       = (int)$this->query('id');
        $incident = $this->incidentModel->find($id);
        if (!$incident) { $this->flash('danger', t('incident_not_found')); $this->redirect('incidents'); }

        $errors   = [];
        $vehicles = $this->db->query("SELECT id,numero,marque,modele FROM vehicles ORDER BY marque")->fetchAll();
        $clients  = $this->db->query("SELECT id,nom,prenom FROM clients ORDER BY nom")->fetchAll();
        $data     = $incident;

        if ($this->isPost()) {
            $data = array_merge($data, $_POST);
            if (!$errors) {
                $this->incidentModel->update($id, [
                    'vehicle_id'      => $data['vehicle_id'],
                    'client_id'       => $data['client_id'] ?: null,
                    'type'            => $data['type'],
                    'description'     => $data['description'],
                    'cout_reparation' => $data['cout_reparation'] ?: null,
                    'prise_en_charge' => $data['prise_en_charge'],
                    'date_sinistre'   => $data['date_sinistre'],
                    'statut'          => $data['statut'],
                ]);
                $this->auditModel->log('Modification sinistre', 'incidents', "{$incident['reference']} modifié");
                $this->flash('success', t('incident_updated'));
                $this->redirect('incidents');
            }
        }

        $this->view('incidents/edit', compact('errors', 'data', 'vehicles', 'clients', 'id'));
    }

    public function delete(): void
    {
        $this->requireAuth();
        $id       = (int)$this->query('id');
        $incident = $this->incidentModel->find($id);
        if (!$incident) { $this->flash('danger', t('incident_not_found')); $this->redirect('incidents'); }

        $this->incidentModel->delete($id);
        $this->auditModel->log('Suppression sinistre', 'incidents', "{$incident['reference']} supprimé");
        $this->flash('success', t('incident_deleted'));
        $this->redirect('incidents');
    }
}
