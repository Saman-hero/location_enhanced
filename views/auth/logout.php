<?php
session_destroy();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Déconnexion — AutoLocation</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <meta http-equiv="refresh" content="3;url=<?= BASE_URL ?>/?page=login">
  <style>
    *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
    body {
      font-family: 'Inter', sans-serif;
      background: #f7f9fb;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    .lang-bar {
      background: #fff;
      border-bottom: 1px solid #e0e3e5;
      padding: 10px 24px;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .logo-icon {
      width: 32px; height: 32px;
      background: linear-gradient(135deg, #10b981, #059669);
      border-radius: 7px;
      display: flex; align-items: center; justify-content: center;
    }
    .lang-bar span { font-size: 15px; font-weight: 700; color: #0f172a; }
    .wrapper {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px 16px;
      background: linear-gradient(160deg, #f7f9fb 0%, #eef2f7 100%);
    }
    .card {
      background: #fff;
      border-radius: 12px;
      padding: 48px 40px;
      width: 100%;
      max-width: 400px;
      text-align: center;
      box-shadow: 0 10px 40px rgba(15,23,42,.1), 0 2px 8px rgba(15,23,42,.06);
    }
    .icon-circle {
      width: 72px; height: 72px;
      background: linear-gradient(135deg, #0f172a, #1e293b);
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      margin: 0 auto 24px;
      box-shadow: 0 8px 20px rgba(15,23,42,.2);
    }
    h1 { font-size: 22px; font-weight: 700; color: #0f172a; margin-bottom: 8px; }
    p  { font-size: 14px; color: #64748b; line-height: 1.6; margin-bottom: 28px; }
    .progress-bar {
      width: 100%; height: 3px;
      background: #e2e8f0;
      border-radius: 9999px;
      overflow: hidden;
      margin-bottom: 20px;
    }
    .progress-fill {
      height: 100%;
      background: linear-gradient(90deg, #10b981, #059669);
      border-radius: 9999px;
      animation: fill 3s linear forwards;
    }
    @keyframes fill { from { width:0% } to { width:100% } }
    .redirect-note { font-size: 12px; color: #94a3b8; margin-bottom: 24px; }
    .btn {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 11px 24px;
      background: #0f172a;
      color: #fff;
      border: none;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 600;
      font-family: 'Inter', sans-serif;
      cursor: pointer;
      text-decoration: none;
      transition: background .15s;
    }
    .btn:hover { background: #1e293b; }
    .status-bar {
      background: #fff;
      border-top: 1px solid #e0e3e5;
      padding: 10px 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      font-size: 12px;
      color: #006c49;
    }
    .status-dot {
      width: 7px; height: 7px;
      background: #10b981;
      border-radius: 50%;
      animation: pulse 2s infinite;
    }
    @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }
    .footer {
      background: #f7f9fb;
      border-top: 1px solid #e0e3e5;
      padding: 12px 24px;
      text-align: center;
      font-size: 12px;
      color: #76777d;
    }
  </style>
</head>
<body>

<div class="lang-bar">
  <div class="logo-icon">
    <svg width="16" height="16" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24">
      <path d="M19 17H5l-2-4V7a1 1 0 011-1h14a1 1 0 011 1v6l-2 4z"/>
      <circle cx="7.5" cy="17.5" r="1.5"/><circle cx="16.5" cy="17.5" r="1.5"/>
    </svg>
  </div>
  <span>AutoLocation</span>
</div>

<div class="wrapper">
  <div class="card">
    <div class="icon-circle">
      <svg width="32" height="32" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24">
        <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
        <polyline points="16,17 21,12 16,7"/>
        <line x1="21" y1="12" x2="9" y2="12"/>
      </svg>
    </div>
    <h1>Déconnexion réussie</h1>
    <p>Vous avez été déconnecté de votre session.<br>Merci d'avoir utilisé AutoLocation.</p>

    <div class="progress-bar">
      <div class="progress-fill"></div>
    </div>
    <div class="redirect-note">Redirection vers la connexion dans 3 secondes…</div>

    <a href="<?= BASE_URL ?>/?page=login" class="btn">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <line x1="19" y1="12" x2="5" y2="12"/>
        <polyline points="12,19 5,12 12,5"/>
      </svg>
      Se reconnecter
    </a>
  </div>
</div>

<div class="status-bar">
  <div class="status-dot"></div>
  Système opérationnel — Tous les services actifs
</div>
<div class="footer">© <?= date('Y') ?> AutoLocation Fleet Management. Tous droits réservés.</div>

</body>
</html>
