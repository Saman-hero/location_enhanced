<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Payment;
use App\Models\AuditLog;

class PaymentController extends Controller
{
    private Payment  $paymentModel;
    private AuditLog $auditModel;

    public function __construct(\PDO $db)
    {
        parent::__construct($db);
        $this->paymentModel = new Payment($db);
        $this->auditModel   = new AuditLog($db);
    }

    public function index(): void
    {
        $this->requireAuth();
        $search = trim($this->query('search'));
        $page   = max(1, (int)$this->query('p', 1));
        $result = $this->paymentModel->paginatedList($search, 15, $page);
        $flash  = $this->getFlash();

        $totalAll = (float)$this->db->query("SELECT COALESCE(SUM(montant),0) FROM paiements")->fetchColumn();
        $totalMonth = (float)$this->db->query(
            "SELECT COALESCE(SUM(montant),0) FROM paiements WHERE MONTH(date_paiement)=MONTH(NOW()) AND YEAR(date_paiement)=YEAR(NOW())"
        )->fetchColumn();

        $this->view('payments/index', array_merge($result, compact('search', 'flash', 'totalAll', 'totalMonth')));
    }

    public function create(): void
    {
        $this->requireAuth();
        $errors       = [];
        $reservations = $this->db->query(
            "SELECT r.id, r.reference, r.montant_total, c.nom, c.prenom
             FROM reservations r JOIN clients c ON c.id = r.client_id
             WHERE r.statut NOT IN ('annulée','terminée') ORDER BY r.reference"
        )->fetchAll();
        $data = [
            'reservation_id' => $this->query('reservation_id'),
            'montant'        => '',
            'type_paiement'  => 'espèces',
            'type'           => 'solde',
            'date_paiement'  => date('Y-m-d'),
            'notes'          => '',
        ];

        if ($this->isPost()) {
            $data = array_merge($data, $_POST);
            if (!$data['reservation_id'])                          $errors[] = t('err_select_reservation');
            if (!is_numeric($data['montant']) || $data['montant'] <= 0) $errors[] = t('err_amount_invalid');

            if (!$errors) {
                $id = $this->paymentModel->create([
                    'reservation_id' => $data['reservation_id'],
                    'montant'        => $data['montant'],
                    'type_paiement'  => $data['type_paiement'],
                    'type'           => $data['type'],
                    'date_paiement'  => $data['date_paiement'],
                    'notes'          => $data['notes'],
                ]);
                $this->auditModel->log('Création paiement', 'payments', "Paiement {$data['montant']} MAD (ID:$id)");
                $this->flash('success', t('payment_added'));
                $this->redirect('payments');
            }
        }

        $this->view('payments/create', compact('errors', 'data', 'reservations'));
    }
}
