<?php $pageTitle = 'Nouvelle réservation'; $pageBreadcrumb = 'Réservations › Nouveau'; ?>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger"><?= implode('<br>', array_map('h', $errors)) ?></div>
<?php endif; ?>

<div class="card">
  <div class="card-header">
    <span class="card-title">Nouvelle réservation</span>
    <a href="<?= BASE_URL ?>/?page=reservations" class="btn btn-outline btn-sm">← Retour</a>
  </div>
  <div class="card-body">
    <form method="POST">
      <div class="form-grid-2" style="margin-bottom:16px;">
        <div>
          <label class="form-label">Client *</label>
          <select name="client_id" class="form-control" required>
            <option value="">— Sélectionner un client —</option>
            <?php foreach ($clients as $c): ?>
            <option value="<?= $c['id'] ?>" <?= $data['client_id']==$c['id']?'selected':'' ?>><?= h($c['prenom'].' '.$c['nom']) ?> (<?= h($c['cin']??'') ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="form-label">Véhicule *</label>
          <select name="vehicle_id" class="form-control" id="vehicle-sel" required onchange="updatePrice()">
            <option value="">— Sélectionner un véhicule —</option>
            <?php foreach ($vehicles as $v): ?>
            <option value="<?= $v['id'] ?>" data-price="<?= $v['prix_jour'] ?>" data-caution="<?= $v['caution'] ?>" <?= $data['vehicle_id']==$v['id']?'selected':'' ?>>
              <?= h($v['numero'].' — '.$v['marque'].' '.$v['modele']) ?> (<?= number_format($v['prix_jour'],0,',',' ') ?> MAD/j)
            </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-grid-2" style="margin-bottom:16px;">
        <div><label class="form-label">Date de début *</label><input type="datetime-local" name="date_debut" class="form-control" value="<?= h(str_replace(' ','T',substr($data['date_debut']??'',0,16))) ?>" required onchange="calcDays()"></div>
        <div><label class="form-label">Date de fin prévue *</label><input type="datetime-local" name="date_fin_prevue" class="form-control" value="<?= h(str_replace(' ','T',substr($data['date_fin_prevue']??'',0,16))) ?>" required onchange="calcDays()"></div>
      </div>
      <div class="form-grid-2" style="margin-bottom:16px;">
        <div><label class="form-label">Lieu de départ</label><input type="text" name="lieu_depart" class="form-control" value="<?= h($data['lieu_depart']) ?>" placeholder="Agence Casablanca"></div>
        <div><label class="form-label">Lieu de retour</label><input type="text" name="lieu_retour" class="form-control" value="<?= h($data['lieu_retour']) ?>" placeholder="Agence Casablanca"></div>
      </div>
      <div class="form-grid-2" style="margin-bottom:16px;">
        <div><label class="form-label">Statut initial</label>
          <select name="statut" class="form-control">
            <?php foreach(['en attente','confirmée','en cours'] as $s): ?>
            <option value="<?= $s ?>" <?= $data['statut']===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div><label class="form-label">Durée estimée</label><div id="days-display" style="padding:9px 12px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;color:#64748b;">—</div></div>
      </div>

      <div id="summary-box" style="background:#f0fdf4;border:1px solid #a7f3d0;border-radius:10px;padding:16px 20px;margin-bottom:20px;display:none;">
        <div style="font-size:13px;font-weight:600;color:#065f46;margin-bottom:10px;">Récapitulatif</div>
        <div style="display:flex;gap:24px;flex-wrap:wrap;">
          <div><span style="font-size:11px;color:#6b7280;">Durée</span><br><strong id="sum-days">—</strong></div>
          <div><span style="font-size:11px;color:#6b7280;">Prix/jour</span><br><strong id="sum-price">—</strong></div>
          <div><span style="font-size:11px;color:#6b7280;">Montant total</span><br><strong id="sum-total" style="font-size:18px;color:#059669;">—</strong></div>
          <div><span style="font-size:11px;color:#6b7280;">Caution</span><br><strong id="sum-caution">—</strong></div>
        </div>
      </div>

      <div style="margin-bottom:24px;"><label class="form-label">Commentaire</label><textarea name="commentaire" class="form-control" rows="3" placeholder="Notes, instructions particulières..."><?= h($data['commentaire']) ?></textarea></div>
      <div style="display:flex;gap:10px;">
        <button type="submit" class="btn btn-emerald">Créer la réservation</button>
        <a href="<?= BASE_URL ?>/?page=reservations" class="btn btn-outline">Annuler</a>
      </div>
    </form>
  </div>
</div>

<?php $extraScripts = <<<'JS'
<script>
function updatePrice() {
  const sel = document.getElementById('vehicle-sel');
  const opt = sel.options[sel.selectedIndex];
  window._price   = parseFloat(opt.dataset.price   || 0);
  window._caution = parseFloat(opt.dataset.caution || 0);
  calcDays();
}
function calcDays() {
  const d1 = document.querySelector('[name=date_debut]').value;
  const d2 = document.querySelector('[name=date_fin_prevue]').value;
  if (!d1 || !d2) return;
  const diff = (new Date(d2) - new Date(d1)) / 86400000;
  const days = Math.max(1, Math.ceil(diff));
  document.getElementById('days-display').textContent = days + ' jour(s)';
  if (window._price) {
    const total = days * window._price;
    document.getElementById('sum-days').textContent    = days + ' j';
    document.getElementById('sum-price').textContent   = window._price.toLocaleString('fr-MA') + ' MAD';
    document.getElementById('sum-total').textContent   = total.toLocaleString('fr-MA') + ' MAD';
    document.getElementById('sum-caution').textContent = (window._caution||0).toLocaleString('fr-MA') + ' MAD';
    document.getElementById('summary-box').style.display = 'block';
  }
}
window._price = 0; window._caution = 0;
updatePrice();
</script>
JS;
?>
