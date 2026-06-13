<?php
// Variables provided by AuthController via view()
$error = $error ?? '';
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" <?= $isRtl ? 'dir="rtl"' : '' ?>>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= t('login_title') ?> — AutoLocation</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <?php if ($isRtl): ?>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">
  <?php endif; ?>
  <style>
    *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }

    body {
      font-family: <?= $isRtl ? "'Cairo'" : "'Inter'" ?>, sans-serif;
      background: #f7f9fb;
      color: #191c1e;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      direction: <?= $isRtl ? 'rtl' : 'ltr' ?>;
    }

    /* ── Top bar ── */
    .top-bar {
      background: #fff;
      border-bottom: 1px solid #e0e3e5;
      padding: 10px 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .brand {
      display: flex;
      align-items: center;
      gap: 10px;
      text-decoration: none;
    }
    .brand-icon {
      width: 32px; height: 32px;
      background: linear-gradient(135deg, #10b981, #059669);
      border-radius: 7px;
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
    }
    .brand-name { font-size: 15px; font-weight: 700; color: #0f172a; }

    /* Language switcher */
    .lang-switcher {
      display: flex;
      gap: 4px;
      align-items: center;
      background: #f1f5f9;
      border-radius: 8px;
      padding: 4px;
    }
    .lang-btn {
      display: flex;
      align-items: center;
      gap: 5px;
      padding: 5px 10px;
      border-radius: 6px;
      border: none;
      background: transparent;
      font-size: 12px;
      font-weight: 500;
      color: #64748b;
      cursor: pointer;
      text-decoration: none;
      transition: all .15s;
      white-space: nowrap;
    }
    .lang-btn:hover { background: #fff; color: #0f172a; }
    .lang-btn.active {
      background: #fff;
      color: #0f172a;
      box-shadow: 0 1px 3px rgba(15,23,42,.1);
    }
    .lang-flag { font-size: 16px; line-height: 1; }

    /* ── Login wrapper ── */
    .login-wrapper {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px 16px;
      background: linear-gradient(160deg, #f7f9fb 0%, #eef2f7 100%);
    }
    .login-card {
      background: #fff;
      border-radius: 12px;
      padding: 40px;
      width: 100%;
      max-width: 420px;
      box-shadow: 0 10px 40px rgba(15,23,42,.1), 0 2px 8px rgba(15,23,42,.06);
    }

    /* ── Logo ── */
    .logo-area { text-align: center; margin-bottom: 32px; }
    .logo-circle {
      width: 64px; height: 64px;
      background: linear-gradient(135deg, #0f172a, #1e293b);
      border-radius: 16px;
      display: flex; align-items: center; justify-content: center;
      margin: 0 auto 16px;
      box-shadow: 0 8px 20px rgba(15,23,42,.2);
    }
    .logo-area h1 { font-size: 26px; font-weight: 700; color: #0f172a; margin-bottom: 4px; }
    .logo-area p  { font-size: 13px; color: #76777d; }

    /* ── Error ── */
    .error-box {
      background: #fee2e2;
      border: 1px solid #fecaca;
      border-radius: 8px;
      padding: 11px 14px;
      font-size: 13px;
      color: #991b1b;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    /* ── Form ── */
    .form-group { margin-bottom: 20px; }
    .form-label {
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: 11px;
      font-weight: 600;
      color: #374151;
      margin-bottom: 6px;
      text-transform: uppercase;
      letter-spacing: .05em;
    }
    .form-label svg { width: 15px; height: 15px; color: #10b981; }

    .input-wrapper {
      position: relative;
      display: flex;
      align-items: center;
      border: 1.5px solid #c6c6cd;
      border-radius: 8px;
      background: #fff;
      transition: border-color .2s, box-shadow .2s;
    }
    .input-wrapper:focus-within {
      border-color: #0f172a;
      box-shadow: 0 0 0 3px rgba(15,23,42,.08);
    }
    .icon-left {
      position: absolute;
      <?= $isRtl ? 'right' : 'left' ?>: 12px;
      color: #94a3b8;
      pointer-events: none;
    }
    .input-wrapper input {
      flex: 1;
      border: none; outline: none;
      padding: 12px <?= $isRtl ? '42px' : '12px' ?> 12px <?= $isRtl ? '12px' : '42px' ?>;
      font-size: 14px;
      font-family: inherit;
      background: transparent;
      color: #0f172a;
      border-radius: 8px;
      text-align: <?= $isRtl ? 'right' : 'left' ?>;
    }
    .input-wrapper input::placeholder { color: #94a3b8; }
    .toggle-btn {
      background: none; border: none; cursor: pointer;
      padding: 0 12px;
      color: #94a3b8;
      display: flex; align-items: center;
      transition: color .15s;
    }
    .toggle-btn:hover { color: #0f172a; }

    .remember-row {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 24px;
      flex-direction: <?= $isRtl ? 'row-reverse' : 'row' ?>;
      justify-content: <?= $isRtl ? 'flex-end' : 'flex-start' ?>;
    }
    .remember-row input[type=checkbox] { width:16px; height:16px; accent-color:#0f172a; cursor:pointer; }
    .remember-row label { font-size:13px; color:#475569; cursor:pointer; }

    .submit-btn {
      width: 100%; padding: 13px;
      background: #0f172a; color: #fff;
      border: none; border-radius: 8px;
      font-size: 15px; font-weight: 600;
      font-family: inherit;
      cursor: pointer;
      transition: background .15s;
      display: flex; align-items: center; justify-content: center; gap: 8px;
    }
    .submit-btn:hover:not(:disabled) { background: #1e293b; }
    .submit-btn:disabled { opacity: .7; cursor: not-allowed; }

    .spinner {
      width: 18px; height: 18px;
      border: 2px solid rgba(255,255,255,.3);
      border-top-color: #fff;
      border-radius: 50%;
      animation: spin .7s linear infinite;
      display: none;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    .forgot { text-align: center; margin-top: 16px; }
    .forgot a { font-size: 13px; color: #f97316; text-decoration: none; }
    .forgot a:hover { opacity: .8; }

    /* ── Status / Footer ── */
    .status-bar {
      background: #fff; border-top: 1px solid #e0e3e5;
      padding: 10px 24px;
      display: flex; align-items: center; justify-content: center; gap: 8px;
      font-size: 12px; color: #006c49;
    }
    .status-dot {
      width: 7px; height: 7px; background: #10b981;
      border-radius: 50%; animation: pulse 2s infinite;
    }
    @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }
    .footer {
      background: #f7f9fb; border-top: 1px solid #e0e3e5;
      padding: 12px 24px; text-align: center;
      font-size: 12px; color: #76777d;
    }
  </style>
</head>
<body>

<!-- Top bar -->
<div class="top-bar">
  <a href="<?= BASE_URL ?>/" class="brand">
    <div class="brand-icon">
      <svg width="16" height="16" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24">
        <path d="M19 17H5l-2-4V7a1 1 0 011-1h14a1 1 0 011 1v6l-2 4z"/>
        <circle cx="7.5" cy="17.5" r="1.5"/><circle cx="16.5" cy="17.5" r="1.5"/>
      </svg>
    </div>
    <span class="brand-name">AutoLocation</span>
  </a>

  <!-- Language switcher -->
  <div class="lang-switcher">
    <a href="<?= $langSwitchBase ?>fr" class="lang-btn <?= $lang==='fr'?'active':'' ?>">
      <span class="lang-flag">🇫🇷</span> FR
    </a>
    <a href="<?= $langSwitchBase ?>en" class="lang-btn <?= $lang==='en'?'active':'' ?>">
      <span class="lang-flag">🇬🇧</span> EN
    </a>
    <a href="<?= $langSwitchBase ?>ar" class="lang-btn <?= $lang==='ar'?'active':'' ?>">
      <span class="lang-flag">🇲🇦</span> AR
    </a>
  </div>
</div>

<!-- Login -->
<div class="login-wrapper">
  <div class="login-card">

    <div class="logo-area">
      <div class="logo-circle">
        <svg width="28" height="28" fill="none" stroke="#fff" stroke-width="2.2" viewBox="0 0 24 24">
          <path d="M19 17H5l-2-4V7a1 1 0 011-1h14a1 1 0 011 1v6l-2 4z"/>
          <circle cx="7.5" cy="17.5" r="1.5"/><circle cx="16.5" cy="17.5" r="1.5"/>
        </svg>
      </div>
      <h1><?= t('app_name') ?></h1>
      <p><?= t('app_subtitle') ?></p>
    </div>

    <?php if ($error): ?>
    <div class="error-box">
      <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><circle cx="12" cy="16" r=".5" fill="currentColor"/></svg>
      <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <form method="POST" id="login-form">
      <div class="form-group">
        <label class="form-label">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
          <?= t('username') ?>
        </label>
        <div class="input-wrapper">
          <span class="icon-left"><svg width="20" height="20" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg></span>
          <input type="text" name="username"
                 value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                 placeholder="<?= t('username_ph') ?>" required autofocus>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
          <?= t('password') ?>
        </label>
        <div class="input-wrapper">
          <span class="icon-left"><svg width="20" height="20" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg></span>
          <input type="password" name="password" id="password"
                 placeholder="<?= t('password_ph') ?>" required>
          <button type="button" class="toggle-btn" id="toggle-pass">
            <svg id="eye-icon" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          </button>
        </div>
      </div>

      <div class="remember-row">
        <input type="checkbox" id="remember" name="remember">
        <label for="remember"><?= t('remember') ?></label>
      </div>

      <button type="submit" class="submit-btn" id="submit-btn">
        <div class="spinner" id="spinner"></div>
        <span id="btn-text"><?= t('submit') ?></span>
      </button>

      <div class="forgot">
        <a href="#"><?= t('forgot') ?></a>
      </div>
    </form>
  </div>
</div>

<div class="status-bar">
  <div class="status-dot"></div>
  <?= t('status') ?>
</div>
<div class="footer"><?= t('footer', date('Y')) ?></div>

<script>
  const EYE_OPEN = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
  const EYE_OFF  = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>';
  document.getElementById('toggle-pass').addEventListener('click', function() {
    const input = document.getElementById('password');
    const icon  = document.getElementById('eye-icon');
    input.type  = input.type === 'password' ? 'text' : 'password';
    icon.innerHTML = input.type === 'password' ? EYE_OPEN : EYE_OFF;
  });

  document.getElementById('login-form').addEventListener('submit', function() {
    const btn    = document.getElementById('submit-btn');
    btn.disabled = true;
    document.getElementById('spinner').style.display = 'block';
    document.getElementById('btn-text').textContent  = '<?= t('submitting') ?>';
  });
</script>
</body>
</html>
