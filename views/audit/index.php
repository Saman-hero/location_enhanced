<?php $pageTitle = t('audit_log'); $pageBreadcrumb = t('audit_log'); ?>

<div class="card" style="margin-bottom:20px;">
  <div class="card-body" style="padding:16px 20px;">
    <form method="GET" action="<?= BASE_URL ?>/" style="display:flex;gap:8px;flex-wrap:wrap;align-items:flex-end;">
      <input type="hidden" name="page" value="audit">
      <div>
        <label class="form-label" style="font-size:11px;"><?= t('btn_search') ?></label>
        <input type="text" name="search" value="<?= h($search) ?>" placeholder="<?= t('audit_search_ph') ?>" class="form-control" style="width:200px;">
      </div>
      <div>
        <label class="form-label" style="font-size:11px;"><?= t('module') ?></label>
        <select name="module" class="form-control" style="width:150px;">
          <option value=""><?= t('all') ?></option>
          <?php foreach ($modules as $m): ?>
          <option value="<?= h($m) ?>" <?= $module===$m?'selected':'' ?>><?= h($m) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="form-label" style="font-size:11px;"><?= t('date_from') ?></label>
        <input type="date" name="date_from" class="form-control" value="<?= h($dateFrom) ?>">
      </div>
      <div>
        <label class="form-label" style="font-size:11px;"><?= t('date_to') ?></label>
        <input type="date" name="date_to" class="form-control" value="<?= h($dateTo) ?>">
      </div>
      <button type="submit" class="btn btn-outline"><?= t('btn_filter') ?></button>
      <?php if ($search||$module||$dateFrom||$dateTo): ?><a href="<?= BASE_URL ?>/?page=audit" class="btn btn-outline"><?= t('btn_reset') ?></a><?php endif; ?>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header"><span class="card-title"><?= t('events') ?> (<?= $total ?>)</span></div>
  <div style="overflow-x:auto;">
    <table class="data-table">
      <thead>
        <tr><th><?= t('datetime_col') ?></th><th><?= t('performed_by') ?></th><th><?= t('action') ?></th><th><?= t('module') ?></th><th><?= t('details_col') ?></th></tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
        <tr>
          <td style="white-space:nowrap;font-size:12px;color:#64748b;" dir="ltr"><?= date('d/m/Y H:i:s', strtotime($r['created_at'])) ?></td>
          <td style="font-size:13px;"><?= h($r['user_username']??'—') ?></td>
          <td style="font-weight:500;"><?= h($r['action']) ?></td>
          <td><span style="font-size:11px;background:#f1f5f9;color:#64748b;padding:2px 8px;border-radius:99px;font-weight:500;"><?= h($r['module']??'') ?></span></td>
          <td style="font-size:12px;color:#64748b;max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= h($r['details']??'') ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($rows)): ?>
        <tr><td colspan="5" style="text-align:center;color:#94a3b8;padding:40px;"><?= t('no_events') ?></td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php if ($pages > 1): ?>
  <div style="padding:16px 22px;border-top:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;">
    <span style="font-size:13px;color:#64748b;"><?= sprintf(t('page_x_of_y'), $page, $pages) ?></span>
    <div class="pagination">
      <?php $base = BASE_URL.'/?page=audit&search='.urlencode($search).'&module='.urlencode($module).'&date_from='.urlencode($dateFrom).'&date_to='.urlencode($dateTo);
      for ($i=1;$i<=$pages;$i++): ?>
      <a href="<?= $base ?>&p=<?= $i ?>" class="page-btn <?= $i===$page?'active':'' ?>"><?= $i ?></a>
      <?php endfor; ?>
    </div>
  </div>
  <?php endif; ?>
</div>
