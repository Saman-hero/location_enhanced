<?php $pageTitle = t('edit_maintenance').' #'.$id; $pageBreadcrumb = t('maintenance').' › '.t('btn_edit'); ?>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger"><?= implode('<br>', array_map('h', $errors)) ?></div>
<?php endif; ?>

<div class="card">
  <div class="card-header">
    <span class="card-title"><?= t('edit_maintenance') ?> #<?= $id ?></span>
    <a href="<?= BASE_URL ?>/?page=maintenance" class="btn btn-outline btn-sm">← <?= t('btn_back') ?></a>
  </div>
  <div class="card-body">
    <form method="POST">
      <div class="form-grid-2" style="margin-bottom:16px;">
        <div><label class="form-label"><?= t('vehicle') ?> *</label>
          <select name="vehicle_id" class="form-control" required>
            <?php foreach ($vehicles as $v): ?>
            <option value="<?= $v['id'] ?>" <?= $data['vehicle_id']==$v['id']?'selected':'' ?>><?= h($v['numero'].' — '.$v['marque'].' '.$v['modele']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div><label class="form-label"><?= t('maintenance_type_label') ?> *</label>
          <select name="type_maintenance" class="form-control" required>
            <?php foreach(['vidange','révision','pneus','freins','carrosserie','autre'] as $tp): ?>
            <option value="<?= $tp ?>" <?= $data['type_maintenance']===$tp?'selected':'' ?>><?= ucfirst($tp) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-grid-2" style="margin-bottom:16px;">
        <div><label class="form-label"><?= t('scheduled_date') ?></label><input type="date" name="date_prevue" class="form-control" value="<?= h($data['date_prevue']??'') ?>"></div>
        <div><label class="form-label"><?= t('done_date') ?></label><input type="date" name="date_realisee" class="form-control" value="<?= h($data['date_realisee']??'') ?>"></div>
      </div>
      <div class="form-grid-3" style="margin-bottom:16px;">
        <div><label class="form-label"><?= t('vehicle_mileage') ?></label><input type="number" name="kilometrage" class="form-control" value="<?= h($data['kilometrage']??'') ?>"></div>
        <div><label class="form-label"><?= t('cost') ?> (MAD)</label><input type="number" name="cout" class="form-control" value="<?= h($data['cout']??'') ?>" step="0.01"></div>
        <div><label class="form-label"><?= t('status') ?></label>
          <select name="statut" class="form-control">
            <?php foreach(['planifiée','en cours','terminée','annulée'] as $s): ?>
            <option value="<?= $s ?>" <?= ($data['statut']??'')===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div style="margin-bottom:16px;"><label class="form-label"><?= t('technician') ?></label><input type="text" name="technicien" class="form-control" value="<?= h($data['technicien']??'') ?>"></div>
      <div style="margin-bottom:24px;"><label class="form-label"><?= t('description') ?></label><textarea name="description" class="form-control" rows="3"><?= h($data['description']??'') ?></textarea></div>
      <div style="display:flex;gap:10px;">
        <button type="submit" class="btn btn-emerald"><?= t('btn_save') ?></button>
        <a href="<?= BASE_URL ?>/?page=maintenance" class="btn btn-outline"><?= t('btn_cancel') ?></a>
      </div>
    </form>
  </div>
</div>
