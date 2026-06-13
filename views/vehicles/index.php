<style>
  .fleet-card { transition: box-shadow .2s ease; }
  .fleet-card:hover { box-shadow: 0 10px 25px rgba(15,23,42,.12); }
  .fleet-card:hover .card-img { transform: scale(1.04); }
  .card-img { transition: transform .5s ease; }
</style>

<?php
// Helper: status badge config
function vehicleStatusBadge(string $statut): array {
    return match($statut) {
        'disponible'   => ['bg' => 'bg-emerald-600',  'text' => 'text-white',     'dot' => 'bg-emerald-300', 'label' => 'Disponible',   'pulse' => true],
        'loué'         => ['bg' => 'bg-blue-600',     'text' => 'text-white',     'dot' => 'bg-blue-300',    'label' => 'Loué',         'pulse' => false],
        'maintenance'  => ['bg' => 'bg-amber-500',    'text' => 'text-white',     'dot' => '',               'label' => 'Maintenance',  'pulse' => false],
        'indisponible' => ['bg' => 'bg-red-600',      'text' => 'text-white',     'dot' => '',               'label' => 'Indisponible', 'pulse' => false],
        default        => ['bg' => 'bg-slate-500',    'text' => 'text-white',     'dot' => '',               'label' => ucfirst($statut), 'pulse' => false],
    };
}

// Helper: fuel icon
function fuelIcon(string $carburant): string {
    return match(strtolower($carburant)) {
        'électrique', 'electrique' => 'bolt',
        'hybride'                  => 'ev_station',
        default                    => 'local_gas_station',
    };
}

// Helper: transmission icon
function transIcon(string $t): string {
    return 'settings_input_component';
}

// Helper: gradient per brand
function brandGradient(string $marque): string {
    $h = crc32(strtolower($marque));
    $colors = [
        ['from-slate-700','to-slate-900'],
        ['from-blue-700','to-blue-900'],
        ['from-emerald-700','to-emerald-900'],
        ['from-violet-700','to-violet-900'],
        ['from-rose-700','to-rose-900'],
        ['from-amber-700','to-amber-900'],
        ['from-cyan-700','to-cyan-900'],
    ];
    $idx = abs($h) % count($colors);
    return 'bg-gradient-to-br ' . $colors[$idx][0] . ' ' . $colors[$idx][1];
}
?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
  <div>
    <h1 class="text-2xl font-bold text-slate-900">Flotte active</h1>
    <p class="text-sm text-slate-500 mt-1">
      Statut en temps réel · <span class="font-semibold text-slate-700"><?= $total ?></span> véhicule<?= $total > 1 ? 's' : '' ?> dans le catalogue
    </p>
  </div>
  <a href="<?= BASE_URL ?>/?page=vehicles/add"
     class="inline-flex items-center gap-2 bg-slate-900 text-white px-5 py-2.5 rounded-lg text-sm font-semibold hover:bg-slate-700 active:scale-95 transition-all">
    <span class="material-symbols-outlined text-[18px]">add_circle</span>
    Ajouter un véhicule
  </a>
</div>

<!-- Filter Bar -->
<div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 mb-8">
  <form method="GET" action="<?= BASE_URL ?>/" class="flex flex-wrap items-center gap-3">
    <input type="hidden" name="page" value="vehicles">

    <!-- Search -->
    <div class="relative flex-1 min-w-[200px]">
      <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
      <input type="text" name="search" value="<?= h($search) ?>"
             placeholder="Recherche véhicules, immat…"
             class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none">
    </div>

    <!-- Brand -->
    <select name="marque" onchange="this.form.submit()"
            class="bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:ring-1 focus:ring-emerald-500 outline-none">
      <option value="">Marque : Toutes</option>
      <?php foreach ($brands as $b): ?>
      <option value="<?= h($b) ?>" <?= $marque===$b ? 'selected' : '' ?>><?= h($b) ?></option>
      <?php endforeach; ?>
    </select>

    <!-- Category -->
    <select name="categorie" onchange="this.form.submit()"
            class="bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:ring-1 focus:ring-emerald-500 outline-none">
      <option value="">Catégorie : Toutes</option>
      <?php foreach(['économique','berline','SUV','premium','utilitaire'] as $c): ?>
      <option value="<?= $c ?>" <?= $categorie===$c ? 'selected' : '' ?>><?= ucfirst($c) ?></option>
      <?php endforeach; ?>
    </select>

    <!-- Status -->
    <select name="statut" onchange="this.form.submit()"
            class="bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:ring-1 focus:ring-emerald-500 outline-none">
      <option value="">Statut : Tous</option>
      <?php foreach(['disponible','loué','maintenance','indisponible'] as $s): ?>
      <option value="<?= $s ?>" <?= $statut===$s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
      <?php endforeach; ?>
    </select>

    <button type="submit" class="px-4 py-2.5 bg-slate-900 text-white rounded-lg text-sm font-semibold hover:bg-slate-700 transition-colors">
      Filtrer
    </button>
    <?php if ($search || $statut || $categorie || $marque): ?>
    <a href="<?= BASE_URL ?>/?page=vehicles"
       class="flex items-center gap-1.5 px-4 py-2.5 text-slate-500 hover:text-slate-900 text-sm font-medium transition-colors">
      <span class="material-symbols-outlined text-[18px]">refresh</span>
      Réinitialiser
    </a>
    <?php endif; ?>
  </form>
