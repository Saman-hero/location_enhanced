<?php $pageTitle = 'Maintenance'; $pageBreadcrumb = 'Gestion de la maintenance'; ?>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
  <form method="GET" action="<?= BASE_URL ?>/" style="display:flex;gap:8px;flex-wrap:wrap;">
    <input type="hidden" name="page" value="maintenance">
    <input type="text" name="search" value="<?= h($search) ?>" placeholder="Véhicule, type..." class="form-control" style="width:220px;">
    <select name="statut" class="form-control" style="width:160px;" onchange="this.form.submit()">
      <option value="">Tous les statuts</option>
      <?php foreach(['planifiée','en cours','terminée','annulée'] as $s): ?>
      <option value="<?= $s ?>" <?= $statut===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
      <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-outline">Filtrer</button>
    <?php if ($search||$statut): ?><a href="<?= BASE_URL ?>/?page=maintenance" class="btn btn-outline">Réinitialiser</a><?php endif; ?>
  </form>
  <a href="<?= BASE_URL ?>/?page=maintenance/add" class="btn btn-emerald">
    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    Planifier maintenance
  </a>
</div>

<?php if (!empty($counts)): ?>
<div style="display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap;">
  <?php $statColors = ['planifiée'=>'#3b82f6','en cours'=>'#f59e0b','terminée'=>'#10b981','annulée'=>'#94a3b8'];
  foreach ($counts as $s => $c): ?>
  <div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:12px 20px;display:flex;gap:10px;align-items:center;">
    <span style="font-size:20px;font-weight:700;color:<?= $statColors[$s]??'#64748b' ?>;"><?= $c ?></span>
    <span style="font-size:13px;color:#64748b;"><?= ucfirst($s) ?></span>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="card">
  <div class="card-header"><span class="card-title">Maintenances (<?= $total ?>)</span></div>
  <div style="overflow-x:auto;">
    <table class="data-table">
      <thead>
        <tr><th>Véhicule</th><th>Type</th><th>Date prévue</th><th>Date réalisée</th><th>Technicien</th><th>Coût</th><th>Statut</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
        <tr>
          <td>
            <div style="font-weight:500;"><?= h(($r['marque']??'').' '.($r['modele']??'')) ?></div>
            <div style="font-size:12px;color:#94a3b8;"><?= h($r['numero']??'') ?></div>
          </td>
          <td><?= h(ucfirst($r['type_maintenance'])) ?></td>
          <td><?= $r['date_prevue'] ? date('d/m/Y', strtotime($r['date_prevue'])) : '—' ?></td>
          <td><?= $r['date_realisee'] ? date('d/m/Y', strtotime($r['date_realisee'])) : '—' ?></td>
          <td><?= h($r['technicien']??'—') ?></td>
          <td><?= $r['cout'] ? number_format($r['cout'],0,',',' ').' MAD' : '—' ?></td>
          <td><?= status_badge($r['statut']) ?></td>
          <td>
            <div style="display:flex;gap:5px;">
              <a href="<?= BASE_URL ?>/?page=maintenance/edit&id=<?= $r['id'] ?>" class="btn btn-outline btn-sm">Éditer</a>
              <a href="<?= BASE_URL ?>/?page=maintenance/delete&id=<?= $r['id'] ?>" class="btn btn-outline btn-sm" onclick="return confirm('Supprimer cette maintenance ?')" style="color:#ef4444;">Suppr.</a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($rows)): ?>
        <tr><td colspan="8" style="text-align:center;color:#94a3b8;padding:40px;">Aucune maintenance trouvée</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php if ($pages > 1): ?>
  <div style="padding:16px 22px;border-top:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;">
    <span style="font-size:13px;color:#64748b;">Page <?= $page ?> / <?= $pages ?></span>
    <div class="pagination">
      <?php $base = BASE_URL.'/?page=maintenance&search='.urlencode($search).'&statut='.urlencode($statut);
      for ($i=1;$i<=$pages;$i++): ?>
      <a href="<?= $base ?>&p=<?= $i ?>" class="page-btn <?= $i===$page?'active':'' ?>"><?= $i ?></a>
      <?php endfor; ?>
    </div>
  </div>
  <?php endif; ?>
</div>
