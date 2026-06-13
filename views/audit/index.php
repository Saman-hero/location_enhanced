<?php $pageTitle = 'Journal d\'audit'; $pageBreadcrumb = 'Journal d\'activité'; ?>

<div class="card" style="margin-bottom:20px;">
  <div class="card-body" style="padding:16px 20px;">
    <form method="GET" action="<?= BASE_URL ?>/" style="display:flex;gap:8px;flex-wrap:wrap;align-items:flex-end;">
      <input type="hidden" name="page" value="audit">
      <div>
        <label class="form-label" style="font-size:11px;">Recherche</label>
        <input type="text" name="search" value="<?= h($search) ?>" placeholder="Action, détails..." class="form-control" style="width:200px;">
      </div>
      <div>
        <label class="form-label" style="font-size:11px;">Module</label>
        <select name="module" class="form-control" style="width:150px;">
          <option value="">Tous</option>
          <?php foreach ($modules as $m): ?>
          <option value="<?= h($m) ?>" <?= $module===$m?'selected':'' ?>><?= h($m) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="form-label" style="font-size:11px;">Date début</label>
        <input type="date" name="date_from" class="form-control" value="<?= h($dateFrom) ?>">
      </div>
      <div>
        <label class="form-label" style="font-size:11px;">Date fin</label>
        <input type="date" name="date_to" class="form-control" value="<?= h($dateTo) ?>">
      </div>
      <button type="submit" class="btn btn-outline">Filtrer</button>
      <?php if ($search||$module||$dateFrom||$dateTo): ?><a href="<?= BASE_URL ?>/?page=audit" class="btn btn-outline">Réinitialiser</a><?php endif; ?>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header"><span class="card-title">Événements (<?= $total ?>)</span></div>
  <div style="overflow-x:auto;">
    <table class="data-table">
      <thead>
        <tr><th>Date / Heure</th><th>Utilisateur</th><th>Action</th><th>Module</th><th>Détails</th></tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
        <tr>
          <td style="white-space:nowrap;font-size:12px;color:#64748b;"><?= date('d/m/Y H:i:s', strtotime($r['created_at'])) ?></td>
          <td style="font-size:13px;"><?= h($r['user_username']??'—') ?></td>
          <td style="font-weight:500;"><?= h($r['action']) ?></td>
          <td><span style="font-size:11px;background:#f1f5f9;color:#64748b;padding:2px 8px;border-radius:99px;font-weight:500;"><?= h($r['module']??'') ?></span></td>
          <td style="font-size:12px;color:#64748b;max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= h($r['details']??'') ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($rows)): ?>
        <tr><td colspan="5" style="text-align:center;color:#94a3b8;padding:40px;">Aucun événement trouvé</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php if ($pages > 1): ?>
  <div style="padding:16px 22px;border-top:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;">
    <span style="font-size:13px;color:#64748b;">Page <?= $page ?> / <?= $pages ?></span>
    <div class="pagination">
      <?php $base = BASE_URL.'/?page=audit&search='.urlencode($search).'&module='.urlencode($module).'&date_from='.urlencode($dateFrom).'&date_to='.urlencode($dateTo);
      for ($i=1;$i<=$pages;$i++): ?>
      <a href="<?= $base ?>&p=<?= $i ?>" class="page-btn <?= $i===$page?'active':'' ?>"><?= $i ?></a>
      <?php endfor; ?>
    </div>
  </div>
  <?php endif; ?>
</div>
