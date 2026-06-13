# Script de Présentation PFA — AutoLocation (Système de Gestion de Flotte)

> À copier-coller dans Notion (Ctrl+V / Cmd+V conserve le formatting Markdown)

---

## 1. Introduction (30 sec)

"Mon projet est un **système de gestion de location de véhicules**, développé en **PHP 8.2** avec **MySQL**, en architecture **MVC faite à la main** (sans framework comme Laravel). Il comprend un back-office admin et un portail public pour les clients."

**Fonctionnalités principales :**
- Gestion de flotte (véhicules, photos, statuts)
- Réservations, clients, paiements
- Maintenance et incidents
- Journal d'audit
- Portail client public (réservation en ligne)
- Multilingue (FR / EN / AR avec support RTL)

---

## 2. Architecture générale (1 min)

"J'ai construit mon propre mini-framework MVC. Le flux d'une requête suit toujours le même chemin :"

```
Navigateur → index.php → Router → Controller → Model → Base de données
                                       ↓
                                     View (HTML) → Layout → Réponse
```

---

## 3. Point d'entrée : index.php (1 min)

**Fichier : `index.php`**

```php
<?php
define('APP_PATH', __DIR__);

require_once __DIR__ . '/config/database.php';

// Autoloader PSR-4 : App\ → app/
spl_autoload_register(function (string $class): void {
    if (strpos($class, 'App\\') !== 0) return;
    $file = APP_PATH . '/app/' . str_replace('\\', '/', substr($class, 4)) . '.php';
    if (file_exists($file)) require $file;
});

$db = (new Database())->getConnection();

$router = new App\Core\Router();
require APP_PATH . '/config/routes.php';
$router->dispatch($db);
```

**À expliquer :**
- C'est le **front controller unique** : toutes les requêtes passent par ce fichier
- L'**autoloader** charge automatiquement les classes (ex: `App\Models\Vehicle` → `app/Models/Vehicle.php`) — pas besoin de faire des `require` partout
- `$db` = connexion PDO partagée, injectée dans tous les contrôleurs
- `routes.php` enregistre toutes les routes, puis `dispatch()` traite la requête

---

## 4. Les routes : config/routes.php (1 min)

```php
$router->get ('vehicles',      VehicleController::class, 'index');
$router->get ('vehicles/add',  VehicleController::class, 'create');
$router->post('vehicles/add',  VehicleController::class, 'create');
$router->get ('vehicles/edit', VehicleController::class, 'edit');
```

**À expliquer :**
- Chaque ligne associe une URL (`?page=vehicles`) + méthode HTTP (GET/POST) à un **Contrôleur** et une **méthode**
- Exemple : `index.php?page=vehicles` → appelle `VehicleController::index()`
- `vehicles/add` en GET affiche le formulaire, en POST traite la soumission (même méthode `create()` gère les deux cas)

---

## 5. Le Router : app/Core/Router.php (1-2 min)

```php
public function dispatch(\PDO $db): void
{
    $page   = trim($_GET['page'] ?? '', '/');
    $method = strtoupper($_SERVER['REQUEST_METHOD']);

    $handler = $this->routes[$method][$page]
            ?? $this->routes['GET'][$page]
            ?? null;

    if (!$handler) { $this->notFound(); return; }

    [$controllerClass, $action] = $handler;
    $controller = new $controllerClass($db);
    $controller->$action();
}
```

**À expliquer :**
- Récupère `page` depuis l'URL et la méthode HTTP
- Cherche la route correspondante dans le tableau enregistré
- **Instancie dynamiquement** le contrôleur (`new $controllerClass($db)`) et appelle la méthode (`$controller->$action()`)
- Si aucune route ne correspond → page 404

---

## 6. La classe Controller (parent) : app/Core/Controller.php (1-2 min)

**Méthodes clés à montrer :**

```php
protected function view(string $template, array $viewData = []): void
{
    extract($viewData);
    ob_start();
    require APP_PATH . '/views/' . $template . '.php';
    $content = ob_get_clean();
    require APP_PATH . '/views/layouts/' . $this->layout . '.php';
}

protected function requireAuth(): void
{
    if (empty($_SESSION['user_id'])) {
        $this->redirect('login');
    }
}
```

**À expliquer :**
- `view()` : capture le HTML de la vue (`ob_start`/`ob_get_clean`) puis l'injecte dans un **layout** commun (header, menu, footer)
- `requireAuth()` / `requireAdmin()` : sécurité — vérifie la session avant d'autoriser l'accès
- `flash()` : messages de succès/erreur affichés après une redirection (pattern "flash message")
- Toutes les classes Controller (Vehicle, Reservation, Client...) héritent de cette classe → **pas de duplication de code**

---

## 7. La classe Model (parent) : app/Core/Model.php (1-2 min)

```php
public function create(array $data): int
{
    $cols = implode(', ', array_map(fn($k) => "`$k`", array_keys($data)));
    $placeholders = implode(', ', array_fill(0, count($data), '?'));
    $this->db->prepare("INSERT INTO `{$this->table}` ($cols) VALUES ($placeholders)")
             ->execute(array_values($data));
    return (int)$this->db->lastInsertId();
}
```

**À expliquer :**
- CRUD générique : `find()`, `all()`, `create()`, `update()`, `delete()`, `paginate()`
- Toutes les requêtes utilisent **PDO préparé** (`prepare`/`execute`) → **protection contre l'injection SQL**
- Chaque modèle enfant (ex: `Vehicle`) définit juste `$table = 'vehicles'` et hérite de tout le CRUD

