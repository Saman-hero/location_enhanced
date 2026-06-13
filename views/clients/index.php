<?php $pageTitle = t('clients'); $pageBreadcrumb = t('clients'); ?>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
  <form method="GET" action="<?= BASE_URL ?>/" style="display:flex;gap:8px;flex-wrap:wrap;">
    <input type="hidden" name="page" value="clients">
    <input type="text" name="search" value="<?= h($search) ?>" placeholder="<?= t('search_client') ?>" class="form-control" style="width:240px;">
    <select name="statut" class="form-control" style="width:160px;" onchange="this.form.submit()">
      <option value=""><?= t('all_statuses') ?></option>
      <?php foreach(['actif','inactif','liste_noire'] as $s): ?>
      <option value="<?= $s ?>" <?= $statut===$s?'selected':'' ?>><?= ucfirst(str_replace('_',' ',$s)) ?></option>
      <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-outline"><?= t('btn_filter') ?></button>
    <?php if ($search||$statut): ?><a href="<?= BASE_URL ?>/?page=clients" class="btn btn-outline"><?= t('btn_reset') ?></a><?php endif; ?>
  </form>
  <a href="<?= BASE_URL ?>/?page=clients/add" class="btn btn-emerald">
    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    <?= t('add_client') ?>
  </a>
</div>

<div class="card">
  <div class="card-header"><span class="card-title"><?= t('clients') ?> (<?= $total ?>)</span></div>
  <div style="overflow-x:auto;">
    <table class="data-table">
      <thead>
        <tr><th><?= t('client') ?></th><th><?= t('cin') ?></th><th><?= t('phone') ?></th><th><?= t('email') ?></th><th>Type</th><th><?= t('status') ?></th><th><?= t('actions') ?></th></tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
        <tr>
          <td>
            <a href="<?= BASE_URL ?>/?page=clients/show&id=<?= $r['id'] ?>" style="font-weight:600;color:#3b82f6;"><?= h($r['prenom'].' '.$r['nom']) ?></a>
            <?php if (!empty($r['ville'])): ?><div style="font-size:12px;color:#94a3b8;"><?= h($r['ville']) ?></div><?php endif; ?>
          </td>
          <td dir="ltr"><?= h($r['cin']??'—') ?></td>
          <td dir="ltr"><?= h($r['telephone']??'—') ?></td>
          <td><?= h($r['email']??'—') ?></td>
          <td><?= h(ucfirst($r['type_client']??'particulier')) ?></td>
          <td><?= status_badge($r['statut']) ?></td>
          <td>
            <div style="display:flex;gap:5px;">
              <a href="<?= BASE_URL ?>/?page=clients/show&id=<?= $r['id'] ?>" class="btn btn-outline btn-sm"><?= t('btn_view') ?></a>
              <a href="<?= BASE_URL ?>/?page=clients/edit&id=<?= $r['id'] ?>" class="btn btn-outline btn-sm"><?= t('btn_edit_short') ?></a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($rows)): ?>
        <tr><td colspan="7" style="text-align:center;color:#94a3b8;padding:40px;"><?= t('client_not_found_list') ?></td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php if ($pages > 1): ?>
  <div style="padding:16px 22px;border-top:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;">
    <span style="font-size:13px;color:#64748b;"><?= sprintf(t('page_x_of_y'), $page, $pages) ?></span>
    <div class="pagination">
      <?php $base = BASE_URL.'/?page=clients&search='.urlencode($search).'&statut='.urlencode($statut);
      for ($i=1;$i<=$pages;$i++): ?>
      <a href="<?= $base ?>&p=<?= $i ?>" class="page-btn <?= $i===$page?'active':'' ?>"><?= $i ?></a>
      <?php endfor; ?>
    </div>
  </div>
  <?php endif; ?>
</div>
