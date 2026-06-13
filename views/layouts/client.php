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
  <script src="https://cdn.tailwindcss.com"></script>
  <script>tailwind.config={theme:{extend:{colors:{emerald:{500:'#10b981',400:'#34d399',600:'#059669'},azure:{500:'#3b82f6',600:'#2563eb',100:'#dbeafe'}}}}}</script>
  <style>
    * { font-family: <?= $isRtl ? "'Noto Sans Arabic'" : "'Inter'" ?>, sans-serif; }
    body { background:#f8fafc; }

    .client-nav { background:#0f172a; height:64px; display:flex; align-items:center; justify-content:space-between; padding:0 32px; position:sticky; top:0; z-index:50; }

    .btn { display:inline-flex; align-items:center; gap:6px; padding:9px 18px; border-radius:8px; font-size:14px; font-weight:500; cursor:pointer; text-decoration:none; border:none; transition:all .15s; }
    .btn-primary { background:#0f172a; color:#fff; }
    .btn-emerald { background:#10b981; color:#fff; }
    .btn-emerald:hover { background:#059669; }
    .btn-outline { background:#fff; color:#0f172a; border:1px solid #e2e8f0; }
    .btn-outline:hover { background:#f8fafc; }
    .btn-sm { padding:6px 14px; font-size:13px; }

    .card { background:#fff; border-radius:12px; border:1px solid #e2e8f0; box-shadow:0 1px 4px rgba(15,23,42,.05); }

    .badge { display:inline-flex; align-items:center; padding:3px 10px; border-radius:9999px; font-size:11px; font-weight:600; }
    .badge-green   { background:#d1fae5; color:#065f46; }
    .badge-blue    { background:#dbeafe; color:#1e40af; }
    .badge-yellow  { background:#fef9c3; color:#92400e; }
    .badge-red     { background:#fee2e2; color:#991b1b; }
    .badge-gray    { background:#f1f5f9; color:#475569; }

    .form-label { display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:5px; }
    .form-control { width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; font-size:14px; color:#0f172a; outline:none; transition:border .15s; background:#fff; text-align:<?= $isRtl ? 'right' : 'left' ?>; }
    .form-control:focus { border-color:#3b82f6; box-shadow:0 0 0 3px rgba(59,130,246,.1); }
    select.form-control { appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:<?= $isRtl ? 'left' : 'right' ?> 12px center; <?= $isRtl ? 'padding-left:32px;' : 'padding-right:32px;' ?> }

    .alert { padding:12px 16px; border-radius:8px; font-size:14px; margin-bottom:18px; display:flex; align-items:center; gap:10px; }
    .alert-success { background:#d1fae5; color:#065f46; border:1px solid #a7f3d0; }
    .alert-danger  { background:#fee2e2; color:#991b1b; border:1px solid #fecaca; }

    .vehicle-card { background:#fff; border-radius:12px; border:1px solid #e2e8f0; overflow:hidden; transition:all .2s; box-shadow:0 1px 4px rgba(15,23,42,.05); }
    .vehicle-card:hover { box-shadow:0 10px 24px rgba(15,23,42,.1); transform:translateY(-3px); }
    .vehicle-img { width:100%; height:200px; object-fit:cover; background:#f1f5f9; }
    .vehicle-img-placeholder { width:100%; height:200px; background:linear-gradient(135deg,#f1f5f9,#e2e8f0); display:flex; align-items:center; justify-content:center; }

    .spec-pill { display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border-radius:6px; background:#f8fafc; border:1px solid #e2e8f0; font-size:12px; color:#475569; font-weight:500; }

    .search-box { background:#fff; border-radius:16px; padding:24px 28px; box-shadow:0 4px 24px rgba(15,23,42,.12); border:1px solid #e2e8f0; }

    .hero { background:linear-gradient(135deg,#0f172a 0%,#1e293b 60%,#0f2a1a 100%); padding:64px 0 80px; position:relative; overflow:hidden; }
    .hero::before { content:''; position:absolute; top:-60px; <?= $isRtl ? 'left:-60px;' : 'right:-60px;' ?> width:400px; height:400px; background:radial-gradient(circle,rgba(16,185,129,.15) 0%,transparent 70%); pointer-events:none; }

    .client-footer { background:#0f172a; color:#94a3b8; padding:40px 32px; margin-top:80px; }
    .vehicles-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:24px; }

    /* Language switcher */
    .lang-switcher { display:flex; align-items:center; gap:4px; background:rgba(255,255,255,.08); border-radius:8px; padding:3px; }
    .lang-btn { padding:4px 10px; border-radius:6px; font-size:12px; font-weight:600; text-decoration:none; color:#94a3b8; transition:all .15s; }
    .lang-btn.active { background:rgba(255,255,255,.15); color:#fff; }
    .lang-btn:hover:not(.active) { color:#f1f5f9; }

    @media(max-width:1024px) { .vehicles-grid { grid-template-columns:repeat(2,1fr); } }
    @media(max-width:640px)  { .vehicles-grid { grid-template-columns:1fr; } .hero { padding:40px 0 60px; } .client-nav { padding:0 16px; } }

    /* RTL fix: numbers always render left-to-right */
    .num { direction: ltr; unicode-bidi: isolate; display: inline-block; }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="client-nav">
  <a href="<?= BASE_URL ?>/?page=client" style="display:flex;align-items:center;gap:10px;text-decoration:none;">
    <div style="width:36px;height:36px;background:linear-gradient(135deg,#10b981,#059669);border-radius:8px;display:flex;align-items:center;justify-content:center;">
      <svg width="18" height="18" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 17H5l-2-4V7a1 1 0 011-1h14a1 1 0 011 1v6l-2 4z"/><circle cx="7.5" cy="17.5" r="1.5"/><circle cx="16.5" cy="17.5" r="1.5"/></svg>
    </div>
    <div>
      <div style="color:#f1f5f9;font-weight:700;font-size:15px;line-height:1.1;"><?= t('app_name') ?></div>
      <div style="color:#475569;font-size:11px;"><?= t('app_subtitle') ?></div>
    </div>
  </a>
  <div style="display:flex;align-items:center;gap:16px;">
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
    <a href="<?= BASE_URL ?>/?page=client" style="color:#94a3b8;font-size:14px;text-decoration:none;font-weight:500;" onmouseover="this.style.color='#f1f5f9'" onmouseout="this.style.color='#94a3b8'"><?= t('nav_vehicles') ?></a>
    <a href="<?= BASE_URL ?>/" class="btn btn-emerald btn-sm">
      <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
      <?= t('nav_dashboard') ?>
    </a>
  </div>
</nav>

<?= $content ?>

<!-- Footer -->
<footer class="client-footer">
  <div style="max-width:1200px;margin:0 auto;display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:40px;padding-bottom:32px;border-bottom:1px solid rgba(255,255,255,.07);">
    <div>
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;">
        <div style="width:32px;height:32px;background:linear-gradient(135deg,#10b981,#059669);border-radius:7px;display:flex;align-items:center;justify-content:center;">
          <svg width="16" height="16" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 17H5l-2-4V7a1 1 0 011-1h14a1 1 0 011 1v6l-2 4z"/><circle cx="7.5" cy="17.5" r="1.5"/><circle cx="16.5" cy="17.5" r="1.5"/></svg>
        </div>
        <span style="color:#f1f5f9;font-weight:700;font-size:15px;"><?= t('app_name') ?></span>
      </div>
      <p style="font-size:13px;line-height:1.7;color:#64748b;"><?= t('app_subtitle') ?></p>
    </div>
    <div>
      <div style="color:#f1f5f9;font-weight:600;font-size:14px;margin-bottom:14px;"><?= t('nav_vehicles') ?></div>
      <div style="display:flex;flex-direction:column;gap:8px;">
        <a href="<?= BASE_URL ?>/?page=client" style="color:#64748b;font-size:13px;text-decoration:none;" onmouseover="this.style.color='#10b981'" onmouseout="this.style.color='#64748b'"><?= t('portal_title') ?></a>
      </div>
    </div>
    <div>
      <div style="color:#f1f5f9;font-weight:600;font-size:14px;margin-bottom:14px;">Contact</div>
      <div style="display:flex;flex-direction:column;gap:10px;">
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#64748b;">
          <svg width="14" height="14" fill="none" stroke="#10b981" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 10.8a19.79 19.79 0 01-3.07-8.67A2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 14.92z"/></svg>
          +212 600-000-000
        </div>
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#64748b;">
          <svg width="14" height="14" fill="none" stroke="#10b981" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          contact@autolocation.ma
        </div>
      </div>
    </div>
  </div>
  <div style="max-width:1200px;margin:0 auto;padding-top:20px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;">
    <span style="font-size:12px;color:#334155;">© <?= date('Y') ?> <?= t('app_name') ?></span>
    <span style="font-size:12px;color:#334155;">Lun–Sam : 8h–20h</span>
  </div>
</footer>
</body>
</html>
