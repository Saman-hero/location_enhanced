<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\AuditLog;

class AuditController extends Controller
{
    private AuditLog $auditModel;

    public function __construct(\PDO $db)
    {
        parent::__construct($db);
        $this->auditModel = new AuditLog($db);
    }

    public function index(): void
    {
        $this->requireAuth();
        $search   = trim($this->query('search'));
        $module   = $this->query('module');
        $dateFrom = $this->query('date_from');
        $dateTo   = $this->query('date_to');
        $page     = max(1, (int)$this->query('p', 1));

        $result  = $this->auditModel->paginatedList($search, $module, $dateFrom, $dateTo, 20, $page);
        $modules = $this->auditModel->distinctModules();

        $this->view('audit/index', array_merge($result, compact('search', 'module', 'dateFrom', 'dateTo', 'modules')));
    }
}
