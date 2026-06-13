<!-- KPI Row -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:24px;">

  <div class="kpi-card">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;">
      <div>
        <div class="kpi-value"><?= $stats['total_vehicles'] ?></div>
        <div class="kpi-label">Véhicules totaux</div>
      </div>
      <div class="stat-icon" style="background:#f0fdf4;">
        <svg width="20" height="20" fill="none" stroke="#10b981" stroke-width="2" viewBox="0 0 24 24"><path d="M19 17H5l-2-4V7a1 1 0 011-1h14a1 1 0 011 1v6l-2 4z"/><circle cx="7.5" cy="17.5" r="1.5"/><circle cx="16.5" cy="17.5" r="1.5"/></svg>
      </div>
    </div>
    <div style="margin-top:12px;display:flex;gap:10px;font-size:12px;">
      <span class="badge badge-green"><?= $stats['dispo'] ?> dispo</span>
      <span class="badge badge-blue"><?= $stats['loue'] ?> loué</span>
      <span class="badge badge-yellow"><?= $stats['maint'] ?> maint.</span>
    </div>
  </div>

  <div class="kpi-card">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;">
      <div>
        <div class="kpi-value"><?= $stats['active_reservations'] ?></div>
        <div class="kpi-label">Réservations actives</div>
      </div>
      <div class="stat-icon" style="background:#eff6ff;">
        <svg width="20" height="20" fill="none" stroke="#3b82f6" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      </div>
    </div>
    <div class="kpi-trend" style="color:#3b82f6;">Taux utilisation: <?= $stats['util_rate'] ?>%</div>
  </div>

  <div class="kpi-card">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;">
      <div>
        <div class="kpi-value"><?= number_format($stats['revenue_month'], 0, ',', ' ') ?> <span style="font-size:16px;font-weight:500;color:#64748b;">MAD</span></div>
        <div class="kpi-label">Revenus ce mois</div>
      </div>
      <div class="stat-icon" style="background:#f0fdf4;">
        <svg width="20" height="20" fill="none" stroke="#10b981" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
      </div>
    </div>
    <div class="kpi-trend <?= $stats['rev_trend'] >= 0 ? 'trend-up' : 'trend-down' ?>">
      <?= $stats['rev_trend'] >= 0 ? '▲' : '▼' ?> <?= abs($stats['rev_trend']) ?>% vs mois dernier
    </div>
  </div>

  <div class="kpi-card">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;">
      <div>
        <div class="kpi-value"><?= $stats['active_clients'] ?></div>
        <div class="kpi-label">Clients actifs</div>
      </div>
      <div class="stat-icon" style="background:#eff6ff;">
        <svg width="20" height="20" fill="none" stroke="#3b82f6" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
      </div>
    </div>
    <?php if ($stats['open_incidents'] > 0): ?>
    <div class="kpi-trend trend-down"><?= $stats['open_incidents'] ?> sinistre(s) ouvert(s)</div>
    <?php else: ?>
    <div class="kpi-trend trend-up">Aucun sinistre ouvert</div>
    <?php endif; ?>
  </div>

</div>

<!-- Charts row -->
<div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;margin-bottom:24px;">

  <div class="card">
    <div class="card-header">
      <span class="card-title">Réservations — 6 derniers mois</span>
    </div>
    <div class="card-body">
      <canvas id="reservationChart" height="110"></canvas>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <span class="card-title">Statut de la flotte</span>
    </div>
    <div class="card-body" style="display:flex;flex-direction:column;align-items:center;">
      <canvas id="fleetChart" width="160" height="160"></canvas>
      <div style="margin-top:14px;display:flex;flex-wrap:wrap;gap:8px;justify-content:center;">
        <span class="badge badge-green">Disponible (<?= $stats['dispo'] ?>)</span>
        <span class="badge badge-blue">Loué (<?= $stats['loue'] ?>)</span>
        <span class="badge badge-yellow">Maintenance (<?= $stats['maint'] ?>)</span>
      </div>
    </div>
  </div>

</div>

<!-- Recent reservations table -->
<div class="card">
  <div class="card-header">
    <span class="card-title">Réservations récentes</span>
    <a href="<?= BASE_URL ?>/?page=reservations" class="btn btn-outline btn-sm">Voir tout</a>
  </div>
  <div style="overflow-x:auto;">
    <table class="data-table">
      <thead>
        <tr>
          <th>Référence</th>
          <th>Client</th>
          <th>Véhicule</th>
          <th>Début</th>
          <th>Fin prévue</th>
          <th>Montant</th>
          <th>Statut</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($recentReservations as $r): ?>
        <tr>
          <td><span style="font-weight:600;color:#0f172a;"><?= h($r['reference']) ?></span></td>
          <td><?= h($r['prenom'].' '.$r['nom']) ?></td>
          <td><?= h($r['marque'].' '.$r['modele']) ?> <span style="color:#94a3b8;font-size:12px;">(<?= h($r['numero']) ?>)</span></td>
          <td><?= date('d/m/Y', strtotime($r['date_debut'])) ?></td>
          <td><?= date('d/m/Y', strtotime($r['date_fin_prevue'])) ?></td>
          <td><?= $r['montant_total'] ? number_format($r['montant_total'],2,',',' ').' MAD' : '—' ?></td>
          <td><?= status_badge($r['statut']) ?></td>
          <td><a href="<?= BASE_URL ?>/?page=reservations/show&id=<?= $r['id'] ?>" class="btn btn-outline btn-sm">Voir</a></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($recentReservations)): ?>
        <tr><td colspan="8" style="text-align:center;color:#94a3b8;padding:32px;">Aucune réservation</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
$chartLabels = $stats['chart_labels'] ?? '[]';
$chartValues = $stats['chart_values'] ?? '[]';
$statusLabels = $stats['status_labels'] ?? '[]';
$statusValues = $stats['status_values'] ?? '[]';
$extraScripts = <<<JS
<script>
// Reservation bar chart
new Chart(document.getElementById('reservationChart'), {
  type: 'bar',
  data: {
    labels: {$chartLabels},
    datasets: [{
      label: 'Réservations',
      data: {$chartValues},
      backgroundColor: 'rgba(16,185,129,.8)',
      borderRadius: 6,
    }]
  },
  options: {
    plugins: { legend: { display:false } },
    scales: {
      y: { beginAtZero:true, grid:{ color:'#f1f5f9' }, ticks:{ color:'#64748b', font:{size:12} } },
      x: { grid:{ display:false }, ticks:{ color:'#64748b', font:{size:12} } }
    }
  }
});

// Fleet donut chart
new Chart(document.getElementById('fleetChart'), {
  type: 'doughnut',
  data: {
    labels: {$statusLabels},
    datasets: [{
      data: {$statusValues},
      backgroundColor: ['#10b981','#3b82f6','#f59e0b','#ef4444'],
      borderWidth: 0,
    }]
  },
  options: {
    cutout: '72%',
    plugins: { legend: { display:false }, tooltip:{ enabled:true } }
  }
});
</script>
JS;
