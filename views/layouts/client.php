<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $pageTitle ?? 'AutoLocation' ?> — AutoLocation</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { inter: ['Inter', 'sans-serif'] },
          colors: {
            brand:   { 900:'#0f172a', 800:'#1e293b', 700:'#334155', 600:'#475569', 500:'#64748b' },
            emerald: { 500:'#10b981', 400:'#34d399', 600:'#059669' },
            azure:   { 500:'#3b82f6', 600:'#2563eb', 100:'#dbeafe' },
          }
        }
      }
    }
  </script>
  <style>
    * { font-family: 'Inter', sans-serif; }
    body { background: #f8fafc; }

    .client-nav { background: #0f172a; height: 64px; display: flex; align-items: center; justify-content: space-between; padding: 0 32px; position: sticky; top: 0; z-index: 50; }

    .btn { display:inline-flex; align-items:center; gap:6px; padding:9px 18px; border-radius:8px; font-size:14px; font-weight:500; cursor:pointer; text-decoration:none; border:none; transition:all .15s; }
    .btn-primary  { background:#0f172a; color:#fff; }
    .btn-primary:hover  { background:#1e293b; }
    .btn-emerald  { background:#10b981; color:#fff; }
    .btn-emerald:hover  { background:#059669; }
    .btn-outline  { background:#fff; color:#0f172a; border:1px solid #e2e8f0; }
    .btn-outline:hover  { background:#f8fafc; }
    .btn-sm { padding:6px 14px; font-size:13px; }

    .card { background:#fff; border-radius:12px; border:1px solid #e2e8f0; box-shadow:0 1px 4px rgba(15,23,42,.05); }

    .badge { display:inline-flex; align-items:center; padding:3px 10px; border-radius:9999px; font-size:11px; font-weight:600; }
    .badge-green   { background:#d1fae5; color:#065f46; }
    .badge-blue    { background:#dbeafe; color:#1e40af; }
    .badge-yellow  { background:#fef9c3; color:#92400e; }
    .badge-red     { background:#fee2e2; color:#991b1b; }
    .badge-gray    { background:#f1f5f9; color:#475569; }
    .badge-premium { background:#fef3c7; color:#92400e; }

    .form-label { display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:5px; }
    .form-control { width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; font-size:14px; color:#0f172a; outline:none; transition:border .15s; background:#fff; }
    .form-control:focus { border-color:#3b82f6; box-shadow:0 0 0 3px rgba(59,130,246,.1); }
    select.form-control { appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 12px center; padding-right:32px; }

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
    .hero::before { content:''; position:absolute; top:-60px; right:-60px; width:400px; height:400px; background:radial-gradient(circle,rgba(16,185,129,.15) 0%,transparent 70%); pointer-events:none; }

    .client-footer { background:#0f172a; color:#94a3b8; padding:40px 32px; margin-top:80px; }

    .vehicles-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:24px; }
    @media(max-width:1024px) { .vehicles-grid { grid-template-columns:repeat(2,1fr); } }
    @media(max-width:640px)  { .vehicles-grid { grid-template-columns:1fr; } .hero { padding:40px 0 60px; } }
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
      <div style="color:#f1f5f9;font-weight:700;font-size:15px;line-height:1.1;">AutoLocation</div>
      <div style="color:#475569;font-size:11px;">Location de véhicules</div>
    </div>
  </a>
  <div style="display:flex;align-items:center;gap:16px;">
    <a href="<?= BASE_URL ?>/?page=client" style="color:#94a3b8;font-size:14px;text-decoration:none;font-weight:500;transition:color .15s;" onmouseover="this.style.color='#f1f5f9'" onmouseout="this.style.color='#94a3b8'">Nos véhicules</a>
    <a href="tel:+212600000000" style="color:#94a3b8;font-size:14px;text-decoration:none;font-weight:500;display:flex;align-items:center;gap:6px;">
      <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 10.8a19.79 19.79 0 01-3.07-8.67A2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 14.92z"/></svg>
      +212 600-000-000
    </a>
    <a href="<?= BASE_URL ?>/" class="btn btn-emerald btn-sm">
      <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
      Espace admin
    </a>
  </div>
</nav>

<?= $content ?>

<!-- Footer -->
<footer class="client-footer">
  <div style="max-width:1200px;margin:0 auto;display:grid;grid-template-columns:repeat(3,1fr);gap:40px;padding-bottom:32px;border-bottom:1px solid rgba(255,255,255,.07);">
    <div>
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;">
        <div style="width:32px;height:32px;background:linear-gradient(135deg,#10b981,#059669);border-radius:7px;display:flex;align-items:center;justify-content:center;">
          <svg width="16" height="16" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 17H5l-2-4V7a1 1 0 011-1h14a1 1 0 011 1v6l-2 4z"/><circle cx="7.5" cy="17.5" r="1.5"/><circle cx="16.5" cy="17.5" r="1.5"/></svg>
        </div>
        <span style="color:#f1f5f9;font-weight:700;font-size:15px;">AutoLocation</span>
      </div>
      <p style="font-size:13px;line-height:1.7;color:#64748b;">Votre partenaire de confiance pour la location de véhicules au Maroc. Flotte moderne, service premium.</p>
    </div>
    <div>
      <div style="color:#f1f5f9;font-weight:600;font-size:14px;margin-bottom:14px;">Navigation</div>
      <div style="display:flex;flex-direction:column;gap:8px;">
        <a href="<?= BASE_URL ?>/?page=client" style="color:#64748b;font-size:13px;text-decoration:none;" onmouseover="this.style.color='#10b981'" onmouseout="this.style.color='#64748b'">Parcourir la flotte</a>
        <a href="#" style="color:#64748b;font-size:13px;text-decoration:none;" onmouseover="this.style.color='#10b981'" onmouseout="this.style.color='#64748b'">Nos tarifs</a>
        <a href="#" style="color:#64748b;font-size:13px;text-decoration:none;" onmouseover="this.style.color='#10b981'" onmouseout="this.style.color='#64748b'">Conditions générales</a>
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
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#64748b;">
          <svg width="14" height="14" fill="none" stroke="#10b981" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Casablanca, Maroc
        </div>
      </div>
    </div>
  </div>
  <div style="max-width:1200px;margin:0 auto;padding-top:20px;display:flex;justify-content:space-between;align-items:center;">
    <span style="font-size:12px;color:#334155;">© <?= date('Y') ?> AutoLocation — Tous droits réservés</span>
    <span style="font-size:12px;color:#334155;">Lun–Sam : 8h–20h</span>
  </div>
</footer>

<script>
  document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
      const t = document.querySelector(a.getAttribute('href'));
      if (t) { e.preventDefault(); t.scrollIntoView({behavior:'smooth'}); }
    });
  });
</script>
</body>
</html>
