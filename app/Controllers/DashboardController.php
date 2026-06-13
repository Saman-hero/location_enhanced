<?php
namespace App\Controllers;

use App\Core\Controller;

// Page d'accueil de l'admin : statistiques (KPI), graphiques et
// dernières réservations. Calcule tout via des requêtes SQL agrégées.
class DashboardController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();

        // Compteur global de visites de l'application (table app_settings)
        $this->db->exec("UPDATE app_settings SET value = value + 1 WHERE `key` = 'visit_count'");
        $stmt = $this->db->prepare("SELECT value FROM app_settings WHERE `key` = 'visit_count'");
        $stmt->execute();
        $visitCount = (int)$stmt->fetchColumn();

        // -- KPI : état de la flotte --
        $totalVehicles = (int)$this->db->query("SELECT COUNT(*) FROM vehicles")->fetchColumn();
        $dispo  = (int)$this->db->query("SELECT COUNT(*) FROM vehicles WHERE statut='disponible'")->fetchColumn();
        $loue   = (int)$this->db->query("SELECT COUNT(*) FROM vehicles WHERE statut='loué'")->fetchColumn();
        $maint  = (int)$this->db->query("SELECT COUNT(*) FROM vehicles WHERE statut='maintenance'")->fetchColumn();

        $activeReservations = (int)$this->db->query("SELECT COUNT(*) FROM reservations WHERE statut IN ('en cours','confirmée')")->fetchColumn();
        // Taux d'utilisation de la flotte = % de véhicules actuellement loués
        $utilRate = $totalVehicles > 0 ? round(($loue / $totalVehicles) * 100) : 0;

        // -- KPI : revenus du mois courant vs mois précédent --
        $revMonth = (float)$this->db->query(
            "SELECT COALESCE(SUM(montant),0) FROM paiements WHERE MONTH(date_paiement)=MONTH(NOW()) AND YEAR(date_paiement)=YEAR(NOW())"
        )->fetchColumn();
        $revLast = (float)$this->db->query(
            "SELECT COALESCE(SUM(montant),0) FROM paiements WHERE MONTH(date_paiement)=MONTH(DATE_SUB(NOW(),INTERVAL 1 MONTH)) AND YEAR(date_paiement)=YEAR(DATE_SUB(NOW(),INTERVAL 1 MONTH))"
        )->fetchColumn();
        // Variation en % du revenu par rapport au mois précédent
        $revTrend = $revLast > 0 ? round((($revMonth - $revLast) / $revLast) * 100) : 0;

        $activeClients = (int)$this->db->query("SELECT COUNT(*) FROM clients WHERE statut='actif'")->fetchColumn();
        $openIncidents = (int)$this->db->query("SELECT COUNT(*) FROM sinistres WHERE statut='ouvert'")->fetchColumn();

        // -- Graphique : nombre de réservations par mois sur les 6 derniers mois --
        $chartLabelsArr = [];
        $chartValuesArr = [];
        for ($i = 5; $i >= 0; $i--) {
            $ym = date('Y-m', strtotime("-$i months"));
            $chartLabelsArr[] = date('M Y', strtotime($ym . '-01'));
            $s = $this->db->prepare("SELECT COUNT(*) FROM reservations WHERE DATE_FORMAT(date_debut,'%Y-%m')=?");
            $s->execute([$ym]);
            $chartValuesArr[] = (int)$s->fetchColumn();
        }

        // -- Graphique : répartition des véhicules par statut --
        $statusRows = $this->db->query("SELECT statut, COUNT(*) cnt FROM vehicles GROUP BY statut")->fetchAll();
        $statusLabelsArr = array_column($statusRows, 'statut');
        $statusValuesArr = array_column($statusRows, 'cnt');

        $stats = [
            'total_vehicles'      => $totalVehicles,
            'dispo'               => $dispo,
            'loue'                => $loue,
            'maint'               => $maint,
            'active_reservations' => $activeReservations,
            'util_rate'           => $utilRate,
            'revenue_month'       => $revMonth,
            'rev_trend'           => $revTrend,
            'active_clients'      => $activeClients,
            'open_incidents'      => $openIncidents,
            'chart_labels'        => json_encode($chartLabelsArr),
            'chart_values'        => json_encode($chartValuesArr),
            'status_labels'       => json_encode($statusLabelsArr),
            'status_values'       => json_encode($statusValuesArr),
        ];

        // -- Les 5 réservations les plus récentes (avec jointures véhicule + client) --
        $recentReservations = $this->db->query(
            "SELECT r.*, v.marque, v.modele, v.numero,
                    c.nom, c.prenom
             FROM reservations r
             JOIN vehicles v ON v.id = r.vehicle_id
             JOIN clients  c ON c.id = r.client_id
             ORDER BY r.id DESC LIMIT 5"
        )->fetchAll();

        $flash = $this->getFlash();

        $this->view('dashboard/index', compact('stats', 'recentReservations', 'flash', 'visitCount'));
    }
}
