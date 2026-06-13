<?php $pageTitle = 'Utilisateurs'; $pageBreadcrumb = 'Gestion des utilisateurs'; ?>

<div style="display:flex;justify-content:flex-end;margin-bottom:20px;">
  <a href="<?= BASE_URL ?>/?page=users/add" class="btn btn-emerald">
    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    Nouvel utilisateur
  </a>
</div>

<div class="card">
  <div class="card-header"><span class="card-title">Utilisateurs (<?= count($users) ?>)</span></div>
  <div style="overflow-x:auto;">
    <table class="data-table">
      <thead>
        <tr><th>Utilisateur</th><th>Identifiant</th><th>Rôle</th><th>Créé le</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php foreach ($users as $u): ?>
        <tr>
          <td>
            <div style="display:flex;align-items:center;gap:10px;">
              <div class="avatar" style="width:36px;height:36px;font-size:13px;background:#ede9fe;color:#7c3aed;"><?= strtoupper(substr($u['prenom']??'',0,1).substr($u['nom'],0,1)) ?></div>
              <div>
                <div style="font-weight:600;"><?= h(($u['prenom']??'').' '.$u['nom']) ?></div>
              </div>
            </div>
          </td>
          <td style="font-family:monospace;color:#64748b;"><?= h($u['username']) ?></td>
          <td>
            <?php $roleColor = $u['role']==='admin' ? '#ef4444' : '#3b82f6'; ?>
            <span style="font-size:12px;font-weight:600;color:<?= $roleColor ?>;background:<?= $roleColor ?>15;padding:3px 8px;border-radius:99px;"><?= ucfirst($u['role']) ?></span>
          </td>
          <td style="color:#94a3b8;font-size:13px;"><?= $u['created_at'] ? date('d/m/Y', strtotime($u['created_at'])) : '—' ?></td>
          <td>
            <div style="display:flex;gap:5px;">
              <a href="<?= BASE_URL ?>/?page=users/edit&id=<?= $u['id'] ?>" class="btn btn-outline btn-sm">Éditer</a>
              <?php if ($u['id'] != ($_SESSION['user_id']??0)): ?>
              <a href="<?= BASE_URL ?>/?page=users/delete&id=<?= $u['id'] ?>" class="btn btn-outline btn-sm" onclick="return confirm('Supprimer cet utilisateur ?')" style="color:#ef4444;">Suppr.</a>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($users)): ?>
        <tr><td colspan="5" style="text-align:center;color:#94a3b8;padding:40px;">Aucun utilisateur</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
