<?php
require_once __DIR__ . '/includes/db.php';

$current_page = 'inicio';
$show_boot    = true;
$page_title   = 'MY ARCADE ZONE — El Salón Recreativo en Español';

// Últimas 6 reseñas publicadas
$ultimas = db()->query("
  SELECT slug, titulo, desarrollador, anno, genero, nota, imagen_cover
  FROM juegos
  WHERE publicada = 1
  ORDER BY fecha_publicacion DESC
  LIMIT 6
")->fetchAll();

// Total reseñas
$total_resenas = db()->query("SELECT COUNT(*) FROM juegos WHERE publicada = 1")->fetchColumn();

// Últimas 3 entradas del blog
$blog_reciente = db()->query("
  SELECT slug, titulo, categoria, fecha
  FROM blog_posts
  WHERE publicado = 1
  ORDER BY fecha DESC
  LIMIT 3
")->fetchAll();

require __DIR__ . '/includes/header.php';
?>

  <!-- BANNER VIDEO -->
  <div class="arcade-banner">
    <video class="arcade-banner-bg" autoplay loop muted playsinline
           style="width:100%;height:100%;object-fit:cover;object-position:center 30%;position:absolute;inset:0;z-index:0">
      <source src="/assets/images/arcade-hero.mp4" type="video/mp4">
    </video>
    <div class="arcade-banner-overlay"></div>
    <div class="arcade-banner-text">
      <div class="arcade-banner-title">EL SALÓN RECREATIVO<br><span>QUE SIEMPRE SOÑASTE</span></div>
    </div>
  </div>

  <div class="layout">
    <main>

      <!-- HERO -->
      <div class="hero">
        <div class="hero-tag">★ BIENVENIDA · PRESENTACIÓN</div>
        <div class="hero-body">
          <div class="hero-text">
            <div class="hero-title">EL SALÓN RECREATIVO<br><span>QUE NO PUDISTE CERRAR</span></div>
            <div class="hero-desc">
              Eran los 80 y los 90. Entrabas con un puñado de <strong>duros en el bolsillo</strong> y salías sin saber cómo había pasado el tiempo. Street Fighter, Final Fight, Out Run, RoboCop... Nadie te echaba. Nadie te decía que era tarde. <strong>Bienvenido de nuevo.</strong>
            </div>
            <a href="/resenas.php" class="btn-primary">► VER TODAS LAS RESEÑAS</a>
          </div>
          <div class="hero-ficha">
            <div class="hero-ficha-title">EL SALÓN</div>
            <div class="ficha-row"><span>Inaugurado</span><span>1978</span></div>
            <div class="ficha-row"><span>Moneda</span><span>25 ptas.</span></div>
            <div class="ficha-row"><span>Horario</span><span>Siempre abierto</span></div>
            <div class="ficha-row"><span>Máquinas</span><span>∞</span></div>
            <div class="ficha-row"><span>Idioma</span><span>Español</span></div>
          </div>
        </div>
      </div>

      <!-- STATS BAR -->
      <div class="stats-bar">
        <div class="stat-cell">
          <span class="stat-val"><?= $total_resenas ?: '—' ?></span>
          <span class="stat-lbl">Reseñas</span>
        </div>
        <div class="stat-cell"><span class="stat-val">MAME</span><span class="stat-lbl">Emulación</span></div>
        <div class="stat-cell"><span class="stat-val">OST</span><span class="stat-lbl">Bandas Sonoras</span></div>
        <div class="stat-cell"><span class="stat-val">1978</span><span class="stat-lbl">Primer juego</span></div>
        <div class="stat-cell"><span class="stat-val">1999</span><span class="stat-lbl">Último año</span></div>
      </div>

      <!-- ÚLTIMAS RESEÑAS -->
      <div class="home-section">
        <div class="section-hdr">
          <span class="section-hdr-title">🕹️ ÚLTIMAS RESEÑAS</span>
          <a href="/resenas.php" class="section-hdr-link">VER TODAS ►</a>
        </div>
        <div class="section-body">
          <div class="reviews-grid">
            <?php if ($ultimas): ?>
              <?php foreach ($ultimas as $i => $j): ?>
              <a href="/resena.php?slug=<?= urlencode($j['slug']) ?>" class="game-card"<?= $j['genero'] === 'Pinball' ? ' style="border-color:rgba(255,215,0,0.4)"' : '' ?>>
                <?php if ($j['genero'] === 'Pinball'): ?>
                <span class="badge-new" style="background:var(--amarillo);color:#000">🎰 PINBALL</span>
                <?php elseif ($i === 0): ?><span class="badge-new">NUEVA</span><?php endif; ?>
                <?php if ($j['imagen_cover']): ?>
                <img class="card-thumb" src="<?= htmlspecialchars($j['imagen_cover']) ?>" alt="<?= htmlspecialchars($j['titulo']) ?>">
                <?php else: ?>
                <div class="card-thumb-placeholder"><?= htmlspecialchars(strtoupper($j['titulo'])) ?></div>
                <?php endif; ?>
                <div class="card-body">
                  <div class="card-meta" style="margin-bottom:4px"><?= htmlspecialchars($j['anno'] . ' · ' . strtoupper($j['genero'])) ?></div>
                  <div class="card-title"><?= htmlspecialchars($j['titulo']) ?></div>
                  <div class="card-dev"><?= htmlspecialchars($j['desarrollador']) ?></div>
                  <div style="display:flex;justify-content:space-between;align-items:center;margin-top:6px">
                    <span class="card-cta">► LEER RESEÑA</span>
                    <span class="card-rating<?= $j['nota'] < 5 ? ' low' : '' ?>"><?= $j['nota'] ?></span>
                  </div>
                </div>
              </a>
              <?php endforeach; ?>
            <?php else: ?>
              <p style="font-family:'Share Tech Mono',monospace;font-size:11px;color:#444;padding:20px">
                > Próximamente las primeras reseñas...
              </p>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- BLOG + ACCESOS RÁPIDOS -->
      <div class="two-col">

        <div class="home-section" style="margin-bottom:0">
          <div class="section-hdr">
            <span class="section-hdr-title">📝 BLOG — MAQUINITAS & RETRO</span>
            <a href="/blog.php" class="section-hdr-link">VER TODO ►</a>
          </div>
          <div class="section-body">
            <?php if ($blog_reciente): ?>
              <?php foreach ($blog_reciente as $post): ?>
              <div class="blog-entry">
                <div class="blog-tag"><?= htmlspecialchars(strtoupper($post['categoria'])) ?></div>
                <a href="/blog.php?slug=<?= urlencode($post['slug']) ?>" class="blog-title">
                  <?= htmlspecialchars($post['titulo']) ?>
                </a>
              </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="blog-entry">
                <div class="blog-tag">PRÓXIMAMENTE</div>
                <span class="blog-title" style="cursor:default;color:#444">Artículos sobre maquinitas, salones recreativos y más...</span>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <div class="home-section" style="margin-bottom:0">
          <div class="section-hdr">
            <span class="section-hdr-title">⚡ ACCESOS RÁPIDOS</span>
          </div>
          <div class="section-body">
            <div class="qa-grid">
              <a href="/hardware.php" class="qa-btn">
                <span class="qa-icon">🕹️</span>
                <div><span class="qa-label">HARDWARE</span><span class="qa-desc">CPS-1, Neo Geo, JAMMA...</span></div>
              </a>
              <a href="/emulacion.php" class="qa-btn">
                <span class="qa-icon">💾</span>
                <div><span class="qa-label">EMULACIÓN</span><span class="qa-desc">MAME · RetroArch · FBA</span></div>
              </a>
              <a href="/retrocassete.php" class="qa-btn">
                <span class="qa-icon">🎵</span>
                <div><span class="qa-label">BANDAS SONORAS</span><span class="qa-desc">OST arcade originales</span></div>
              </a>
              <a href="/salon/" class="qa-btn">
                <span class="qa-icon">🏮</span>
                <div><span class="qa-label">SALÓN VIRTUAL</span><span class="qa-desc">Entra al recreativo</span></div>
              </a>
              <a href="/resenas.php" class="qa-btn">
                <span class="qa-icon">⭐</span>
                <div><span class="qa-label">RESEÑAS</span><span class="qa-desc">Análisis con sprites</span></div>
              </a>
              <a href="/blog.php" class="qa-btn">
                <span class="qa-icon">📱</span>
                <div><span class="qa-label">MAQUINITAS</span><span class="qa-desc">Guías de compra</span></div>
              </a>
            </div>
          </div>
        </div>

      </div>

    </main>

    <!-- SIDEBAR -->
    <aside class="sidebar">

      <div class="widget">
        <div class="widget-header">RESEÑAS PUBLICADAS</div>
        <div class="widget-body">
          <?php if ($ultimas): ?>
            <?php foreach ($ultimas as $j): ?>
            <div class="widget-game">
              <div>
                <a href="/resena.php?slug=<?= urlencode($j['slug']) ?>" class="wg-title">
                  <?= htmlspecialchars(strtoupper($j['titulo'])) ?>
                </a>
                <div class="wg-meta"><?= htmlspecialchars($j['anno'] . ' · ' . strtoupper($j['genero'])) ?> · <span>PUBLICADA</span></div>
              </div>
            </div>
            <?php endforeach; ?>
          <?php else: ?>
            <p style="font-family:'Share Tech Mono',monospace;font-size:10px;color:#333">Aún no hay reseñas publicadas.</p>
          <?php endif; ?>
        </div>
      </div>

      <div class="widget">
        <div class="widget-header">GÉNEROS</div>
        <div class="widget-body">
          <div class="genre-tags">
            <?php
            $generos = ['LUCHA','BEAT \'EM UP','SHOOTER','PLATAFORMAS','CARRERAS','PUZZLE','DEPORTES','ACCIÓN','RUN & GUN','RPG'];
            foreach ($generos as $g):
            ?>
            <a href="/resenas.php?genero=<?= urlencode($g) ?>" class="genre-tag"><?= htmlspecialchars($g) ?></a>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <div class="widget">
        <div class="widget-header">DATOS DEL ARCADE</div>
        <div class="widget-body">
          <div class="widget-stat-row"><span>Placa CPS-1</span><span>1988</span></div>
          <div class="widget-stat-row"><span>Placa CPS-2</span><span>1993</span></div>
          <div class="widget-stat-row"><span>Neo Geo MVS</span><span>1990</span></div>
          <div class="widget-stat-row"><span>JAMMA estándar</span><span>1985</span></div>
          <div class="widget-stat-row"><span>Moneda España</span><span>25 ptas.</span></div>
          <div class="widget-stat-row"><span>Edad dorada</span><span>1980–1996</span></div>
        </div>
      </div>

      <div class="widget">
        <div class="widget-header">RECOMENDACIONES Y CONTACTO</div>
        <div class="widget-body" style="padding:0">

          <div style="padding:10px 14px 6px;font-family:'Share Tech Mono',monospace;font-size:9px;letter-spacing:3px;color:var(--amarillo)">► WEBS AMIGAS</div>

          <a href="https://c64zone.neocities.org/" target="_blank" rel="noopener"
             style="display:flex;align-items:center;gap:10px;padding:10px 14px;text-decoration:none;border-top:1px solid rgba(0,238,255,0.06);transition:background .2s"
             onmouseover="this.style.background='rgba(0,238,255,0.04)'" onmouseout="this.style.background='transparent'">
            <div style="width:36px;height:36px;background:rgba(0,238,255,0.08);border:1px solid rgba(0,238,255,0.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:18px">💾</div>
            <div>
              <div style="font-family:'Bebas Neue',sans-serif;font-size:13px;letter-spacing:2px;color:var(--cyan)">C64 ZONE</div>
              <div style="font-family:'Share Tech Mono',monospace;font-size:9px;color:#444;margin-top:1px">El Universo Commodore 64 en Español</div>
            </div>
          </a>

          <div style="padding:10px 14px 6px;font-family:'Share Tech Mono',monospace;font-size:9px;letter-spacing:3px;color:var(--amarillo);border-top:1px solid rgba(0,238,255,0.06)">► RSS FEED</div>

          <a href="/rss.php" target="_blank" rel="noopener"
             style="display:flex;align-items:center;gap:10px;padding:10px 14px;text-decoration:none;border-top:1px solid rgba(0,238,255,0.06);transition:background .2s"
             onmouseover="this.style.background='rgba(255,102,0,0.04)'" onmouseout="this.style.background='transparent'">
            <div style="width:36px;height:36px;background:rgba(255,102,0,0.08);border:1px solid rgba(255,102,0,0.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:18px">📡</div>
            <div>
              <div style="font-family:'Bebas Neue',sans-serif;font-size:13px;letter-spacing:2px;color:#f60">SUSCRIBIRSE AL FEED</div>
              <div style="font-family:'Share Tech Mono',monospace;font-size:9px;color:#444;margin-top:1px">Reseñas + Blog · RSS 2.0</div>
            </div>
          </a>

        </div>
      </div>

    </aside>
  </div>

<?php require __DIR__ . '/includes/footer.php'; ?>
