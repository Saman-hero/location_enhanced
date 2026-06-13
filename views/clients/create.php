<?php $pageTitle = 'Nouveau client'; $pageBreadcrumb = 'Clients › Nouveau'; ?>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger"><?= implode('<br>', array_map('h', $errors)) ?></div>
<?php endif; ?>

<div class="card">
  <div class="card-header">
    <span class="card-title">Nouveau client</span>
    <a href="<?= BASE_URL ?>/?page=clients" class="btn btn-outline btn-sm">← Retour</a>
  </div>
  <div class="card-body">
    <form method="POST">
      <div class="form-grid-2" style="margin-bottom:16px;">
        <div><label class="form-label">Prénom *</label><input type="text" name="prenom" class="form-control" value="<?= h($data['prenom']) ?>" required></div>
        <div><label class="form-label">Nom *</label><input type="text" name="nom" class="form-control" value="<?= h($data['nom']) ?>" required></div>
      </div>
      <div class="form-grid-2" style="margin-bottom:16px;">
        <div><label class="form-label">CIN</label><input type="text" name="cin" class="form-control" value="<?= h($data['cin']) ?>"></div>
        <div><label class="form-label">Téléphone</label><input type="text" name="telephone" class="form-control" value="<?= h($data['telephone']) ?>"></div>
      </div>
      <div class="form-grid-2" style="margin-bottom:16px;">
        <div><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= h($data['email']) ?>"></div>
        <div><label class="form-label">Ville</label><input type="text" name="ville" class="form-control" value="<?= h($data['ville']) ?>"></div>
      </div>
      <div style="margin-bottom:16px;"><label class="form-label">Adresse</label><input type="text" name="adresse" class="form-control" value="<?= h($data['adresse']) ?>"></div>

      <hr style="border:none;border-top:1px solid #f1f5f9;margin:20px 0;">
      <div style="font-size:13px;font-weight:600;color:#64748b;margin-bottom:12px;">Permis de conduire</div>
      <div class="form-grid-3" style="margin-bottom:16px;">
        <div><label class="form-label">N° permis</label><input type="text" name="permis_numero" class="form-control" value="<?= h($data['permis_numero']) ?>"></div>
        <div><label class="form-label">Catégorie</label>
          <select name="permis_categorie" class="form-control">
            <?php foreach(['A','B','C','D','BE','CE'] as $cat): ?>
            <option value="<?= $cat ?>" <?= $data['permis_categorie']===$cat?'selected':'' ?>><?= $cat ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div><label class="form-label">Expiration</label><input type="date" name="permis_expiration" class="form-control" value="<?= h($data['permis_expiration']) ?>"></div>
      </div>

      <hr style="border:none;border-top:1px solid #f1f5f9;margin:20px 0;">
      <div class="form-grid-3" style="margin-bottom:16px;">
        <div><label class="form-label">Type client</label>
          <select name="type_client" class="form-control">
            <?php foreach(['particulier','entreprise'] as $t): ?>
            <option value="<?= $t ?>" <?= $data['type_client']===$t?'selected':'' ?>><?= ucfirst($t) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div><label class="form-label">Entreprise</label><input type="text" name="entreprise" class="form-control" value="<?= h($data['entreprise']) ?>"></div>
        <div><label class="form-label">Statut</label>
          <select name="statut" class="form-control">
            <?php foreach(['actif','inactif','liste_noire'] as $s): ?>
            <option value="<?= $s ?>" <?= $data['statut']===$s?'selected':'' ?>><?= ucfirst(str_replace('_',' ',$s)) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div style="margin-bottom:24px;"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="3"><?= h($data['notes']) ?></textarea></div>
      <div style="display:flex;gap:10px;">
        <button type="submit" class="btn btn-emerald">Enregistrer</button>
        <a href="<?= BASE_URL ?>/?page=clients" class="btn btn-outline">Annuler</a>
      </div>
    </form>
  </div>
</div>
