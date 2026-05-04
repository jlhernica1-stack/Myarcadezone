<?php
require_once __DIR__ . '/includes/db.php';

$current_page = 'resenas';
$page_title   = 'Reseñas — MY ARCADE ZONE';

$genero_filtro = trim($_GET['genero'] ?? '');

if ($genero_filtro) {
    $stmt = db()->prepare("
        SELECT slug, titulo, desarrollador, publisher, anno, genero, nota, badge_tipo, badge_texto, imagen_cover, fecha_publicacion
        FROM juegos
        WHERE publicada = 1 AND genero = ?
        ORDER BY fecha_publicacion DESC
    ");
    $stmt->execute([$genero_filtro]);
} else {
    $stmt = db()->query("
        SELECT slug, titulo, desarrollador, publisher, anno, genero, nota, badge_tipo, badge_texto, imagen_cover, fecha_publicacion
        FROM juegos
        WHERE publicada = 1
        ORDER BY fecha_publicacion DESC
    ");
}
$juegos = $stmt->fetchAll();

// Géneros disponibles para el filtro
$generos_db = db()->query("
    SELECT DISTINCT genero FROM juegos WHERE publicada = 1 AND genero IS NOT NULL ORDER BY genero
")->fetchAll(PDO::FETCH_COLUMN);

require __DIR__ . '/includes/header.php';
?>

  <div class="layout">
    <main>

      <div class="home-section">
        <div class="section-hdr">
          <span class="section-hdr-title">🕹️ RESEÑAS</span>
          <span style="font-family:'Share Tech Mono',monospace;font-size:10px;color:#444">
            <?= count($juegos) ?> RESEÑA<?= count($juegos) !== 1 ? 'S' : '' ?>
            <?= $genero_filtro ? ' · ' . htmlspecialchars(strtoupper($genero_filtro)) : '' ?>
          </span>
        </div>

        <!-- Filtro por género -->
        <?php if ($generos_db): ?>
        <div style="padding:12px 20px;border-bottom:1px solid rgba(0,238,255,0.08);display:flex;flex-wrap:wrap;gap:6px">
          <a href="/resenas.php" class="genre-tag<?= !$genero_filtro ? ' active-filter' : '' ?>"
             style="<?= !$genero_filtro ? 'background:var(--cyan);color:#000;border-color:var(--cyan)' : '' ?>">TODOS</a>
          <?php foreach ($generos_db as $g): ?>
          <a href="/resenas.php?genero=<?= urlencode($g) ?>" class="genre-tag"
             style="<?= $genero_filtro === $g ? 'background:var(--cyan);color:#000;border-color:var(--cyan)' : '' ?>">
            <?= htmlspecialchars(strtoupper($g)) ?>
          </a>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="section-body">
          <div class="reviews-grid">
            <?php if ($juegos): ?>
              <?php foreach ($juegos as $j): ?>
              <a href="/resena.php?slug=<?= urlencode($j['slug']) ?>" class="game-card"<?= $j['genero'] === 'Pinball' ? ' style="border-color:rgba(255,215,0,0.4)"' : '' ?>>
                <?php if ($j['genero'] === 'Pinball'): ?>
                <span class="badge-new" style="background:var(--amarillo);color:#000">🎰 PINBALL</span>
                <?php elseif ($j['badge_texto']): ?>
                <span class="badge-new"><?= htmlspecialchars($j['badge_texto']) ?></span>
                <?php endif; ?>
                <?php if ($j['imagen_cover']): ?>
                <img class="card-thumb" src="<?= htmlspecialchars($j['imagen_cover']) ?>" alt="<?= htmlspecialchars($j['titulo']) ?>">
                <?php else: ?>
                <div class="card-thumb-placeholder"><?= htmlspecialchars(strtoupper($j['titulo'])) ?></div>
                <?php endif; ?>
                <div class="card-body">
                  <div class="card-meta" style="margin-bottom:4px"><?= htmlspecialchars($j['anno'] . ' · ' . strtoupper($j['genero'])) ?></div>
                  <div class="card-title"><?= htmlspecialchars($j['titulo']) ?></div>
                  <div class="card-dev"><?= htmlspecialchars($j['desarrollador'] ?? '') ?></div>
                  <div style="display:flex;justify-content:space-between;align-items:center;margin-top:6px">
                    <span class="card-cta">► LEER RESEÑA</span>
                    <?php if ($j['nota']): ?>
                    <span class="card-rating<?= $j['nota'] < 5 ? ' low' : '' ?>"><?= $j['nota'] ?></span>
                    <?php endif; ?>
                  </div>
                </div>
              </a>
              <?php endforeach; ?>
            <?php else: ?>
              <p style="font-family:'Share Tech Mono',monospace;font-size:11px;color:#444;padding:30px;grid-column:1/-1">
                > No hay reseñas publicadas<?= $genero_filtro ? ' para este género' : '' ?> todavía.
              </p>
            <?php endif; ?>
          </div>
        </div>
      </div>

    </main>

    <!-- SIDEBAR -->
    <aside class="sidebar">
      <div class="widget">
        <div class="widget-header">GÉNEROS</div>
        <div class="widget-body">
          <div class="genre-tags">
            <?php
            $todos_generos = ['LUCHA','BEAT \'EM UP','SHOOTER','PLATAFORMAS','CARRERAS','PUZZLE','DEPORTES','ACCIÓN','RUN & GUN','RPG','PINBALL'];
            foreach ($todos_generos as $g):
            ?>
            <a href="/resenas.php?genero=<?= urlencode($g) ?>" class="genre-tag"><?= htmlspecialchars($g) ?></a>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </aside>
  </div>

<?php require __DIR__ . '/includes/footer.php'; ?>
