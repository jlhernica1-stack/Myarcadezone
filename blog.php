<?php
require_once __DIR__ . '/includes/db.php';
$db = db();

// ¿Ficha de post individual?
$slug = trim($_GET['slug'] ?? '');

if ($slug) {
    $post = $db->prepare("SELECT * FROM blog_posts WHERE slug = ? AND publicado = 1");
    $post->execute([$slug]);
    $post = $post->fetch();
    if (!$post) { header('Location: /blog.php'); exit; }

    $current_page     = 'blog';
    $page_title       = htmlspecialchars($post['titulo']) . ' — MY ARCADE ZONE';
    $meta_description = mb_substr(strip_tags($post['contenido_html']), 0, 160);
    $og_type  = 'article';
    $og_image = !empty($post['imagen_url']) ? SITE_URL . $post['imagen_url'] : SITE_URL . '/assets/images/og-default.jpg';

    $json_ld = json_encode([
        '@context'         => 'https://schema.org',
        '@type'            => 'BlogPosting',
        'headline'         => $post['titulo'],
        'description'      => $meta_description,
        'datePublished'    => $post['fecha'],
        'image'            => $og_image,
        'author'           => ['@type' => 'Organization', 'name' => 'MY ARCADE ZONE'],
        'publisher'        => ['@type' => 'Organization', 'name' => 'MY ARCADE ZONE'],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    require __DIR__ . '/includes/header.php';
?>

<style>
.blog-post-hero {
  width: 100%;
  max-height: 480px;
  overflow: hidden;
  position: relative;
  background: #000;
}
.blog-post-hero img {
  width: 100%;
  max-height: 480px;
  object-fit: cover;
  display: block;
  opacity: .85;
}
.blog-post-hero video {
  width: 100%;
  max-height: 480px;
  object-fit: cover;
  display: block;
}
.blog-post-hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to bottom, transparent 40%, rgba(0,0,0,0.7) 100%);
  pointer-events: none;
}
.blog-post-wrap { max-width: 820px; padding: 32px 24px; }
.blog-post-categoria {
  font-family: 'Share Tech Mono', monospace;
  font-size: 10px;
  letter-spacing: 3px;
  color: var(--magenta);
  margin-bottom: 10px;
}
.blog-post-titulo {
  font-family: 'Bebas Neue', sans-serif;
  font-size: clamp(28px, 5vw, 46px);
  letter-spacing: 2px;
  color: var(--blanco);
  line-height: 1.1;
  margin-bottom: 12px;
}
.blog-post-fecha {
  font-family: 'Share Tech Mono', monospace;
  font-size: 10px;
  color: #444;
  letter-spacing: 2px;
  margin-bottom: 28px;
}
.blog-post-body { font-family: 'Rajdhani', sans-serif; font-size: 17px; color: #999; line-height: 1.85; }
.blog-post-body h2 {
  font-family: 'Bebas Neue', sans-serif;
  font-size: 22px; letter-spacing: 3px;
  color: var(--cyan);
  margin: 36px 0 14px;
  border-left: 3px solid var(--cyan);
  padding-left: 12px;
}
.blog-post-body p { margin-bottom: 18px; }
.blog-post-body strong { color: var(--blanco); }
.blog-post-body em { color: var(--amarillo); font-style: normal; }
.blog-post-body hr {
  border: none;
  border-top: 1px solid rgba(0,238,255,0.1);
  margin: 32px 0;
}
.blog-post-body blockquote {
  border-left: 3px solid var(--magenta);
  padding: 12px 18px;
  background: rgba(255,0,170,0.04);
  margin: 24px 0;
  font-size: 16px;
  color: #bbb;
}
.blog-back {
  font-family: 'Share Tech Mono', monospace;
  font-size: 10px;
  letter-spacing: 2px;
  color: #444;
  text-decoration: none;
  display: inline-block;
  margin-bottom: 24px;
  transition: color .2s;
}
.blog-back:hover { color: var(--cyan); }
</style>

<div class="layout">
  <main>
    <div class="home-section">

      <?php if ($post['video_url']): ?>
      <div class="blog-post-hero">
        <video autoplay loop muted playsinline style="width:100%;max-height:480px;object-fit:cover;display:block">
          <source src="<?= htmlspecialchars($post['video_url']) ?>" type="video/mp4">
        </video>
        <div class="blog-post-hero-overlay"></div>
      </div>
      <?php elseif ($post['imagen_url']): ?>
      <div class="blog-post-hero">
        <img src="<?= htmlspecialchars($post['imagen_url']) ?>" alt="<?= htmlspecialchars($post['titulo']) ?>">
        <div class="blog-post-hero-overlay"></div>
      </div>
      <?php endif; ?>

      <div class="section-body blog-post-wrap">
        <a href="/blog.php" class="blog-back">← VOLVER AL BLOG</a>

        <?php if ($post['categoria']): ?>
        <div class="blog-post-categoria">★ <?= htmlspecialchars($post['categoria']) ?></div>
        <?php endif; ?>

        <div class="blog-post-titulo"><?= htmlspecialchars($post['titulo']) ?></div>
        <div class="blog-post-fecha"><?= date('d/m/Y', strtotime($post['fecha'])) ?></div>

        <div class="blog-post-body">
          <?= $post['contenido_html'] ?>
        </div>
      </div>

    </div>
  </main>
</div>

<?php
    require __DIR__ . '/includes/footer.php';

} else {

    // ── LISTADO ──
    $posts = $db->query("
        SELECT id, slug, titulo, categoria, imagen_url, video_url, fecha
        FROM blog_posts
        WHERE publicado = 1
        ORDER BY fecha DESC
    ")->fetchAll();

    $current_page = 'blog';
    $page_title   = 'Blog — MY ARCADE ZONE';
    require __DIR__ . '/includes/header.php';
?>

<style>
.blog-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
  padding: 24px;
}
.blog-card {
  background: var(--negro-panel);
  border: 1px solid rgba(0,238,255,0.08);
  text-decoration: none;
  display: block;
  transition: border-color .2s, transform .2s;
  overflow: hidden;
}
.blog-card:hover { border-color: var(--cyan); transform: translateY(-3px); }
.blog-card-thumb {
  width: 100%;
  height: 180px;
  object-fit: cover;
  display: block;
  background: #0a0a0a;
}
.blog-card-thumb-placeholder {
  width: 100%; height: 180px;
  background: linear-gradient(135deg, #0a0a1a, #0d0d20);
  display: flex; align-items: center; justify-content: center;
  font-family: 'Bebas Neue', sans-serif;
  font-size: 48px; color: rgba(0,238,255,0.1);
}
.blog-card-body { padding: 16px; }
.blog-card-cat {
  font-family: 'Share Tech Mono', monospace;
  font-size: 9px; letter-spacing: 3px;
  color: var(--magenta); margin-bottom: 8px;
}
.blog-card-title {
  font-family: 'Bebas Neue', sans-serif;
  font-size: 20px; letter-spacing: 2px;
  color: var(--blanco); line-height: 1.2;
  margin-bottom: 8px;
}
.blog-card-fecha {
  font-family: 'Share Tech Mono', monospace;
  font-size: 9px; color: #333; letter-spacing: 1px;
}
@media (max-width: 600px) {
  .blog-grid { grid-template-columns: 1fr; padding: 14px; gap: 14px; }
}
</style>

<div class="layout">
  <main>
    <div class="home-section">
      <div class="section-hdr" style="padding:16px 20px">
        <span class="section-hdr-title">📝 BLOG</span>
        <span style="font-family:'Share Tech Mono',monospace;font-size:9px;color:#444"><?= count($posts) ?> ENTRADAS</span>
      </div>

      <?php if ($posts): ?>
      <div class="blog-grid">
        <?php foreach ($posts as $p): ?>
        <a href="/blog.php?slug=<?= urlencode($p['slug']) ?>" class="blog-card">
          <?php if ($p['video_url']): ?>
            <video class="blog-card-thumb" autoplay loop muted playsinline>
              <source src="<?= htmlspecialchars($p['video_url']) ?>" type="video/mp4">
            </video>
          <?php elseif ($p['imagen_url']): ?>
            <img class="blog-card-thumb" src="<?= htmlspecialchars($p['imagen_url']) ?>" alt="<?= htmlspecialchars($p['titulo']) ?>">
          <?php else: ?>
            <div class="blog-card-thumb-placeholder">★</div>
          <?php endif; ?>
          <div class="blog-card-body">
            <?php if ($p['categoria']): ?>
            <div class="blog-card-cat">★ <?= htmlspecialchars($p['categoria']) ?></div>
            <?php endif; ?>
            <div class="blog-card-title"><?= htmlspecialchars($p['titulo']) ?></div>
            <div class="blog-card-fecha"><?= date('d/m/Y', strtotime($p['fecha'])) ?></div>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
      <?php else: ?>
      <div style="padding:48px;text-align:center;font-family:'Share Tech Mono',monospace;font-size:11px;color:#333;letter-spacing:3px">
        PRÓXIMAMENTE — PRIMERA ENTRADA EN CAMINO
      </div>
      <?php endif; ?>

    </div>
  </main>
</div>

<?php
    require __DIR__ . '/includes/footer.php';
}
?>
