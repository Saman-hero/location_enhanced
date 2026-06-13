<?php
namespace App\Core;

// Routeur central : associe chaque URL (?page=...) + méthode HTTP (GET/POST)
// à un Contrôleur et une de ses méthodes (action).
class Router
{
    // Tableau des routes enregistrées : [METHODE][page] = [Controller, action]
    private array $routes = [];

    // Enregistre une route générique pour une méthode HTTP donnée
    public function add(string $method, string $page, string $controller, string $action): void
    {
        $this->routes[strtoupper($method)][$page] = [$controller, $action];
    }

    // Raccourci pour enregistrer une route GET (ex: afficher une page)
    public function get(string $page, string $controller, string $action): void
    {
        $this->add('GET', $page, $controller, $action);
    }

    // Raccourci pour enregistrer une route POST (ex: soumission de formulaire)
    public function post(string $page, string $controller, string $action): void
    {
        $this->add('POST', $page, $controller, $action);
    }

    // Point central appelé depuis index.php : analyse la requête courante,
    // trouve la route correspondante et exécute l'action du contrôleur.
    public function dispatch(\PDO $db): void
    {
        // Récupère le nom de la page depuis ?page=... (ex: "vehicles/add")
        $page   = trim($_GET['page'] ?? '', '/');
        $method = strtoupper($_SERVER['REQUEST_METHOD']);

        // Cherche d'abord une route pour la méthode courante (GET/POST),
        // sinon retombe sur une route GET (utile pour les pages sans POST)
        $handler = $this->routes[$method][$page]
                ?? $this->routes['GET'][$page]
                ?? null;

        // Aucune route trouvée → page 404
        if (!$handler) {
            $this->notFound();
            return;
        }

        [$controllerClass, $action] = $handler;

        if (!class_exists($controllerClass)) {
            $this->notFound();
            return;
        }

        // Instanciation dynamique du contrôleur avec la connexion DB
        $controller = new $controllerClass($db);

        if (!method_exists($controller, $action)) {
            $this->notFound();
            return;
        }

        // Exécute l'action (ex: VehicleController->index())
        $controller->$action();
    }

    // Affiche la page d'erreur 404 (page introuvable)
    private function notFound(): void
    {
        http_response_code(404);
        $tpl = APP_PATH . '/views/errors/404.php';
        if (file_exists($tpl)) {
            require $tpl;
        } else {
            echo '<h1>404 — Page introuvable</h1>';
        }
    }
}