---

## 8. Étude de cas complète : Module Véhicules (3-4 min — le cœur de la démo)

### a) Le Modèle : app/Models/Vehicle.php

```php
class Vehicle extends Model
{
    protected string $table = 'vehicles';

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
}
```

**À expliquer :**
- Construction dynamique d'une clause `WHERE` selon les filtres actifs (recherche, statut, catégorie, marque)
- Utilise `paginate()` du parent pour gérer pagination + comptage total
- Toujours des **paramètres liés (`?`)**, jamais de concaténation directe avec les données utilisateur

### b) Le Contrôleur : app/Controllers/VehicleController.php — méthode index()

```php
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
```

**À expliquer :**
1. `requireAuth()` — vérifie que l'utilisateur est connecté
2. Récupère les filtres depuis l'URL (`$_GET`) via `query()`
3. Appelle le modèle pour récupérer les données filtrées + paginées
4. Passe tout à la vue avec `view()`

### c) Méthode create() — formulaire + traitement (1-2 min)

```php
public function create(): void
{
    $this->requireAuth();
    $errors = [];
    $data   = [ /* valeurs par défaut */ ];

    if ($this->isPost()) {
        $data = array_merge($data, $_POST);

        // Validation
        if (!trim($data['numero'])) $errors[] = t('err_numero_required');
        if (!is_numeric($data['prix_jour'])) $errors[] = t('err_prix_required');

        if (!$errors) {
            $photos = $this->handleMultiplePhotoUploads('photos', $errors);
            if (!$errors) {
                $id = $this->vehicleModel->create([...]);
                foreach ($photos as $i => $url) {
                    $this->vehicleModel->addImage($id, $url, $i);
                }
                $this->auditModel->log('Création véhicule', 'vehicles', "Véhicule {$data['numero']} ajouté (ID:$id)");
                $this->flash('success', t('vehicle_added'));
                $this->redirect('vehicles');
            }
        }
    }
    $this->view('vehicles/create', compact('errors', 'data'));
}
```

**À expliquer :**
- Même méthode gère **affichage du formulaire (GET)** et **traitement (POST)**
- Validation manuelle des champs obligatoires
- Upload de photos multiples (vers **Cloudinary**, avec fallback stockage local)
- **Journal d'audit** : chaque action est tracée (`auditModel->log()`)
- Pattern **redirect-after-post** + message flash pour éviter la re-soumission de formulaire

### d) Upload Cloudinary (si le prof creuse — 1 min)

```php
private function uploadOnePhoto(array $file, array &$errors): ?string
{
    // Validation taille + type MIME
    $signature = sha1('timestamp=' . $timestamp . $apiSecret);
    $ch = curl_init("https://api.cloudinary.com/v1_1/{$cloudName}/image/upload");
    curl_setopt_array($ch, [...]);
    $response = curl_exec($ch);
    // Si Cloudinary échoue → fallback : sauvegarde locale dans uploads/vehicles/
}
```

**À expliquer :** intégration d'une API externe via cURL, avec signature de sécurité et fallback local en cas d'échec — bonne pratique de robustesse.

---

## 9. Le multilingue (FR/EN/AR) — 1 min

- Fichiers `lang/fr.php`, `lang/en.php`, `lang/ar.php` : tableaux clé → traduction
- Fonction globale `t('cle')` retourne le texte traduit selon la langue active
- L'arabe utilise un layout **RTL** (right-to-left) différent

---

## 10. Sécurité — points à mentionner si questions (1 min)

- **Requêtes préparées PDO** partout → pas d'injection SQL
- **Sessions** pour l'authentification (`requireAuth`, `requireAdmin`)
- **Rôles** : admin vs opérateur (gestion des utilisateurs réservée aux admins)
- **Journal d'audit** : traçabilité de toutes les actions create/update/delete
- Validation des fichiers uploadés (taille, type MIME)

---

## 11. Conclusion (30 sec)

"Le projet suit une architecture MVC propre, sans dépendance lourde, ce qui m'a permis de comprendre en profondeur le fonctionnement interne d'un framework (routing, ORM basique, templating). Tous les modules (véhicules, réservations, clients, paiements, maintenance, incidents) suivent exactement le même pattern Controller → Model → View."

---

## Annexe — Réponses aux questions probables

**Q: Pourquoi pas un framework (Laravel/Symfony) ?**
→ Objectif pédagogique : comprendre le fonctionnement interne (routing, MVC, ORM) en le construisant soi-même.

**Q: Comment gérez-vous la sécurité contre l'injection SQL ?**
→ 100% requêtes préparées PDO (`prepare()` + `execute()`), jamais de concaténation directe de variables dans le SQL.

**Q: Comment fonctionne l'autoload des classes ?**
→ `spl_autoload_register` : convertit le namespace `App\Models\Vehicle` en chemin de fichier `app/Models/Vehicle.php`.

**Q: Comment gérez-vous les rôles utilisateurs ?**
→ Stocké en session (`$_SESSION['user_role']`), vérifié via `requireAdmin()` dans les contrôleurs sensibles (ex: UserController).

**Q: Comment fonctionne la pagination ?**
→ `Model::paginate()` calcule `COUNT(*)`, le nombre de pages, et utilise `LIMIT/OFFSET` en SQL.
