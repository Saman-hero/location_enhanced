<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Reservation;
use App\Models\Vehicle;
use App\Models\Client;
use App\Models\AuditLog;

// Gère le cycle de vie des réservations : liste/recherche, création
// (calcul automatique du montant), modification, détails, suppression.
class ReservationController extends Controller
{
    private Reservation $reservationModel;
    private Vehicle     $vehicleModel;
    private Client      $clientModel;
    private AuditLog    $auditModel;

    public function __construct(\PDO $db)
    {
        parent::__construct($db);
        $this->reservationModel = new Reservation($db);
        $this->vehicleModel     = new Vehicle($db);
        $this->clientModel      = new Client($db);
        $this->auditModel       = new AuditLog($db);
    }

    // Liste paginée des réservations, avec recherche (référence, client,
    // immatriculation) et filtre par statut.
    public function index(): void
    {
        $this->requireAuth();
        $search  = trim($this->query('search'));
        $statut  = $this->query('statut');
        $page    = max(1, (int)$this->query('p', 1));
        $result  = $this->reservationModel->paginatedList($search, $statut, 15, $page);
        $flash   = $this->getFlash();
        $this->view('reservations/index', array_merge($result, compact('search', 'statut', 'flash')));
    }

    // Formulaire de création d'une réservation.
    // Calcule automatiquement : nombre de jours, prix journalier (copié
    // du véhicule), caution et montant total = nb_jours * prix_jour.
    // Génère une référence unique (LOC-AAAA-XXXXX).
    public function create(): void
    {
        $this->requireAuth();
        $errors   = [];
        $vehicles = $this->vehicleModel->all('marque, modele');
        $clients  = $this->clientModel->forSelect();
        $data     = ['vehicle_id'=>'','client_id'=>'','date_debut'=>'','date_fin_prevue'=>'',
                     'lieu_depart'=>'','lieu_retour'=>'','statut'=>'en attente','commentaire'=>''];

        if ($this->isPost()) {
            $data = array_merge($data, $_POST);
            if (!$data['vehicle_id'])   $errors[] = t('err_select_vehicle');
            if (!$data['client_id'])    $errors[] = t('err_select_client');
            if (!$data['date_debut'])   $errors[] = t('err_date_start');
            if (!$data['date_fin_prevue']) $errors[] = t('err_date_end');
            if ($data['date_debut'] && $data['date_fin_prevue'] && $data['date_debut'] >= $data['date_fin_prevue'])
                $errors[] = t('err_date_order');

            if (!$errors) {
                // Référence unique de la réservation (ex: LOC-2026-04231)
                $ref     = $this->reservationModel->generateReference();
                // Calcul de la durée et du montant total de la location
                $d1      = new \DateTime($data['date_debut']);
                $d2      = new \DateTime($data['date_fin_prevue']);
                $nbJours = max(1, $d1->diff($d2)->days);
                $vehicle = $this->vehicleModel->find((int)$data['vehicle_id']);
                $montant = $nbJours * ($vehicle['prix_jour'] ?? 0);

                $id = $this->reservationModel->create([
                    'reference'     => $ref,
                    'vehicle_id'    => $data['vehicle_id'],
                    'client_id'     => $data['client_id'],
                    'date_debut'    => $data['date_debut'],
                    'date_fin_prevue'=> $data['date_fin_prevue'],
                    'lieu_depart'   => $data['lieu_depart'],
                    'lieu_retour'   => $data['lieu_retour'],
                    'prix_jour'     => $vehicle['prix_jour'] ?? 0,
                    'nb_jours'      => $nbJours,
                    'caution'       => $vehicle['caution'] ?? 0,
                    'montant_total' => $montant,
                    'statut'        => $data['statut'],
                    'commentaire'   => $data['commentaire'],
                    'created_by'    => $_SESSION['user_id'] ?? null,
                ]);
                $this->auditModel->log('Création réservation', 'reservations', "Réservation $ref créée (ID:$id)");
                $this->flash('success', t('reservation_added'));
                $this->redirect('reservations');
            }
        }

        $this->view('reservations/create', compact('errors', 'data', 'vehicles', 'clients'));
    }

