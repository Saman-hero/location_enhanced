<?php $__lang = current_lang(); $__dir = $__lang === 'ar' ? 'rtl' : 'ltr'; ?>
<!DOCTYPE html>
<html lang="<?= $__lang ?>" dir="<?= $__dir ?>">
<head>
<meta charset="UTF-8">
<title>404 — AutoLocation</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<style>
  body { font-family: 'Inter', sans-serif; background: #f8fafc; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
  .box { text-align: center; padding: 60px 40px; }
  .num { font-size: 80px; font-weight: 700; color: #e2e8f0; line-height: 1; }
  h1 { font-size: 22px; font-weight: 600; color: #0f172a; margin: 12px 0 8px; }
  p { color: #64748b; margin-bottom: 24px; }
  a { display: inline-block; padding: 10px 24px; background: #0f172a; color: #fff; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 600; }
</style>
</head>
<body>
  <div class="box">
    <div class="num">404</div>
    <h1><?= t('404_title') ?></h1>
    <p><?= t('404_msg') ?></p>
    <a href="<?= BASE_URL ?>/"><?= t('404_back') ?></a>
  </div>
</body>
</html>
