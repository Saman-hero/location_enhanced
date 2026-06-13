<?php $pageTitle = 'Nos véhicules disponibles'; $pageBreadcrumb = ''; ?>

<!-- Hero -->
<div class="hero">
  <div style="max-width:1200px;margin:0 auto;padding:0 32px;text-align:center;position:relative;z-index:1;">
    <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(16,185,129,.15);border:1px solid rgba(16,185,129,.3);border-radius:99px;padding:6px 16px;font-size:12px;font-weight:600;color:#34d399;margin-bottom:20px;letter-spacing:.04em;">
      <span style="width:6px;height:6px;background:#10b981;border-radius:50%;animation:pulse 2s infinite;display:inline-block;"></span>
      FLOTTE DISPONIBLE
    </div>
    <h1 style="font-size:40px;font-weight:800;color:#f1f5f9;margin-bottom:12px;line-height:1.2;">Réservez votre véhicule</h1>
    <p style="color:#94a3b8;font-size:17px;max-width:500px;margin:0 auto;">Sélectionnez vos dates et trouvez le véhicule idéal parmi notre flotte</p>
  </div>
</div>

<div style="max-width:1200px;margin:0 auto;padding:0 32px;">

  <!-- Search box -->
  <div class="search-box" style="margin-top:-32px;margin-bottom:40px;position:relative;z-index:10;">
    <form method="GET" action="<?= BASE_URL ?>/" style="display:flex;gap:16px;flex-wrap:wrap;align-items:flex-end;">
      <input type="hidden" name="page" value="client">
      <div style="flex:1;min-width:140px;">
        <label class="form-label">Date de départ</label>
        <input type="date" name="date_debut" class="form-control" value="<?= h($dateDebut) ?>" min="<?= date('Y-m-d') ?>">
      </div>
      <div style="flex:1;min-width:140px;">
        <label class="form-label">Date de retour</label>
        <input type="date" name="date_fin" class="form-control" value="<?= h($dateFin) ?>" min="<?= date('Y-m-d') ?>">
      </div>
      <div style="min-width:130px;">
        <label class="form-label">Catégorie</label>
        <select name="categorie" class="form-control">
          <option value="">Toutes</option>
          <?php foreach(['économique','berline','suv','utilitaire','luxe'] as $cat): ?>
          <option value="<?= $cat ?>" <?= $categorie===$cat?'selected':'' ?>><?= ucfirst($cat) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div style="min-width:120px;">
        <label class="form-label">Prix max / jour</label>
        <input type="number" name="prix_max" class="form-control" value="<?= h($prixMax ?: '') ?>" placeholder="Illimité">
      </div>
      <div style="flex:1;min-width:160px;">
        <label class="form-label">Marque, modèle…</label>
        <input type="text" name="search" class="form-control" value="<?= h($search) ?>" placeholder="Ex: Toyota, SUV…">
      </div>
      <div style="display:flex;gap:8px;align-items:flex-end;">
        <button type="submit" class="btn btn-emerald">
          <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          Rechercher
        </button>
        <?php if ($dateDebut||$dateFin||$categorie||$prixMax||$search): ?>
        <a href="<?= BASE_URL ?>/?page=client" class="btn btn-outline">Réinitialiser</a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <?php if ($dateDebut && $dateFin && $nbJours > 0): ?>
  <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:12px 18px;margin-bottom:28px;font-size:14px;color:#1e40af;display:flex;align-items:center;gap:8px;">
    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
    Durée sélectionnée : <strong><?= $nbJours ?> jour(s)</strong> &nbsp;·&nbsp; du <?= date('d/m/Y', strtotime($dateDebut)) ?> au <?= date('d/m/Y', strtotime($dateFin)) ?>
  </div>
  <?php endif; ?>

  <!-- Results header -->
  <?php if (!empty($vehicles)): ?>
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
    <p style="font-size:14px;color:#64748b;"><strong style="color:#0f172a;"><?= count($vehicles) ?></strong> véhicule<?= count($vehicles)>1?'s':'' ?> disponible<?= count($vehicles)>1?'s':'' ?></p>
  </div>
  <?php endif; ?>

  <!-- Vehicle grid -->
  <?php if (empty($vehicles)): ?>
  <div style="text-align:center;padding:80px 20px;">
    <div style="width:80px;height:80px;background:#f1f5f9;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
      <svg width="40" height="40" fill="none" stroke="#94a3b8" stroke-width="1.5" viewBox="0 0 24 24"><path d="M19 17H5l-2-4V7a1 1 0 011-1h14a1 1 0 011 1v6l-2 4z"/><circle cx="7.5" cy="17.5" r="1.5"/><circle cx="16.5" cy="17.5" r="1.5"/></svg>
    </div>
    <p style="font-size:17px;font-weight:600;color:#0f172a;margin-bottom:6px;">Aucun véhicule disponible</p>
    <p style="font-size:14px;color:#94a3b8;">Essayez d'autres dates ou une autre catégorie</p>
  </div>
  <?php else: ?>

  <div class="vehicles-grid">
    <?php foreach ($vehicles as $v): ?>
    <div class="vehicle-card">

      <!-- Image -->
      <?php if (!empty($v['image_url'])): ?>
      <img src="<?= h(vehicle_img_url($v['image_url'])) ?>"
           alt="<?= h($v['marque'].' '.$v['modele']) ?>"
           class="vehicle-img">
      <?php else: ?>
      <div class="vehicle-img-placeholder">
        <svg width="52" height="52" fill="none" stroke="#cbd5e1" stroke-width="1.2" viewBox="0 0 24 24">
          <path d="M19 17H5l-2-4V7a1 1 0 011-1h14a1 1 0 011 1v6l-2 4z"/>
          <circle cx="7.5" cy="17.5" r="1.5"/><circle cx="16.5" cy="17.5" r="1.5"/>
        </svg>
      </div>
      <?php endif; ?>

      <!-- Content -->
      <div style="padding:20px;">

        <!-- Title + category -->
        <div style="margin-bottom:12px;">
          <h3 style="font-size:17px;font-weight:700;color:#0f172a;margin-bottom:4px;line-height:1.3;"><?= h($v['marque'].' '.$v['modele']) ?></h3>
          <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
            <span style="font-size:12px;font-weight:600;color:#059669;background:#d1fae5;padding:2px 8px;border-radius:99px;"><?= ucfirst(h($v['categorie'])) ?></span>
            <span style="font-size:12px;color:#94a3b8;"><?= h($v['annee']??'') ?></span>
            <span style="font-size:12px;color:#94a3b8;">·</span>
            <span style="font-size:12px;color:#94a3b8;"><?= ucfirst(h($v['carburant']??'')) ?></span>
          </div>
        </div>

        <!-- Spec pills -->
        <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:16px;">
          <span class="spec-pill">
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
            <?= (int)($v['nb_places']??5) ?> places
          </span>
          <span class="spec-pill">
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83"/></svg>
            <?= ucfirst(h($v['transmission']??'Manuelle')) ?>
          </span>
          <?php if (!empty($v['caution']) && $v['caution'] > 0): ?>
          <span class="spec-pill">
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
            Caution <?= number_format($v['caution'],0,',',' ') ?> MAD
          </span>
          <?php endif; ?>
        </div>

        <!-- Price + CTA -->
        <div style="display:flex;justify-content:space-between;align-items:center;padding-top:14px;border-top:1px solid #f1f5f9;">
          <div>
            <div>
              <span style="font-size:24px;font-weight:800;color:#059669;"><?= number_format($v['prix_jour'],0,',',' ') ?></span>
              <span style="font-size:13px;color:#94a3b8;font-weight:500;"> MAD/jour</span>
            </div>
            <?php if ($nbJours > 0): ?>
            <div style="font-size:12px;color:#64748b;margin-top:2px;">
              Total <?= $nbJours ?>j : <strong style="color:#0f172a;"><?= number_format($v['prix_jour']*$nbJours,0,',',' ') ?> MAD</strong>
            </div>
            <?php endif; ?>
          </div>
          <a href="<?= BASE_URL ?>/?page=client/reserve&vehicle_id=<?= $v['id'] ?>&date_debut=<?= urlencode($dateDebut) ?>&date_fin=<?= urlencode($dateFin) ?>"
             class="btn btn-emerald">
            Réserver
          </a>
        </div>

      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <?php endif; ?>
</div>

<style>
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }
</style>
