<?php $pageTitle = 'Modifier '.h($data['marque'].' '.$data['modele']); $pageBreadcrumb = 'Véhicules › Modifier'; ?>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger"><?= implode('<br>', array_map('h', $errors)) ?></div>
<?php endif; ?>

<div class="card">
  <div class="card-header">
    <span class="card-title">Modifier le véhicule</span>
    <a href="<?= BASE_URL ?>/?page=vehicles/show&id=<?= $id ?>" class="btn btn-outline btn-sm">← Retour</a>
  </div>
  <div class="card-body">
    <form method="POST" enctype="multipart/form-data">
      <div class="form-grid-3" style="margin-bottom:16px;">
        <div><label class="form-label">Numéro interne *</label><input type="text" name="numero" class="form-control" value="<?= h($data['numero']) ?>" required></div>
        <div><label class="form-label">Immatriculation</label><input type="text" name="immatriculation" class="form-control" value="<?= h($data['immatriculation'] ?? '') ?>"></div>
        <div><label class="form-label">Statut</label>
          <select name="statut" class="form-control">
            <?php foreach(['disponible','loué','maintenance','indisponible'] as $s): ?>
            <option value="<?= $s ?>" <?= $data['statut']===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-grid-3" style="margin-bottom:16px;">
        <div><label class="form-label">Marque *</label><input type="text" name="marque" class="form-control" value="<?= h($data['marque']) ?>" required></div>
        <div><label class="form-label">Modèle</label><input type="text" name="modele" class="form-control" value="<?= h($data['modele'] ?? '') ?>"></div>
        <div><label class="form-label">Année</label><input type="number" name="annee" class="form-control" value="<?= h($data['annee'] ?? '') ?>"></div>
      </div>
      <div class="form-grid-3" style="margin-bottom:16px;">
        <div><label class="form-label">Couleur</label><input type="text" name="couleur" class="form-control" value="<?= h($data['couleur'] ?? '') ?>"></div>
        <div><label class="form-label">Nb. places</label><input type="number" name="nb_places" class="form-control" value="<?= h($data['nb_places']) ?>" min="1" max="20"></div>
        <div><label class="form-label">Catégorie</label>
          <select name="categorie" class="form-control">
            <?php foreach(['économique','berline','SUV','premium','utilitaire'] as $c): ?>
            <option value="<?= $c ?>" <?= $data['categorie']===$c?'selected':'' ?>><?= ucfirst($c) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-grid-3" style="margin-bottom:16px;">
        <div><label class="form-label">Carburant</label>
          <select name="carburant" class="form-control">
            <?php foreach(['essence','diesel','hybride','électrique'] as $c): ?>
            <option value="<?= $c ?>" <?= ($data['carburant']??'')===$c?'selected':'' ?>><?= ucfirst($c) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div><label class="form-label">Transmission</label>
          <select name="transmission" class="form-control">
            <option value="manuelle" <?= ($data['transmission']??'')==='manuelle'?'selected':'' ?>>Manuelle</option>
            <option value="automatique" <?= ($data['transmission']??'')==='automatique'?'selected':'' ?>>Automatique</option>
          </select>
        </div>
        <div><label class="form-label">Kilométrage</label><input type="number" name="kilometrage" class="form-control" value="<?= h($data['kilometrage']) ?>" min="0"></div>
      </div>
      <div class="form-grid-2" style="margin-bottom:16px;">
        <div><label class="form-label">Prix / jour (MAD) *</label><input type="number" name="prix_jour" class="form-control" value="<?= h($data['prix_jour']) ?>" step="0.01" required></div>
        <div><label class="form-label">Caution (MAD)</label><input type="number" name="caution" class="form-control" value="<?= h($data['caution']) ?>" step="0.01"></div>
      </div>
      <div style="margin-bottom:16px;">
        <label class="form-label">Photos du véhicule</label>

        <?php if (!empty($vehicleImages)): ?>
        <div style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:12px;">
          <?php foreach ($vehicleImages as $img): ?>
          <div style="position:relative;display:inline-block;">
            <img src="<?= h(vehicle_img_url($img['image_url'])) ?>" alt=""
                 style="height:100px;width:140px;object-fit:cover;border-radius:8px;border:1px solid #e2e8f0;">
            <a href="<?= BASE_URL ?>/?page=vehicles/deleteImage&image_id=<?= $img['id'] ?>&vehicle_id=<?= $id ?>"
               onclick="return confirm('Supprimer cette photo ?')"
               style="position:absolute;top:4px;right:4px;background:rgba(220,38,38,0.85);color:#fff;border-radius:50%;width:22px;height:22px;display:flex;align-items:center;justify-content:center;font-size:13px;text-decoration:none;line-height:1;">&times;</a>
          </div>
          <?php endforeach; ?>
        </div>
        <?php elseif (!empty($data['image_url'])): ?>
        <div style="margin-bottom:10px;">
          <img src="<?= h(vehicle_img_url($data['image_url'])) ?>" alt="Photo actuelle"
               style="height:100px;width:140px;object-fit:cover;border-radius:8px;border:1px solid #e2e8f0;">
        </div>
        <?php endif; ?>

        <input type="file" name="photos[]" class="form-control" accept="image/jpeg,image/png,image/webp" multiple>
        <div style="font-size:12px;color:#94a3b8;margin-top:4px;">Sélectionnez plusieurs photos à la fois (intérieur, extérieur…) · max 5 Mo chacune</div>
      </div>
      <div style="margin-bottom:24px;"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3"><?= h($data['description'] ?? '') ?></textarea></div>
      <div style="display:flex;gap:10px;">
        <button type="submit" class="btn btn-emerald">Enregistrer</button>
        <a href="<?= BASE_URL ?>/?page=vehicles/show&id=<?= $id ?>" class="btn btn-outline">Annuler</a>
      </div>
    </form>
  </div>
</div>
<script>
function previewPhoto(input) {
  const preview = document.getElementById('photo-preview');
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
    reader.readAsDataURL(input.files[0]);
  }
}
</script>
