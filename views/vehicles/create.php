<?php $pageTitle = t('add_vehicle'); $pageBreadcrumb = t('vehicles').' › '.t('btn_new'); ?>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger"><?= implode('<br>', array_map('h', $errors)) ?></div>
<?php endif; ?>

<div class="card">
  <div class="card-header">
    <span class="card-title"><?= t('vehicle_info') ?></span>
    <a href="<?= BASE_URL ?>/?page=vehicles" class="btn btn-outline btn-sm">← <?= t('btn_back') ?></a>
  </div>
  <div class="card-body">
    <form method="POST" enctype="multipart/form-data">
      <div class="form-grid-3" style="margin-bottom:16px;">
        <div><label class="form-label"><?= t('vehicle_internal_num') ?> *</label><input type="text" name="numero" class="form-control" value="<?= h($data['numero']) ?>" placeholder="VH-001" required></div>
        <div><label class="form-label"><?= t('vehicle_plate') ?></label><input type="text" name="immatriculation" class="form-control" value="<?= h($data['immatriculation']) ?>" placeholder="123-A-45"></div>
        <div><label class="form-label"><?= t('status') ?></label>
          <select name="statut" class="form-control">
            <?php foreach(['disponible','loué','maintenance','indisponible'] as $s): ?>
            <option value="<?= $s ?>" <?= $data['statut']===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-grid-3" style="margin-bottom:16px;">
        <div><label class="form-label"><?= t('vehicle_brand') ?> *</label><input type="text" name="marque" class="form-control" value="<?= h($data['marque']) ?>" placeholder="Toyota" required></div>
        <div><label class="form-label"><?= t('vehicle_model') ?> *</label><input type="text" name="modele" class="form-control" value="<?= h($data['modele']) ?>" placeholder="Corolla" required></div>
        <div><label class="form-label"><?= t('vehicle_year') ?></label><input type="number" name="annee" class="form-control" value="<?= h($data['annee']) ?>" min="2000" max="<?= date('Y')+1 ?>"></div>
      </div>
      <div class="form-grid-3" style="margin-bottom:16px;">
        <div><label class="form-label"><?= t('vehicle_color') ?></label><input type="text" name="couleur" class="form-control" value="<?= h($data['couleur']) ?>" placeholder="Blanc"></div>
        <div><label class="form-label"><?= t('vehicle_seats') ?></label><input type="number" name="nb_places" class="form-control" value="<?= h($data['nb_places']) ?>" min="1" max="20"></div>
        <div><label class="form-label"><?= t('vehicle_category') ?></label>
          <select name="categorie" class="form-control">
            <?php foreach(['économique','berline','SUV','premium','utilitaire'] as $c): ?>
            <option value="<?= $c ?>" <?= $data['categorie']===$c?'selected':'' ?>><?= ucfirst($c) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-grid-3" style="margin-bottom:16px;">
        <div><label class="form-label"><?= t('vehicle_fuel') ?></label>
          <select name="carburant" class="form-control">
            <?php foreach(['essence','diesel','hybride','électrique'] as $c): ?>
            <option value="<?= $c ?>" <?= ($data['carburant']??'')===$c?'selected':'' ?>><?= ucfirst($c) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div><label class="form-label"><?= t('vehicle_transmission') ?></label>
          <select name="transmission" class="form-control">
            <option value="manuelle" <?= ($data['transmission']??'')==='manuelle'?'selected':'' ?>><?= t('vehicle_manual') ?></option>
            <option value="automatique" <?= ($data['transmission']??'')==='automatique'?'selected':'' ?>><?= t('vehicle_auto') ?></option>
          </select>
        </div>
        <div><label class="form-label"><?= t('vehicle_mileage') ?></label><input type="number" name="kilometrage" class="form-control" value="<?= h($data['kilometrage']) ?>" min="0"></div>
      </div>
      <div class="form-grid-2" style="margin-bottom:16px;">
        <div><label class="form-label"><?= t('vehicle_price') ?> *</label><input type="number" name="prix_jour" class="form-control" value="<?= h($data['prix_jour']) ?>" step="0.01" min="0" required></div>
        <div><label class="form-label"><?= t('vehicle_deposit') ?></label><input type="number" name="caution" class="form-control" value="<?= h($data['caution']) ?>" step="0.01" min="0"></div>
      </div>
      <div style="margin-bottom:16px;">
        <label class="form-label"><?= t('vehicle_photos') ?></label>
        <input type="file" name="photos[]" class="form-control" accept="image/jpeg,image/png,image/webp" multiple>
        <div style="font-size:12px;color:#94a3b8;margin-top:4px;"><?= t('vehicle_photos_hint') ?></div>
      </div>
      <div style="margin-bottom:24px;"><label class="form-label"><?= t('vehicle_desc') ?></label><textarea name="description" class="form-control" rows="3" placeholder="Informations supplémentaires..."><?= h($data['description']) ?></textarea></div>
      <div style="display:flex;gap:10px;">
        <button type="submit" class="btn btn-emerald"><?= t('btn_save') ?></button>
        <a href="<?= BASE_URL ?>/?page=vehicles" class="btn btn-outline"><?= t('btn_cancel') ?></a>
      </div>
    </form>
  </div>
</div>
