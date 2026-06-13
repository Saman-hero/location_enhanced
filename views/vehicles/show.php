<?php $pageTitle = $vehicle['marque'].' '.$vehicle['modele']; $pageBreadcrumb = t('vehicles').' › '.t('vehicle_detail'); ?>

<div style="display:flex;justify-content:flex-end;gap:8px;margin-bottom:20px;">
  <a href="<?= BASE_URL ?>/?page=vehicles/edit&id=<?= $vehicle['id'] ?>" class="btn btn-outline"><?= t('btn_edit') ?></a>
  <a href="<?= BASE_URL ?>/?page=reservations/add&vehicle_id=<?= $vehicle['id'] ?>" class="btn btn-emerald"><?= t('add_reservation') ?></a>
</div>

<div style="display:grid;grid-template-columns:1fr 2fr;gap:20px;margin-bottom:24px;">
  <div class="card">
    <div class="card-body">
      <div style="text-align:center;padding:12px 0 20px;">
        <div style="width:72px;height:72px;background:#f0fdf4;border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
          <svg width="36" height="36" fill="none" stroke="#10b981" stroke-width="1.5" viewBox="0 0 24 24"><path d="M19 17H5l-2-4V7a1 1 0 011-1h14a1 1 0 011 1v6l-2 4z"/><circle cx="7.5" cy="17.5" r="1.5"/><circle cx="16.5" cy="17.5" r="1.5"/></svg>
        </div>
        <h2 style="font-size:20px;font-weight:700;color:#0f172a;"><?= h($vehicle['marque'].' '.$vehicle['modele']) ?></h2>
        <div style="color:#64748b;font-size:13px;"><?= h($vehicle['annee'] ?? '') ?> · <?= h($vehicle['couleur'] ?? '') ?></div>
        <div style="margin-top:10px;"><?= status_badge($vehicle['statut']) ?></div>
      </div>
      <hr class="divider">
      <dl style="margin-top:16px;">
        <?php $rows = [
          [t('vehicle_internal_num'), $vehicle['numero']],
          [t('vehicle_plate'), $vehicle['immatriculation']??'—'],
          [t('vehicle_category'), ucfirst($vehicle['categorie'])],
          [t('vehicle_fuel'), ucfirst($vehicle['carburant']??'—')],
          [t('vehicle_transmission'), ucfirst($vehicle['transmission']??'—')],
          [t('vehicle_seats'), $vehicle['nb_places']],
          [t('vehicle_mileage'), number_format($vehicle['kilometrage'],0,',',' ').' km'],
          [t('vehicle_price'), number_format($vehicle['prix_jour'],2,',',' ').' MAD'],
          [t('vehicle_deposit'), number_format($vehicle['caution'],2,',',' ').' MAD'],
        ];
        foreach ($rows as [$label, $val]): ?>
        <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #f8fafc;">
          <dt style="font-size:12px;color:#94a3b8;font-weight:500;"><?= $label ?></dt>
          <dd style="font-size:13px;color:#0f172a;font-weight:500;margin:0;" dir="ltr"><?= h((string)$val) ?></dd>
        </div>
        <?php endforeach; ?>
      </dl>
    </div>
  </div>

  <div style="display:flex;flex-direction:column;gap:20px;">
    <div class="card">
      <div class="card-header">
        <span class="card-title"><?= t('reservation_history') ?></span>
        <a href="<?= BASE_URL ?>/?page=reservations" class="btn btn-outline btn-sm"><?= t('view_all') ?></a>
      </div>
      <div style="overflow-x:auto;">
        <table class="data-table">
          <thead><tr><th><?= t('reference') ?></th><th><?= t('client') ?></th><th><?= t('start_date') ?></th><th><?= t('end_date') ?></th><th><?= t('amount') ?></th><th><?= t('status') ?></th></tr></thead>
          <tbody>
            <?php foreach ($reservations as $r): ?>
            <tr>
              <td dir="ltr"><a href="<?= BASE_URL ?>/?page=reservations/show&id=<?= $r['id'] ?>" style="color:#3b82f6;font-weight:600;"><?= h($r['reference']) ?></a></td>
              <td><?= h($r['prenom'].' '.$r['nom']) ?></td>
              <td dir="ltr"><?= date('d/m/Y', strtotime($r['date_debut'])) ?></td>
              <td dir="ltr"><?= $r['date_fin_prevue'] ? date('d/m/Y', strtotime($r['date_fin_prevue'])) : '—' ?></td>
              <td dir="ltr"><?= $r['montant_total'] ? number_format($r['montant_total'],0,',',' ').' MAD' : '—' ?></td>
              <td><?= status_badge($r['statut']) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($reservations)): ?>
            <tr><td colspan="6" style="text-align:center;color:#94a3b8;padding:24px;"><?= t('no_reservations') ?></td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <span class="card-title"><?= t('maintenance') ?></span>
        <a href="<?= BASE_URL ?>/?page=maintenance/add&vehicle_id=<?= $vehicle['id'] ?>" class="btn btn-outline btn-sm">+ <?= t('btn_add') ?></a>
      </div>
      <div style="overflow-x:auto;">
        <table class="data-table">
          <thead><tr><th><?= t('maintenance_type') ?></th><th><?= t('scheduled_date') ?></th><th><?= t('cost') ?></th><th><?= t('status') ?></th></tr></thead>
          <tbody>
            <?php foreach ($maintenance as $m): ?>
            <tr>
              <td style="font-weight:500;"><?= h($m['type_maintenance']) ?></td>
              <td dir="ltr"><?= $m['date_prevue'] ? date('d/m/Y', strtotime($m['date_prevue'])) : '—' ?></td>
              <td dir="ltr"><?= $m['cout'] ? number_format($m['cout'],0,',',' ').' MAD' : '—' ?></td>
              <td><?= status_badge($m['statut']) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($maintenance)): ?>
            <tr><td colspan="4" style="text-align:center;color:#94a3b8;padding:24px;"><?= t('no_data') ?></td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
