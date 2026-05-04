<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/config.php';

$db = db();

$posts = $db->query("
    SELECT titulo, slug, categoria AS categoria, contenido_html AS descripcion, fecha, imagen_url AS imagen, 'blog' AS tipo
    FROM blog_posts
    WHERE publicado = 1
    UNION ALL
    SELECT titulo, slug, genero AS categoria, descripcion_corta AS descripcion, fecha_publicacion AS fecha, imagen_cover AS imagen, 'resena' AS tipo
    FROM juegos
    WHERE publicada = 1
    ORDER BY fecha DESC
    LIMIT 30
")->fetchAll();

header('Content-Type: application/rss+xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:media="http://search.yahoo.com/mrss/">
  <channel>
    <title>MY ARCADE ZONE</title>
    <link><?= SITE_URL ?></link>
    <description>Reseñas, nostalgia y cultura arcade en español</description>
    <language>es</language>
    <atom:link href="<?= SITE_URL ?>/rss.php" rel="self" type="application/rss+xml"/>
    <lastBuildDate><?= date('r') ?></lastBuildDate>

    <?php foreach ($posts as $p):
        $url  = $p['tipo'] === 'blog'
              ? SITE_URL . '/blog.php?slug='  . urlencode($p['slug'])
              : SITE_URL . '/resena.php?slug=' . urlencode($p['slug']);
        $prefix = $p['tipo'] === 'resena' ? '[RESEÑA] ' : '[BLOG] ';
        $desc   = mb_substr(strip_tags($p['descripcion'] ?? ''), 0, 300);
        $imagen = !empty($p['imagen']) ? SITE_URL . $p['imagen'] : null;
    ?>
    <item>
      <title><?= htmlspecialchars($prefix . $p['titulo']) ?></title>
      <link><?= $url ?></link>
      <guid isPermaLink="true"><?= $url ?></guid>
      <pubDate><?= date('r', strtotime($p['fecha'])) ?></pubDate>
      <?php if ($p['categoria']): ?>
      <category><?= htmlspecialchars($p['categoria']) ?></category>
      <?php endif; ?>
      <description><![CDATA[<?php if ($imagen): ?><img src="<?= htmlspecialchars($imagen) ?>" alt="<?= htmlspecialchars($p['titulo']) ?>" style="max-width:100%"><br><br><?php endif; ?><?= $desc ?>...]]></description>
      <?php if ($imagen): ?>
      <enclosure url="<?= htmlspecialchars($imagen) ?>" type="image/jpeg" length="0"/>
      <media:content url="<?= htmlspecialchars($imagen) ?>" medium="image"/>
      <?php endif; ?>
    </item>
    <?php endforeach; ?>

  </channel>
</rss>