</div>

<!-- Vehicle Grid -->
<?php if (empty($rows)): ?>
<div class="bg-white rounded-xl border border-slate-200 p-16 text-center">
  <span class="material-symbols-outlined text-slate-300 text-[64px]">directions_car</span>
  <p class="text-slate-500 mt-4 text-sm">Aucun véhicule trouvé</p>
</div>
<?php else: ?>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
  <?php foreach ($rows as $v):
    $badge = vehicleStatusBadge($v['statut']);
    $isUnavailable = in_array($v['statut'], ['maintenance', 'indisponible', 'loué']);
    $gradient = brandGradient($v['marque']);
    $carburant = $v['carburant'] ?? 'essence';
    $transmission = $v['transmission'] ?? 'manuelle';
  ?>
  <div class="fleet-card bg-white rounded-xl overflow-hidden border border-slate-200 flex flex-col <?= $isUnavailable ? 'opacity-85' : '' ?>">

    <!-- Image / Placeholder -->
    <div class="relative h-[200px] overflow-hidden <?= !empty($v['image_url']) ? '' : $gradient ?>">
      <?php if (!empty($v['image_url'])): ?>
      <img src="<?= h(vehicle_img_url($v['image_url'])) ?>"
           alt="<?= h($v['marque'].' '.$v['modele']) ?>"
           class="card-img absolute inset-0 w-full h-full object-cover">
      <?php else: ?>
      <!-- Vehicle icon placeholder -->
      <div class="absolute inset-0 flex flex-col items-center justify-center gap-2 card-img">
        <svg width="72" height="72" fill="none" viewBox="0 0 24 24" stroke="rgba(255,255,255,0.35)" stroke-width="1.2">
          <path d="M19 17H5l-2-4V7a1 1 0 011-1h14a1 1 0 011 1v6l-2 4z"/>
          <circle cx="7.5" cy="17.5" r="1.5"/><circle cx="16.5" cy="17.5" r="1.5"/>
        </svg>
        <span class="text-white/50 text-xs font-semibold tracking-widest uppercase"><?= h($v['marque']) ?></span>
      </div>
      <?php endif; ?>
      <!-- Numero badge top-left -->
      <span class="absolute top-3 left-3 bg-black/30 backdrop-blur-sm text-white px-2 py-0.5 rounded text-xs font-mono font-semibold">
        <?= h($v['numero']) ?>
      </span>
      <!-- Status badge top-right -->
      <span class="absolute top-3 right-3 <?= $badge['bg'] ?> <?= $badge['text'] ?> px-2.5 py-1 rounded-md text-xs font-semibold flex items-center gap-1.5 shadow">
        <?php if ($badge['dot']): ?>
        <span class="w-1.5 h-1.5 rounded-full <?= $badge['dot'] ?> <?= $badge['pulse'] ? 'animate-pulse' : '' ?>"></span>
        <?php endif; ?>
        <?= $badge['label'] ?>
      </span>
    </div>

    <!-- Card body -->
    <div class="p-5 flex-1 flex flex-col">
      <div class="flex justify-between items-start mb-1">
        <h3 class="font-semibold text-slate-900 text-base leading-tight"><?= h($v['marque'] . ' ' . $v['modele']) ?></h3>
        <span class="text-xs text-slate-400 font-medium ml-2 shrink-0"><?= h($v['annee'] ?? '') ?></span>
      </div>
      <p class="text-xs text-slate-400 font-mono mb-4"><?= h($v['immatriculation'] ?? '—') ?></p>

      <!-- Feature pills -->
      <div class="flex flex-wrap gap-1.5 mb-6">
        <span class="bg-slate-100 text-slate-600 px-2.5 py-1 rounded-full text-[11px] font-semibold flex items-center gap-1">
          <span class="material-symbols-outlined text-[13px]">person</span>
          <?= (int)$v['nb_places'] ?> places
        </span>
        <span class="bg-slate-100 text-slate-600 px-2.5 py-1 rounded-full text-[11px] font-semibold flex items-center gap-1">
          <span class="material-symbols-outlined text-[13px]"><?= transIcon($transmission) ?></span>
          <?= ucfirst(h($transmission)) ?>
        </span>
        <span class="bg-slate-100 text-slate-600 px-2.5 py-1 rounded-full text-[11px] font-semibold flex items-center gap-1">
          <span class="material-symbols-outlined text-[13px]"><?= fuelIcon($carburant) ?></span>
          <?= ucfirst(h($carburant)) ?>
        </span>
        <?php if ($v['categorie']): ?>
        <span class="bg-slate-100 text-slate-600 px-2.5 py-1 rounded-full text-[11px] font-semibold">
          <?= ucfirst(h($v['categorie'])) ?>
        </span>
        <?php endif; ?>
      </div>

      <!-- Footer: price + actions -->
      <div class="mt-auto pt-4 border-t border-slate-100 flex items-center justify-between">
        <div>
          <span class="text-xl font-bold text-slate-900"><?= number_format($v['prix_jour'], 0, ',', ' ') ?></span>
          <span class="text-xs text-slate-400 uppercase ml-1 font-semibold">MAD/j</span>
        </div>
        <div class="flex gap-2">
          <a href="<?= BASE_URL ?>/?page=vehicles/show&id=<?= $v['id'] ?>"
             class="p-2 border border-slate-200 rounded-lg text-slate-500 hover:bg-slate-50 transition-colors"
             title="Voir détails">
            <span class="material-symbols-outlined text-[18px]">visibility</span>
          </a>
          <a href="<?= BASE_URL ?>/?page=vehicles/edit&id=<?= $v['id'] ?>"
             class="p-2 border border-slate-200 rounded-lg text-slate-500 hover:bg-slate-50 transition-colors"
             title="Modifier">
            <span class="material-symbols-outlined text-[18px]">edit</span>
          </a>
          <a href="<?= BASE_URL ?>/?page=vehicles/delete&id=<?= $v['id'] ?>"
             class="p-2 border border-red-100 rounded-lg text-red-400 hover:bg-red-50 transition-colors"
             title="Supprimer"
             onclick="return confirm('Supprimer ce véhicule ?')">
            <span class="material-symbols-outlined text-[18px]">delete</span>
          </a>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Pagination -->
