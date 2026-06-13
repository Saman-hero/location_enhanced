<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Vehicle;
use App\Models\AuditLog;

class VehicleController extends Controller
{
    private Vehicle  $vehicleModel;
    private AuditLog $auditModel;

    public function __construct(\PDO $db)
    {
        parent::__construct($db);
        $this->vehicleModel = new Vehicle($db);
        $this->auditModel   = new AuditLog($db);
    }

    public function index(): void
    {
        $this->requireAuth();

        $search    = trim($this->query('search'));
        $statut    = $this->query('statut');
        $categorie = $this->query('categorie');
        $marque    = trim($this->query('marque'));
        $page      = max(1, (int)$this->query('p', 1));

        $result = $this->vehicleModel->paginatedSearch($search, $statut, $categorie, $marque, 12, $page);
        $brands = $this->vehicleModel->distinctBrands();
        $flash  = $this->getFlash();

        $this->view('vehicles/index', array_merge($result, compact('search', 'statut', 'categorie', 'marque', 'brands', 'flash')));
    }

    /** Upload a vehicle photo. Returns stored filename or null on failure/no file. */
    private function handlePhotoUpload(string $field, array &$errors): ?string
    {
        if (empty($_FILES[$field]['tmp_name'])) return null;
        $file = $_FILES[$field];
        if ($file['error'] !== UPLOAD_ERR_OK) { $errors[] = 'Erreur lors du téléchargement de la photo.'; return null; }
        if ($file['size'] > 5 * 1024 * 1024)  { $errors[] = 'La photo ne doit pas dépasser 5 Mo.'; return null; }
        $mime = mime_content_type($file['tmp_name']);
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (!isset($allowed[$mime])) { $errors[] = 'Format accepté : JPEG, PNG, WebP.'; return null; }
        $ext  = $allowed[$mime];
        $name = uniqid('vh_', true) . '.' . $ext;
        $dest = APP_PATH . '/uploads/vehicles/' . $name;
        if (!move_uploaded_file($file['tmp_name'], $dest)) { $errors[] = 'Impossible de sauvegarder la photo.'; return null; }
        return $name;
    }

    public function create(): void
    {
        $this->requireAuth();

        $errors = [];
        $data   = ['numero'=>'','immatriculation'=>'','marque'=>'','modele'=>'','annee'=>date('Y'),
                   'couleur'=>'','nb_places'=>5,'categorie'=>'berline','kilometrage'=>0,
                   'statut'=>'disponible','prix_jour'=>'','caution'=>'',
                   'carburant'=>'essence','transmission'=>'manuelle','description'=>'','image_url'=>''];

        if ($this->isPost()) {
            $data = array_merge($data, $_POST);

            if (!trim($data['numero']))          $errors[] = 'Le numéro est obligatoire.';
            if (!trim($data['marque']))          $errors[] = 'La marque est obligatoire.';
            if (!trim($data['modele']))          $errors[] = 'Le modèle est obligatoire.';
            if (!is_numeric($data['prix_jour'])) $errors[] = 'Le prix/jour doit être un nombre.';

            if (!$errors) {
                $chk = $this->db->prepare("SELECT id FROM vehicles WHERE numero=?");
                $chk->execute([$data['numero']]);
                if ($chk->fetch()) $errors[] = 'Ce numéro de véhicule existe déjà.';
            }

            $photoName = $this->handlePhotoUpload('photo', $errors);

            if (!$errors) {
                $id = $this->vehicleModel->create([
                    'numero'         => $data['numero'],
                    'immatriculation'=> $data['immatriculation'],
                    'marque'         => $data['marque'],
                    'modele'         => $data['modele'],
                    'annee'          => $data['annee']      !== '' ? (int)$data['annee']      : null,
                    'couleur'        => $data['couleur'],
                    'nb_places'      => $data['nb_places']  !== '' ? (int)$data['nb_places']  : null,
                    'categorie'      => $data['categorie'],
                    'kilometrage'    => $data['kilometrage'] !== '' ? (int)$data['kilometrage'] : 0,
                    'statut'         => $data['statut'],
                    'prix_jour'      => $data['prix_jour'],
                    'caution'        => $data['caution']    !== '' ? $data['caution']          : 0,
                    'carburant'      => $data['carburant'],
                    'transmission'   => $data['transmission'],
                    'description'    => $data['description'],
                    'image_url'      => $photoName ?? '',
                ]);
                $this->auditModel->log('Création véhicule', 'vehicles', "Véhicule {$data['numero']} ajouté (ID:$id)");
                $this->flash('success', "Véhicule {$data['numero']} ajouté avec succès.");
                $this->redirect('vehicles');
            }
        }

        $this->view('vehicles/create', compact('errors', 'data'));
    }

