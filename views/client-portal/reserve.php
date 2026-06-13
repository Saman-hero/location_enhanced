<?php
$pageTitle = 'Réserver '.$vehicle['marque'].' '.$vehicle['modele'];
$montantBase = $nbJours > 0 ? $nbJours * $vehicle['prix_jour'] : $vehicle['prix_jour'];
$caution     = $vehicle['caution'] ?? 0;
$jLabel = t('days');
?>
<style>
  .reserve-layout {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 32px;
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 32px 80px;
    align-items: start;
  }
  @media(max-width:1024px) {
    .reserve-layout { grid-template-columns: 1fr; }
    .sticky-widget  { position: static !important; }
  }

  /* ── Gallery ── */
  .gallery-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    grid-template-rows: 220px 220px;
    gap: 10px;
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 32px;
  }
  .gallery-main  { grid-row: 1 / 3; }
  .gallery-grid img,
  .gallery-placeholder { width:100%; height:100%; object-fit:cover; display:block; }
  .gallery-placeholder {
    background: linear-gradient(135deg,#1e293b,#0f172a);
    display: flex; align-items: center; justify-content: center;
  }

  /* ── Section titles ── */
  .section-title {
    font-size: 18px; font-weight: 700; color: #0f172a;
    margin: 0 0 16px; padding-bottom: 10px;
    border-bottom: 2px solid #f1f5f9;
  }

  /* ── Spec cards ── */
  .specs-grid {
    display: grid; grid-template-columns: repeat(4,1fr); gap: 12px;
    margin-bottom: 32px;
  }
  @media(max-width:640px) { .specs-grid { grid-template-columns: repeat(2,1fr); } }
  .spec-card {
    background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
    padding: 16px; text-align: center;
  }
  .spec-icon {
    width: 40px; height: 40px; border-radius: 10px;
    background: #f0fdf4; display: flex; align-items: center; justify-content: center;
    margin: 0 auto 10px;
  }
  .spec-label { font-size: 11px; color: #94a3b8; font-weight: 500; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 4px; }
  .spec-value { font-size: 15px; font-weight: 700; color: #0f172a; }

  /* ── Form sections ── */
  .form-section { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 24px; margin-bottom: 20px; }
  .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
  @media(max-width:640px) { .form-row { grid-template-columns: 1fr; } }

  /* ── Sticky widget ── */
  .sticky-widget {
    position: sticky; top: 90px;
    background: #fff; border: 1px solid #e2e8f0; border-radius: 20px;
    box-shadow: 0 8px 32px rgba(15,23,42,.10);
    overflow: hidden;
  }
  .widget-header {
    background: linear-gradient(135deg,#0f172a,#1e293b);
    padding: 24px;
    text-align: center;
  }
  .widget-price-main { font-size: 36px; font-weight: 800; color: #fff; line-height: 1; }
  .widget-price-sub  { font-size: 13px; color: #94a3b8; margin-top: 4px; }
  .widget-body { padding: 24px; }

  .date-field { margin-bottom: 14px; }
  .date-field label {
    display: flex; align-items: center; gap: 6px;
    font-size: 12px; font-weight: 600; color: #374151;
    text-transform: uppercase; letter-spacing: .04em; margin-bottom: 6px;
  }

  .price-summary { background: #f8fafc; border-radius: 10px; padding: 16px; margin: 16px 0; }
  .price-row { display: flex; justify-content: space-between; font-size: 13px; color: #64748b; padding: 5px 0; }
  .price-row:last-child { border-top: 1px solid #e2e8f0; margin-top: 6px; padding-top: 10px; font-weight: 700; font-size: 15px; color: #0f172a; }

  .cta-btn {
    width: 100%; padding: 14px; background: #10b981; color: #fff;
    border: none; border-radius: 10px; font-size: 16px; font-weight: 700;
    cursor: pointer; transition: background .15s;
    display: flex; align-items: center; justify-content: center; gap: 8px;
  }
  .cta-btn:hover { background: #059669; }
  .cta-note { text-align: center; font-size: 12px; color: #94a3b8; margin-top: 10px; display: flex; align-items: center; justify-content: center; gap: 4px; }

  /* ── Error ── */
  .error-banner { background: #fee2e2; border: 1px solid #fecaca; border-radius: 10px; padding: 14px 18px; color: #991b1b; font-size: 14px; margin-bottom: 20px; }
</style>

<form method="POST" id="reserve-form">

<div class="reserve-layout">

  <!-- ════════════ LEFT COLUMN ════════════ -->
  <div>

    <!-- Breadcrumb -->
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:24px;font-size:13px;color:#94a3b8;">
      <a href="<?= BASE_URL ?>/?page=client" style="color:#94a3b8;text-decoration:none;display:flex;align-items:center;gap:4px;">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
        <?= t('portal_title') ?>
      </a>
      <span>›</span>
      <span style="color:#0f172a;font-weight:500;"><?= h($vehicle['marque'].' '.$vehicle['modele']) ?></span>
    </div>

    <?php if (!empty($errors)): ?>
    <div class="error-banner">
      <strong>Veuillez corriger les erreurs suivantes :</strong><br>
      <?= implode('<br>', array_map('h', $errors)) ?>
    </div>
    <?php endif; ?>

    <!-- Vehicle title -->
    <div style="margin-bottom:20px;">
      <div style="font-size:12px;font-weight:600;color:#10b981;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;"><?= h(ucfirst($vehicle['categorie'])) ?></div>
      <h1 style="font-size:28px;font-weight:800;color:#0f172a;margin:0 0 6px;line-height:1.2;"><?= h($vehicle['marque'].' '.$vehicle['modele']) ?></h1>
      <p style="font-size:15px;color:#64748b;margin:0;"><?= h($vehicle['annee']??'') ?> · <?= h(ucfirst($vehicle['carburant']??'')) ?> · <?= h(ucfirst($vehicle['transmission']??'')) ?></p>
    </div>

    <!-- Gallery -->
    <?php
      $allImages = array_map(fn($img) => vehicle_img_url($img['image_url']), $vehicleImages ?? []);
      if (empty($allImages) && !empty($vehicle['image_url'])) $allImages = [vehicle_img_url($vehicle['image_url'])];
    ?>
    <div style="position:relative;">
      <!-- Main photo viewer -->
      <div id="gallery-main" style="height:340px;border-radius:16px;overflow:hidden;background:#0f172a;position:relative;">
        <?php if (!empty($allImages)): ?>
        <img id="gallery-main-img" src="<?= h($allImages[0]) ?>" alt="<?= h($vehicle['marque']) ?>"
             style="width:100%;height:100%;object-fit:cover;transition:opacity .35s ease;">
        <?php if (count($allImages) > 1): ?>
        <button onclick="galleryPrev()" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);background:rgba(0,0,0,.5);border:none;color:#fff;width:36px;height:36px;border-radius:50%;cursor:pointer;font-size:18px;display:flex;align-items:center;justify-content:center;">&#8249;</button>
        <button onclick="galleryNext()" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:rgba(0,0,0,.5);border:none;color:#fff;width:36px;height:36px;border-radius:50%;cursor:pointer;font-size:18px;display:flex;align-items:center;justify-content:center;">&#8250;</button>
        <div style="position:absolute;bottom:10px;right:14px;background:rgba(0,0,0,.5);color:#fff;font-size:12px;padding:3px 9px;border-radius:99px;" id="gallery-counter">1 / <?= count($allImages) ?></div>
        <?php endif; ?>
        <?php else: ?>
        <div style="height:100%;display:flex;align-items:center;justify-content:center;">
          <svg width="64" height="64" fill="none" stroke="rgba(255,255,255,0.15)" stroke-width="1" viewBox="0 0 24 24"><path d="M19 17H5l-2-4V7a1 1 0 011-1h14a1 1 0 011 1v6l-2 4z"/><circle cx="7.5" cy="17.5" r="1.5"/><circle cx="16.5" cy="17.5" r="1.5"/></svg>
        </div>
        <?php endif; ?>
      </div>

      <!-- Thumbnails -->
      <?php if (count($allImages) > 1): ?>
      <div style="display:flex;gap:8px;margin-top:10px;overflow-x:auto;padding-bottom:4px;">
        <?php foreach ($allImages as $ti => $timg): ?>
        <img src="<?= h($timg) ?>" onclick="galleryGo(<?= $ti ?>)"
             style="height:64px;width:90px;object-fit:cover;border-radius:8px;cursor:pointer;border:2px solid <?= $ti===0?'#10b981':'transparent' ?>;flex-shrink:0;transition:border-color .2s;"
             class="gallery-thumb" id="thumb-<?= $ti ?>">
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>

    <script>
    const galleryImgs = <?= json_encode($allImages) ?>;
    let galleryIdx = 0;
    function galleryGo(i) {
      galleryIdx = (i + galleryImgs.length) % galleryImgs.length;
      const img = document.getElementById('gallery-main-img');
      img.style.opacity = '0';
      setTimeout(() => { img.src = galleryImgs[galleryIdx]; img.style.opacity = '1'; }, 180);
      document.getElementById('gallery-counter') && (document.getElementById('gallery-counter').textContent = (galleryIdx+1) + ' / ' + galleryImgs.length);
      document.querySelectorAll('.gallery-thumb').forEach((t,ti) => t.style.borderColor = ti===galleryIdx?'#10b981':'transparent');
    }
    function galleryNext() { galleryGo(galleryIdx + 1); }
    function galleryPrev() { galleryGo(galleryIdx - 1); }
    </script>

    <!-- Specs -->
    <div class="specs-grid">
      <div class="spec-card">
        <div class="spec-icon"><svg width="20" height="20" fill="none" stroke="#10b981" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg></div>
        <div class="spec-label"><?= t('vehicle_seats') ?></div>
        <div class="spec-value"><?= (int)($vehicle['nb_places']??5) ?></div>
      </div>
      <div class="spec-card">
        <div class="spec-icon"><svg width="20" height="20" fill="none" stroke="#10b981" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83"/></svg></div>
        <div class="spec-label"><?= t('vehicle_transmission') ?></div>
        <div class="spec-value"><?= ucfirst(h($vehicle['transmission']??'—')) ?></div>
      </div>
      <div class="spec-card">
        <div class="spec-icon"><svg width="20" height="20" fill="none" stroke="#10b981" stroke-width="2" viewBox="0 0 24 24"><path d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/></svg></div>
        <div class="spec-label"><?= t('vehicle_fuel') ?></div>
        <div class="spec-value"><?= ucfirst(h($vehicle['carburant']??'—')) ?></div>
      </div>
      <div class="spec-card">
        <div class="spec-icon"><svg width="20" height="20" fill="none" stroke="#10b981" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2a10 10 0 100 20A10 10 0 0012 2z"/><path d="M12 6v6l4 2"/></svg></div>
        <div class="spec-label"><?= t('vehicle_mileage') ?></div>
        <div class="spec-value"><?= number_format($vehicle['kilometrage']??0,0,',',' ') ?> km</div>
      </div>
    </div>

    <!-- Client info form -->
    <div class="form-section">
      <div class="section-title">
        <svg width="18" height="18" fill="none" stroke="#10b981" stroke-width="2" viewBox="0 0 24 24" style="vertical-align:middle;margin-right:6px;"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
        <?= t('portal_your_info') ?>
      </div>

      <div class="form-row">
        <div>
          <label class="form-label"><?= t('res_form_firstname') ?> *</label>
          <input type="text" name="prenom" class="form-control" value="<?= h($data['prenom']) ?>" placeholder="Mohammed" required>
        </div>
        <div>
          <label class="form-label"><?= t('res_form_name') ?> *</label>
          <input type="text" name="nom" class="form-control" value="<?= h($data['nom']) ?>" placeholder="Alaoui" required>
        </div>
      </div>
      <div class="form-row">
        <div>
          <label class="form-label"><?= t('res_form_cin') ?> *</label>
          <input type="text" name="cin" class="form-control" value="<?= h($data['cin']) ?>" placeholder="AB123456" required>
        </div>
        <div>
          <label class="form-label"><?= t('res_form_phone') ?> *</label>
          <input type="tel" name="telephone" class="form-control" value="<?= h($data['telephone']) ?>" placeholder="+212 6XX-XXXXXX" required>
        </div>
      </div>
      <div class="form-row">
        <div>
          <label class="form-label"><?= t('res_form_email') ?></label>
          <input type="email" name="email" class="form-control" value="<?= h($data['email']) ?>" placeholder="email@exemple.ma">
        </div>
        <div>
          <label class="form-label"><?= t('res_form_license') ?></label>
          <input type="text" name="permis_numero" class="form-control" value="<?= h($data['permis_numero']) ?>" placeholder="123456789">
        </div>
      </div>
      <div style="max-width:240px;">
        <label class="form-label"><?= t('res_form_license_exp') ?></label>
        <input type="date" name="permis_expiration" class="form-control" value="<?= h($data['permis_expiration']) ?>">
      </div>
    </div>

    <!-- Rental details -->
    <div class="form-section">
      <div class="section-title">
        <svg width="18" height="18" fill="none" stroke="#10b981" stroke-width="2" viewBox="0 0 24 24" style="vertical-align:middle;margin-right:6px;"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        <?= t('portal_trip_details') ?>
      </div>
      <div class="form-row">
        <div>
          <label class="form-label"><?= t('portal_date_from') ?> *</label>
          <input type="datetime-local" name="date_debut" class="form-control"
                 value="<?= h(str_replace(' ','T',substr($data['date_debut'],0,16))) ?>"
                 onchange="updateTotal()" required>
        </div>
        <div>
          <label class="form-label"><?= t('portal_date_to') ?> *</label>
          <input type="datetime-local" name="date_fin_prevue" class="form-control"
                 value="<?= h(str_replace(' ','T',substr($data['date_fin_prevue'],0,16))) ?>"
                 onchange="updateTotal()" required>
        </div>
      </div>
      <div class="form-row">
        <div>
          <label class="form-label"><?= t('portal_departure') ?></label>
          <input type="text" name="lieu_depart" class="form-control" value="<?= h($data['lieu_depart']) ?>" placeholder="Agence Casablanca">
        </div>
        <div>
          <label class="form-label"><?= t('portal_return') ?></label>
          <input type="text" name="lieu_retour" class="form-control" value="<?= h($data['lieu_retour']) ?>" placeholder="Agence Casablanca">
        </div>
      </div>
    </div>

  </div><!-- end left column -->

  <!-- ════════════ RIGHT COLUMN (sticky widget) ════════════ -->
  <div>
    <div class="sticky-widget">

      <div class="widget-header">
        <div style="font-size:12px;color:#64748b;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;"><?= t('per_day') ?></div>
        <div class="widget-price-main num"><?= number_format($vehicle['prix_jour'],0,',',' ') ?> <span style="font-size:16px;font-weight:400;">MAD</span></div>
        <div class="widget-price-sub"><?= h($vehicle['marque'].' '.$vehicle['modele']) ?></div>
      </div>

      <div class="widget-body">

        <!-- Price summary -->
        <div class="price-summary">
          <div class="price-row">
            <span id="summary-label" class="num"><?= $nbJours > 0 ? $nbJours.' '.t('days') : '1 '.t('day') ?> × <?= number_format($vehicle['prix_jour'],0,',',' ') ?> MAD</span>
            <span id="summary-base" class="num"><?= number_format($montantBase,0,',',' ') ?> MAD</span>
          </div>
          <?php if ($caution > 0): ?>
          <div class="price-row">
            <span><?= t('portal_deposit_ref') ?></span>
            <span class="num"><?= number_format($caution,0,',',' ') ?> MAD</span>
          </div>
          <?php endif; ?>
          <div class="price-row" style="border-top:1px solid #e2e8f0;margin-top:6px;padding-top:10px;font-weight:700;font-size:15px;color:#0f172a;">
            <span><?= t('portal_total_est') ?></span>
            <span id="summary-total" class="num" style="color:#059669;"><?= number_format($montantBase + $caution,0,',',' ') ?> MAD</span>
          </div>
        </div>

        <button type="submit" class="cta-btn">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4"/><path d="M12 2a10 10 0 100 20A10 10 0 0012 2z"/></svg>
          <?= t('portal_confirm_res') ?>
        </button>
        <div class="cta-note">
          <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
          <?= t('portal_no_charge') ?>
        </div>

        <div style="margin-top:20px;padding-top:16px;border-top:1px solid #f1f5f9;">
          <div style="font-size:12px;color:#94a3b8;margin-bottom:8px;font-weight:500;"><?= t('portal_guarantees') ?></div>
          <?php foreach ([
            [t('portal_free_cancel'), '#10b981'],
            [t('portal_roadside'), '#3b82f6'],
            [t('portal_cleaned'), '#8b5cf6'],
          ] as [$txt, $col]): ?>
          <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:#475569;margin-bottom:6px;">
            <svg width="14" height="14" fill="none" stroke="<?= $col ?>" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/></svg>
            <?= $txt ?>
          </div>
          <?php endforeach; ?>
        </div>

      </div>
    </div>
  </div><!-- end right column -->

</div><!-- end reserve-layout -->
</form>

<script>
const prixJour = <?= (float)$vehicle['prix_jour'] ?>;
const caution  = <?= (float)($vehicle['caution'] ?? 0) ?>;
const jLabel   = <?= json_encode($jLabel) ?>;

function updateTotal() {
  const d1 = document.querySelector('[name="date_debut"]').value;
  const d2 = document.querySelector('[name="date_fin_prevue"]').value;
  if (!d1 || !d2) return;
  const diff = (new Date(d2) - new Date(d1)) / 86400000;
  const jours = Math.max(1, Math.round(diff));
  const base  = jours * prixJour;
  document.getElementById('summary-label').textContent = jours + ' ' + jLabel + ' × ' + prixJour.toLocaleString('fr-FR') + ' MAD';
  document.getElementById('summary-base').textContent  = base.toLocaleString('fr-FR') + ' MAD';
  document.getElementById('summary-total').textContent = (base + caution).toLocaleString('fr-FR') + ' MAD';
}
</script>
