<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/sprite.php';

$slug = trim($_GET['slug'] ?? '');
if (!$slug) { header('Location: /resenas.php'); exit; }

$db = db();

// Juego
$stmt = $db->prepare("SELECT * FROM juegos WHERE slug = ? AND publicada = 1");
$stmt->execute([$slug]);
$juego = $stmt->fetch();
if (!$juego) { http_response_code(404); include __DIR__ . '/404.php'; exit; }

// Secciones de texto
$secciones = $db->prepare("SELECT titulo_h2, contenido_html FROM secciones_resena WHERE juego_id = ? ORDER BY orden");
$secciones->execute([$juego['id']]);
$secciones = $secciones->fetchAll();

// Galería
$galeria = $db->prepare("SELECT imagen_url, caption FROM galeria WHERE juego_id = ? ORDER BY orden");
$galeria->execute([$juego['id']]);
$galeria = $galeria->fetchAll();

// Votos
$votos_data = $db->prepare("SELECT AVG(puntuacion) as media, COUNT(*) as total FROM votos WHERE juego_id = ?");
$votos_data->execute([$juego['id']]);
$votos_data = $votos_data->fetch();

// Comentarios
$comentarios = $db->prepare("
    SELECT nombre, texto, fecha FROM comentarios
    WHERE juego_id = ? AND aprobado = 1
    ORDER BY fecha DESC LIMIT 30
");
$comentarios->execute([$juego['id']]);
$comentarios = $comentarios->fetchAll();

// Anterior / siguiente reseña
$anterior = $db->prepare("
    SELECT slug, titulo FROM juegos WHERE publicada = 1 AND fecha_publicacion < ?
    ORDER BY fecha_publicacion DESC LIMIT 1
");
$anterior->execute([$juego['fecha_publicacion']]);
$anterior = $anterior->fetch();

$siguiente = $db->prepare("
    SELECT slug, titulo FROM juegos WHERE publicada = 1 AND fecha_publicacion > ?
    ORDER BY fecha_publicacion ASC LIMIT 1
");
$siguiente->execute([$juego['fecha_publicacion']]);
$siguiente = $siguiente->fetch();

// Personajes del juego
$chars_juego = $db->prepare("
    SELECT p.slug, p.nombre, p.sprite_url
    FROM personajes p
    INNER JOIN juego_personaje jp ON jp.personaje_id = p.id
    WHERE jp.juego_id = ?
    ORDER BY p.nombre
");
$chars_juego->execute([$juego['id']]);
$chars_juego = $chars_juego->fetchAll();

// Datos pinball si aplica
$pb = [];
if ($juego['genero'] === 'Pinball') {
    $pb_rows = $db->prepare("SELECT clave, valor FROM pinball_datos WHERE juego_id = ?");
    $pb_rows->execute([$juego['id']]);
    foreach ($pb_rows->fetchAll() as $r) $pb[$r['clave']] = $r['valor'];
}

$current_page     = 'resenas';
$page_title       = htmlspecialchars($juego['titulo']) . ' — Reseña · MY ARCADE ZONE';
$meta_description = $juego['descripcion_corta']
    ? strip_tags($juego['descripcion_corta'])
    : 'Reseña de ' . $juego['titulo'] . ' en MY ARCADE ZONE';
$og_type  = 'article';
$og_image = !empty($juego['imagen_url']) ? SITE_URL . $juego['imagen_url'] : SITE_URL . '/assets/images/og-default.jpg';

$json_ld = json_encode([
    '@context' => 'https://schema.org',
    '@type'    => 'Review',
    'name'     => $juego['titulo'],
    'reviewBody' => $meta_description,
    'reviewRating' => [
        '@type'       => 'Rating',
        'ratingValue' => $juego['nota'] ?? null,
        'bestRating'  => '10',
        'worstRating' => '0',
    ],
    'author' => ['@type' => 'Organization', 'name' => 'MY ARCADE ZONE'],
    'itemReviewed' => [
        '@type'       => 'VideoGame',
        'name'        => $juego['titulo'],
        'datePublished' => $juego['anno'] ?? null,
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

require __DIR__ . '/includes/header.php';
?>

  <div class="breadcrumb">
    <a href="/">INICIO</a><span>►</span>
    <a href="/resenas.php">RESEÑAS</a><span>►</span>
    <?php if ($juego['genero']): ?>
    <a href="/resenas.php?genero=<?= urlencode($juego['genero']) ?>"><?= htmlspecialchars(strtoupper($juego['genero'])) ?></a><span>►</span>
    <?php endif; ?>
    <span class="current"><?= htmlspecialchars(strtoupper($juego['titulo'])) ?></span>
  </div>

  <div class="layout">
    <main>

      <div class="article-card">
        <div class="article-header">
          <span>RESEÑA — <?= htmlspecialchars(strtoupper($juego['titulo'])) ?><?= $juego['anno'] ? ' (' . $juego['anno'] . ')' : '' ?></span>
          <div class="window-btns"><div class="btn-close"></div><div class="btn-min"></div><div class="btn-max"></div></div>
        </div>
        <div class="article-body">

          <?php if ($juego['badge_texto']): ?>
          <div class="game-badge badge-<?= htmlspecialchars($juego['badge_tipo'] ?: 'destacada') ?>">
            ★ <?= htmlspecialchars($juego['badge_texto']) ?>
          </div>
          <?php endif; ?>

          <h1 class="article-title"><?= htmlspecialchars($juego['titulo']) ?></h1>
          <?php if ($juego['descripcion_corta']): ?>
          <div class="article-subtitle"><?= htmlspecialchars($juego['descripcion_corta']) ?></div>
          <?php endif; ?>

          <div class="article-meta">
            <?php if ($juego['fecha_publicacion']): ?>
            <span>📅 <?= date('d/m/Y', strtotime($juego['fecha_publicacion'])) ?></span>
            <?php endif; ?>
            <span>✍ AUTOR: MYARCADEZONE</span>
            <?php if ($juego['genero']): ?>
            <span>🕹️ <?= htmlspecialchars(strtoupper($juego['genero'])) ?></span>
            <?php endif; ?>
          </div>

          <?php if ($juego['audio_url']): ?>
          <!-- MUSIC BANNER -->
          <div class="music-banner" id="music-banner">
            <div class="music-banner-left">
              <span class="music-banner-icon">🎵</span>
              <div>
                <span class="music-banner-title">BANDA SONORA DISPONIBLE</span>
                <span class="music-banner-sub"><?= htmlspecialchars($juego['audio_titulo'] ?: $juego['titulo']) ?></span>
              </div>
            </div>
            <div class="music-banner-right">
              <button class="music-banner-btn play" onclick="toggleMusic()">► REPRODUCIR</button>
              <button class="music-banner-btn close" onclick="document.getElementById('music-banner').classList.add('hidden')">✕</button>
            </div>
          </div>
          <?php endif; ?>

          <div class="article-text-wrap">

            <!-- INFOBOX -->
            <div class="game-infobox">
              <div class="infobox-header">FICHA TÉCNICA</div>
              <div class="infobox-body">

                <div class="game-cover">
                  <?php if ($juego['imagen_cover']): ?>
                  <img src="<?= htmlspecialchars($juego['imagen_cover']) ?>" alt="<?= htmlspecialchars($juego['titulo']) ?>">
                  <?php else: ?>
                  <span style="font-family:'Bebas Neue',sans-serif;font-size:11px;letter-spacing:2px;color:#333;text-align:center;padding:8px">
                    <?= htmlspecialchars(strtoupper($juego['titulo'])) ?>
                  </span>
                  <?php endif; ?>
                </div>

                <?php
                if ($juego['genero'] === 'Pinball') {
                    $ficha = [
                        'TÍTULO'      => $juego['titulo'],
                        'FABRICANTE'  => $pb['pb_fabricante'] ?? null,
                        'AÑO'         => $juego['anno'],
                        'SISTEMA'     => $pb['pb_sistema'] ?? null,
                        'BOLAS'       => $pb['pb_bolas'] ?? null,
                        'MULTIBOLA'   => !empty($pb['pb_multibola']) && $pb['pb_multibola'] === '1' ? 'SÍ' : null,
                        'UNIDADES'    => $pb['pb_unidades'] ?? null,
                        'PRECIO'      => $pb['pb_precio'] ?? null,
                        'EMULACIÓN'   => $pb['pb_emulacion'] ?? null,
                    ];
                } else {
                    $ficha = [
                        'TÍTULO'      => $juego['titulo'],
                        'DESARROLLADOR' => $juego['desarrollador'],
                        'PUBLISHER'   => $juego['publisher'],
                        'AÑO'         => $juego['anno'],
                        'GÉNERO'      => $juego['genero'],
                        'PLATAFORMA'  => $juego['plataforma_original'],
                        'OTROS SISTEMAS' => $juego['plataformas'],
                    ];
                }
                foreach ($ficha as $label => $valor):
                    if (!$valor) continue;
                ?>
                <div class="infobox-row">
                  <span class="infobox-label"><?= htmlspecialchars($label) ?></span>
                  <span class="infobox-value"><?= htmlspecialchars($valor) ?></span>
                </div>
                <?php endforeach; ?>

                <?php if ($juego['nota']): ?>
                <div class="rating-box">
                  <div class="rating-label">NOTA MYARCADEZONE</div>
                  <span class="rating-number"><?= $juego['nota'] ?></span>
                </div>
                <?php endif; ?>

                <?php if ($juego['audio_url']): ?>
                <div class="music-player">
                  <span class="music-player-label">🎵 BANDA SONORA</span>
                  <div class="music-visualizer" id="visualizer">
                    <span></span><span></span><span></span><span></span>
                    <span></span><span></span><span></span>
                  </div>
                  <div class="music-track"><?= htmlspecialchars($juego['audio_titulo'] ?: $juego['titulo']) ?></div>
                  <button class="music-btn" id="music-btn" onclick="toggleMusic()">► REPRODUCIR</button>
                </div>
                <?php endif; ?>

              </div>
            </div><!-- /infobox -->

            <!-- ARTICLE TEXT -->
            <div class="article-text">

              <?php foreach ($secciones as $i => $sec): ?>
              <?php if ($sec['titulo_h2']): ?>
              <h2>► <?= htmlspecialchars($sec['titulo_h2']) ?></h2>
              <?php endif; ?>
              <?= $sec['contenido_html'] /* HTML controlado por el admin */ ?>
              <?php if ($i === 0 && !empty($juego['video_youtube'])):
                  $yt_id = $juego['video_youtube'];
                  if (preg_match('/(?:v=|shorts\/|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $yt_id, $m)) $yt_id = $m[1];
              ?>
              <div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;margin:28px 0">
                <iframe src="https://www.youtube.com/embed/<?= htmlspecialchars($yt_id) ?>"
                        style="position:absolute;top:0;left:0;width:100%;height:100%;border:0"
                        allowfullscreen loading="lazy"></iframe>
              </div>
              <?php endif; ?>
              <?php endforeach; ?>

              <?php if ($galeria): ?>
              <h2>► GALERÍA</h2>
              <div class="gallery">
                <?php foreach ($galeria as $img): ?>
                <div class="gallery-item">
                  <img src="<?= htmlspecialchars($img['imagen_url']) ?>" alt="<?= htmlspecialchars($img['caption'] ?? '') ?>">
                  <?php if ($img['caption']): ?>
                  <div class="gallery-caption"><?= htmlspecialchars(strtoupper($img['caption'])) ?></div>
                  <?php endif; ?>
                </div>
                <?php endforeach; ?>
              </div>
              <?php endif; ?>

              <?php if ($juego['veredicto_texto'] || $juego['pros'] || $juego['contras']): ?>
              <div class="verdict-box">
                <div class="verdict-title">► VEREDICTO FINAL</div>
                <?php if ($juego['veredicto_texto']): ?>
                <p><?= nl2br(htmlspecialchars($juego['veredicto_texto'])) ?></p>
                <?php endif; ?>
                <?php if ($juego['pros'] || $juego['contras']): ?>
                <div class="verdict-grid">
                  <div class="verdict-pros">
                    <h4>LO MEJOR</h4>
                    <?php foreach (array_filter(explode("\n", $juego['pros'] ?? '')) as $pro): ?>
                    + <?= htmlspecialchars(trim($pro)) ?><br>
                    <?php endforeach; ?>
                  </div>
                  <div class="verdict-cons">
                    <h4>LO PEOR</h4>
                    <?php foreach (array_filter(explode("\n", $juego['contras'] ?? '')) as $con): ?>
                    - <?= htmlspecialchars(trim($con)) ?><br>
                    <?php endforeach; ?>
                  </div>
                </div>
                <?php endif; ?>
              </div>
              <?php endif; ?>

              <?php if ($juego['links_html']): ?>
              <div class="links-section">
                <h3>🔗 DÓNDE ENCONTRAR MÁS INFO</h3>
                <?= $juego['links_html'] ?>
              </div>
              <?php endif; ?>

              <!-- PERSONAJES -->
              <?php if ($chars_juego): ?>
              <div class="chars-resena-block">
                <div class="chars-resena-hdr">
                  <span>👾 PERSONAJES</span>
                  <a href="/personajes.php?juego=<?= urlencode($juego['slug']) ?>"
                     class="chars-resena-all">VER TODOS ►</a>
                </div>
                <div class="chars-resena-grid">
                  <?php foreach ($chars_juego as $ch): ?>
                  <a href="/personaje.php?slug=<?= urlencode($ch['slug']) ?>" class="char-mini">
                    <?php if ($ch['sprite_url']): ?>
                    <?= render_sprite($ch['sprite_url'], $ch['nombre'], 'char-mini-sprite') ?>
                    <?php else: ?>
                    <div class="char-mini-ph">?</div>
                    <?php endif; ?>
                    <div class="char-mini-name"><?= htmlspecialchars(strtoupper($ch['nombre'])) ?></div>
                  </a>
                  <?php endforeach; ?>
                </div>
              </div>
              <?php endif; ?>

              <!-- COMUNIDAD -->
              <div class="community-section">

                <div class="community-widget">
                  <div class="community-header">★ PUNTÚA ESTE JUEGO</div>
                  <div class="community-body">
                    <p class="vote-prompt">¿Cuánto le das a <?= htmlspecialchars($juego['titulo']) ?>?</p>
                    <div class="stars-row" id="stars-row">
                      <?php for ($i = 1; $i <= 10; $i++): ?>
                      <span class="star" data-v="<?= $i ?>">★</span>
                      <?php endfor; ?>
                    </div>
                    <div class="vote-result" id="vote-result">
                      <?php if ($votos_data['total'] > 0): ?>
                      Puntuación de la comunidad: <span><?= number_format($votos_data['media'], 1) ?>/10</span>
                      · <span><?= $votos_data['total'] ?> voto<?= $votos_data['total'] !== '1' ? 's' : '' ?></span>
                      <?php else: ?>
                      Sé el primero en votar.
                      <?php endif; ?>
                    </div>
                  </div>
                </div>

                <div class="community-widget" style="margin-top:16px">
                  <div class="community-header">💬 COMENTARIOS DE LA COMUNIDAD</div>
                  <div class="community-body">
                    <div class="comment-form">
                      <input type="text" id="comment-name" placeholder="TU NOMBRE (ej: PLAYER_1)" maxlength="30">
                      <textarea id="comment-text" placeholder="¿Qué recuerdas de este juego?" maxlength="500" rows="3"></textarea>
                      <button class="comment-submit" id="comment-btn" onclick="submitComment()">► ENVIAR COMENTARIO</button>
                    </div>
                    <div id="comments-list">
                      <?php if ($comentarios): ?>
                        <?php foreach ($comentarios as $c): ?>
                        <div class="comment-item">
                          <div class="comment-meta">
                            <?= htmlspecialchars($c['nombre']) ?>
                            <time><?= date('d/m/Y', strtotime($c['fecha'])) ?></time>
                          </div>
                          <div class="comment-text"><?= nl2br(htmlspecialchars($c['texto'])) ?></div>
                        </div>
                        <?php endforeach; ?>
                      <?php else: ?>
                        <div class="no-comments">Sé el primero en comentar.</div>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>

              </div>

              <div class="review-nav">
                <?php if ($anterior): ?>
                <a href="/resena.php?slug=<?= urlencode($anterior['slug']) ?>">◄ <?= htmlspecialchars(strtoupper($anterior['titulo'])) ?></a>
                <?php else: ?>
                <a href="/resenas.php">◄ TODAS LAS RESEÑAS</a>
                <?php endif; ?>
                <?php if ($siguiente): ?>
                <a href="/resena.php?slug=<?= urlencode($siguiente['slug']) ?>"><?= htmlspecialchars(strtoupper($siguiente['titulo'])) ?> ►</a>
                <?php endif; ?>
              </div>

            </div><!-- /article-text -->
          </div><!-- /article-text-wrap -->
        </div><!-- /article-body -->
      </div><!-- /article-card -->

    </main>

    <!-- SIDEBAR -->
    <aside class="sidebar">
      <div class="widget">
        <div class="widget-header">MÁS RESEÑAS</div>
        <div class="widget-body">
          <?php
          $otras = $db->prepare("
              SELECT slug, titulo, anno, genero FROM juegos
              WHERE publicada = 1 AND id != ?
              ORDER BY fecha_publicacion DESC LIMIT 6
          ");
          $otras->execute([$juego['id']]);
          foreach ($otras->fetchAll() as $o):
          ?>
          <div class="widget-game">
            <div>
              <a href="/resena.php?slug=<?= urlencode($o['slug']) ?>" class="wg-title"><?= htmlspecialchars(strtoupper($o['titulo'])) ?></a>
              <div class="wg-meta"><?= htmlspecialchars($o['anno'] . ' · ' . strtoupper($o['genero'])) ?></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="widget">
        <div class="widget-header">GÉNEROS</div>
        <div class="widget-body">
          <div class="genre-tags">
            <?php
            $generos = ['LUCHA','BEAT \'EM UP','SHOOTER','PLATAFORMAS','CARRERAS','PUZZLE','ACCIÓN'];
            foreach ($generos as $g):
            ?>
            <a href="/resenas.php?genero=<?= urlencode($g) ?>" class="genre-tag"><?= htmlspecialchars($g) ?></a>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </aside>
  </div>

<?php if ($juego['audio_url']): ?>
<audio id="bgm" preload="auto" loop>
  <source src="<?= htmlspecialchars($juego['audio_url']) ?>" type="audio/mpeg">
</audio>
<?php endif; ?>

<?php
$extra_js = "
const juego_id = {$juego['id']};

" . ($juego['audio_url'] ? "
/* ── MÚSICA ── */
const audio     = document.getElementById('bgm');
const musicBtn  = document.getElementById('music-btn');
const viz       = document.getElementById('visualizer');
const bannerBtn = document.querySelector('.music-banner-btn.play');

function setPlaying() {
  if (musicBtn)  { musicBtn.textContent = '■ DETENER'; musicBtn.classList.add('playing'); }
  if (viz)       viz.classList.add('active');
  if (bannerBtn) { bannerBtn.textContent = '■ DETENER'; bannerBtn.classList.add('playing'); }
}
function setStopped() {
  if (musicBtn)  { musicBtn.textContent = '► REPRODUCIR'; musicBtn.classList.remove('playing'); }
  if (viz)       viz.classList.remove('active');
  if (bannerBtn) { bannerBtn.textContent = '► REPRODUCIR'; bannerBtn.classList.remove('playing'); }
}
function toggleMusic() {
  if (!audio) return;
  if (audio.paused) {
    audio.play().then(setPlaying).catch(() => {
      if (musicBtn) musicBtn.textContent = '✕ NO ENCONTRADO';
    });
  } else {
    audio.pause();
    audio.currentTime = 0;
    setStopped();
  }
}
audio.addEventListener('ended', setStopped);
" : '') . "

/* ── VOTOS ── */
const stars = document.querySelectorAll('.star');
stars.forEach(star => {
  star.addEventListener('mouseover', () => {
    const v = parseInt(star.dataset.v);
    stars.forEach(s => s.classList.toggle('hovered', parseInt(s.dataset.v) <= v));
  });
  star.addEventListener('mouseleave', () => stars.forEach(s => s.classList.remove('hovered')));
  star.addEventListener('click', async () => {
    const v = parseInt(star.dataset.v);
    stars.forEach(s => s.classList.toggle('selected', parseInt(s.dataset.v) <= v));
    document.getElementById('vote-result').textContent = 'Enviando voto...';
    await fetch('/api/votos.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({juego_id, puntuacion: v})
    });
    const res = await fetch('/api/votos.php?juego_id=' + juego_id);
    const data = await res.json();
    const el = document.getElementById('vote-result');
    if (data.total > 0) {
      el.innerHTML = 'Puntuación de la comunidad: <span>' + data.media + '/10</span> · <span>' + data.total + ' votos</span>';
    } else {
      el.textContent = 'Sé el primero en votar.';
    }
  });
});

/* ── COMENTARIOS ── */
async function submitComment() {
  const nombre = document.getElementById('comment-name').value.trim();
  const texto  = document.getElementById('comment-text').value.trim();
  if (!nombre || !texto) { alert('Rellena tu nombre y el comentario.'); return; }
  const btn = document.getElementById('comment-btn');
  btn.textContent = '... ENVIANDO'; btn.disabled = true;
  const res = await fetch('/api/comentarios.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({juego_id, nombre, texto})
  });
  const data = await res.json();
  if (data.ok) {
    document.getElementById('comment-name').value = '';
    document.getElementById('comment-text').value = '';
    const list = document.getElementById('comments-list');
    const div = document.createElement('div');
    div.className = 'comment-item';
    div.innerHTML = '<div class=\"comment-meta\">' + nombre.toUpperCase() + '<time>Ahora</time></div>' +
                    '<div class=\"comment-text\">' + texto.replace(/</g,'&lt;') + '</div>';
    list.prepend(div);
    const nc = list.querySelector('.no-comments');
    if (nc) nc.remove();
  }
  btn.textContent = '► ENVIAR COMENTARIO'; btn.disabled = false;
}
";
require __DIR__ . '/includes/footer.php';
?>
