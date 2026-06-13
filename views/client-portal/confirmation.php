<?php $pageTitle = 'Réservation confirmée'; ?>
<style>
  /* ════════════ PRINT STYLES ════════════ */
  @media print {
    /* Hide everything except the receipt */
    .client-nav, .client-footer, .no-print,
    .success-hero, .step, .confirm-card-header svg { display: none !important; }

    body, * { font-family: 'Inter', Arial, sans-serif !important; }
    body { background: #fff !important; }

    .confirm-wrap { max-width: 100% !important; padding: 0 !important; margin: 0 !important; }

    /* Receipt wrapper */
    .print-receipt {
      display: block !important;
      border: 2px solid #0f172a;
      border-radius: 0;
      padding: 0;
      page-break-inside: avoid;
    }

    .confirm-card { box-shadow: none !important; border: 1px solid #ddd !important; border-radius: 4px !important; margin-bottom: 12px !important; }
    .confirm-card-header { background: #f8fafc !important; padding: 10px 16px !important; font-size: 11px !important; }
    .confirm-card-body { padding: 12px 16px !important; }

    /* Force colors for print */
    .vehicle-banner { background: #0f172a !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .vehicle-banner * { color: #fff !important; }

    .ref-badge { border: 1.5px solid #000 !important; background: #fff !important; color: #000 !important; }
    .success-circle { box-shadow: none !important; }

    .price-line:last-child span:last-child { color: #059669 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }

    @page {
      margin: 15mm 12mm;
      size: A4 portrait;
    }
  }

  /* Hide receipt header in screen mode (shown only on print) */
  .print-receipt-header { display: none; }
  @media print {
    .print-receipt-header {
      display: flex !important;
      justify-content: space-between;
      align-items: center;
      padding: 16px 24px;
      border-bottom: 2px solid #0f172a;
      margin-bottom: 16px;
    }
  }
</style>
<style>
  .confirm-wrap {
    max-width: 760px;
    margin: 0 auto;
    padding: 48px 24px 80px;
  }

  /* ── Success hero ── */
  .success-hero {
    text-align: center;
    margin-bottom: 40px;
  }
  .success-circle {
    width: 88px; height: 88px;
    background: linear-gradient(135deg, #10b981, #059669);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 20px;
    box-shadow: 0 8px 32px rgba(16,185,129,.35);
    animation: pop .4s cubic-bezier(.175,.885,.32,1.275);
  }
  @keyframes pop { from { transform:scale(0); opacity:0; } to { transform:scale(1); opacity:1; } }

  .ref-badge {
    display: inline-flex; align-items: center; gap: 8px;
    background: #f0fdf4; border: 1.5px solid #86efac;
    border-radius: 99px; padding: 6px 18px;
    font-size: 15px; font-weight: 700; color: #059669;
    letter-spacing: .04em; margin-top: 14px;
  }

  /* ── Cards ── */
  .confirm-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 20px;
  }
  .confirm-card-header {
    padding: 16px 24px;
    border-bottom: 1px solid #f1f5f9;
    display: flex; align-items: center; justify-content: space-between;
    font-size: 13px; font-weight: 700; color: #0f172a;
    text-transform: uppercase; letter-spacing: .05em;
  }
  .confirm-card-body { padding: 24px; }

  /* ── Vehicle banner ── */
  .vehicle-banner {
    display: flex; align-items: center; gap: 20px;
    padding: 20px 24px;
    background: linear-gradient(135deg, #0f172a, #1e293b);
  }
  .vehicle-thumb {
    width: 100px; height: 70px; border-radius: 10px; overflow: hidden;
    flex-shrink: 0; background: #1e293b;
    display: flex; align-items: center; justify-content: center;
  }
  .vehicle-thumb img { width:100%; height:100%; object-fit:cover; }

  /* ── Detail rows ── */
  .detail-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0;
  }
  @media(max-width:540px) { .detail-grid { grid-template-columns: 1fr; } }

  .detail-item {
    padding: 14px 0;
    border-bottom: 1px solid #f8fafc;
    display: flex; flex-direction: column; gap: 3px;
  }
  .detail-item:nth-child(odd)  { padding-right: 24px; }
  .detail-item:nth-child(even) { padding-left: 24px; border-left: 1px solid #f8fafc; }
  .detail-label { font-size: 11px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; }
  .detail-value { font-size: 14px; font-weight: 600; color: #0f172a; }

  /* ── Price breakdown ── */
  .price-line {
    display: flex; justify-content: space-between; align-items: center;
    padding: 10px 0; font-size: 14px; color: #475569;
    border-bottom: 1px solid #f8fafc;
  }
  .price-line:last-child {
    border-bottom: none; border-top: 2px solid #e2e8f0;
    margin-top: 4px; padding-top: 14px;
    font-size: 17px; font-weight: 800; color: #0f172a;
  }
  .price-line:last-child span:last-child { color: #059669; }

  /* ── Status pill ── */
  .status-pill {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 4px 12px; border-radius: 99px; font-size: 12px; font-weight: 600;
  }
  .status-attente  { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
  .status-confirmee { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; }
  .status-dot { width: 7px; height: 7px; border-radius: 50%; background: currentColor; animation: blink 1.4s infinite; }
  @keyframes blink { 0%,100%{opacity:1} 50%{opacity:.3} }

  /* ── Next steps ── */
  .step { display: flex; gap: 16px; padding: 16px 0; border-bottom: 1px solid #f8fafc; }
  .step:last-child { border-bottom: none; }
  .step-num {
    width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0;
    background: #f0fdf4; border: 2px solid #86efac;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 800; color: #059669;
  }
  .step-title { font-size: 14px; font-weight: 600; color: #0f172a; margin-bottom: 2px; }
  .step-desc  { font-size: 13px; color: #64748b; }
</style>

<div class="confirm-wrap">

  <!-- Print-only receipt header -->
  <div class="print-receipt-header">
    <div style="display:flex;align-items:center;gap:10px;">
      <div style="width:36px;height:36px;background:#0f172a;border-radius:7px;display:flex;align-items:center;justify-content:center;">
        <svg width="18" height="18" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 17H5l-2-4V7a1 1 0 011-1h14a1 1 0 011 1v6l-2 4z"/><circle cx="7.5" cy="17.5" r="1.5"/><circle cx="16.5" cy="17.5" r="1.5"/></svg>
      </div>
      <div>
        <div style="font-weight:800;font-size:16px;color:#0f172a;">AutoLocation</div>
        <div style="font-size:11px;color:#64748b;">Reçu de réservation</div>
      </div>
    </div>
    <div style="text-align:right;">
      <div style="font-size:13px;font-weight:700;color:#0f172a;">Réf. <?= h($reservation['reference']) ?></div>
      <div style="font-size:11px;color:#64748b;">Imprimé le <?= date('d/m/Y à H:i') ?></div>
    </div>
  </div>

  <!-- Success hero -->
  <div class="success-hero">
    <div class="success-circle">
      <svg width="40" height="40" fill="none" stroke="#fff" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
    </div>
    <h1 style="font-size:28px;font-weight:800;color:#0f172a;margin:0 0 8px;">Réservation enregistrée !</h1>
    <p style="font-size:16px;color:#64748b;margin:0;">Votre demande a bien été reçue. L'agence vous contactera pour confirmer.</p>
    <div class="ref-badge">
      <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="9" y="2" width="6" height="4" rx="1"/><path d="M16 4h2a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h2"/></svg>
      Réf. <?= h($reservation['reference']) ?>
    </div>
  </div>

  <!-- Vehicle banner -->
  <div class="confirm-card">
    <div class="vehicle-banner">
      <div class="vehicle-thumb">
        <?php if (!empty($reservation['image_url'])): ?>
        <img src="<?= BASE_URL ?>/uploads/vehicles/<?= h($reservation['image_url']) ?>" alt="<?= h($reservation['marque']) ?>">
        <?php else: ?>
        <svg width="40" height="40" fill="none" stroke="rgba(255,255,255,0.3)" stroke-width="1.2" viewBox="0 0 24 24"><path d="M19 17H5l-2-4V7a1 1 0 011-1h14a1 1 0 011 1v6l-2 4z"/><circle cx="7.5" cy="17.5" r="1.5"/><circle cx="16.5" cy="17.5" r="1.5"/></svg>
        <?php endif; ?>
      </div>
      <div style="flex:1;">
        <div style="font-size:11px;color:#10b981;font-weight:600;text-transform:uppercase;letter-spacing:.07em;margin-bottom:4px;"><?= h(ucfirst($reservation['vehicle_categorie']??'')) ?></div>
        <div style="font-size:20px;font-weight:800;color:#f1f5f9;line-height:1.2;"><?= h($reservation['marque'].' '.$reservation['modele']) ?></div>
        <div style="font-size:13px;color:#64748b;margin-top:3px;"><?= h($reservation['vehicle_annee']??'') ?> · <?= h(ucfirst($reservation['vehicle_carburant']??'')) ?></div>
      </div>
      <div style="text-align:right;">
        <?php
          $sc = $reservation['statut'] === 'confirmée' ? 'status-confirmee' : 'status-attente';
        ?>
        <div class="status-pill <?= $sc ?>">
          <span class="status-dot"></span>
          <?= ucfirst(h($reservation['statut'])) ?>
        </div>
      </div>
    </div>

    <!-- Booking details -->
    <div class="confirm-card-body">
      <div class="detail-grid">
        <div class="detail-item">
          <span class="detail-label">Date de départ</span>
          <span class="detail-value"><?= date('d/m/Y', strtotime($reservation['date_debut'])) ?></span>
          <span style="font-size:12px;color:#94a3b8;"><?= date('H:i', strtotime($reservation['date_debut'])) ?></span>
        </div>
        <div class="detail-item">
          <span class="detail-label">Date de retour</span>
          <span class="detail-value"><?= date('d/m/Y', strtotime($reservation['date_fin_prevue'])) ?></span>
          <span style="font-size:12px;color:#94a3b8;"><?= date('H:i', strtotime($reservation['date_fin_prevue'])) ?></span>
        </div>
        <?php if (!empty($reservation['lieu_depart'])): ?>
        <div class="detail-item">
          <span class="detail-label">Lieu de départ</span>
          <span class="detail-value"><?= h($reservation['lieu_depart']) ?></span>
        </div>
        <?php endif; ?>
        <?php if (!empty($reservation['lieu_retour'])): ?>
        <div class="detail-item">
          <span class="detail-label">Lieu de retour</span>
          <span class="detail-value"><?= h($reservation['lieu_retour']) ?></span>
        </div>
        <?php endif; ?>
        <div class="detail-item">
          <span class="detail-label">Durée</span>
          <span class="detail-value"><?= (int)($reservation['nb_jours']??1) ?> jour(s)</span>
        </div>
        <div class="detail-item">
          <span class="detail-label">Immatriculation</span>
          <span class="detail-value"><?= h($reservation['immatriculation']??$reservation['vehicle_numero']??'—') ?></span>
        </div>
      </div>
    </div>
  </div>

  <!-- Two columns: client + price -->
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">

    <!-- Client info -->
    <div class="confirm-card">
      <div class="confirm-card-header">
        <span>Client</span>
        <svg width="15" height="15" fill="none" stroke="#10b981" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
      </div>
      <div class="confirm-card-body" style="padding:20px 24px;">
        <div style="font-size:17px;font-weight:700;color:#0f172a;margin-bottom:14px;"><?= h($reservation['client_prenom'].' '.$reservation['client_nom']) ?></div>
        <?php foreach ([
          ['CIN', $reservation['client_cin']??'—'],
          ['Tél.', $reservation['client_tel']??'—'],
          ['Email', $reservation['client_email']??'—'],
        ] as [$l,$v]): ?>
        <div style="display:flex;gap:8px;align-items:center;font-size:13px;margin-bottom:8px;color:#475569;">
          <span style="color:#94a3b8;min-width:44px;"><?= $l ?></span>
          <span style="font-weight:500;color:#0f172a;"><?= h((string)$v) ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Price breakdown -->
    <div class="confirm-card">
      <div class="confirm-card-header">
        <span>Récapitulatif</span>
        <svg width="15" height="15" fill="none" stroke="#10b981" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
      </div>
      <div class="confirm-card-body" style="padding:20px 24px;">
        <div class="price-line">
          <span><?= (int)($reservation['nb_jours']??1) ?> jour(s) × <?= number_format($reservation['prix_jour']??0,0,',',' ') ?> MAD</span>
          <span><?= number_format($reservation['montant_total']??0,0,',',' ') ?> MAD</span>
        </div>
        <?php if (($reservation['caution']??0) > 0): ?>
        <div class="price-line">
          <span>Caution <span style="font-size:11px;color:#94a3b8;">(remboursable)</span></span>
          <span><?= number_format($reservation['caution'],0,',',' ') ?> MAD</span>
        </div>
        <?php endif; ?>
        <?php if (($reservation['frais_extra']??0) > 0): ?>
        <div class="price-line">
          <span>Frais supplémentaires</span>
          <span><?= number_format($reservation['frais_extra'],0,',',' ') ?> MAD</span>
        </div>
        <?php endif; ?>
        <div class="price-line">
          <span>Total</span>
          <span><?= number_format(($reservation['montant_total']??0)+($reservation['caution']??0),0,',',' ') ?> MAD</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Next steps -->
  <div class="confirm-card no-print">
    <div class="confirm-card-header">
      <span>Prochaines étapes</span>
    </div>
    <div class="confirm-card-body">
      <div class="step">
        <div class="step-num">1</div>
        <div>
          <div class="step-title">Confirmation par l'agence</div>
          <div class="step-desc">Un conseiller va examiner votre demande et vous contactera par téléphone ou email sous 24h.</div>
        </div>
      </div>
      <div class="step">
        <div class="step-num">2</div>
        <div>
          <div class="step-title">Préparation du véhicule</div>
          <div class="step-desc">Le véhicule sera nettoyé, contrôlé et prêt à la date et heure convenues.</div>
        </div>
      </div>
      <div class="step">
        <div class="step-num">3</div>
        <div>
          <div class="step-title">Remise des clés</div>
          <div class="step-desc">Présentez-vous avec votre CIN et permis de conduire au lieu de départ indiqué.</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Alert + CTA -->
  <div class="no-print" style="background:#fffbeb;border:1px solid #fde68a;border-radius:12px;padding:16px 20px;margin-bottom:28px;display:flex;gap:12px;align-items:flex-start;">
    <svg width="20" height="20" fill="none" stroke="#f59e0b" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><circle cx="12" cy="16" r=".5" fill="#f59e0b"/></svg>
    <div style="font-size:13px;color:#92400e;">
      <strong>Aucun paiement requis maintenant.</strong> Le règlement s'effectue directement à l'agence lors de la prise en charge du véhicule.
    </div>
  </div>

  <div class="no-print" style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
    <a href="<?= BASE_URL ?>/?page=client" class="btn btn-emerald" style="padding:12px 28px;font-size:15px;">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
      Retour aux véhicules
    </a>
    <button onclick="window.print()" class="btn btn-outline" style="padding:12px 28px;font-size:15px;">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
      Imprimer le reçu
    </button>
  </div>

</div>
