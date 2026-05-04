<?php
require __DIR__ . '/_auth.php';
require_once dirname(__DIR__) . '/includes/db.php';

$db = db();

$personajes = $db->query("
    SELECT p.id, p.slug, p.nombre, p.juego_origen, p.sprite_url,
           COUNT(DISTINCT jp.juego_id) AS num_juegos,
           COUNT(DISTINCT pd.id) AS num_datos
    FROM personajes p
    LEFT JOIN juego_personaje jp ON jp.personaje_id = p.id
    LEFT JOIN personaje_datos pd ON pd.personaje_id = p.id
    GROUP BY p.id
    ORDER BY p.nombre
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Personajes — Admin MY ARCADE ZONE</title>
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
.sprite-mini {
  width:40px; height:40px; object-fit:contain;
  background:#050510; border:1px solid rgba(0,238,255,0.1);
  image-rendering:pixelated;
}
.sprite-placeholder {
  width:40px; height:40px;
  background:#050510; border:1px solid rgba(0,238,255,0.05);
  display:flex; align-items:center; justify-content:center;
  font-size:18px;
}
</style>
</head>
<body>
<div class="admin-topbar">
  <div class="admin-topbar-logo">PERSONAJES</div>
  <div style="font-family:'Share Tech Mono',monospace;font-size:10px;color:#444;display:flex;gap:16px">
    <a href="/admin/personaje-form.php" style="color:var(--cyan)">+ NUEVO PERSONAJE</a>
    <a href="/admin/" style="color:#555">◄ ADMIN</a>
  </div>
</div>

<div class="admin-wrap">

  <div class="admin-card">
    <div class="admin-card-header">
      👾 TODOS LOS PERSONAJES
      <span style="font-family:'Share Tech Mono',monospace;font-size:10px;color:#444;font-weight:normal;margin-left:12px">
        <?= count($personajes) ?> PERSONAJE<?= count($personajes) !== 1 ? 'S' : '' ?>
      </span>
    </div>
    <div class="admin-card-body" style="padding:0">

      <?php if ($personajes): ?>
      <table class="admin-table">
        <thead>
          <tr>
            <th style="width:48px"></th>
            <th>NOMBRE</th>
            <th>SLUG</th>
            <th>JUEGO DE ORIGEN</th>
            <th style="text-align:center">JUEGOS</th>
            <th style="text-align:center">DATOS</th>
            <th style="width:140px"></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($personajes as $p): ?>
          <tr>
            <td style="padding:8px;text-align:center">
              <?php if ($p['sprite_url']): ?>
              <img src="<?= htmlspecialchars($p['sprite_url']) ?>" alt="" class="sprite-mini">
              <?php else: ?>
              <div class="sprite-placeholder">👤</div>
              <?php endif; ?>
            </td>
            <td style="font-family:'Rajdhani',sans-serif;font-weight:700;font-size:14px;color:var(--blanco)">
              <?= htmlspecialchars($p['nombre']) ?>
            </td>
            <td style="font-family:'Share Tech Mono',monospace;font-size:10px;color:#555">
              <?= htmlspecialchars($p['slug']) ?>
            </td>
            <td style="font-family:'Share Tech Mono',monospace;font-size:10px;color:#888">
              <?= htmlspecialchars($p['juego_origen']) ?>
            </td>
            <td style="text-align:center;font-family:'Share Tech Mono',monospace;font-size:11px;color:<?= $p['num_juegos'] ? 'var(--cyan)' : '#333' ?>">
              <?= $p['num_juegos'] ?>
            </td>
            <td style="text-align:center;font-family:'Share Tech Mono',monospace;font-size:11px;color:<?= $p['num_datos'] ? 'var(--amarillo)' : '#333' ?>">
              <?= $p['num_datos'] ?>
            </td>
            <td style="padding:8px;display:flex;gap:6px;justify-content:flex-end">
              <a href="/personaje.php?slug=<?= urlencode($p['slug']) ?>"
                 target="_blank"
                 class="admin-btn admin-btn-sm" style="color:#555;border-color:#222">VER</a>
              <a href="/admin/personaje-form.php?id=<?= $p['id'] ?>"
                 class="admin-btn admin-btn-primary admin-btn-sm">EDITAR</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php else: ?>
      <div style="padding:40px;text-align:center;font-family:'Share Tech Mono',monospace;font-size:11px;color:#333">
        > No hay personajes creados todavía.<br><br>
        <a href="/admin/personaje-form.php" class="admin-btn admin-btn-primary">+ CREAR PRIMER PERSONAJE</a>
      </div>
      <?php endif; ?>

    </div>
  </div>

</div>
</body>
</html>
