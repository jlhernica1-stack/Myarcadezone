<?php
require __DIR__ . '/_auth.php';
require_once dirname(__DIR__) . '/includes/db.php';

$db     = db();
$tracks = $db->query("SELECT * FROM retrocassete_tracks ORDER BY orden ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Retrocassete — Admin MY ARCADE ZONE</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Share+Tech+Mono&family=Rajdhani:wght@400;600;700&display=swap">
<link rel="stylesheet" href="/assets/css/style.css">
<style>
body::before { display:none; }
.admin-topbar {
  background:var(--negro-panel); border-bottom:2px solid var(--cyan);
  padding:12px 24px; display:flex; align-items:center; justify-content:space-between;
}
.admin-topbar-logo {
  font-family:'Bebas Neue',sans-serif; font-size:20px; letter-spacing:5px;
  background:linear-gradient(180deg,#fff,var(--cyan));
  -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;
}
</style>
</head>
<body>
<div class="admin-topbar">
  <div class="admin-topbar-logo">RETROCASSETE</div>
  <div style="font-family:'Share Tech Mono',monospace;font-size:10px;color:#444;display:flex;gap:16px">
    <a href="/admin/retrocassete-form.php" style="color:var(--cyan)">+ NUEVA PISTA</a>
    <a href="/retrocassete.php" target="_blank" style="color:#555">VER PÁGINA</a>
    <a href="/admin/" style="color:#555">◄ ADMIN</a>
  </div>
</div>

<div class="admin-wrap">
  <div class="admin-card">
    <div class="admin-card-header">
      🎵 PISTAS DE AUDIO
      <a href="/admin/retrocassete-form.php" class="admin-btn admin-btn-primary admin-btn-sm">+ NUEVA PISTA</a>
    </div>
    <div class="admin-card-body" style="padding:0">
      <table class="admin-table">
        <thead>
          <tr>
            <th>#</th>
            <th>TÍTULO</th>
            <th>JUEGO</th>
            <th>COMPOSITOR</th>
            <th>ESTADO</th>
            <th>ACCIONES</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($tracks): ?>
            <?php foreach ($tracks as $t): ?>
            <tr>
              <td style="color:#444"><?= $t['orden'] ?></td>
              <td style="color:var(--blanco)"><?= htmlspecialchars($t['titulo']) ?></td>
              <td><?= htmlspecialchars($t['juego']) ?></td>
              <td style="color:#555"><?= htmlspecialchars($t['compositor'] ?? '—') ?></td>
              <td>
                <span class="<?= $t['publicado'] ? 'badge-publicada' : 'badge-borrador' ?>">
                  <?= $t['publicado'] ? '● ACTIVA' : '○ OCULTA' ?>
                </span>
              </td>
              <td>
                <a href="/admin/retrocassete-form.php?id=<?= $t['id'] ?>"
                   class="admin-btn admin-btn-primary admin-btn-sm">EDITAR</a>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" style="text-align:center;color:#333;padding:30px">
                No hay pistas todavía.
                <a href="/admin/retrocassete-form.php" style="color:var(--cyan)">Añadir la primera</a>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
