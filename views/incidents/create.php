<?php $pageTitle = t('add_incident'); $pageBreadcrumb = t('incidents').' › '.t('btn_new'); ?>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger"><?= implode('<br>', array_map('h', $errors)) ?></div>
<?php endif; ?>

<div class="card">
  <div class="card-header">
    <span class="card-title"><?= t('add_incident') ?></span>
    <a href="<?= BASE_URL ?>/?page=incidents" class="btn btn-outline btn-sm">← <?= t('btn_back') ?></a>
  </div>
  <div class="card-body">
    <form method="POST">
      <div class="form-grid-2" style="margin-bottom:16px;">
        <div><label class="form-label"><?= t('vehicle') ?> *</label>
          <select name="vehicle_id" class="form-control" required>
            <option value=""><?= t('select_vehicle') ?></option>
            <?php foreach ($vehicles as $v): ?>
            <option value="<?= $v['id'] ?>" <?= $data['vehicle_id']==$v['id']?'selected':'' ?>><?= h($v['numero'].' — '.$v['marque'].' '.$v['modele']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div><label class="form-label"><?= t('client') ?></label>
          <select name="client_id" class="form-control">
            <option value=""><?= t('select_none') ?></option>
            <?php foreach ($clients as $c): ?>
            <option value="<?= $c['id'] ?>" <?= $data['client_id']==$c['id']?'selected':'' ?>><?= h($c['prenom'].' '.$c['nom']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-grid-2" style="margin-bottom:16px;">
        <div><label class="form-label"><?= t('incident_type_label') ?></label>
          <select name="type" class="form-control">
            <?php foreach(['accident','vol','vandalisme','bris de glace','autre'] as $tp): ?>
            <option value="<?= $tp ?>" <?= $data['type']===$tp?'selected':'' ?>><?= ucfirst($tp) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div><label class="form-label"><?= t('incident_date') ?></label><input type="date" name="date_sinistre" class="form-control" value="<?= h($data['date_sinistre']) ?>"></div>
      </div>
      <div class="form-grid-2" style="margin-bottom:16px;">
        <div><label class="form-label"><?= t('repair_cost') ?> (MAD)</label><input type="number" name="cout_reparation" class="form-control" value="<?= h($data['cout_reparation']) ?>" step="0.01"></div>
        <div><label class="form-label"><?= t('incident_covered_by') ?></label>
          <select name="prise_en_charge" class="form-control">
            <?php foreach(['client','assurance','agence'] as $p): ?>
            <option value="<?= $p ?>" <?= $data['prise_en_charge']===$p?'selected':'' ?>><?= ucfirst($p) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div style="margin-bottom:16px;"><label class="form-label"><?= t('status') ?></label>
        <select name="statut" class="form-control" style="max-width:200px;">
          <?php foreach(['ouvert','en cours','clôturé'] as $s): ?>
          <option value="<?= $s ?>" <?= $data['statut']===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div style="margin-bottom:24px;"><label class="form-label"><?= t('description') ?></label><textarea name="description" class="form-control" rows="4"><?= h($data['description']) ?></textarea></div>
      <div style="display:flex;gap:10px;">
        <button type="submit" class="btn btn-emerald"><?= t('btn_save') ?></button>
        <a href="<?= BASE_URL ?>/?page=incidents" class="btn btn-outline"><?= t('btn_cancel') ?></a>
      </div>
    </form>
  </div>
</div>
