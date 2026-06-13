<?php $pageTitle = t('payments'); $pageBreadcrumb = t('payments'); ?>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
  <form method="GET" action="<?= BASE_URL ?>/" style="display:flex;gap:8px;flex-wrap:wrap;">
    <input type="hidden" name="page" value="payments">
    <input type="text" name="search" value="<?= h($search) ?>" placeholder="Référence, <?= t('client') ?>..." class="form-control" style="width:240px;">
    <button type="submit" class="btn btn-outline"><?= t('btn_filter') ?></button>
    <?php if ($search): ?><a href="<?= BASE_URL ?>/?page=payments" class="btn btn-outline"><?= t('btn_reset') ?></a><?php endif; ?>
  </form>
  <a href="<?= BASE_URL ?>/?page=payments/add" class="btn btn-emerald">
    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    <?= t('new_payment_btn') ?>
  </a>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
  <div class="card">
    <div class="card-body" style="text-align:center;padding:20px;">
      <div style="font-size:12px;color:#94a3b8;margin-bottom:6px;"><?= t('total_collected') ?></div>
      <div style="font-size:24px;font-weight:700;color:#059669;"><span class="num"><?= number_format($totalAll,2,',',' ') ?> MAD</span></div>
    </div>
  </div>
  <div class="card">
    <div class="card-body" style="text-align:center;padding:20px;">
      <div style="font-size:12px;color:#94a3b8;margin-bottom:6px;"><?= t('this_month') ?></div>
      <div style="font-size:24px;font-weight:700;color:#3b82f6;"><span class="num"><?= number_format($totalMonth,2,',',' ') ?> MAD</span></div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header"><span class="card-title"><?= t('payments') ?> (<?= $total ?>)</span></div>
  <div style="overflow-x:auto;">
    <table class="data-table">
      <thead>
        <tr><th><?= t('date') ?></th><th><?= t('reservation_col') ?></th><th><?= t('client') ?></th><th>Type</th><th><?= t('payment_method_col') ?></th><th><?= t('amount') ?></th></tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
        <tr>
          <td dir="ltr"><?= date('d/m/Y', strtotime($r['date_paiement'])) ?></td>
          <td><a href="<?= BASE_URL ?>/?page=reservations/show&id=<?= $r['reservation_id'] ?>" style="color:#3b82f6;font-weight:600;"><?= h($r['reference']??'#'.$r['reservation_id']) ?></a></td>
          <td><?= h(($r['client_prenom']??'').' '.($r['client_nom']??'')) ?></td>
          <td><?= h(ucfirst($r['type']??'')) ?></td>
          <td><?= h(ucfirst($r['type_paiement']??'')) ?></td>
          <td style="font-weight:600;color:#059669;" dir="ltr"><?= number_format($r['montant'],2,',',' ') ?> MAD</td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($rows)): ?>
        <tr><td colspan="6" style="text-align:center;color:#94a3b8;padding:40px;"><?= t('no_payments_found') ?></td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php if ($pages > 1): ?>
  <div style="padding:16px 22px;border-top:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;">
    <span style="font-size:13px;color:#64748b;"><?= sprintf(t('page_x_of_y'), $page, $pages) ?></span>
    <div class="pagination">
      <?php $base = BASE_URL.'/?page=payments&search='.urlencode($search);
      for ($i=1;$i<=$pages;$i++): ?>
      <a href="<?= $base ?>&p=<?= $i ?>" class="page-btn <?= $i===$page?'active':'' ?>"><?= $i ?></a>
      <?php endfor; ?>
    </div>
  </div>
  <?php endif; ?>
</div>
