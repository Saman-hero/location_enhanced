<?php
namespace App\Models;

use App\Core\Model;

class Vehicle extends Model
{
    protected string $table = 'vehicles';

    public function available(): array
    {
        return $this->query(
            "SELECT * FROM vehicles WHERE statut = 'disponible' ORDER BY categorie, prix_jour"
        );
    }

    public function availableForPeriod(string $dateDebut, string $dateFin): array
    {
        return $this->query(
            "SELECT v.* FROM vehicles v
             WHERE v.statut = 'disponible'
               AND v.id NOT IN (
                   SELECT vehicle_id FROM reservations
                   WHERE statut NOT IN ('annulée','terminée')
                     AND date_debut < ? AND date_fin > ?
               )
             ORDER BY v.categorie, v.prix_jour",
            [$dateFin, $dateDebut]
        );
    }

    public function search(string $search, string $statut, string $categorie, string $marque): array
    {
        $where  = ['1=1'];
        $params = [];
        if ($search) {
            $where[]  = "(numero LIKE ? OR immatriculation LIKE ? OR marque LIKE ? OR modele LIKE ?)";
            $params   = array_merge($params, ["%$search%", "%$search%", "%$search%", "%$search%"]);
        }
        if ($statut)    { $where[] = "statut = ?";       $params[] = $statut; }
        if ($categorie) { $where[] = "categorie = ?";    $params[] = $categorie; }
        if ($marque)    { $where[] = "marque LIKE ?";    $params[] = "%$marque%"; }
        return $this->query(
            "SELECT * FROM vehicles WHERE " . implode(' AND ', $where) . " ORDER BY id DESC",
            $params
        );
    }

    public function paginatedSearch(string $search, string $statut, string $categorie, string $marque, int $perPage, int $page): array
    {
        $where  = ['1=1'];
        $params = [];
        if ($search) {
            $where[]  = "(numero LIKE ? OR immatriculation LIKE ? OR marque LIKE ? OR modele LIKE ?)";
            $params   = array_merge($params, ["%$search%", "%$search%", "%$search%", "%$search%"]);
        }
        if ($statut)    { $where[] = "statut = ?";    $params[] = $statut; }
        if ($categorie) { $where[] = "categorie = ?"; $params[] = $categorie; }
        if ($marque)    { $where[] = "marque LIKE ?"; $params[] = "%$marque%"; }

        return $this->paginate(implode(' AND ', $where), $params, $perPage, $page, 'id DESC');
    }

    public function distinctBrands(): array
    {
        return $this->db->query("SELECT DISTINCT marque FROM vehicles ORDER BY marque")
                        ->fetchAll(\PDO::FETCH_COLUMN);
    }

    // -- Multiple images --

    public function getImages(int $vehicleId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM vehicle_images WHERE vehicle_id = ? ORDER BY ordre, id");
        $stmt->execute([$vehicleId]);
        return $stmt->fetchAll();
    }

    public function addImage(int $vehicleId, string $imageUrl, int $ordre = 0): void
    {
        $stmt = $this->db->prepare("INSERT INTO vehicle_images (vehicle_id, image_url, ordre) VALUES (?,?,?)");
        $stmt->execute([$vehicleId, $imageUrl, $ordre]);
    }

    public function deleteImage(int $imageId): void
    {
        $stmt = $this->db->prepare("DELETE FROM vehicle_images WHERE id = ?");
        $stmt->execute([$imageId]);
    }

    public function getImagesForVehicles(array $vehicleIds): array
    {
        if (!$vehicleIds) return [];
        $in   = implode(',', array_map('intval', $vehicleIds));
        $rows = $this->db->query("SELECT * FROM vehicle_images WHERE vehicle_id IN ($in) ORDER BY vehicle_id, ordre, id")->fetchAll();
        $map  = [];
        foreach ($rows as $r) $map[$r['vehicle_id']][] = $r['image_url'];
        return $map;
    }
}
