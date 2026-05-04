<?php
require __DIR__ . '/_auth.php';
require_once dirname(__DIR__) . '/includes/db.php';

$db   = db();
$items = $db->query("
    SELECT h.id, h.slug, h.nombre, h.fabricante, h.anno, h.categoria, h.publicado,
           (SELECT COUNT(*) FROM hardware_specs hs WHERE hs.hardware_id = h.id) as n_specs,
           (SELECT COUNT(*) FROM hardware_galeria hg WHERE hg.hardware_id = h.id) as n_fotos
    FROM hardware h
    ORDER BY h.anno ASC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Hardware — Admin MY ARCADE ZONE</title>
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
  <div class="admin-topbar-logo">HARDWARE</div>
  <div style="font-family:'Share Tech Mono',monospace;font-size:10px;color:#444;display:flex;gap:16px">
    <a href="/admin/hardware-form.php" style="color:var(--cyan)">+ NUEVO</a>
    <a href="/admin/" style="color:#555">◄ ADMIN</a>
  </div>
</div>

<div class="admin-wrap">

  <div class="admin-card">
    <div class="admin-card-header">
      🕹️ FICHAS DE HARDWARE
      <a href="/admin/hardware-form.php" class="admin-btn admin-btn-primary admin-btn-sm">+ NUEVO</a>
    </div>
    <div class="admin-card-body" style="padding:0">
      <table class="admin-table">
        <thead>
          <tr>
            <th>NOMBRE</th>
            <th>FABRICANTE</th>
            <th>AÑO</th>
            <th>CATEGORÍA</th>
            <th>SPECS</th>
            <th>FOTOS</th>
            <th>ESTADO</th>
            <th>ACCIONES</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($items): ?>
            <?php foreach ($items as $h): ?>
            <tr>
              <td style="color:var(--blanco)"><?= htmlspecialchars($h['nombre']) ?></td>
              <td><?= htmlspecialchars($h['fabricante'] ?? '—') ?></td>
              <td><?= $h['anno'] ?: '—' ?></td>
              <td style="text-transform:uppercase"><?= htmlspecialchars($h['categoria']) ?></td>
              <td style="color:var(--amarillo)"><?= $h['n_specs'] ?></td>
              <td style="color:#555"><?= $h['n_fotos'] ?></td>
              <td>
                <span class="<?= $h['publicado'] ? 'badge-publicada' : 'badge-borrador' ?>">
                  <?= $h['publicado'] ? '● PUBLICADO' : '○ BORRADOR' ?>
                </span>
              </td>
              <td>
                <a href="/admin/hardware-form.php?id=<?= $h['id'] ?>"
                   class="admin-btn admin-btn-primary admin-btn-sm">EDITAR</a>
                &nbsp;
                <?php if ($h['publicado']): ?>
                <a href="/hardware-ficha.php?slug=<?= urlencode($h['slug']) ?>" target="_blank"
                   class="admin-btn admin-btn-sm" style="color:#555;border-color:#333">VER</a>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="8" style="text-align:center;color:#333;padding:30px">
                No hay hardware todavía.
                <a href="/admin/hardware-form.php" style="color:var(--cyan)">Crear el primero</a>
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
