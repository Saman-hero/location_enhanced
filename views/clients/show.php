<?php $pageTitle = $client['prenom'].' '.$client['nom']; $pageBreadcrumb = 'Clients › Détails'; ?>

<div style="display:flex;justify-content:flex-end;gap:8px;margin-bottom:20px;">
  <a href="<?= BASE_URL ?>/?page=clients/edit&id=<?= $client['id'] ?>" class="btn btn-outline">Modifier</a>
  <a href="<?= BASE_URL ?>/?page=reservations/add&client_id=<?= $client['id'] ?>" class="btn btn-emerald">Nouvelle réservation</a>
</div>

<div style="display:grid;grid-template-columns:1fr 2fr;gap:20px;margin-bottom:24px;">
  <div class="card">
    <div class="card-body">
      <div style="text-align:center;padding:12px 0 20px;">
        <div class="avatar" style="width:64px;height:64px;font-size:22px;background:#dbeafe;color:#1e40af;margin:0 auto 14px;"><?= strtoupper(substr($client['prenom'],0,1).substr($client['nom'],0,1)) ?></div>
        <h2 style="font-size:20px;font-weight:700;color:#0f172a;"><?= h($client['prenom'].' '.$client['nom']) ?></h2>
        <div style="color:#64748b;font-size:13px;"><?= h($client['type_client']??'particulier') ?><?= !empty($client['entreprise']) ? ' · '.h($client['entreprise']) : '' ?></div>
        <div style="margin-top:10px;"><?= status_badge($client['statut']) ?></div>
      </div>
      <hr class="divider">
      <dl style="margin-top:16px;">
        <?php $rows = [
          ['CIN', $client['cin']??'—'],
          ['Téléphone', $client['telephone']??'—'],
          ['Email', $client['email']??'—'],
          ['Adresse', $client['adresse']??'—'],
          ['Ville', $client['ville']??'—'],
          ['N° permis', $client['permis_numero']??'—'],
          ['Catégorie permis', $client['permis_categorie']??'—'],
          ['Expiration permis', $client['permis_expiration'] ? date('d/m/Y', strtotime($client['permis_expiration'])) : '—'],
        ];
        foreach ($rows as [$label, $val]): ?>
        <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #f8fafc;">
          <dt style="font-size:12px;color:#94a3b8;font-weight:500;"><?= $label ?></dt>
          <dd style="font-size:13px;color:#0f172a;font-weight:500;margin:0;text-align:right;"><?= h((string)$val) ?></dd>
        </div>
        <?php endforeach; ?>
      </dl>
      <?php if (!empty($client['notes'])): ?>
      <div style="margin-top:12px;padding:10px;background:#f8fafc;border-radius:8px;font-size:13px;color:#64748b;"><?= h($client['notes']) ?></div>
      <?php endif; ?>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <span class="card-title">Historique des réservations</span>
      <a href="<?= BASE_URL ?>/?page=reservations/add&client_id=<?= $client['id'] ?>" class="btn btn-outline btn-sm">+ Nouvelle</a>
    </div>
    <div style="overflow-x:auto;">
      <table class="data-table">
        <thead><tr><th>Référence</th><th>Véhicule</th><th>Début</th><th>Fin prévue</th><th>Montant</th><th>Statut</th></tr></thead>
        <tbody>
          <?php foreach ($reservations as $r): ?>
          <tr>
            <td><a href="<?= BASE_URL ?>/?page=reservations/show&id=<?= $r['id'] ?>" style="color:#3b82f6;font-weight:600;"><?= h($r['reference']) ?></a></td>
            <td>
              <div style="font-weight:500;"><?= h($r['marque'].' '.$r['modele']) ?></div>
              <div style="font-size:12px;color:#94a3b8;"><?= h($r['numero']??'') ?></div>
            </td>
            <td><?= date('d/m/Y', strtotime($r['date_debut'])) ?></td>
            <td><?= $r['date_fin_prevue'] ? date('d/m/Y', strtotime($r['date_fin_prevue'])) : '—' ?></td>
            <td><?= $r['montant_total'] ? number_format($r['montant_total'],0,',',' ').' MAD' : '—' ?></td>
            <td><?= status_badge($r['statut']) ?></td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($reservations)): ?>
          <tr><td colspan="6" style="text-align:center;color:#94a3b8;padding:24px;">Aucune réservation</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
