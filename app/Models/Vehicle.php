<?php
namespace App\Models;

use App\Core\Model;

// Accès aux données de la table `vehicles` + gestion des photos
// multiples (table vehicle_images).
class Vehicle extends Model
{
    protected string $table = 'vehicles';

    // Véhicules disponibles à la location (statut = 'disponible')
    public function available(): array
    {
        return $this->query(
            "SELECT * FROM vehicles WHERE statut = 'disponible' ORDER BY categorie, prix_jour"
        );
    }

    // Véhicules disponibles pour une période donnée : exclut ceux ayant
    // déjà une réservation (non annulée/terminée) qui chevauche les dates.
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

    // Recherche libre (numéro, immat, marque, modèle, description) combinée
    // à des filtres exacts (statut, catégorie, marque) — construit dynamiquement
    // la clause WHERE avec des paramètres liés (sécurité anti-injection SQL).
    public function search(string $search, string $statut, string $categorie, string $marque): array
    {
        $where  = ['1=1'];
        $params = [];
        if ($search) {
            $where[]  = "(numero LIKE ? OR immatriculation LIKE ? OR marque LIKE ? OR modele LIKE ? OR description LIKE ?)";
            $params   = array_merge($params, ["%$search%", "%$search%", "%$search%", "%$search%", "%$search%"]);
        }
        if ($statut)    { $where[] = "statut = ?";       $params[] = $statut; }
        if ($categorie) { $where[] = "categorie = ?";    $params[] = $categorie; }
        if ($marque)    { $where[] = "marque LIKE ?";    $params[] = "%$marque%"; }
        return $this->query(
            "SELECT * FROM vehicles WHERE " . implode(' AND ', $where) . " ORDER BY id DESC",
            $params
        );
    }

    // Même recherche que search(), mais avec pagination (utilisée par
    // VehicleController::index() pour la liste des véhicules).
    public function paginatedSearch(string $search, string $statut, string $categorie, string $marque, int $perPage, int $page): array
    {
        $where  = ['1=1'];
        $params = [];
        if ($search) {
            $where[]  = "(numero LIKE ? OR immatriculation LIKE ? OR marque LIKE ? OR modele LIKE ? OR description LIKE ?)";
            $params   = array_merge($params, ["%$search%", "%$search%", "%$search%", "%$search%", "%$search%"]);
        }
        if ($statut)    { $where[] = "statut = ?";    $params[] = $statut; }
        if ($categorie) { $where[] = "categorie = ?"; $params[] = $categorie; }
        if ($marque)    { $where[] = "marque LIKE ?"; $params[] = "%$marque%"; }

        return $this->paginate(implode(' AND ', $where), $params, $perPage, $page, 'id DESC');
    }

    // Liste des marques distinctes présentes dans le parc (pour le filtre du formulaire)
    public function distinctBrands(): array
    {
        return $this->db->query("SELECT DISTINCT marque FROM vehicles ORDER BY marque")
                        ->fetchAll(\PDO::FETCH_COLUMN);
    }

    // -- Gestion des photos multiples (table vehicle_images) --

    // Récupère toutes les photos d'un véhicule, triées par ordre d'affichage
    public function getImages(int $vehicleId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM vehicle_images WHERE vehicle_id = ? ORDER BY ordre, id");
        $stmt->execute([$vehicleId]);
        return $stmt->fetchAll();
    }

    // Ajoute une photo (URL Cloudinary ou nom de fichier local) pour un véhicule
    public function addImage(int $vehicleId, string $imageUrl, int $ordre = 0): void
    {
        $stmt = $this->db->prepare("INSERT INTO vehicle_images (vehicle_id, image_url, ordre) VALUES (?,?,?)");
        $stmt->execute([$vehicleId, $imageUrl, $ordre]);
    }

    // Supprime une photo par son ID
    public function deleteImage(int $imageId): void
    {
        $stmt = $this->db->prepare("DELETE FROM vehicle_images WHERE id = ?");
        $stmt->execute([$imageId]);
    }

    // Récupère les photos pour plusieurs véhicules en une seule requête
    // (évite le problème "N+1 requêtes" sur la page de liste)
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
