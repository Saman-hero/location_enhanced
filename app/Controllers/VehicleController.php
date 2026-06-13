<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Vehicle;
use App\Models\AuditLog;

// Gère le CRUD des véhicules (liste/recherche, ajout, modification,
// affichage détaillé, suppression) ainsi que l'upload des photos.
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

    // Liste paginée des véhicules, avec recherche et filtres
    // (statut, catégorie, marque) passés via l'URL (?search=...&statut=...)
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

    /** Upload multiple vehicle photos. Returns array of URLs. */
    private function handleMultiplePhotoUploads(string $field, array &$errors): array
    {
        $urls = [];
        if (empty($_FILES[$field]['tmp_name'])) return $urls;
        $files = $_FILES[$field];
        // Normalize to array format
        if (!is_array($files['tmp_name'])) {
            $files = array_map(fn($v) => [$v], $files);
        }
        $count = count($files['tmp_name']);
        for ($i = 0; $i < $count; $i++) {
            if (empty($files['tmp_name'][$i])) continue;
            $single = [
                'tmp_name' => $files['tmp_name'][$i],
                'error'    => $files['error'][$i],
                'size'     => $files['size'][$i],
                'type'     => $files['type'][$i],
            ];
            $url = $this->uploadOnePhoto($single, $errors);
            if ($url) $urls[] = $url;
        }
        return $urls;
    }

    /** Upload one photo file array to Cloudinary (fallback: local). */
    private function uploadOnePhoto(array $file, array &$errors): ?string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) { $errors[] = t('err_photo_upload'); return null; }
        if ($file['size'] > 5 * 1024 * 1024)  { $errors[] = t('err_photo_size'); return null; }
        $mime    = mime_content_type($file['tmp_name']);
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (!isset($allowed[$mime])) { $errors[] = t('err_photo_format'); return null; }

        $cloudName = getenv('CLOUDINARY_CLOUD_NAME') ?: 'duuvvlak5';
        $apiKey    = getenv('CLOUDINARY_API_KEY')    ?: '984527874378622';
        $apiSecret = getenv('CLOUDINARY_API_SECRET') ?: 'zSuE5jPg_paaAjPnyFtwU7ZFjoo';
        $timestamp = time();
        $signature = sha1('timestamp=' . $timestamp . $apiSecret);

        $ch = curl_init("https://api.cloudinary.com/v1_1/{$cloudName}/image/upload");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => [
                'file'      => new \CURLFile($file['tmp_name'], $mime),
                'api_key'   => $apiKey,
                'timestamp' => $timestamp,
                'signature' => $signature,
            ],
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response) {
            $res = json_decode($response, true);
            if (!empty($res['secure_url'])) return $res['secure_url'];
        }

        $ext  = $allowed[$mime];
        $name = uniqid('vh_', true) . '.' . $ext;
        $dest = APP_PATH . '/uploads/vehicles/' . $name;
        if (!move_uploaded_file($file['tmp_name'], $dest)) { $errors[] = t('err_photo_save'); return null; }
        return $name;
    }

    // Supprime une photo spécifique d'un véhicule (table vehicle_images)
    public function deleteImage(): void
    {
        $this->requireAuth();
        $imageId   = (int)$this->query('image_id');
        $vehicleId = (int)$this->query('vehicle_id');
        $this->vehicleModel->deleteImage($imageId);
        $this->flash('success', t('photo_deleted'));
        $this->redirect('vehicles/edit', ['id' => $vehicleId]);
    }

    // Affiche le formulaire d'ajout (GET) et traite sa soumission (POST) :
    // validation des champs, vérification d'unicité du numéro, upload
    // des photos, création en base puis journalisation (audit log).
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

            if (!trim($data['numero']))          $errors[] = t('err_numero_required');
            if (!trim($data['marque']))          $errors[] = t('err_marque_required');
            if (!trim($data['modele']))          $errors[] = t('err_modele_required');
            if (!is_numeric($data['prix_jour'])) $errors[] = t('err_prix_required');

            if (!$errors) {
                $chk = $this->db->prepare("SELECT id FROM vehicles WHERE numero=?");
                $chk->execute([$data['numero']]);
                if ($chk->fetch()) $errors[] = t('err_numero_exists');
            }

            $photos = $this->handleMultiplePhotoUploads('photos', $errors);

            if (!$errors) {
                $coverUrl = !empty($photos) ? $photos[0] : '';
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
                    'image_url'      => $coverUrl,
                ]);
                foreach ($photos as $i => $url) {
                    $this->vehicleModel->addImage($id, $url, $i);
                }
                $this->auditModel->log('Création véhicule', 'vehicles', "Véhicule {$data['numero']} ajouté (ID:$id)");
                $this->flash('success', t('vehicle_added'));
                $this->redirect('vehicles');
            }
        }

        $this->view('vehicles/create', compact('errors', 'data'));
    }

    // Affiche le formulaire d'édition pré-rempli (GET) et traite sa
    // soumission (POST) : mêmes validations que create(), met à jour
    // la fiche véhicule et ajoute d'éventuelles nouvelles photos.
    public function edit(): void
    {
        $this->requireAuth();
        $id      = (int)$this->query('id');
        $vehicle = $this->vehicleModel->find($id);
        if (!$vehicle) { $this->flash('danger', t('vehicle_not_found')); $this->redirect('vehicles'); }

        $errors = [];
        $data   = $vehicle;

        if ($this->isPost()) {
            $data = array_merge($data, $_POST);
            if (!trim($data['marque']))          $errors[] = t('err_marque_required');
            if (!trim($data['modele']))          $errors[] = t('err_modele_required');
            if (!is_numeric($data['prix_jour'])) $errors[] = t('err_prix_required');

            $newPhotos = $this->handleMultiplePhotoUploads('photos', $errors);

            if (!$errors) {
                $existingImages = $this->vehicleModel->getImages($id);
                $coverUrl = $vehicle['image_url'] ?? '';
                if (!empty($newPhotos)) {
                    $nextOrdre = count($existingImages);
                    foreach ($newPhotos as $i => $url) {
                        $this->vehicleModel->addImage($id, $url, $nextOrdre + $i);
                    }
                    if (empty($existingImages) && empty($coverUrl)) {
                        $coverUrl = $newPhotos[0];
                    }
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
                    'image_url'      => $coverUrl,
                ]);
                $this->auditModel->log('Modification véhicule', 'vehicles', "Véhicule {$data['numero']} modifié");
                $this->flash('success', t('vehicle_updated'));
                $this->redirect('vehicles/edit', ['id' => $id]);
            }
        }

        $vehicleImages = $this->vehicleModel->getImages($id);
        $this->view('vehicles/edit', compact('errors', 'data', 'id', 'vehicleImages'));
    }

    // Affiche la fiche détaillée d'un véhicule : infos générales,
    // ses 5 dernières réservations et ses 5 derniers entretiens.
    public function show(): void
    {
        $this->requireAuth();
        $id      = (int)$this->query('id');
        $vehicle = $this->vehicleModel->find($id);
        if (!$vehicle) { $this->flash('danger', t('vehicle_not_found')); $this->redirect('vehicles'); }

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

    // Supprime un véhicule (confirmation côté vue via JS confirm())
    // et enregistre l'action dans le journal d'audit.
    public function delete(): void
    {
        $this->requireAuth();
        $id      = (int)$this->query('id');
        $vehicle = $this->vehicleModel->find($id);
        if (!$vehicle) { $this->flash('danger', t('vehicle_not_found')); $this->redirect('vehicles'); }

        $this->vehicleModel->delete($id);
        $this->auditModel->log('Suppression véhicule', 'vehicles', "Véhicule {$vehicle['numero']} supprimé");
        $this->flash('success', t('vehicle_deleted'));
        $this->redirect('vehicles');
    }
}
