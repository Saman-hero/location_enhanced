<?php $pageTitle = t('edit_reservation').' '.$data['reference']; $pageBreadcrumb = t('reservations').' › '.t('btn_edit'); ?>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger"><?= implode('<br>', array_map('h', $errors)) ?></div>
<?php endif; ?>

<div class="card">
  <div class="card-header">
    <span class="card-title"><?= t('edit_reservation') ?> <?= h($data['reference']) ?></span>
    <a href="<?= BASE_URL ?>/?page=reservations/show&id=<?= $id ?>" class="btn btn-outline btn-sm">← <?= t('btn_back') ?></a>
  </div>
  <div class="card-body">
    <form method="POST">
      <div class="form-grid-2" style="margin-bottom:16px;">
        <div><label class="form-label"><?= t('client') ?> *</label>
          <select name="client_id" class="form-control" required>
            <?php foreach ($clients as $c): ?>
            <option value="<?= $c['id'] ?>" <?= $data['client_id']==$c['id']?'selected':'' ?>><?= h($c['prenom'].' '.$c['nom']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div><label class="form-label"><?= t('vehicle') ?> *</label>
          <select name="vehicle_id" class="form-control" required>
            <?php foreach ($vehicles as $v): ?>
            <option value="<?= $v['id'] ?>" <?= $data['vehicle_id']==$v['id']?'selected':'' ?>><?= h($v['numero'].' — '.$v['marque'].' '.$v['modele']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-grid-2" style="margin-bottom:16px;">
        <div><label class="form-label"><?= t('start_date') ?> *</label><input type="datetime-local" name="date_debut" class="form-control" value="<?= h(str_replace(' ','T',substr($data['date_debut']??'',0,16))) ?>" required></div>
        <div><label class="form-label"><?= t('end_date') ?> *</label><input type="datetime-local" name="date_fin_prevue" class="form-control" value="<?= h(str_replace(' ','T',substr($data['date_fin_prevue']??'',0,16))) ?>" required></div>
      </div>
      <div class="form-grid-2" style="margin-bottom:16px;">
        <div><label class="form-label"><?= t('return_date') ?></label><input type="datetime-local" name="date_retour_effectif" class="form-control" value="<?= h($data['date_retour_effectif'] ? str_replace(' ','T',substr($data['date_retour_effectif'],0,16)) : '') ?>"></div>
        <div><label class="form-label"><?= t('status') ?></label>
          <select name="statut" class="form-control">
            <?php foreach(['en attente','confirmée','en cours','terminée','annulée'] as $s): ?>
            <option value="<?= $s ?>" <?= $data['statut']===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-grid-2" style="margin-bottom:16px;">
        <div><label class="form-label"><?= t('departure_location') ?></label><input type="text" name="lieu_depart" class="form-control" value="<?= h($data['lieu_depart']??'') ?>"></div>
        <div><label class="form-label"><?= t('return_location') ?></label><input type="text" name="lieu_retour" class="form-control" value="<?= h($data['lieu_retour']??'') ?>"></div>
      </div>
      <div class="form-grid-3" style="margin-bottom:16px;">
        <div><label class="form-label"><?= t('start_mileage') ?></label><input type="number" name="km_depart" class="form-control" value="<?= h($data['km_depart']??'') ?>"></div>
        <div><label class="form-label"><?= t('end_mileage') ?></label><input type="number" name="km_retour" class="form-control" value="<?= h($data['km_retour']??'') ?>"></div>
        <div><label class="form-label"><?= t('extra_fees') ?> (MAD)</label><input type="number" name="frais_extra" class="form-control" value="<?= h($data['frais_extra']??0) ?>" step="0.01"></div>
      </div>
      <div style="margin-bottom:24px;"><label class="form-label"><?= t('comment') ?></label><textarea name="commentaire" class="form-control" rows="3"><?= h($data['commentaire']??'') ?></textarea></div>
      <div style="display:flex;gap:10px;">
        <button type="submit" class="btn btn-emerald"><?= t('btn_save') ?></button>
        <a href="<?= BASE_URL ?>/?page=reservations/show&id=<?= $id ?>" class="btn btn-outline"><?= t('btn_cancel') ?></a>
      </div>
    </form>
  </div>
</div>
