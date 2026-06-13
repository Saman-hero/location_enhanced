<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Vehicle;
use App\Models\Client;
use App\Models\Reservation;

class ClientPortalController extends Controller
{
    protected string $layout = 'client';

    private Vehicle     $vehicleModel;
    private Client      $clientModel;
    private Reservation $reservationModel;

    public function __construct(\PDO $db)
    {
        parent::__construct($db);
        $this->vehicleModel     = new Vehicle($db);
        $this->clientModel      = new Client($db);
        $this->reservationModel = new Reservation($db);
    }

    public function index(): void
    {
        $dateDebut = $this->query('date_debut');
        $dateFin   = $this->query('date_fin');
        $categorie = $this->query('categorie');
        $prixMax   = (int)$this->query('prix_max');
        $search    = trim($this->query('search'));

        $where  = ["v.statut = 'disponible'"];
        $params = [];

        if ($search)    { $where[] = "(v.marque LIKE ? OR v.modele LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
        if ($categorie) { $where[] = "v.categorie = ?";  $params[] = $categorie; }
        if ($prixMax)   { $where[] = "v.prix_jour <= ?"; $params[] = $prixMax; }

        if ($dateDebut && $dateFin) {
            $where[]  = "v.id NOT IN (SELECT vehicle_id FROM reservations WHERE statut NOT IN ('annulée','terminée') AND date_debut < ? AND date_fin_prevue > ?)";
            $params[] = $dateFin;
            $params[] = $dateDebut;
        }

        $sql      = "SELECT v.* FROM vehicles v WHERE " . implode(' AND ', $where) . " ORDER BY v.categorie, v.prix_jour";
        $stmt     = $this->db->prepare($sql);
        $stmt->execute($params);
        $vehicles = $stmt->fetchAll();

        $nbJours = 0;
        if ($dateDebut && $dateFin) {
            $d1 = new \DateTime($dateDebut);
            $d2 = new \DateTime($dateFin);
            $nbJours = max(1, $d1->diff($d2)->days);
        }

        $this->view('client-portal/index', compact('vehicles', 'dateDebut', 'dateFin', 'categorie', 'prixMax', 'search', 'nbJours'));
    }

    public function reserve(): void
    {
        $vehicleId = (int)$this->query('vehicle_id');
        $vehicle   = $this->vehicleModel->find($vehicleId);
        if (!$vehicle || $vehicle['statut'] !== 'disponible') { $this->redirect('client'); }

        $dateDebut = $this->query('date_debut');
        $dateFin   = $this->query('date_fin');
        $errors    = [];
        $data      = [
            'nom' => '', 'prenom' => '', 'cin' => '', 'permis_numero' => '',
            'permis_expiration' => '', 'telephone' => '', 'email' => '',
            'date_debut'  => ($dateDebut ? $dateDebut . ' 09:00' : ''),
            'date_fin_prevue' => ($dateFin ? $dateFin . ' 18:00' : ''),
            'lieu_depart' => '', 'lieu_retour' => '',
        ];

        if ($this->isPost()) {
            $data = array_merge($data, $_POST);
            if (!trim($data['nom']))       $errors[] = 'Le nom est obligatoire.';
            if (!trim($data['prenom']))    $errors[] = 'Le prénom est obligatoire.';
            if (!trim($data['cin']))       $errors[] = 'Le CIN est obligatoire.';
            if (!trim($data['telephone'])) $errors[] = 'Le téléphone est obligatoire.';
            if (!$data['date_debut'])      $errors[] = 'Date de début obligatoire.';
            if (!$data['date_fin_prevue']) $errors[] = 'Date de fin obligatoire.';
            if ($data['date_debut'] && $data['date_fin_prevue'] && $data['date_debut'] >= $data['date_fin_prevue'])
                $errors[] = 'La date de retour doit être après la date de départ.';

            if (!$errors) {
                // Find or create client by CIN
                $stmt = $this->db->prepare("SELECT id, statut FROM clients WHERE cin = ? LIMIT 1");
                $stmt->execute([$data['cin']]);
                $existingClient = $stmt->fetch();

                if ($existingClient && $existingClient['statut'] === 'liste_noire') {
                    $errors[] = 'Votre compte est suspendu. Veuillez contacter l\'agence.';
                }

                if (!$errors) {
                    if ($existingClient) {
                        $clientId = $existingClient['id'];
                        $this->clientModel->update($clientId, [
                            'nom' => $data['nom'], 'prenom' => $data['prenom'],
                            'telephone' => $data['telephone'], 'email' => $data['email'],
                            'permis_numero' => $data['permis_numero'],
                            'permis_expiration' => $data['permis_expiration'] ?: null,
                        ]);
                    } else {
                        $clientId = $this->clientModel->create([
                            'nom' => $data['nom'], 'prenom' => $data['prenom'],
                            'cin' => $data['cin'], 'permis_numero' => $data['permis_numero'],
                            'permis_expiration' => $data['permis_expiration'] ?: null,
                            'telephone' => $data['telephone'], 'email' => $data['email'],
                            'statut' => 'actif',
                        ]);
                    }

                    $d1      = new \DateTime($data['date_debut']);
                    $d2      = new \DateTime($data['date_fin_prevue']);
                    $nbJours = max(1, $d1->diff($d2)->days);
                    $montant = $nbJours * $vehicle['prix_jour'];
                    $ref     = $this->reservationModel->generateReference();

                    $resId = $this->reservationModel->create([
                        'reference'     => $ref,
                        'vehicle_id'    => $vehicleId,
                        'client_id'     => $clientId,
                        'date_debut'    => $data['date_debut'],
                        'date_fin_prevue'=> $data['date_fin_prevue'],
                        'lieu_depart'   => $data['lieu_depart'],
                        'lieu_retour'   => $data['lieu_retour'],
                        'prix_jour'     => $vehicle['prix_jour'],
                        'nb_jours'      => $nbJours,
                        'caution'       => $vehicle['caution'],
                        'montant_total' => $montant,
                        'statut'        => 'en attente',
                    ]);

                    $this->redirect('client/confirmation', ['id' => $resId]);
                }
            }
        }

        $nbJours = 0;
        if ($data['date_debut'] && $data['date_fin_prevue']) {
            try {
                $d1 = new \DateTime($data['date_debut']);
                $d2 = new \DateTime($data['date_fin_prevue']);
                $nbJours = max(1, $d1->diff($d2)->days);
            } catch (\Exception $e) {}
        }

        $this->view('client-portal/reserve', compact('vehicle', 'data', 'errors', 'nbJours'));
    }

    public function confirmation(): void
    {
        $id          = (int)$this->query('id');
        $reservation = $this->reservationModel->withDetails($id);
        if (!$reservation) { $this->redirect('client'); }
        $this->view('client-portal/confirmation', compact('reservation'));
    }
}
