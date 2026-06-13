<?php $pageTitle = 'Modifier '.$data['username']; $pageBreadcrumb = 'Utilisateurs › Modifier'; ?>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger"><?= implode('<br>', array_map('h', $errors)) ?></div>
<?php endif; ?>

<div class="card">
  <div class="card-header">
    <span class="card-title">Modifier <?= h($data['username']) ?></span>
    <a href="<?= BASE_URL ?>/?page=users" class="btn btn-outline btn-sm">← Retour</a>
  </div>
  <div class="card-body">
    <form method="POST">
      <div class="form-grid-2" style="margin-bottom:16px;">
        <div><label class="form-label">Prénom</label><input type="text" name="prenom" class="form-control" value="<?= h($data['prenom']??'') ?>"></div>
        <div><label class="form-label">Nom *</label><input type="text" name="nom" class="form-control" value="<?= h($data['nom']) ?>" required></div>
      </div>
      <div style="margin-bottom:16px;">
        <label class="form-label">Identifiant</label>
        <input type="text" class="form-control" value="<?= h($data['username']) ?>" disabled style="background:#f8fafc;color:#94a3b8;">
      </div>
      <div style="margin-bottom:16px;"><label class="form-label">Rôle</label>
        <select name="role" class="form-control" style="max-width:200px;">
          <?php foreach(['operateur','admin'] as $r): ?>
          <option value="<?= $r ?>" <?= ($data['role']??'')===$r?'selected':'' ?>><?= ucfirst($r) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div style="margin-bottom:8px;"><label class="form-label">Nouveau mot de passe</label><input type="password" name="password" class="form-control" autocomplete="new-password" placeholder="Laisser vide pour ne pas changer"></div>
      <div style="margin-bottom:24px;font-size:12px;color:#94a3b8;">Minimum 6 caractères. Laissez vide pour conserver le mot de passe actuel.</div>
      <div style="display:flex;gap:10px;">
        <button type="submit" class="btn btn-emerald">Enregistrer</button>
        <a href="<?= BASE_URL ?>/?page=users" class="btn btn-outline">Annuler</a>
      </div>
    </form>
  </div>
</div>
