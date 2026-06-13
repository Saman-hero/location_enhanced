<?php $pageTitle = $reservation['reference']; $pageBreadcrumb = 'Réservations › Détails';
$totalPaid = array_sum(array_column($payments, 'montant'));
$balance   = ($reservation['montant_total'] ?? 0) - $totalPaid;
?>

<div style="display:flex;justify-content:flex-end;gap:8px;margin-bottom:20px;">
  <?php if (!in_array($reservation['statut'],['terminée','annulée'])): ?>
  <a href="<?= BASE_URL ?>/?page=reservations/edit&id=<?= $reservation['id'] ?>" class="btn btn-outline">Modifier</a>
  <?php endif; ?>
  <a href="<?= BASE_URL ?>/?page=payments/add&reservation_id=<?= $reservation['id'] ?>" class="btn btn-emerald">Ajouter un paiement</a>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">
  <div class="card">
    <div class="card-header"><span class="card-title">Réservation</span><?= status_badge($reservation['statut']) ?></div>
    <div class="card-body">
      <dl>
        <?php $rows = [
          ['Référence', $reservation['reference']],
          ['Date début', date('d/m/Y H:i', strtotime($reservation['date_debut']))],
          ['Date fin prévue', date('d/m/Y H:i', strtotime($reservation['date_fin_prevue']))],
          ['Retour effectif', $reservation['date_retour_effectif'] ? date('d/m/Y H:i', strtotime($reservation['date_retour_effectif'])) : '—'],
          ['Lieu départ', $reservation['lieu_depart'] ?: '—'],
          ['Lieu retour', $reservation['lieu_retour'] ?: '—'],
          ['Km départ', $reservation['km_depart'] ? number_format($reservation['km_depart'],0,',',' ').' km' : '—'],
          ['Km retour', $reservation['km_retour'] ? number_format($reservation['km_retour'],0,',',' ').' km' : '—'],
          ['Nb. jours', $reservation['nb_jours'] ?? '—'],
          ['Prix/jour', number_format($reservation['prix_jour']??0,2,',',' ').' MAD'],
          ['Caution', number_format($reservation['caution']??0,2,',',' ').' MAD'],
          ['Frais extra', number_format($reservation['frais_extra']??0,2,',',' ').' MAD'],
          ['Montant total', number_format($reservation['montant_total']??0,2,',',' ').' MAD'],
        ];
        foreach ($rows as [$label, $val]): ?>
        <div style="display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid #f8fafc;">
          <dt style="font-size:12px;color:#94a3b8;font-weight:500;"><?= $label ?></dt>
          <dd style="font-size:13px;color:#0f172a;font-weight:500;margin:0;"><?= h((string)$val) ?></dd>
        </div>
        <?php endforeach; ?>
      </dl>
      <?php if ($reservation['commentaire']): ?>
      <div style="margin-top:12px;padding:10px;background:#f8fafc;border-radius:8px;font-size:13px;color:#64748b;"><?= h($reservation['commentaire']) ?></div>
      <?php endif; ?>
    </div>
  </div>

  <div style="display:flex;flex-direction:column;gap:20px;">
    <div class="card">
      <div class="card-header"><span class="card-title">Client</span></div>
      <div class="card-body">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
          <div class="avatar" style="width:44px;height:44px;font-size:16px;background:#dbeafe;color:#1e40af;"><?= strtoupper(substr($reservation['client_prenom'],0,1).substr($reservation['client_nom'],0,1)) ?></div>
          <div>
            <div style="font-weight:600;font-size:15px;color:#0f172a;"><?= h($reservation['client_prenom'].' '.$reservation['client_nom']) ?></div>
            <div style="font-size:12px;color:#94a3b8;"><?= h($reservation['client_cin']??'') ?></div>
          </div>
        </div>
        <div style="font-size:13px;color:#64748b;display:flex;flex-direction:column;gap:4px;">
          <?php if ($reservation['client_email']??''): ?><span><?= h($reservation['client_email']) ?></span><?php endif; ?>
          <?php if ($reservation['client_tel']??''): ?><span><?= h($reservation['client_tel']) ?></span><?php endif; ?>
        </div>
        <a href="<?= BASE_URL ?>/?page=clients/show&id=<?= $reservation['client_id'] ?>" class="btn btn-outline btn-sm" style="margin-top:12px;">Voir le client</a>
      </div>
    </div>
    <div class="card">
      <div class="card-header"><span class="card-title">Véhicule</span></div>
      <div class="card-body">
        <div style="font-weight:600;font-size:15px;color:#0f172a;"><?= h($reservation['marque'].' '.$reservation['modele']) ?></div>
        <div style="font-size:13px;color:#64748b;margin-top:4px;"><?= h($reservation['vehicle_numero']??'') ?> · <?= h(ucfirst($reservation['vehicle_categorie']??'')) ?></div>
        <a href="<?= BASE_URL ?>/?page=vehicles/show&id=<?= $reservation['vehicle_id'] ?>" class="btn btn-outline btn-sm" style="margin-top:12px;">Voir le véhicule</a>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <span class="card-title">Paiements</span>
    <div style="display:flex;align-items:center;gap:12px;">
      <?php $balColor = $balance > 0 ? '#ef4444' : '#10b981';
            $balLabel = $balance > 0 ? 'Solde restant: '.number_format($balance,2,',',' ').' MAD' : 'Entièrement payé'; ?>
      <span style="font-size:13px;font-weight:600;color:<?= $balColor ?>;"><?= $balLabel ?></span>
      <a href="<?= BASE_URL ?>/?page=payments/add&reservation_id=<?= $reservation['id'] ?>" class="btn btn-outline btn-sm">+ Paiement</a>
    </div>
  </div>
  <div style="overflow-x:auto;">
    <table class="data-table">
      <thead><tr><th>Date</th><th>Type</th><th>Moyen</th><th>Montant</th></tr></thead>
      <tbody>
        <?php foreach ($payments as $p): ?>
        <tr>
          <td><?= date('d/m/Y', strtotime($p['date_paiement'])) ?></td>
          <td><?= h(ucfirst($p['type'])) ?></td>
          <td><?= h(ucfirst($p['type_paiement'])) ?></td>
          <td style="font-weight:600;"><?= number_format($p['montant'],2,',',' ') ?> MAD</td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($payments)): ?>
        <tr><td colspan="4" style="text-align:center;color:#94a3b8;padding:24px;">Aucun paiement enregistré</td></tr>
        <?php endif; ?>
        <?php if ($payments): ?>
        <tr style="background:#f8fafc;font-weight:600;">
          <td colspan="3" style="text-align:right;color:#64748b;font-size:13px;">Total payé</td>
          <td style="color:#059669;"><?= number_format($totalPaid,2,',',' ') ?> MAD</td>
        </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
