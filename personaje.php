<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/sprite.php';

$slug = trim($_GET['slug'] ?? '');
if (!$slug) { header('Location: /resenas.php'); exit; }

$db = db();
$p  = $db->prepare("SELECT * FROM personajes WHERE slug = ?");
$p->execute([$slug]);
$p = $p->fetch();
if (!$p) { header('HTTP/1.0 404 Not Found'); exit('Personaje no encontrado.'); }

$datos = $db->prepare("SELECT clave, valor FROM personaje_datos WHERE personaje_id = ? ORDER BY orden");
$datos->execute([$p['id']]);
$datos = $datos->fetchAll();

$juegos_p = $db->prepare("
    SELECT j.slug, j.titulo, j.anno
    FROM juegos j
    INNER JOIN juego_personaje jp ON jp.juego_id = j.id
    WHERE jp.personaje_id = ? AND j.publicada = 1
    ORDER BY j.anno
");
$juegos_p->execute([$p['id']]);
$juegos_p = $juegos_p->fetchAll();

$current_page = 'resenas';
$page_title   = $p['nombre'] . ' — MY ARCADE ZONE';

require __DIR__ . '/includes/header.php';
?>

<div class="layout">
  <main>

    <!-- CABECERA PERSONAJE -->
    <div class="char-hero">
      <div class="char-hero-sprite">
        <?php if ($p['sprite_url']): ?>
        <?= render_sprite($p['sprite_url'], $p['nombre'], 'char-hero-img') ?>
        <?php else: ?>
        <div class="char-hero-placeholder">?</div>
        <?php endif; ?>
        <div class="char-hero-scanlines"></div>
      </div>
      <div class="char-hero-info">
        <div class="char-hero-game"><?= htmlspecialchars(strtoupper($p['juego_origen'] ?: '')) ?></div>
        <h1 class="char-hero-name"><?= htmlspecialchars(strtoupper($p['nombre'])) ?></h1>

        <?php if ($datos): ?>
        <div class="char-ficha">
          <?php foreach ($datos as $d): ?>
          <div class="char-ficha-row">
            <span class="char-ficha-key"><?= htmlspecialchars(strtoupper($d['clave'])) ?></span>
            <span class="char-ficha-val"><?= htmlspecialchars($d['valor']) ?></span>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if ($juegos_p): ?>
        <div class="char-aparece">
          <div class="char-aparece-label">APARECE EN</div>
          <?php foreach ($juegos_p as $jj): ?>
          <a href="/personajes.php?juego=<?= urlencode($jj['slug']) ?>"
             class="char-juego-link">
            <?= htmlspecialchars($jj['titulo']) ?> (<?= $jj['anno'] ?>)
          </a>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- NOTAS / DESCRIPCIÓN -->
    <?php if ($p['notas_html']): ?>
    <div class="article-card" style="margin-bottom:0">
      <div class="article-body char-notas">
        <?= $p['notas_html'] ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- VOLVER -->
    <?php if ($juegos_p): ?>
    <div style="margin-top:20px;display:flex;gap:10px;flex-wrap:wrap">
      <?php foreach ($juegos_p as $jj): ?>
      <a href="/personajes.php?juego=<?= urlencode($jj['slug']) ?>"
         style="font-family:'Share Tech Mono',monospace;font-size:10px;color:var(--cyan);text-decoration:none;
                border:1px solid rgba(0,238,255,0.2);padding:6px 14px;
                transition:border-color .2s"
         onmouseover="this.style.borderColor='var(--cyan)'"
         onmouseout="this.style.borderColor='rgba(0,238,255,0.2)'">
        ◄ TODOS LOS PERSONAJES DE <?= htmlspecialchars(strtoupper($jj['titulo'])) ?>
      </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

  </main>

  <aside class="sidebar">

    <?php if ($juegos_p): ?>
    <div class="widget">
      <div class="widget-header">APARECE EN</div>
      <div class="widget-body">
        <?php foreach ($juegos_p as $jj): ?>
        <div class="widget-game" style="margin-bottom:10px">
          <div>
            <a href="/resena.php?slug=<?= urlencode($jj['slug']) ?>" class="wg-title">
              <?= htmlspecialchars(strtoupper($jj['titulo'])) ?>
            </a>
            <div class="wg-meta"><?= $jj['anno'] ?></div>
            <a href="/personajes.php?juego=<?= urlencode($jj['slug']) ?>"
               style="font-family:'Share Tech Mono',monospace;font-size:9px;color:#555;text-decoration:none">
              VER TODOS LOS PERSONAJES ►
            </a>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <?php if ($datos): ?>
    <div class="widget">
      <div class="widget-header">FICHA</div>
      <div class="widget-body">
        <?php foreach ($datos as $d): ?>
        <div class="widget-stat-row">
          <span><?= htmlspecialchars($d['clave']) ?></span>
          <span style="color:var(--amarillo)"><?= htmlspecialchars($d['valor']) ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

  </aside>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