<?php if ($pages > 1): ?>
<div class="mt-10 flex items-center justify-between border-t border-slate-200 pt-6">
  <p class="text-sm text-slate-500">
    Affichage <span class="font-semibold text-slate-700"><?= ($offset + 1) ?>–<?= min($offset + $per_page, $total) ?></span>
    sur <span class="font-semibold text-slate-700"><?= $total ?></span> véhicules
  </p>
  <div class="flex gap-1.5">
    <?php
    $base = BASE_URL . '/?page=vehicles&search=' . urlencode($search) . '&statut=' . urlencode($statut) . '&categorie=' . urlencode($categorie) . '&marque=' . urlencode($marque);
    ?>
    <a href="<?= $base ?>&p=<?= max(1, $page-1) ?>"
       class="w-9 h-9 flex items-center justify-center border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors <?= $page===1 ? 'pointer-events-none opacity-40' : '' ?>">
      <span class="material-symbols-outlined text-[18px]">chevron_left</span>
    </a>
    <?php for ($i = max(1, $page-2); $i <= min($pages, $page+2); $i++): ?>
    <a href="<?= $base ?>&p=<?= $i ?>"
       class="w-9 h-9 flex items-center justify-center rounded-lg text-sm font-bold transition-colors <?= $i === $page ? 'bg-slate-900 text-white' : 'border border-slate-200 hover:bg-slate-50 text-slate-700' ?>">
      <?= $i ?>
    </a>
    <?php endfor; ?>
    <a href="<?= $base ?>&p=<?= min($pages, $page+1) ?>"
       class="w-9 h-9 flex items-center justify-center border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors <?= $page===$pages ? 'pointer-events-none opacity-40' : '' ?>">
      <span class="material-symbols-outlined text-[18px]">chevron_right</span>
    </a>
  </div>
</div>
<?php endif; ?>
