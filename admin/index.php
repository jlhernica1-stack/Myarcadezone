<?php
require __DIR__ . '/_auth.php';
require_once dirname(__DIR__) . '/includes/db.php';

$db = db();
$total_juegos     = $db->query("SELECT COUNT(*) FROM juegos")->fetchColumn();
$total_publicados = $db->query("SELECT COUNT(*) FROM juegos WHERE publicada = 1")->fetchColumn();
$total_comentarios = $db->query("SELECT COUNT(*) FROM comentarios WHERE aprobado = 1")->fetchColumn();
$total_votos      = $db->query("SELECT COUNT(*) FROM votos")->fetchColumn();
$total_personajes = $db->query("SELECT COUNT(*) FROM personajes")->fetchColumn();
$total_blog       = $db->query("SELECT COUNT(*) FROM blog_posts WHERE publicado = 1")->fetchColumn();
$total_hardware   = $db->query("SELECT COUNT(*) FROM hardware")->fetchColumn();
$total_tracks     = $db->query("SELECT COUNT(*) FROM retrocassete_tracks")->fetchColumn();

$juegos = $db->query("
    SELECT id, slug, titulo, anno, genero, nota, publicada, fecha_publicacion
    FROM juegos ORDER BY created_at DESC
")->fetchAll();

$comentarios_recientes = $db->query("
    SELECT c.nombre, c.texto, c.fecha, j.titulo as juego
    FROM comentarios c JOIN juegos j ON j.id = c.juego_id
    WHERE c.aprobado = 1
    ORDER BY c.fecha DESC LIMIT 5
")->fetchAll();

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: /admin/login.php'); exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin — MY ARCADE ZONE</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Share+Tech+Mono&family=Rajdhani:wght@400;600;700&display=swap">
<link rel="stylesheet" href="/assets/css/style.css">
<style>
body::before { display:none; }
.admin-topbar {
  background:var(--negro-panel);
  border-bottom:2px solid var(--cyan);
  padding:12px 24px;
  display:flex; align-items:center;
  justify-content:space-between;
}
.admin-topbar-logo {
  font-family:'Bebas Neue',sans-serif;
  font-size:22px; letter-spacing:5px;
  background:linear-gradient(180deg,#fff,var(--cyan));
  -webkit-background-clip:text; -webkit-text-fill-color:transparent;
  background-clip:text;
}
.admin-topbar-sub {
  font-family:'Share Tech Mono',monospace;
  font-size:9px; letter-spacing:3px; color:var(--magenta);
}
.admin-topbar-right a {
  font-family:'Share Tech Mono',monospace;
  font-size:10px; letter-spacing:2px;
  color:#444; text-decoration:none; transition:color .2s;
}
.admin-topbar-right a:hover { color:var(--rojo); }
.admin-stats-row {
  display:grid;
  grid-template-columns:repeat(auto-fill,minmax(120px,1fr));
  gap:16px; margin-bottom:24px;
}
.admin-stat-card {
  background:var(--negro-panel);
  border:1px solid rgba(0,238,255,0.1);
  padding:16px; text-align:center;
}
.admin-stat-n {
  font-family:'Bebas Neue',sans-serif;
  font-size:36px; color:var(--amarillo);
  text-shadow:0 0 12px rgba(255,215,0,0.4);
  line-height:1; display:block;
}
.admin-stat-l {
  font-family:'Share Tech Mono',monospace;
  font-size:9px; letter-spacing:2px; color:#444;
}
@media(max-width:600px) { .admin-stats-row { grid-template-columns:1fr 1fr; } }
.admin-stat-card[onclick]:hover { border-color:var(--cyan); cursor:pointer; }
</style>
</head>
<body>
<div class="admin-topbar">
  <div>
    <div class="admin-topbar-logo">MY ARCADE ZONE</div>
    <div class="admin-topbar-sub">◄ PANEL DE ADMINISTRACIÓN ►</div>
  </div>
  <div class="admin-topbar-right">
    <a href="/admin/blog-list.php" style="color:var(--verde)">📝 BLOG</a>
    &nbsp;&nbsp;
    <a href="/admin/retrocassete-list.php" style="color:var(--magenta)">🎵 OST</a>
    &nbsp;&nbsp;
    <a href="/admin/hardware-list.php" style="color:var(--amarillo)">🕹️ HARDWARE</a>
    &nbsp;&nbsp;
    <a href="/admin/personajes-list.php" style="color:var(--cyan)">👾 PERSONAJES</a>
    &nbsp;&nbsp;
    <a href="/admin/acercade-form.php" style="color:#aaa">ℹ️ ACERCA DE</a>
    &nbsp;&nbsp;
    <a href="/">VER SITIO</a>
    &nbsp;&nbsp;
    <a href="?logout=1">CERRAR SESIÓN</a>
  </div>
</div>

<div class="admin-wrap">

  <!-- Stats -->
  <div class="admin-stats-row">
    <div class="admin-stat-card">
      <span class="admin-stat-n"><?= $total_publicados ?></span>
      <span class="admin-stat-l">Reseñas publicadas</span>
    </div>
    <div class="admin-stat-card">
      <span class="admin-stat-n"><?= $total_juegos - $total_publicados ?></span>
      <span class="admin-stat-l">Borradores</span>
    </div>
    <div class="admin-stat-card">
      <span class="admin-stat-n"><?= $total_comentarios ?></span>
      <span class="admin-stat-l">Comentarios</span>
    </div>
    <div class="admin-stat-card">
      <span class="admin-stat-n"><?= $total_votos ?></span>
      <span class="admin-stat-l">Votos recibidos</span>
    </div>
    <div class="admin-stat-card" style="cursor:pointer" onclick="location='/admin/personajes-list.php'">
      <span class="admin-stat-n"><?= $total_personajes ?></span>
      <span class="admin-stat-l">Personajes</span>
    </div>
    <div class="admin-stat-card" style="cursor:pointer" onclick="location='/admin/hardware-list.php'">
      <span class="admin-stat-n"><?= $total_hardware ?></span>
      <span class="admin-stat-l">Hardware</span>
    </div>
    <div class="admin-stat-card" style="cursor:pointer" onclick="location='/admin/blog-list.php'">
      <span class="admin-stat-n"><?= $total_blog ?></span>
      <span class="admin-stat-l">Entradas blog</span>
    </div>
    <div class="admin-stat-card" style="cursor:pointer" onclick="location='/admin/retrocassete-list.php'">
      <span class="admin-stat-n"><?= $total_tracks ?></span>
      <span class="admin-stat-l">Pistas OST</span>
    </div>
  </div>

  <!-- Lista de juegos -->
  <div class="admin-card">
    <div class="admin-card-header">
      🕹️ RESEÑAS
      <a href="/admin/juego-form.php" class="admin-btn admin-btn-primary admin-btn-sm">+ NUEVA RESEÑA</a>
    </div>
    <div class="admin-card-body" style="padding:0">
      <table class="admin-table">
        <thead>
          <tr>
            <th>TÍTULO</th>
            <th>AÑO</th>
            <th>GÉNERO</th>
            <th>NOTA</th>
            <th>ESTADO</th>
            <th>ACCIONES</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($juegos): ?>
            <?php foreach ($juegos as $j): ?>
            <tr>
              <td style="color:var(--blanco)"><?= htmlspecialchars($j['titulo']) ?></td>
              <td><?= $j['anno'] ?></td>
              <td><?= htmlspecialchars($j['genero'] ?? '—') ?></td>
              <td style="color:var(--amarillo)"><?= $j['nota'] ?? '—' ?></td>
              <td>
                <span class="<?= $j['publicada'] ? 'badge-publicada' : 'badge-borrador' ?>">
                  <?= $j['publicada'] ? '● PUBLICADA' : '○ BORRADOR' ?>
                </span>
              </td>
              <td>
                <a href="/admin/juego-form.php?id=<?= $j['id'] ?>" class="admin-btn admin-btn-primary admin-btn-sm">EDITAR</a>
                &nbsp;
                <a href="/admin/secciones-form.php?juego_id=<?= $j['id'] ?>" class="admin-btn admin-btn-sm" style="color:var(--cyan);border-color:var(--cyan)">SECCIONES</a>
                &nbsp;
                <?php if ($j['publicada']): ?>
                <a href="/resena.php?slug=<?= urlencode($j['slug']) ?>" target="_blank" class="admin-btn admin-btn-sm" style="color:#555;border-color:#333">VER</a>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="6" style="text-align:center;color:#333;padding:30px">No hay reseñas todavía. <a href="/admin/juego-form.php" style="color:var(--cyan)">Crear la primera</a></td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Comentarios recientes -->
  <?php if ($comentarios_recientes): ?>
  <div class="admin-card">
    <div class="admin-card-header">💬 COMENTARIOS RECIENTES</div>
    <div class="admin-card-body" style="padding:0">
      <table class="admin-table">
        <thead>
          <tr><th>USUARIO</th><th>JUEGO</th><th>COMENTARIO</th><th>FECHA</th></tr>
        </thead>
        <tbody>
          <?php foreach ($comentarios_recientes as $c): ?>
          <tr>
            <td style="color:var(--amarillo)"><?= htmlspecialchars($c['nombre']) ?></td>
            <td><?= htmlspecialchars($c['juego']) ?></td>
            <td><?= htmlspecialchars(mb_substr($c['texto'], 0, 60)) ?>...</td>
            <td><?= date('d/m/Y', strtotime($c['fecha'])) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>

</div>
</body>
</html>
