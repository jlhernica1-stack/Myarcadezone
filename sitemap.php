<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/config.php';

$db = db();

header('Content-Type: application/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

  <!-- Páginas estáticas -->
  <url>
    <loc><?= SITE_URL ?>/</loc>
    <changefreq>daily</changefreq>
    <priority>1.0</priority>
  </url>
  <url>
    <loc><?= SITE_URL ?>/resenas.php</loc>
    <changefreq>weekly</changefreq>
    <priority>0.9</priority>
  </url>
  <url>
    <loc><?= SITE_URL ?>/blog.php</loc>
    <changefreq>weekly</changefreq>
    <priority>0.8</priority>
  </url>
  <url>
    <loc><?= SITE_URL ?>/personajes.php</loc>
    <changefreq>weekly</changefreq>
    <priority>0.7</priority>
  </url>
  <url>
    <loc><?= SITE_URL ?>/hardware.php</loc>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
  </url>
  <url>
    <loc><?= SITE_URL ?>/retrocassete.php</loc>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
  </url>
  <url>
    <loc><?= SITE_URL ?>/salon/</loc>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
  </url>
  <url>
    <loc><?= SITE_URL ?>/acercade.php</loc>
    <changefreq>monthly</changefreq>
    <priority>0.4</priority>
  </url>

  <!-- Reseñas -->
  <?php
  $juegos = $db->query("SELECT slug, fecha_publicacion FROM juegos WHERE publicada = 1 ORDER BY fecha_publicacion DESC")->fetchAll();
  foreach ($juegos as $j):
      $lastmod = $j['fecha_publicacion'] ? date('Y-m-d', strtotime($j['fecha_publicacion'])) : date('Y-m-d');
  ?>
  <url>
    <loc><?= SITE_URL ?>/resena.php?slug=<?= urlencode($j['slug']) ?></loc>
    <lastmod><?= $lastmod ?></lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.8</priority>
  </url>
  <?php endforeach; ?>

  <!-- Entradas de blog -->
  <?php
  $posts = $db->query("SELECT slug, fecha FROM blog_posts WHERE publicado = 1 ORDER BY fecha DESC")->fetchAll();
  foreach ($posts as $p):
      $lastmod = $p['fecha'] ? date('Y-m-d', strtotime($p['fecha'])) : date('Y-m-d');
  ?>
  <url>
    <loc><?= SITE_URL ?>/blog.php?slug=<?= urlencode($p['slug']) ?></loc>
    <lastmod><?= $lastmod ?></lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.7</priority>
  </url>
  <?php endforeach; ?>

  <!-- Personajes -->
  <?php
  $personajes = $db->query("SELECT slug FROM personajes ORDER BY nombre")->fetchAll();
  foreach ($personajes as $p):
  ?>
  <url>
    <loc><?= SITE_URL ?>/personaje.php?slug=<?= urlencode($p['slug']) ?></loc>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
  </url>
  <?php endforeach; ?>

</urlset>
