<?php
namespace App\Core;

abstract class Controller
{
    protected \PDO $db;
    protected string $layout = 'admin';

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Render a view template inside a layout.
     * $template: relative path under views/, e.g. 'vehicles/index'
     * $data: variables extracted into template scope
     */
    protected function view(string $template, array $viewData = []): void
    {
        extract($viewData);

        // Capture view content
        ob_start();
        $tplFile = APP_PATH . '/views/' . $template . '.php';
        if (!file_exists($tplFile)) {
            ob_end_clean();
            throw new \RuntimeException("View not found: $tplFile");
        }
        require $tplFile;
        $content = ob_get_clean();

        // Render layout with $content in scope
        $layoutFile = APP_PATH . '/views/layouts/' . $this->layout . '.php';
        require $layoutFile;
    }

    /**
     * Redirect to a page in this app (uses BASE_URL).
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

    protected function requireAuth(): void
    {
        if (empty($_SESSION['user_id'])) {
            $this->redirect('login');
        }
    }

    protected function requireAdmin(): void
    {
        $this->requireAuth();
        if (($_SESSION['user_role'] ?? '') !== 'admin') {
            $this->flash('danger', t('access_denied'));
            $this->redirect('dashboard');
        }
    }

    protected function flash(string $type, string $msg): void
    {
        $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
    }

    protected function getFlash(): ?array
    {
        if (!empty($_SESSION['flash'])) {
            $f = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $f;
        }
        return null;
    }

    /** Return current request method */
    protected function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }

    protected function isPost(): bool
    {
        return $this->method() === 'POST';
    }

    /** POST input helper */
    protected function input(string $key, mixed $default = ''): mixed
    {
        return $_POST[$key] ?? $default;
    }

    /** GET input helper */
    protected function query(string $key, mixed $default = ''): mixed
    {
        return $_GET[$key] ?? $default;
    }
}
