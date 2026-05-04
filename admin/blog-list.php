<?php
require __DIR__ . '/_auth.php';
require_once dirname(__DIR__) . '/includes/db.php';
$db = db();

$posts = $db->query("SELECT id, slug, titulo, categoria, fecha, publicado FROM blog_posts ORDER BY fecha DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Blog — Admin MY ARCADE ZONE</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Share+Tech+Mono&family=Rajdhani:wght@400;600;700&display=swap">
<link rel="stylesheet" href="/assets/css/style.css">
<style>body::before{display:none}</style>
</head>
<body>
<div class="admin-topbar">
  <div>
    <div class="admin-topbar-logo">MY ARCADE ZONE</div>
    <div class="admin-topbar-sub">◄ BLOG ►</div>
  </div>
  <div class="admin-topbar-right">
    <a href="/admin/">← PANEL</a>
    &nbsp;&nbsp;
    <a href="/admin/blog-form.php" style="color:var(--cyan)">+ NUEVA ENTRADA</a>
    &nbsp;&nbsp;
    <a href="/blog.php" target="_blank" style="color:var(--amarillo)">VER BLOG</a>
  </div>
</div>

<div class="admin-wrap">
  <div class="admin-card">
    <div class="admin-card-header">
      📝 ENTRADAS DEL BLOG
      <a href="/admin/blog-form.php" class="admin-btn admin-btn-primary admin-btn-sm">+ NUEVA ENTRADA</a>
    </div>
    <div class="admin-card-body" style="padding:0">
      <table class="admin-table">
        <thead>
          <tr>
            <th>TÍTULO</th>
            <th>CATEGORÍA</th>
            <th>FECHA</th>
            <th>ESTADO</th>
            <th>ACCIONES</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($posts): ?>
            <?php foreach ($posts as $p): ?>
            <tr>
              <td style="color:var(--blanco)"><?= htmlspecialchars($p['titulo']) ?></td>
              <td><?= htmlspecialchars($p['categoria'] ?? '—') ?></td>
              <td><?= $p['fecha'] ? date('d/m/Y', strtotime($p['fecha'])) : '—' ?></td>
              <td>
                <span class="<?= $p['publicado'] ? 'badge-publicada' : 'badge-borrador' ?>">
                  <?= $p['publicado'] ? '● PUBLICADA' : '○ BORRADOR' ?>
                </span>
              </td>
              <td>
                <a href="/admin/blog-form.php?id=<?= $p['id'] ?>" class="admin-btn admin-btn-primary admin-btn-sm">EDITAR</a>
                &nbsp;
                <?php if ($p['publicado']): ?>
                <a href="/blog.php?slug=<?= urlencode($p['slug']) ?>" target="_blank" class="admin-btn admin-btn-sm" style="color:#555;border-color:#333">VER</a>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="5" style="text-align:center;color:#333;padding:30px">No hay entradas. <a href="/admin/blog-form.php" style="color:var(--cyan)">Crear la primera</a></td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
