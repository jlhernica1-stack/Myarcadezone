<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/sprite.php';

$slug_juego = trim($_GET['juego'] ?? '');
if (!$slug_juego) { header('Location: /resenas.php'); exit; }

$db  = db();
$juego = $db->prepare("SELECT * FROM juegos WHERE slug = ? AND publicada = 1");
$juego->execute([$slug_juego]);
$juego = $juego->fetch();
if (!$juego) { header('HTTP/1.0 404 Not Found'); exit('Juego no encontrado.'); }

$personajes = $db->prepare("
    SELECT p.slug, p.nombre, p.sprite_url, p.juego_origen
    FROM personajes p
    INNER JOIN juego_personaje jp ON jp.personaje_id = p.id
    WHERE jp.juego_id = ?
    ORDER BY p.nombre
");
$personajes->execute([$juego['id']]);
$personajes = $personajes->fetchAll();

$current_page = 'resenas';
$page_title   = 'Personajes · ' . $juego['titulo'] . ' — MY ARCADE ZONE';

require __DIR__ . '/includes/header.php';
?>

<div class="layout">
  <main>

    <div class="home-section">

      <!-- Cabecera del juego -->
      <div class="section-hdr" style="flex-direction:column;align-items:flex-start;gap:4px;padding:16px 20px">
        <div style="display:flex;align-items:center;gap:12px;width:100%">
          <a href="/resena.php?slug=<?= urlencode($juego['slug']) ?>"
             style="font-family:'Share Tech Mono',monospace;font-size:10px;color:#555;text-decoration:none">
            ◄ <?= htmlspecialchars(strtoupper($juego['titulo'])) ?>
          </a>
          <span style="font-family:'Share Tech Mono',monospace;font-size:10px;color:#222">·</span>
          <span class="section-hdr-title" style="font-size:16px">👾 SELECCIONA TU PERSONAJE</span>
        </div>
        <div style="font-family:'Share Tech Mono',monospace;font-size:10px;color:#333">
          <?= htmlspecialchars(strtoupper($juego['titulo'])) ?> · <?= $juego['anno'] ?> · <?= count($personajes) ?> PERSONAJE<?= count($personajes) !== 1 ? 'S' : '' ?>
        </div>
      </div>

      <!-- Grid de personajes -->
      <div class="section-body" style="padding:24px 20px">
        <?php if ($personajes): ?>
        <div class="chars-grid">
          <?php foreach ($personajes as $p): ?>
          <a href="/personaje.php?slug=<?= urlencode($p['slug']) ?>" class="char-card">
            <div class="char-sprite-wrap">
              <?php if ($p['sprite_url']): ?>
              <?= render_sprite($p['sprite_url'], $p['nombre'], 'char-sprite') ?>
              <?php else: ?>
              <div class="char-sprite-placeholder">?</div>
              <?php endif; ?>
            </div>
            <div class="char-name"><?= htmlspecialchars(strtoupper($p['nombre'])) ?></div>
          </a>
          <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p style="font-family:'Share Tech Mono',monospace;font-size:11px;color:#333;text-align:center;padding:40px 0">
          > No hay personajes registrados para este juego todavía.
        </p>
        <?php endif; ?>
      </div>

    </div>

  </main>

  <aside class="sidebar">
    <div class="widget">
      <div class="widget-header">JUEGO</div>
      <div class="widget-body">
        <?php if ($juego['imagen_cover']): ?>
        <img src="<?= htmlspecialchars($juego['imagen_cover']) ?>"
             alt="<?= htmlspecialchars($juego['titulo']) ?>"
             style="width:100%;height:auto;display:block;margin-bottom:10px;border:1px solid rgba(0,238,255,0.1)">
        <?php endif; ?>
        <div class="widget-stat-row"><span>Título</span><span><?= htmlspecialchars($juego['titulo']) ?></span></div>
        <div class="widget-stat-row"><span>Año</span><span><?= $juego['anno'] ?></span></div>
        <div class="widget-stat-row"><span>Desarrollador</span><span><?= htmlspecialchars($juego['desarrollador'] ?? '—') ?></span></div>
        <div class="widget-stat-row"><span>Género</span><span><?= htmlspecialchars(strtoupper($juego['genero'] ?? '—')) ?></span></div>
        <?php if ($juego['nota']): ?>
        <div class="widget-stat-row"><span>Nota MAZ</span><span style="color:var(--amarillo);font-weight:700"><?= $juego['nota'] ?>/10</span></div>
        <?php endif; ?>
        <div style="margin-top:10px">
          <a href="/resena.php?slug=<?= urlencode($juego['slug']) ?>"
             class="admin-btn admin-btn-primary" style="display:block;text-align:center;font-size:11px">
            ► LEER RESEÑA COMPLETA
          </a>
        </div>
      </div>
    </div>
  </aside>

</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
