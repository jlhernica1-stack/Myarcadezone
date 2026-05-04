<?php
require_once __DIR__ . '/includes/db.php';

$q  = trim($_GET['q'] ?? '');
$db = db();

$juegos = $personajes = $hardware = [];

if ($q !== '') {
    $like = '%' . $q . '%';

    $juegos = $db->prepare("
        SELECT slug, titulo, desarrollador, anno, genero, nota, imagen_cover
        FROM juegos
        WHERE publicada = 1 AND (titulo LIKE ? OR desarrollador LIKE ? OR descripcion_corta LIKE ?)
        ORDER BY anno DESC LIMIT 12
    ");
    $juegos->execute([$like, $like, $like]);
    $juegos = $juegos->fetchAll();

    $personajes = $db->prepare("
        SELECT slug, nombre, juego_origen, sprite_url
        FROM personajes
        WHERE nombre LIKE ? OR juego_origen LIKE ?
        ORDER BY nombre LIMIT 12
    ");
    $personajes->execute([$like, $like]);
    $personajes = $personajes->fetchAll();

    $hardware = $db->prepare("
        SELECT slug, nombre, categoria, anno
        FROM hardware
        WHERE nombre LIKE ?
        ORDER BY anno LIMIT 6
    ");
    $hardware->execute([$like]);
    $hardware = $hardware->fetchAll();
}

$total = count($juegos) + count($personajes) + count($hardware);

$current_page = 'resenas';
$page_title   = $q ? 'Búsqueda: ' . htmlspecialchars($q) . ' — MY ARCADE ZONE' : 'Buscar — MY ARCADE ZONE';

require __DIR__ . '/includes/header.php';
?>

  <div class="layout">
    <main>

      <div class="article-card">
        <div class="article-header">
          <span>🔍 BÚSQUEDA<?= $q ? ' — ' . htmlspecialchars(strtoupper($q)) : '' ?></span>
          <div class="window-btns"><div class="btn-close"></div><div class="btn-min"></div><div class="btn-max"></div></div>
        </div>
        <div class="article-body" style="padding:20px">

          <!-- FORMULARIO -->
          <form method="GET" action="/buscar.php" style="display:flex;gap:8px;margin-bottom:24px">
            <input type="text" name="q" value="<?= htmlspecialchars($q) ?>"
                   placeholder="BUSCAR EN ARCADE ZONE..."
                   style="flex:1;background:#0a0a0a;border:1px solid rgba(0,238,255,0.3);color:var(--blanco);
                          font-family:'Share Tech Mono',monospace;font-size:12px;letter-spacing:2px;
                          padding:10px 14px;outline:none">
            <button type="submit"
                    style="background:var(--cyan);color:#000;font-family:'Bebas Neue',sans-serif;
                           font-size:14px;letter-spacing:3px;border:none;padding:10px 20px;cursor:pointer">
              ► IR
            </button>
          </form>

          <?php if ($q === ''): ?>
            <p style="font-family:'Share Tech Mono',monospace;font-size:11px;color:#444">
              Escribe algo para buscar entre reseñas, personajes y hardware.
            </p>

          <?php elseif ($total === 0): ?>
            <p style="font-family:'Share Tech Mono',monospace;font-size:11px;color:#444">
              > Sin resultados para «<?= htmlspecialchars($q) ?>». Prueba con otro término.
            </p>

          <?php else: ?>
            <p style="font-family:'Share Tech Mono',monospace;font-size:10px;color:#444;margin-bottom:20px">
              > <?= $total ?> resultado<?= $total !== 1 ? 's' : '' ?> para «<?= htmlspecialchars($q) ?>»
            </p>

            <?php if ($juegos): ?>
            <div style="margin-bottom:28px">
              <div style="font-family:'Bebas Neue',sans-serif;font-size:13px;letter-spacing:4px;
                          color:var(--cyan);border-bottom:1px solid rgba(0,238,255,0.15);
                          padding-bottom:6px;margin-bottom:14px">
                🕹️ RESEÑAS (<?= count($juegos) ?>)
              </div>
              <div class="reviews-grid">
                <?php foreach ($juegos as $j): ?>
                <a href="/resena.php?slug=<?= urlencode($j['slug']) ?>" class="game-card">
                  <?php if ($j['imagen_cover']): ?>
                  <img class="card-thumb" src="<?= htmlspecialchars($j['imagen_cover']) ?>" alt="<?= htmlspecialchars($j['titulo']) ?>">
                  <?php else: ?>
                  <div class="card-thumb-placeholder"><?= htmlspecialchars(strtoupper($j['titulo'])) ?></div>
                  <?php endif; ?>
                  <div class="card-body">
                    <div class="card-meta"><?= htmlspecialchars($j['anno'] . ' · ' . strtoupper($j['genero'] ?? '')) ?></div>
                    <div class="card-title"><?= htmlspecialchars($j['titulo']) ?></div>
                    <div class="card-dev"><?= htmlspecialchars($j['desarrollador']) ?></div>
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:6px">
                      <span class="card-cta">► LEER RESEÑA</span>
                      <?php if ($j['nota']): ?>
                      <span class="card-rating"><?= $j['nota'] ?></span>
                      <?php endif; ?>
                    </div>
                  </div>
                </a>
                <?php endforeach; ?>
              </div>
            </div>
            <?php endif; ?>

            <?php if ($personajes): ?>
            <div style="margin-bottom:28px">
              <div style="font-family:'Bebas Neue',sans-serif;font-size:13px;letter-spacing:4px;
                          color:var(--magenta);border-bottom:1px solid rgba(255,0,128,0.15);
                          padding-bottom:6px;margin-bottom:14px">
                👾 PERSONAJES (<?= count($personajes) ?>)
              </div>
              <div style="display:flex;flex-wrap:wrap;gap:12px">
                <?php foreach ($personajes as $per): ?>
                <a href="/personaje.php?slug=<?= urlencode($per['slug']) ?>"
                   style="display:flex;align-items:center;gap:12px;background:var(--negro-panel);
                          border:1px solid rgba(255,0,128,0.15);padding:10px 14px;
                          text-decoration:none;transition:border-color .2s;min-width:180px"
                   onmouseover="this.style.borderColor='var(--magenta)'"
                   onmouseout="this.style.borderColor='rgba(255,0,128,0.15)'">
                  <div>
                    <div style="font-family:'Bebas Neue',sans-serif;font-size:14px;
                                letter-spacing:2px;color:var(--blanco)">
                      <?= htmlspecialchars($per['nombre']) ?>
                    </div>
                    <div style="font-family:'Share Tech Mono',monospace;font-size:9px;
                                color:#555;letter-spacing:1px;margin-top:2px">
                      <?= htmlspecialchars($per['juego_origen'] ?? '') ?>
                    </div>
                  </div>
                </a>
                <?php endforeach; ?>
              </div>
            </div>
            <?php endif; ?>

            <?php if ($hardware): ?>
            <div style="margin-bottom:28px">
              <div style="font-family:'Bebas Neue',sans-serif;font-size:13px;letter-spacing:4px;
                          color:var(--amarillo);border-bottom:1px solid rgba(255,215,0,0.15);
                          padding-bottom:6px;margin-bottom:14px">
                🕹️ HARDWARE (<?= count($hardware) ?>)
              </div>
              <div style="display:flex;flex-wrap:wrap;gap:12px">
                <?php foreach ($hardware as $hw): ?>
                <a href="/hardware-ficha.php?slug=<?= urlencode($hw['slug']) ?>"
                   style="background:var(--negro-panel);border:1px solid rgba(255,215,0,0.15);
                          padding:10px 16px;text-decoration:none;transition:border-color .2s"
                   onmouseover="this.style.borderColor='var(--amarillo)'"
                   onmouseout="this.style.borderColor='rgba(255,215,0,0.15)'">
                  <div style="font-family:'Bebas Neue',sans-serif;font-size:14px;
                              letter-spacing:2px;color:var(--blanco)">
                    <?= htmlspecialchars($hw['nombre']) ?>
                  </div>
                  <div style="font-family:'Share Tech Mono',monospace;font-size:9px;
                              color:#555;letter-spacing:1px;margin-top:2px">
                    <?= htmlspecialchars(strtoupper($hw['categoria'] ?? '')) ?> · <?= $hw['anno'] ?>
                  </div>
                </a>
                <?php endforeach; ?>
              </div>
            </div>
            <?php endif; ?>

          <?php endif; ?>

        </div>
      </div>

    </main>

    <aside class="sidebar">
      <div class="widget">
        <div class="widget-header">BÚSQUEDAS RÁPIDAS</div>
        <div class="widget-body">
          <?php foreach (['Street Fighter','Final Fight','Capcom','SNK','Konami','1991','1989','beat em up','lucha'] as $sug): ?>
          <div style="margin-bottom:6px">
            <a href="/buscar.php?q=<?= urlencode($sug) ?>"
               style="font-family:'Share Tech Mono',monospace;font-size:10px;
                      color:#555;text-decoration:none;transition:color .2s"
               onmouseover="this.style.color='var(--cyan)'"
               onmouseout="this.style.color='#555'">
              ► <?= htmlspecialchars($sug) ?>
            </a>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </aside>
  </div>

<?php require __DIR__ . '/includes/footer.php'; ?>
