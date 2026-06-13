<?php $pageTitle = 'Nouvel utilisateur'; $pageBreadcrumb = 'Utilisateurs › Nouveau'; ?>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger"><?= implode('<br>', array_map('h', $errors)) ?></div>
<?php endif; ?>

<div class="card">
  <div class="card-header">
    <span class="card-title">Nouvel utilisateur</span>
    <a href="<?= BASE_URL ?>/?page=users" class="btn btn-outline btn-sm">← Retour</a>
  </div>
  <div class="card-body">
    <form method="POST">
      <div class="form-grid-2" style="margin-bottom:16px;">
        <div><label class="form-label">Prénom</label><input type="text" name="prenom" class="form-control" value="<?= h($data['prenom']) ?>"></div>
        <div><label class="form-label">Nom *</label><input type="text" name="nom" class="form-control" value="<?= h($data['nom']) ?>" required></div>
      </div>
      <div class="form-grid-2" style="margin-bottom:16px;">
        <div><label class="form-label">Identifiant *</label><input type="text" name="username" class="form-control" value="<?= h($data['username']) ?>" required autocomplete="off"></div>
        <div><label class="form-label">Rôle</label>
          <select name="role" class="form-control">
            <?php foreach(['operateur','admin'] as $r): ?>
            <option value="<?= $r ?>" <?= $data['role']===$r?'selected':'' ?>><?= ucfirst($r) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-grid-2" style="margin-bottom:24px;">
        <div><label class="form-label">Mot de passe *</label><input type="password" name="password" class="form-control" required autocomplete="new-password" minlength="6"></div>
        <div style="align-self:flex-end;padding-bottom:4px;font-size:12px;color:#94a3b8;">Minimum 6 caractères</div>
      </div>
      <div style="display:flex;gap:10px;">
        <button type="submit" class="btn btn-emerald">Créer</button>
        <a href="<?= BASE_URL ?>/?page=users" class="btn btn-outline">Annuler</a>
      </div>
    </form>
  </div>
</div>
