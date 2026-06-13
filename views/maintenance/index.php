<?php $pageTitle = t('maintenance'); $pageBreadcrumb = t('maintenance'); ?>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
  <form method="GET" action="<?= BASE_URL ?>/" style="display:flex;gap:8px;flex-wrap:wrap;">
    <input type="hidden" name="page" value="maintenance">
    <input type="text" name="search" value="<?= h($search) ?>" placeholder="<?= t('vehicle') ?>, <?= t('maintenance_type') ?>..." class="form-control" style="width:220px;">
    <select name="statut" class="form-control" style="width:160px;" onchange="this.form.submit()">
      <option value=""><?= t('all_statuses') ?></option>
      <?php foreach(['planifiée','en cours','terminée','annulée'] as $s): ?>
      <option value="<?= $s ?>" <?= $statut===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
      <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-outline"><?= t('btn_filter') ?></button>
    <?php if ($search||$statut): ?><a href="<?= BASE_URL ?>/?page=maintenance" class="btn btn-outline"><?= t('btn_reset') ?></a><?php endif; ?>
  </form>
  <a href="<?= BASE_URL ?>/?page=maintenance/add" class="btn btn-emerald">
    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    <?= t('add_maintenance') ?>
  </a>
</div>

<?php if (!empty($counts)): ?>
<div style="display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap;">
  <?php $statColors = ['planifiée'=>'#3b82f6','en cours'=>'#f59e0b','terminée'=>'#10b981','annulée'=>'#94a3b8'];
  foreach ($counts as $s => $c): ?>
  <div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:12px 20px;display:flex;gap:10px;align-items:center;">
    <span class="num" style="font-size:20px;font-weight:700;color:<?= $statColors[$s]??'#64748b' ?>;"><?= $c ?></span>
    <span style="font-size:13px;color:#64748b;"><?= ucfirst($s) ?></span>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="card">
  <div class="card-header"><span class="card-title"><?= t('maintenance') ?> (<?= $total ?>)</span></div>
  <div style="overflow-x:auto;">
    <table class="data-table">
      <thead>
        <tr><th><?= t('vehicle') ?></th><th><?= t('maintenance_type') ?></th><th><?= t('scheduled_date') ?></th><th><?= t('done_date') ?></th><th><?= t('technician') ?></th><th><?= t('cost') ?></th><th><?= t('status') ?></th><th><?= t('actions') ?></th></tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
        <tr>
          <td>
            <div style="font-weight:500;"><?= h(($r['marque']??'').' '.($r['modele']??'')) ?></div>
            <div style="font-size:12px;color:#94a3b8;"><?= h($r['numero']??'') ?></div>
          </td>
          <td><?= h(ucfirst($r['type_maintenance'])) ?></td>
          <td dir="ltr"><?= $r['date_prevue'] ? date('d/m/Y', strtotime($r['date_prevue'])) : '—' ?></td>
          <td dir="ltr"><?= $r['date_realisee'] ? date('d/m/Y', strtotime($r['date_realisee'])) : '—' ?></td>
          <td><?= h($r['technicien']??'—') ?></td>
          <td dir="ltr"><?= $r['cout'] ? number_format($r['cout'],0,',',' ').' MAD' : '—' ?></td>
          <td><?= status_badge($r['statut']) ?></td>
          <td>
            <div style="display:flex;gap:5px;">
              <a href="<?= BASE_URL ?>/?page=maintenance/edit&id=<?= $r['id'] ?>" class="btn btn-outline btn-sm"><?= t('btn_edit_short') ?></a>
              <a href="<?= BASE_URL ?>/?page=maintenance/delete&id=<?= $r['id'] ?>" class="btn btn-outline btn-sm" onclick="return confirm('<?= t('confirm_delete_maint') ?>')" style="color:#ef4444;"><?= t('btn_delete_short') ?></a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($rows)): ?>
        <tr><td colspan="8" style="text-align:center;color:#94a3b8;padding:40px;"><?= t('no_maintenance_found') ?></td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php if ($pages > 1): ?>
  <div style="padding:16px 22px;border-top:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;">
    <span style="font-size:13px;color:#64748b;"><?= sprintf(t('page_x_of_y'), $page, $pages) ?></span>
    <div class="pagination">
      <?php $base = BASE_URL.'/?page=maintenance&search='.urlencode($search).'&statut='.urlencode($statut);
      for ($i=1;$i<=$pages;$i++): ?>
      <a href="<?= $base ?>&p=<?= $i ?>" class="page-btn <?= $i===$page?'active':'' ?>"><?= $i ?></a>
      <?php endfor; ?>
    </div>
  </div>
  <?php endif; ?>
</div>
