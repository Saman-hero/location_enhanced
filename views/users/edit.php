<?php $pageTitle = t('edit_user').' '.$data['username']; $pageBreadcrumb = t('users').' › '.t('btn_edit'); ?>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger"><?= implode('<br>', array_map('h', $errors)) ?></div>
<?php endif; ?>

<div class="card">
  <div class="card-header">
    <span class="card-title"><?= t('edit_user') ?> <?= h($data['username']) ?></span>
    <a href="<?= BASE_URL ?>/?page=users" class="btn btn-outline btn-sm">← <?= t('btn_back') ?></a>
  </div>
  <div class="card-body">
    <form method="POST">
      <div class="form-grid-2" style="margin-bottom:16px;">
        <div><label class="form-label"><?= t('firstname') ?></label><input type="text" name="prenom" class="form-control" value="<?= h($data['prenom']??'') ?>"></div>
        <div><label class="form-label"><?= t('lastname') ?> *</label><input type="text" name="nom" class="form-control" value="<?= h($data['nom']) ?>" required></div>
      </div>
      <div style="margin-bottom:16px;">
        <label class="form-label"><?= t('username_col') ?></label>
        <input type="text" class="form-control" value="<?= h($data['username']) ?>" disabled style="background:#f8fafc;color:#94a3b8;">
      </div>
      <div style="margin-bottom:16px;"><label class="form-label"><?= t('role') ?></label>
        <select name="role" class="form-control" style="max-width:200px;">
          <?php foreach(['operateur','admin'] as $r): ?>
          <option value="<?= $r ?>" <?= ($data['role']??'')===$r?'selected':'' ?>><?= $r === 'admin' ? t('role_admin') : t('role_operator') ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div style="margin-bottom:8px;"><label class="form-label"><?= t('new_password') ?></label><input type="password" name="password" class="form-control" autocomplete="new-password" placeholder="<?= t('password_leave_empty') ?>"></div>
      <div style="margin-bottom:24px;font-size:12px;color:#94a3b8;"><?= t('password_keep') ?></div>
      <div style="display:flex;gap:10px;">
        <button type="submit" class="btn btn-emerald"><?= t('btn_save') ?></button>
        <a href="<?= BASE_URL ?>/?page=users" class="btn btn-outline"><?= t('btn_cancel') ?></a>
      </div>
    </form>
  </div>
</div>