    // Formulaire de modification d'une réservation : permet aussi de
    // saisir la date/km de retour effectif et des frais supplémentaires.
    // Le montant total est recalculé à chaque sauvegarde.
    public function edit(): void
    {
        $this->requireAuth();
        $id          = (int)$this->query('id');
        $reservation = $this->reservationModel->find($id);
        if (!$reservation) { $this->flash('danger', t('reservation_not_found')); $this->redirect('reservations'); }

        $errors   = [];
        $vehicles = $this->vehicleModel->all('marque, modele');
        $clients  = $this->clientModel->forSelect();
        $data     = $reservation;

        if ($this->isPost()) {
            $data = array_merge($data, $_POST);
            if (!$data['vehicle_id'])   $errors[] = t('err_select_vehicle');
            if (!$data['client_id'])    $errors[] = t('err_select_client');
            if ($data['date_debut'] && $data['date_fin_prevue'] && $data['date_debut'] >= $data['date_fin_prevue'])
                $errors[] = t('err_date_order');

            if (!$errors) {
                $d1      = new \DateTime($data['date_debut']);
                $d2      = new \DateTime($data['date_fin_prevue']);
                $nbJours = max(1, $d1->diff($d2)->days);
                $vehicle = $this->vehicleModel->find((int)$data['vehicle_id']);
                $montant = $nbJours * ($vehicle['prix_jour'] ?? 0);

                $this->reservationModel->update($id, [
                    'vehicle_id'          => $data['vehicle_id'],
                    'client_id'           => $data['client_id'],
                    'date_debut'          => $data['date_debut'],
                    'date_fin_prevue'     => $data['date_fin_prevue'],
                    'date_retour_effectif'=> $data['date_retour_effectif'] ?: null,
                    'lieu_depart'         => $data['lieu_depart'],
                    'lieu_retour'         => $data['lieu_retour'],
                    'km_depart'           => $data['km_depart'] ?: null,
                    'km_retour'           => $data['km_retour'] ?: null,
                    'prix_jour'           => $vehicle['prix_jour'] ?? 0,
                    'nb_jours'            => $nbJours,
                    'caution'             => $vehicle['caution'] ?? 0,
                    'montant_total'       => $montant,
                    'frais_extra'         => $data['frais_extra'] ?: 0,
                    'statut'              => $data['statut'],
                    'commentaire'         => $data['commentaire'],
                ]);
                $this->auditModel->log('Modification réservation', 'reservations', "{$reservation['reference']} modifiée");
                $this->flash('success', t('reservation_updated'));
                $this->redirect('reservations/show', ['id' => $id]);
            }
        }

        $this->view('reservations/edit', compact('errors', 'data', 'vehicles', 'clients', 'id'));
    }

    // Fiche détaillée d'une réservation : infos véhicule + client (via
    // jointures SQL dans withDetails) et historique des paiements liés.
    public function show(): void
    {
        $this->requireAuth();
        $id          = (int)$this->query('id');
        $reservation = $this->reservationModel->withDetails($id);
        if (!$reservation) { $this->flash('danger', t('reservation_not_found')); $this->redirect('reservations'); }

        $stmt = $this->db->prepare(
            "SELECT * FROM paiements WHERE reservation_id = ? ORDER BY date_paiement DESC"
        );
        $stmt->execute([$id]);
        $payments = $stmt->fetchAll();

        $this->view('reservations/show', compact('reservation', 'payments'));
    }

    // Supprime une réservation et journalise l'action
    public function delete(): void
    {
        $this->requireAuth();
        $id          = (int)$this->query('id');
        $reservation = $this->reservationModel->find($id);
        if (!$reservation) { $this->flash('danger', t('reservation_not_found')); $this->redirect('reservations'); }

        $this->reservationModel->delete($id);
        $this->auditModel->log('Suppression réservation', 'reservations', "{$reservation['reference']} supprimée");
        $this->flash('success', t('reservation_deleted'));
        $this->redirect('reservations');
    }
}