    public function edit(): void
    {
        $this->requireAuth();
        $id      = (int)$this->query('id');
        $vehicle = $this->vehicleModel->find($id);
        if (!$vehicle) { $this->flash('danger', 'Véhicule introuvable.'); $this->redirect('vehicles'); }

        $errors = [];
        $data   = $vehicle;

        if ($this->isPost()) {
            $data = array_merge($data, $_POST);
            if (!trim($data['marque']))          $errors[] = 'La marque est obligatoire.';
            if (!trim($data['modele']))          $errors[] = 'Le modèle est obligatoire.';
            if (!is_numeric($data['prix_jour'])) $errors[] = 'Le prix/jour doit être un nombre.';

            $photoName = $this->handlePhotoUpload('photo', $errors);

            if (!$errors) {
                $imageUrl = $photoName ?? ($vehicle['image_url'] ?? '');
                // Delete old photo if a new one was uploaded
                if ($photoName && !empty($vehicle['image_url'])) {
                    $old = APP_PATH . '/uploads/vehicles/' . $vehicle['image_url'];
                    if (file_exists($old)) @unlink($old);
                }
                $this->vehicleModel->update($id, [
                    'immatriculation'=> $data['immatriculation'],
                    'marque'         => $data['marque'],
                    'modele'         => $data['modele'],
                    'annee'          => $data['annee']      !== '' ? (int)$data['annee']      : null,
                    'couleur'        => $data['couleur'],
                    'nb_places'      => $data['nb_places']  !== '' ? (int)$data['nb_places']  : null,
                    'categorie'      => $data['categorie'],
                    'kilometrage'    => $data['kilometrage'] !== '' ? (int)$data['kilometrage'] : 0,
                    'statut'         => $data['statut'],
                    'prix_jour'      => $data['prix_jour'],
                    'caution'        => $data['caution']    !== '' ? $data['caution']          : 0,
                    'carburant'      => $data['carburant'],
                    'transmission'   => $data['transmission'],
                    'description'    => $data['description'],
                    'image_url'      => $imageUrl,
                ]);
                $this->auditModel->log('Modification véhicule', 'vehicles', "Véhicule {$data['numero']} modifié");
                $this->flash('success', "Véhicule {$data['numero']} mis à jour.");
                $this->redirect('vehicles');
            }
        }

        $this->view('vehicles/edit', compact('errors', 'data', 'id'));
    }

    public function show(): void
    {
        $this->requireAuth();
        $id      = (int)$this->query('id');
        $vehicle = $this->vehicleModel->find($id);
        if (!$vehicle) { $this->flash('danger', 'Véhicule introuvable.'); $this->redirect('vehicles'); }

        $reservations = $this->db->prepare(
            "SELECT r.*, c.nom, c.prenom FROM reservations r JOIN clients c ON c.id=r.client_id
             WHERE r.vehicle_id = ? ORDER BY r.date_debut DESC LIMIT 5"
        );
        $reservations->execute([$id]);
        $reservations = $reservations->fetchAll();

        $maintenance = $this->db->prepare(
            "SELECT * FROM maintenance WHERE vehicle_id = ? ORDER BY date_prevue DESC LIMIT 5"
        );
        $maintenance->execute([$id]);
        $maintenance = $maintenance->fetchAll();

        $this->view('vehicles/show', compact('vehicle', 'reservations', 'maintenance'));
    }

    public function delete(): void
    {
        $this->requireAuth();
        $id      = (int)$this->query('id');
        $vehicle = $this->vehicleModel->find($id);
        if (!$vehicle) { $this->flash('danger', 'Véhicule introuvable.'); $this->redirect('vehicles'); }

        $this->vehicleModel->delete($id);
        $this->auditModel->log('Suppression véhicule', 'vehicles', "Véhicule {$vehicle['numero']} supprimé");
        $this->flash('success', "Véhicule {$vehicle['numero']} supprimé.");
        $this->redirect('vehicles');
    }
}
