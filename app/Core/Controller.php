<?php
namespace App\Core;

// Classe parente de tous les contrôleurs (Vehicle, Client, Reservation...).
// Fournit les outils communs : affichage des vues, redirections,
// authentification/rôles, messages flash et accès aux entrées GET/POST.
abstract class Controller
{
    protected \PDO $db;
    protected string $layout = 'admin'; // layout HTML utilisé par défaut (admin.php)

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Affiche une vue à l'intérieur du layout.
     * $template: chemin relatif sous views/, ex: 'vehicles/index'
     * $viewData: variables rendues disponibles dans la vue (extract)
     */
    protected function view(string $template, array $viewData = []): void
    {
        extract($viewData);

        // 1) Génère le HTML de la vue dans un buffer (sans l'afficher tout de suite)
        ob_start();
        $tplFile = APP_PATH . '/views/' . $template . '.php';
        if (!file_exists($tplFile)) {
            ob_end_clean();
            throw new \RuntimeException("View not found: $tplFile");
        }
        require $tplFile;
        $content = ob_get_clean();

        // 2) Insère ce HTML ($content) dans le layout commun (header/menu/footer)
        $layoutFile = APP_PATH . '/views/layouts/' . $this->layout . '.php';
        require $layoutFile;
    }

    /**
     * Redirige vers une autre page de l'application (avec BASE_URL).
     */
    protected function redirect(string $page = '', array $params = []): void
    {
        $url = BASE_URL . '/';
        if ($page !== '') {
            $params = array_merge(['page' => $page], $params);
        }
        if ($params) {
            $url .= '?' . http_build_query($params);
        }
        header('Location: ' . $url);
        exit;
    }

    /** Durée d'inactivité avant déconnexion automatique (30 minutes) */
    private const SESSION_TTL = 1800;

    // Vérifie que l'utilisateur est connecté.
    // Gère aussi le timeout de session : si l'utilisateur est inactif
    // depuis plus de SESSION_TTL secondes, il est déconnecté automatiquement.
    protected function requireAuth(): void
    {
        if (empty($_SESSION['user_id'])) {
            $this->redirect('login');
        }

        if (!empty($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > self::SESSION_TTL) {
            session_unset();
            session_destroy();
            $this->redirect('login');
        }
        // Met à jour l'horodatage de la dernière activité (à chaque requête authentifiée)
        $_SESSION['last_activity'] = time();
    }

    // Vérifie que l'utilisateur est connecté ET a le rôle "admin".
    // Sinon, redirige vers le dashboard avec un message d'erreur.
    protected function requireAdmin(): void
    {
        $this->requireAuth();
        if (($_SESSION['user_role'] ?? '') !== 'admin') {
            $this->flash('danger', t('access_denied'));
            $this->redirect('dashboard');
        }
    }

    // Enregistre un message "flash" (succès/erreur) en session,
    // affiché une seule fois après une redirection.
    protected function flash(string $type, string $msg): void
    {
        $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
    }

    // Récupère et supprime le message flash courant (s'il existe).
    protected function getFlash(): ?array
    {
        if (!empty($_SESSION['flash'])) {
            $f = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $f;
        }
        return null;
    }

    /** Retourne la méthode HTTP de la requête courante (GET/POST...) */
    protected function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }

    // Vrai si la requête courante est une soumission de formulaire (POST)
    protected function isPost(): bool
    {
        return $this->method() === 'POST';
    }

    /** Récupère une valeur de $_POST (avec valeur par défaut) */
    protected function input(string $key, mixed $default = ''): mixed
    {
        return $_POST[$key] ?? $default;
    }

    /** Récupère une valeur de $_GET (avec valeur par défaut) */
    protected function query(string $key, mixed $default = ''): mixed
    {
        return $_GET[$key] ?? $default;
    }
}
