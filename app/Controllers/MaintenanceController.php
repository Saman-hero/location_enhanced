<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Maintenance;
use App\Models\Vehicle;
use App\Models\AuditLog;

class MaintenanceController extends Controller
{
    private Maintenance $maintenanceModel;
    private Vehicle     $vehicleModel;
    private AuditLog    $auditModel;

    public function __construct(\PDO $db)
    {
        parent::__construct($db);
        $this->maintenanceModel = new Maintenance($db);
        $this->vehicleModel     = new Vehicle($db);
        $this->auditModel       = new AuditLog($db);
    }

    public function index(): void
    {
        $this->requireAuth();
        $search = trim($this->query('search'));
        $statut = $this->query('statut');
        $page   = max(1, (int)$this->query('p', 1));
        $result = $this->maintenanceModel->paginatedList($search, $statut, 15, $page);
        $flash  = $this->getFlash();

        $counts = $this->db->query("SELECT statut, COUNT(*) c FROM maintenance GROUP BY statut")
                           ->fetchAll(\PDO::FETCH_KEY_PAIR);

        $this->view('maintenance/index', array_merge($result, compact('search', 'statut', 'flash', 'counts')));
    }

    public function create(): void
    {
        $this->requireAuth();
        $errors   = [];
        $vehicles = $this->vehicleModel->all('marque, modele');
        $data     = [
            'vehicle_id'       => $this->query('vehicle_id'),
            'type_maintenance' => 'vidange',
            'description'      => '',
            'date_prevue'      => date('Y-m-d'),
            'date_realisee'    => '',
            'kilometrage'      => '',
            'cout'             => '',
            'technicien'       => '',
            'statut'           => 'planifiée',
        ];

        if ($this->isPost()) {
            $data = array_merge($data, $_POST);
            if (!$data['vehicle_id'])       $errors[] = 'Sélectionnez un véhicule.';
            if (!$data['type_maintenance']) $errors[] = 'Type obligatoire.';
            if (!$data['date_prevue'])      $errors[] = 'Date prévue obligatoire.';

            if (!$errors) {
                $id = $this->maintenanceModel->create([
                    'vehicle_id'       => $data['vehicle_id'],
                    'type_maintenance' => $data['type_maintenance'],
                    'description'      => $data['description'],
                    'date_prevue'      => $data['date_prevue'],
                    'date_realisee'    => $data['date_realisee'] ?: null,
                    'kilometrage'      => $data['kilometrage'] ?: null,
                    'cout'             => $data['cout'] ?: null,
                    'technicien'       => $data['technicien'],
                    'statut'           => $data['statut'],
                ]);
                $this->auditModel->log('Création maintenance', 'maintenance', "Maintenance créée (ID:$id)");
                $this->flash('success', 'Maintenance planifiée.');
                $this->redirect('maintenance');
            }
        }

        $this->view('maintenance/create', compact('errors', 'data', 'vehicles'));
    }

    public function edit(): void
    {
        $this->requireAuth();
        $id          = (int)$this->query('id');
        $maintenance = $this->maintenanceModel->find($id);
        if (!$maintenance) { $this->flash('danger', 'Maintenance introuvable.'); $this->redirect('maintenance'); }

        $errors   = [];
        $vehicles = $this->vehicleModel->all('marque, modele');
        $data     = $maintenance;

        if ($this->isPost()) {
            $data = array_merge($data, $_POST);
            if (!$data['vehicle_id'])       $errors[] = 'Sélectionnez un véhicule.';
            if (!$data['type_maintenance']) $errors[] = 'Type obligatoire.';

            if (!$errors) {
                $this->maintenanceModel->update($id, [
                    'vehicle_id'       => $data['vehicle_id'],
                    'type_maintenance' => $data['type_maintenance'],
                    'description'      => $data['description'],
                    'date_prevue'      => $data['date_prevue'],
                    'date_realisee'    => $data['date_realisee'] ?: null,
                    'kilometrage'      => $data['kilometrage'] ?: null,
                    'cout'             => $data['cout'] ?: null,
                    'technicien'       => $data['technicien'],
                    'statut'           => $data['statut'],
                ]);
                $this->auditModel->log('Modification maintenance', 'maintenance', "Maintenance #$id modifiée");
                $this->flash('success', 'Maintenance mise à jour.');
                $this->redirect('maintenance');
            }
        }

        $this->view('maintenance/edit', compact('errors', 'data', 'vehicles', 'id'));
    }

    public function delete(): void
    {
        $this->requireAuth();
        $id = (int)$this->query('id');
        $m  = $this->maintenanceModel->find($id);
        if (!$m) { $this->flash('danger', 'Maintenance introuvable.'); $this->redirect('maintenance'); }

        $this->maintenanceModel->delete($id);
        $this->auditModel->log('Suppression maintenance', 'maintenance', "Maintenance #$id supprimée");
        $this->flash('success', 'Maintenance supprimée.');
        $this->redirect('maintenance');
    }
}
