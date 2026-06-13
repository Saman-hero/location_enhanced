<?php
// ── Base URL (auto-detected) ─────────────────────────────────
if (!defined('BASE_URL')) {
    $scheme   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host     = $_SERVER['HTTP_HOST']    ?? 'localhost';
    $script   = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $base     = rtrim(dirname($script), '/');
    define('BASE_URL', $scheme . '://' . $host . $base);
}

// ── Session ─────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) session_start();

// ── i18n ─────────────────────────────────────────────────────
$_supported_langs = ['fr', 'en', 'ar'];
if (isset($_GET['lang']) && in_array($_GET['lang'], $_supported_langs)) {
    $_SESSION['lang'] = $_GET['lang'];
}
$_current_lang = $_SESSION['lang'] ?? 'fr';
$_lang_file    = __DIR__ . '/../lang/' . $_current_lang . '.php';
$GLOBALS['__t']    = file_exists($_lang_file) ? require $_lang_file : require __DIR__ . '/../lang/fr.php';
$GLOBALS['__lang'] = $_current_lang;

function t(string $key, ...$args): string {
    $str = $GLOBALS['__t'][$key] ?? $key;
    return $args ? sprintf($str, ...$args) : $str;
}
function current_lang(): string { return $GLOBALS['__lang'] ?? 'fr'; }

// ── Flash helper ─────────────────────────────────────────────
function flash(string $type, string $msg): void {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}
function get_flash(): ?array {
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

// ── Auth guard ───────────────────────────────────────────────
function require_auth(): void {
    if (empty($_SESSION['user_id'])) {
        header('Location: /location_enhanced/?page=login');
        exit;
    }
}
function require_admin(): void {
    require_auth();
    if (($_SESSION['user_role'] ?? '') !== 'admin') {
        flash('danger', t('access_denied'));
        header('Location: /location_enhanced/');
        exit;
    }
}

// ── Helpers ──────────────────────────────────────────────────
function h(mixed $v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

function vehicle_img_url(string $url): string {
    if (str_starts_with($url, 'http')) return $url;
    return BASE_URL . '/uploads/vehicles/' . $url;
}

function audit(PDO $db, string $action, string $module, string $details = ''): void {
    $stmt = $db->prepare("INSERT INTO audit_log (user_id, username, action, module, details, ip_address) VALUES (?,?,?,?,?,?)");
    $stmt->execute([
        $_SESSION['user_id']   ?? null,
        $_SESSION['username']  ?? 'system',
        $action, $module, $details,
        $_SERVER['REMOTE_ADDR'] ?? ''
    ]);
}

function status_badge(string $status): string {
    $map = [
        'disponible'   => ['badge-green',  'status_disponible'],
        'loué'         => ['badge-blue',   'status_loue'],
        'maintenance'  => ['badge-yellow', 'status_maintenance'],
        'indisponible' => ['badge-red',    'status_indisponible'],
        'en attente'   => ['badge-yellow', 'status_en_attente'],
        'confirmée'    => ['badge-blue',   'status_confirmee'],
        'en cours'     => ['badge-green',  'status_en_cours'],
        'terminée'     => ['badge-gray',   'status_terminee'],
        'annulée'      => ['badge-red',    'status_annulee'],
        'actif'        => ['badge-green',  'status_actif'],
        'suspendu'     => ['badge-yellow', 'status_suspendu'],
        'liste_noire'  => ['badge-red',    'status_liste_noire'],
        'planifiée'    => ['badge-blue',   'status_planifiee'],
        'ouvert'       => ['badge-red',    'status_ouvert'],
        'clôturé'      => ['badge-gray',   'status_cloture'],
    ];
    [$cls, $key] = $map[$status] ?? ['badge-gray', null];
    $label = $key ? t($key) : h($status);
    return '<span class="badge ' . $cls . '">' . $label . '</span>';
}

function paginate(int $total, int $per_page, int $page): array {
    $pages = max(1, (int)ceil($total / $per_page));
    $page  = max(1, min($page, $pages));
    return ['total' => $total, 'per_page' => $per_page, 'page' => $page, 'pages' => $pages, 'offset' => ($page - 1) * $per_page];
}

// ── Database ─────────────────────────────────────────────────
// Supports Railway env vars (MYSQLHOST, MYSQLUSER …) with local fallback
class Database {
    private string $host;
    private int    $port;
    private string $dbname;
    private string $user;
    private string $pass;
    public  ?PDO   $conn = null;

    public function __construct()
    {
        $this->host   = getenv('MYSQLHOST')     ?: getenv('MYSQL_HOST')     ?: 'localhost';
        $this->port   = (int)(getenv('MYSQLPORT')     ?: getenv('MYSQL_PORT')     ?: 3306);
        $this->dbname = getenv('MYSQLDATABASE') ?: getenv('MYSQL_DATABASE') ?: 'location_enhanced';
        $this->user   = getenv('MYSQLUSER')     ?: getenv('MYSQL_USER')     ?: 'root';
        $this->pass   = getenv('MYSQLPASSWORD') ?: getenv('MYSQL_PASSWORD') ?: '';
    }

    public function getConnection(): PDO {
        if ($this->conn) return $this->conn;
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset=utf8mb4",
                $this->user, $this->pass
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->conn->exec("CREATE TABLE IF NOT EXISTS `vehicle_images` (
                `id`         INT AUTO_INCREMENT PRIMARY KEY,
                `vehicle_id` INT NOT NULL,
                `image_url`  VARCHAR(500) NOT NULL,
                `ordre`      INT DEFAULT 0,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles`(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        } catch (PDOException $e) {
            die('<div style="font-family:monospace;padding:2rem;color:#dc2626;">DB Error: ' . htmlspecialchars($e->getMessage()) . '</div>');
        }
        return $this->conn;
    }
}
