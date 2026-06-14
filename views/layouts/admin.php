<?php
$lang  = $_SESSION['lang'] ?? 'fr';
$isRtl = $lang === 'ar';
$dir   = $isRtl ? 'rtl' : 'ltr';
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $dir ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $pageTitle ?? t('app_name') ?> — <?= t('app_name') ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Noto+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>tailwind.config = { theme: { extend: { colors: { emerald: { 500:'#10b981',400:'#34d399',600:'#059669' }, azure: { 500:'#3b82f6',600:'#2563eb',100:'#dbeafe' } } } } }</script>
  <style>
    * { font-family: <?= $isRtl ? "'Noto Sans Arabic'" : "'Inter'" ?>, sans-serif; }
    body { background:#f8fafc; }

    /* Sidebar */
    .sidebar {
      width:260px; min-height:100vh; background:#0f172a;
      position:fixed; top:0; <?= $isRtl ? 'right:0;' : 'left:0;' ?> z-index:40;
      display:flex; flex-direction:column; transition:transform .25s ease;
    }
    .sidebar-link { display:flex; align-items:center; gap:10px; padding:9px 18px; border-radius:8px; color:#94a3b8; font-size:14px; font-weight:500; text-decoration:none; transition:all .15s; margin:1px 8px; }
    .sidebar-link:hover { background:rgba(255,255,255,.07); color:#f1f5f9; }
    .sidebar-link.active { background:rgba(16,185,129,.15); color:#10b981; }
    .sidebar-link svg { flex-shrink:0; }
    .sidebar-section { font-size:11px; font-weight:600; letter-spacing:.06em; color:#475569; text-transform:uppercase; padding:16px 26px 6px; }

    /* Main */
    .main-content { <?= $isRtl ? 'margin-right:260px;' : 'margin-left:260px;' ?> min-height:100vh; }
    .topbar { background:#fff; border-bottom:1px solid #e2e8f0; height:60px; display:flex; align-items:center; justify-content:space-between; padding:0 28px; position:sticky; top:0; z-index:30; }
    .page-body { padding:28px; }

    /* Cards */
    .card { background:#fff; border-radius:12px; border:1px solid #e2e8f0; box-shadow:0 1px 4px rgba(15,23,42,.05); }
    .card-header { padding:18px 22px 14px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; justify-content:space-between; }
    .card-body { padding:20px 22px; }
    .card-title { font-size:15px; font-weight:600; color:#0f172a; }

    /* KPI cards */
    .kpi-card { background:#fff; border-radius:12px; border:1px solid #e2e8f0; padding:20px 22px; box-shadow:0 1px 4px rgba(15,23,42,.05); }
    .kpi-value { font-size:32px; font-weight:700; color:#0f172a; line-height:1.1; }
    .kpi-label { font-size:13px; color:#64748b; font-weight:500; margin-top:4px; }
    .kpi-trend { font-size:12px; font-weight:600; margin-top:8px; }
    .trend-up   { color:#10b981; }
    .trend-down { color:#ef4444; }

    /* Badges */
    .badge { display:inline-flex; align-items:center; padding:3px 10px; border-radius:9999px; font-size:12px; font-weight:600; }
    .badge-green  { background:#d1fae5; color:#065f46; }
    .badge-blue   { background:#dbeafe; color:#1e40af; }
    .badge-yellow { background:#fef9c3; color:#92400e; }
    .badge-red    { background:#fee2e2; color:#991b1b; }
    .badge-gray   { background:#f1f5f9; color:#475569; }

    /* Buttons */
    .btn { display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border-radius:8px; font-size:14px; font-weight:500; cursor:pointer; text-decoration:none; border:none; transition:all .15s; }
    .btn-primary { background:#0f172a; color:#fff; }
    .btn-primary:hover { background:#1e293b; }
    .btn-emerald { background:#10b981; color:#fff; }
    .btn-emerald:hover { background:#059669; }
    .btn-outline { background:#fff; color:#0f172a; border:1px solid #e2e8f0; }
    .btn-outline:hover { background:#f8fafc; }
    .btn-danger { background:#fee2e2; color:#991b1b; }
    .btn-danger:hover { background:#fecaca; }
    .btn-sm { padding:5px 12px; font-size:13px; }
    .btn-icon { padding:7px; border-radius:8px; display:inline-flex; align-items:center; justify-content:center; }

    /* Table */
    .data-table { width:100%; border-collapse:collapse; }
    .data-table thead th { font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.06em; color:#64748b; padding:10px 14px; text-align:<?= $isRtl ? 'right' : 'left' ?>; background:#f8fafc; border-bottom:1px solid #e2e8f0; }
    .data-table tbody td { padding:13px 14px; font-size:14px; color:#334155; border-bottom:1px solid #f1f5f9; }
    .data-table tbody tr:hover { background:#f8fafc; }
    .data-table tbody tr:last-child td { border-bottom:none; }

    /* Forms */
    .form-label { display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:5px; }
    .form-control { width:100%; padding:9px 12px; border:1px solid #cbd5e1; border-radius:8px; font-size:14px; color:#0f172a; outline:none; transition:border .15s; background:#fff; }
    .form-control:focus { border-color:#3b82f6; box-shadow:0 0 0 3px rgba(59,130,246,.1); }
    select.form-control { appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:<?= $isRtl ? 'left' : 'right' ?> 10px center; <?= $isRtl ? 'padding-left:28px;' : 'padding-right:28px;' ?> }
    .form-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
    .form-grid-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; }

    /* Alert */
    .alert { padding:12px 16px; border-radius:8px; font-size:14px; margin-bottom:18px; display:flex; align-items:center; gap:10px; }
    .alert-success { background:#d1fae5; color:#065f46; border:1px solid #a7f3d0; }
    .alert-danger  { background:#fee2e2; color:#991b1b; border:1px solid #fecaca; }
    .alert-warning { background:#fef9c3; color:#92400e; border:1px solid #fde68a; }
    .alert-info    { background:#dbeafe; color:#1e40af; border:1px solid #bfdbfe; }

    /* Pagination */
    .pagination { display:flex; align-items:center; gap:4px; }
    .page-btn { padding:6px 11px; border-radius:6px; font-size:13px; font-weight:500; text-decoration:none; color:#64748b; border:1px solid #e2e8f0; }
    .page-btn:hover { background:#f1f5f9; }
    .page-btn.active { background:#0f172a; color:#fff; border-color:#0f172a; }
    .page-btn.disabled { opacity:.4; pointer-events:none; }

    /* Misc */
    .avatar { width:36px; height:36px; border-radius:50%; background:#e2e8f0; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:600; color:#475569; flex-shrink:0; }
    .stat-icon { width:44px; height:44px; border-radius:10px; display:flex; align-items:center; justify-content:center; }
    .divider { border:none; border-top:1px solid #f1f5f9; margin:0; }

    /* RTL fix: numbers always render left-to-right */
    .num { direction: ltr; unicode-bidi: isolate; display: inline-block; }
    [dir="rtl"] .kpi-value { direction: ltr; unicode-bidi: isolate; }
    [dir="rtl"] .kpi-trend { direction: ltr; unicode-bidi: isolate; }

    /* Language switcher */
    .lang-switcher { display:flex; align-items:center; gap:4px; background:#f1f5f9; border-radius:8px; padding:3px; }
    .lang-btn { padding:4px 10px; border-radius:6px; font-size:12px; font-weight:600; text-decoration:none; color:#64748b; transition:all .15s; }
    .lang-btn.active { background:#fff; color:#0f172a; box-shadow:0 1px 3px rgba(0,0,0,.1); }
    .lang-btn:hover:not(.active) { color:#0f172a; }

    /* Overlay + Responsive */
    #sidebar-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.4); z-index:39; }
    @media(max-width:768px) {
      .sidebar { transform:<?= $isRtl ? 'translateX(100%)' : 'translateX(-100%)' ?>; }
      .sidebar.open { transform:translateX(0); }
      #sidebar-overlay.show { display:block; }
      .main-content { margin-left:0; margin-right:0; }
      .form-grid-2, .form-grid-3 { grid-template-columns:1fr; }
    }
  </style>
</head>
<body>

<?php $currentPage = $_GET['page'] ?? 'dashboard'; ?>
<?php $langBase = '?' . http_build_query(array_merge($_GET, ['lang' => ''])); ?>

<!-- Sidebar Overlay (mobile) -->
<div id="sidebar-overlay" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
  <!-- Logo -->
  <div style="padding:20px 20px 12px; border-bottom:1px solid rgba(255,255,255,.07);">
    <a href="<?= BASE_URL ?>/" style="display:flex; align-items:center; gap:10px; text-decoration:none;">
      <div style="width:36px;height:36px;background:linear-gradient(135deg,#10b981,#059669);border-radius:8px;display:flex;align-items:center;justify-content:center;">
        <svg width="18" height="18" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 17H5l-2-4V7a1 1 0 011-1h14a1 1 0 011 1v6l-2 4z"/><circle cx="7.5" cy="17.5" r="1.5"/><circle cx="16.5" cy="17.5" r="1.5"/></svg>
      </div>
      <div>
        <div style="color:#f1f5f9;font-weight:700;font-size:15px;line-height:1.1;"><?= t('app_name') ?></div>
        <div style="color:#475569;font-size:11px;"><?= t('app_subtitle') ?></div>
      </div>
    </a>
  </div>

  <!-- Nav -->
  <nav style="flex:1;padding:10px 0;overflow-y:auto;">
    <div class="sidebar-section"><?= t('nav_dashboard') ?></div>
    <a href="<?= BASE_URL ?>/" class="sidebar-link <?= $currentPage==='dashboard'?'active':'' ?>">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
      <?= t('nav_dashboard') ?>
    </a>

    <div class="sidebar-section"><?= t('nav_fleet') ?></div>
    <a href="<?= BASE_URL ?>/?page=vehicles" class="sidebar-link <?= str_starts_with($currentPage,'vehicles')?'active':'' ?>">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 17H5l-2-4V7a1 1 0 011-1h14a1 1 0 011 1v6l-2 4z"/><circle cx="7.5" cy="17.5" r="1.5"/><circle cx="16.5" cy="17.5" r="1.5"/></svg>
      <?= t('nav_vehicles') ?>
    </a>
    <a href="<?= BASE_URL ?>/?page=maintenance" class="sidebar-link <?= str_starts_with($currentPage,'maintenance')?'active':'' ?>">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg>
      <?= t('nav_maintenance') ?>
    </a>
    <a href="<?= BASE_URL ?>/?page=incidents" class="sidebar-link <?= str_starts_with($currentPage,'incidents')?'active':'' ?>">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
      <?= t('nav_incidents') ?>
    </a>

    <div class="sidebar-section"><?= t('nav_reservations') ?></div>
    <a href="<?= BASE_URL ?>/?page=reservations" class="sidebar-link <?= str_starts_with($currentPage,'reservations')?'active':'' ?>">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      <?= t('nav_reservations') ?>
    </a>
    <a href="<?= BASE_URL ?>/?page=clients" class="sidebar-link <?= str_starts_with($currentPage,'clients')?'active':'' ?>">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
      <?= t('nav_clients') ?>
    </a>
    <a href="<?= BASE_URL ?>/?page=payments" class="sidebar-link <?= str_starts_with($currentPage,'payments')?'active':'' ?>">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
      <?= t('nav_payments') ?>
    </a>

    <div class="sidebar-section"><?= t('nav_client_portal') ?></div>
    <a href="<?= BASE_URL ?>/?page=client" target="_blank" class="sidebar-link">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>
      <?= t('nav_client_portal') ?>
    </a>

    <div class="sidebar-section"><?= t('nav_admin') ?></div>
    <a href="<?= BASE_URL ?>/?page=audit" class="sidebar-link <?= str_starts_with($currentPage,'audit')?'active':'' ?>">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14,2 14,8 20,8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
      <?= t('nav_audit') ?>
    </a>
    <a href="<?= BASE_URL ?>/?page=users" class="sidebar-link <?= str_starts_with($currentPage,'users')?'active':'' ?>">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 10-16 0"/></svg>
      <?= t('nav_users') ?>
    </a>
  </nav>

  <!-- Logout -->
  <div style="padding:12px 16px; border-top:1px solid rgba(255,255,255,.07);">
    <a href="<?= BASE_URL ?>/?page=logout"
       style="display:flex;align-items:center;gap:10px;padding:9px 14px;border-radius:8px;color:#94a3b8;font-size:14px;font-weight:500;text-decoration:none;transition:all .15s;border:1px solid rgba(255,255,255,.07);"
       onmouseover="this.style.background='rgba(239,68,68,.12)';this.style.color='#f87171';this.style.borderColor='rgba(239,68,68,.2)'"
       onmouseout="this.style.background='';this.style.color='#94a3b8';this.style.borderColor='rgba(255,255,255,.07)'">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16,17 21,12 16,7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
      <?= t('nav_logout') ?>
    </a>
  </div>
</aside>

<!-- Main content wrapper -->
<div class="main-content">
  <!-- Topbar -->
  <header class="topbar">
    <div style="display:flex;align-items:center;gap:14px;">
      <button onclick="toggleSidebar()" style="background:none;border:none;cursor:pointer;display:none;" id="menu-btn">
        <svg width="20" height="20" fill="none" stroke="#64748b" stroke-width="2" viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
      </button>
      <div>
        <h1 style="font-size:16px;font-weight:600;color:#0f172a;line-height:1.2;"><?= $pageTitle ?? t('dashboard') ?></h1>
        <?php if (!empty($pageBreadcrumb)): ?>
        <div style="font-size:12px;color:#94a3b8;"><?= $pageBreadcrumb ?></div>
        <?php endif; ?>
      </div>
    </div>
    <div style="display:flex;align-items:center;gap:14px;">
      <!-- Language switcher -->
      <div class="lang-switcher">
        <?php
        $currentUrl = '?' . http_build_query(array_filter($_GET, fn($k) => $k !== 'lang', ARRAY_FILTER_USE_KEY));
        $sep = str_contains($currentUrl, '?') && strlen($currentUrl) > 1 ? '&' : '?';
        foreach (['fr' => 'FR', 'en' => 'EN', 'ar' => 'ع'] as $code => $label):
          $isActive = ($lang === $code);
        ?>
        <a href="<?= $currentUrl . $sep ?>lang=<?= $code ?>"
           class="lang-btn <?= $isActive ? 'active' : '' ?>"><?= $label ?></a>
        <?php endforeach; ?>
      </div>
      <span style="font-size:12px;color:#94a3b8;"><?= date('d M Y') ?></span>
    </div>
  </header>

  <!-- Page body -->
  <div class="page-body">
    <?php if (!empty($flash)): ?>
    <div class="alert alert-<?= h($flash['type']) ?>">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <?php if ($flash['type']==='success'): ?><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22,4 12,14.01 9,11.01"/>
        <?php elseif($flash['type']==='danger'): ?><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
        <?php else: ?><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/><?php endif; ?>
      </svg>
      <?= h($flash['msg']) ?>
    </div>
    <?php endif; ?>

    <?= $content ?>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('open');
  document.getElementById('sidebar-overlay').classList.toggle('show');
}
if (window.innerWidth <= 768) document.getElementById('menu-btn').style.display = 'block';
window.addEventListener('resize', () => {
  document.getElementById('menu-btn').style.display = window.innerWidth <= 768 ? 'block' : 'none';
});
document.querySelectorAll('.alert').forEach(el => {
  setTimeout(() => { el.style.opacity='0'; el.style.transition='opacity .5s'; setTimeout(() => el.remove(), 500); }, 5000);
});
document.querySelectorAll('[data-confirm]').forEach(btn => {
  btn.addEventListener('click', e => {
    if (!confirm(btn.dataset.confirm || '<?= t('confirm_delete') ?>')) e.preventDefault();
  });
});
</script>
<?php if (!empty($extraScripts)) echo $extraScripts; ?>
</body>
</html>
