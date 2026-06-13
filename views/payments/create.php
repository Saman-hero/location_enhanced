<?php $pageTitle = t('add_payment'); $pageBreadcrumb = t('payments').' › '.t('btn_new'); ?>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger"><?= implode('<br>', array_map('h', $errors)) ?></div>
<?php endif; ?>

<div class="card">
  <div class="card-header">
    <span class="card-title"><?= t('payment_register') ?></span>
    <a href="<?= BASE_URL ?>/?page=payments" class="btn btn-outline btn-sm">← <?= t('btn_back') ?></a>
  </div>
  <div class="card-body">
    <form method="POST">
      <div style="margin-bottom:16px;">
        <label class="form-label"><?= t('reservation') ?> *</label>
        <select name="reservation_id" class="form-control" required>
          <option value=""><?= t('select_reservation') ?></option>
          <?php foreach ($reservations as $r): ?>
          <option value="<?= $r['id'] ?>" <?= $data['reservation_id']==$r['id']?'selected':'' ?>>
            <?= h($r['reference']) ?> — <?= h($r['prenom'].' '.$r['nom']) ?> (<?= number_format($r['montant_total'],0,',',' ') ?> MAD)
          </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-grid-2" style="margin-bottom:16px;">
        <div><label class="form-label"><?= t('amount') ?> (MAD) *</label><input type="number" name="montant" class="form-control" value="<?= h($data['montant']) ?>" step="0.01" min="0.01" required></div>
        <div><label class="form-label"><?= t('date') ?> *</label><input type="date" name="date_paiement" class="form-control" value="<?= h($data['date_paiement']) ?>" required></div>
      </div>
      <div class="form-grid-2" style="margin-bottom:16px;">
        <div><label class="form-label"><?= t('payment_type') ?></label>
          <select name="type" class="form-control">
            <?php foreach(['acompte','solde','caution','remboursement'] as $tp): ?>
            <option value="<?= $tp ?>" <?= $data['type']===$tp?'selected':'' ?>><?= ucfirst($tp) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div><label class="form-label"><?= t('payment_method') ?></label>
          <select name="type_paiement" class="form-control">
            <?php foreach(['espèces','chèque','virement','carte','autre'] as $m): ?>
            <option value="<?= $m ?>" <?= $data['type_paiement']===$m?'selected':'' ?>><?= ucfirst($m) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div style="margin-bottom:24px;"><label class="form-label"><?= t('notes') ?></label><textarea name="notes" class="form-control" rows="3"><?= h($data['notes']) ?></textarea></div>
      <div style="display:flex;gap:10px;">
        <button type="submit" class="btn btn-emerald"><?= t('btn_save') ?></button>
        <a href="<?= BASE_URL ?>/?page=payments" class="btn btn-outline"><?= t('btn_cancel') ?></a>
      </div>
    </form>
  </div>
</div>
