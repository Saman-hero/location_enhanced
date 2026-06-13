<?php $pageTitle = 'Sinistres'; $pageBreadcrumb = 'Gestion des sinistres'; ?>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
  <form method="GET" action="<?= BASE_URL ?>/" style="display:flex;gap:8px;flex-wrap:wrap;">
    <input type="hidden" name="page" value="incidents">
    <input type="text" name="search" value="<?= h($search) ?>" placeholder="Référence, véhicule..." class="form-control" style="width:220px;">
    <select name="statut" class="form-control" style="width:160px;" onchange="this.form.submit()">
      <option value="">Tous les statuts</option>
      <?php foreach(['ouvert','en cours','clôturé'] as $s): ?>
      <option value="<?= $s ?>" <?= $statut===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
      <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-outline">Filtrer</button>
    <?php if ($search||$statut): ?><a href="<?= BASE_URL ?>/?page=incidents" class="btn btn-outline">Réinitialiser</a><?php endif; ?>
  </form>
  <a href="<?= BASE_URL ?>/?page=incidents/add" class="btn btn-emerald">
    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    Nouveau sinistre
  </a>
</div>

<div class="card">
  <div class="card-header"><span class="card-title">Sinistres (<?= $total ?>)</span></div>
  <div style="overflow-x:auto;">
    <table class="data-table">
      <thead>
        <tr><th>Référence</th><th>Véhicule</th><th>Client</th><th>Type</th><th>Date</th><th>Coût réparation</th><th>Statut</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
        <tr>
          <td style="font-weight:600;"><?= h($r['reference']) ?></td>
          <td>
            <div style="font-weight:500;"><?= h(($r['marque']??'').' '.($r['modele']??'')) ?></div>
            <div style="font-size:12px;color:#94a3b8;"><?= h($r['numero']??'') ?></div>
          </td>
          <td><?= !empty($r['client_nom']) ? h(($r['client_prenom']??'').' '.$r['client_nom']) : '—' ?></td>
          <td><?= h(ucfirst($r['type'])) ?></td>
          <td><?= $r['date_sinistre'] ? date('d/m/Y', strtotime($r['date_sinistre'])) : '—' ?></td>
          <td><?= $r['cout_reparation'] ? number_format($r['cout_reparation'],0,',',' ').' MAD' : '—' ?></td>
          <td><?= status_badge($r['statut']) ?></td>
          <td>
            <div style="display:flex;gap:5px;">
              <a href="<?= BASE_URL ?>/?page=incidents/edit&id=<?= $r['id'] ?>" class="btn btn-outline btn-sm">Éditer</a>
              <a href="<?= BASE_URL ?>/?page=incidents/delete&id=<?= $r['id'] ?>" class="btn btn-outline btn-sm" onclick="return confirm('Supprimer ce sinistre ?')" style="color:#ef4444;">Suppr.</a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($rows)): ?>
        <tr><td colspan="8" style="text-align:center;color:#94a3b8;padding:40px;">Aucun sinistre trouvé</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php if ($pages > 1): ?>
  <div style="padding:16px 22px;border-top:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;">
    <span style="font-size:13px;color:#64748b;">Page <?= $page ?> / <?= $pages ?></span>
    <div class="pagination">
      <?php $base = BASE_URL.'/?page=incidents&search='.urlencode($search).'&statut='.urlencode($statut);
      for ($i=1;$i<=$pages;$i++): ?>
      <a href="<?= $base ?>&p=<?= $i ?>" class="page-btn <?= $i===$page?'active':'' ?>"><?= $i ?></a>
      <?php endfor; ?>
    </div>
  </div>
  <?php endif; ?>
</div>
