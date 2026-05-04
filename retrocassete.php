<?php
require_once __DIR__ . '/includes/db.php';

$current_page = 'retrocassete';
$page_title   = 'Retrocassete — Bandas Sonoras Arcade · MY ARCADE ZONE';

$tracks = db()->query("
    SELECT id, titulo, juego, compositor, url_audio, orden
    FROM retrocassete_tracks
    WHERE publicado = 1
    ORDER BY orden ASC
")->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<div class="layout">
  <main>

    <div class="home-section">
      <div class="section-hdr" style="flex-direction:column;align-items:flex-start;gap:4px;padding:16px 20px">
        <div style="display:flex;align-items:center;gap:12px;width:100%">
          <span class="section-hdr-title" style="font-size:16px">🎵 RETROCASSETE</span>
          <span style="font-family:'Share Tech Mono',monospace;font-size:9px;color:#444;margin-left:auto">// BANDAS SONORAS ARCADE · PRESS PLAY</span>
        </div>
        <div style="font-family:'Share Tech Mono',monospace;font-size:10px;color:#333">
          MÚSICA ORIGINAL DE RECREATIVAS · ENLAZADA DESDE ARCHIVE.ORG
        </div>
      </div>

      <div class="section-body" style="padding:0">

        <!-- MARQUEE -->
        <div class="rc-marquee">
          <div class="rc-marquee-inner">
            ♫ RETROCASSETE · BANDAS SONORAS DE RECREATIVAS &nbsp;&nbsp;
            ► PRESS PLAY ON COIN &nbsp;&nbsp;
            ♫ STREET FIGHTER II · FINAL FIGHT · OUT RUN · MORTAL KOMBAT · METAL SLUG ♫ &nbsp;&nbsp;
            TODOS LOS AUDIOS ENLAZAN A ARCHIVE.ORG · NINGÚN AUDIO SE ALOJA EN ESTE SERVIDOR &nbsp;&nbsp;
            ♫ INSERT COIN TO LISTEN ♫ &nbsp;&nbsp;
          </div>
        </div>

        <!-- PLAYER STAGE: vúmetro | cassette | vúmetro -->
        <div class="rc-stage">

          <!-- VÚMETRO IZQUIERDO -->
          <div class="rc-vumetro-wrap">
            <canvas id="vuLeft" class="rc-vumetro"></canvas>
            <div class="rc-vumetro-label">L</div>
          </div>

          <!-- CASSETTE + CONTROLES -->
          <div class="rc-player-center">

            <svg class="rc-cassette-svg" viewBox="0 0 340 180" xmlns="http://www.w3.org/2000/svg">
              <!-- Cuerpo -->
              <rect x="10" y="20" width="320" height="140" rx="12" fill="#080818" stroke="#00eeff" stroke-width="2.5"/>
              <!-- Ventana de cinta -->
              <rect x="80" y="45" width="180" height="80" rx="6" fill="#000010" stroke="#440088" stroke-width="2"/>
              <!-- Cinta entre bobinas -->
              <rect x="86" y="115" width="168" height="5" fill="#080818"/>
              <!-- Bobina izquierda -->
              <circle cx="120" cy="87" r="28" fill="#000020" stroke="#00eeff" stroke-width="2"/>
              <circle cx="120" cy="87" r="12" fill="#080818" stroke="#440088" stroke-width="1.5"/>
              <g id="reelL">
                <line x1="120" y1="87" x2="120" y2="60" stroke="#00eeff" stroke-width="1.5" opacity="0.7"/>
                <line x1="120" y1="87" x2="143" y2="100" stroke="#00eeff" stroke-width="1.5" opacity="0.7"/>
                <line x1="120" y1="87" x2="97"  y2="100" stroke="#00eeff" stroke-width="1.5" opacity="0.7"/>
              </g>
              <!-- Bobina derecha -->
              <circle cx="220" cy="87" r="28" fill="#000020" stroke="#00eeff" stroke-width="2"/>
              <circle cx="220" cy="87" r="12" fill="#080818" stroke="#440088" stroke-width="1.5"/>
              <g id="reelR">
                <line x1="220" y1="87" x2="220" y2="60" stroke="#00eeff" stroke-width="1.5" opacity="0.7"/>
                <line x1="220" y1="87" x2="243" y2="100" stroke="#00eeff" stroke-width="1.5" opacity="0.7"/>
                <line x1="220" y1="87" x2="197" y2="100" stroke="#00eeff" stroke-width="1.5" opacity="0.7"/>
              </g>
              <!-- Cabezal -->
              <rect id="playhead" x="162" y="110" width="16" height="12" rx="2" fill="#440088" stroke="#aa00ff" stroke-width="1.5"/>
              <!-- Tornillos esquinas -->
              <circle cx="28"  cy="38"  r="5" fill="#000020" stroke="#333366" stroke-width="1.5"/>
              <circle cx="312" cy="38"  r="5" fill="#000020" stroke="#333366" stroke-width="1.5"/>
              <circle cx="28"  cy="152" r="5" fill="#000020" stroke="#333366" stroke-width="1.5"/>
              <circle cx="312" cy="152" r="5" fill="#000020" stroke="#333366" stroke-width="1.5"/>
              <!-- Etiqueta -->
              <rect x="100" y="28" width="140" height="30" rx="3" fill="#0a0020" stroke="#440088" stroke-width="1.5"/>
              <text x="170" y="40" text-anchor="middle" font-family="'Bebas Neue',sans-serif" font-size="9" fill="#ffd700" letter-spacing="3">MY ARCADE ZONE</text>
              <text x="170" y="52" text-anchor="middle" font-family="'Share Tech Mono',monospace" font-size="8" fill="#00eeff">RETROCASSETE VOL.1</text>
              <!-- Agujeros de arrastre -->
              <rect x="10"  y="140" width="25" height="20" rx="3" fill="#000010" stroke="#222244" stroke-width="1"/>
              <rect x="305" y="140" width="25" height="20" rx="3" fill="#000010" stroke="#222244" stroke-width="1"/>
            </svg>

            <!-- NOW PLAYING -->
            <div class="rc-now-playing" id="nowPlaying">
              SELECCIONA UNA PISTA<br>
              <span>▼ INSERT COIN TO LISTEN ▼</span>
            </div>

            <!-- BARRA DE PROGRESO -->
            <div class="rc-progress-wrap">
              <div class="rc-progress" id="progressBar">
                <div class="rc-progress-fill" id="progressFill"></div>
              </div>
              <span class="rc-time" id="timeDisplay">0:00 / 0:00</span>
            </div>

            <!-- CONTROLES -->
            <div class="rc-controls">
              <button class="rc-btn" id="prevBtn">◄◄</button>
              <button class="rc-btn rc-btn-play" id="playBtn">► PLAY</button>
              <button class="rc-btn" id="stopBtn">■ STOP</button>
              <button class="rc-btn" id="nextBtn">►►</button>
            </div>

          </div><!-- /rc-player-center -->

          <!-- VÚMETRO DERECHO -->
          <div class="rc-vumetro-wrap">
            <canvas id="vuRight" class="rc-vumetro"></canvas>
            <div class="rc-vumetro-label">R</div>
          </div>

        </div><!-- /rc-stage -->

        <!-- LISTA DE PISTAS -->
        <?php if ($tracks): ?>
        <div class="rc-tracklist" id="trackList">
          <?php foreach ($tracks as $i => $t): ?>
          <div class="rc-track-item" id="track-<?= $i ?>"
               data-index="<?= $i ?>"
               data-url="<?= htmlspecialchars($t['url_audio']) ?>"
               data-title="<?= htmlspecialchars($t['titulo']) ?>"
               data-juego="<?= htmlspecialchars($t['juego']) ?>">
            <span class="rc-track-num"><?= str_pad($i + 1, 2, '0', STR_PAD_LEFT) ?></span>
            <div class="rc-track-info">
              <div class="rc-track-title"><?= htmlspecialchars($t['titulo']) ?></div>
              <div class="rc-track-meta"><?= htmlspecialchars($t['juego']) ?><?= $t['compositor'] ? ' · ' . htmlspecialchars($t['compositor']) : '' ?></div>
            </div>
            <div style="display:flex;gap:4px">
              <button class="rc-track-play-btn" onclick="event.stopPropagation();loadTrack(<?= $i ?>,true)">►</button>
              <button class="rc-track-stop-btn" onclick="event.stopPropagation();if(currentIndex===<?= $i ?>)document.getElementById('stopBtn').click()">■</button>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div style="text-align:center;padding:40px;font-family:'Share Tech Mono',monospace;font-size:11px;color:#333">
          > Próximamente las primeras pistas...
        </div>
        <?php endif; ?>

        <!-- AVISO LEGAL -->
        <div style="font-family:'Share Tech Mono',monospace;font-size:9px;color:#333;text-align:center;padding:16px 20px;border-top:1px solid rgba(0,238,255,0.05)">
          ℹ NINGÚN AUDIO ESTÁ ALOJADO EN ESTE SERVIDOR — TODOS LOS ENLACES APUNTAN A ARCHIVE.ORG — REPRODUCCIÓN BAJO LICENCIA LIBRE O DOMINIO PÚBLICO
        </div>

      </div>
    </div>

  </main>

  <aside class="sidebar">

    <div class="widget">
      <div class="widget-header">SOBRE RETROCASSETE</div>
      <div class="widget-body">
        <p style="font-family:'Share Tech Mono',monospace;font-size:10px;color:#555;line-height:1.7">
          Bandas sonoras de recreativas clásicas. Música original de arcade en streaming directo desde Archive.org. Ningún audio se aloja en este servidor.
        </p>
      </div>
    </div>

    <div class="widget">
      <div class="widget-header">ESTADÍSTICAS</div>
      <div class="widget-body">
        <div class="widget-stat-row"><span>Pistas</span><span id="statTracks" style="color:var(--amarillo)"><?= count($tracks) ?></span></div>
        <div class="widget-stat-row"><span>Fuente</span><span>ARCHIVE.ORG</span></div>
        <div class="widget-stat-row"><span>Formato</span><span>MP3</span></div>
      </div>
    </div>

    <div class="widget">
      <div class="widget-header">JUEGOS CON OST</div>
      <div class="widget-body">
        <?php foreach ($tracks as $t): ?>
        <div class="widget-stat-row" style="flex-direction:column;align-items:flex-start;gap:2px;padding:6px 0">
          <span style="color:var(--blanco);font-size:10px"><?= htmlspecialchars($t['titulo']) ?></span>
          <span style="font-size:9px;color:#444"><?= htmlspecialchars($t['juego']) ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="widget">
      <div class="widget-header">VER TAMBIÉN</div>
      <div class="widget-body">
        <div style="margin-bottom:8px">
          <a href="/resenas.php" class="admin-btn admin-btn-primary" style="display:block;text-align:center;font-size:11px">
            🕹️ RESEÑAS DE JUEGOS
          </a>
        </div>
        <div>
          <a href="/hardware.php" class="admin-btn" style="display:block;text-align:center;font-size:11px;color:var(--cyan);border-color:var(--cyan)">
            💾 HARDWARE ARCADE
          </a>
        </div>
      </div>
    </div>

  </aside>
</div>

<!-- AUDIO ELEMENT -->
<audio id="player" preload="none" crossorigin="anonymous"></audio>

<script>
/* ── DATOS DE PISTAS ─────────────────────────────────────── */
const tracks = <?= json_encode(array_values(array_map(function($t, $i) {
    return ['index' => $i, 'titulo' => $t['titulo'], 'juego' => $t['juego'], 'compositor' => $t['compositor'] ?? '', 'url' => $t['url_audio']];
}, $tracks, array_keys($tracks))), JSON_UNESCAPED_UNICODE) ?>;

/* ── DOM ─────────────────────────────────────────────────── */
const audio        = document.getElementById('player');
const playBtn      = document.getElementById('playBtn');
const stopBtn      = document.getElementById('stopBtn');
const prevBtn      = document.getElementById('prevBtn');
const nextBtn      = document.getElementById('nextBtn');
const progressFill = document.getElementById('progressFill');
const progressBar  = document.getElementById('progressBar');
const timeDisplay  = document.getElementById('timeDisplay');
const nowPlaying   = document.getElementById('nowPlaying');
const reelL        = document.getElementById('reelL');
const reelR        = document.getElementById('reelR');
const playhead     = document.getElementById('playhead');
const cvL          = document.getElementById('vuLeft');
const cvR          = document.getElementById('vuRight');
const ctxL         = cvL.getContext('2d');
const ctxR         = cvR.getContext('2d');

let currentIndex = -1, isPlaying = false;
let reelAngle = 0, reelRaf = null;
let audioCtx = null, analyser = null, dataArray = null, audioConnected = false;
let simLevel = 0, simTarget = 0, simRaf = null;
let vuSmooth = 0, vuPeak = 0, vuPeakTimer = 0;

/* ── WEB AUDIO API ───────────────────────────────────────── */
function initAudio() {
  if (audioConnected) return;
  try {
    audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    analyser = audioCtx.createAnalyser();
    analyser.fftSize = 256;
    analyser.smoothingTimeConstant = 0.8;
    const src = audioCtx.createMediaElementSource(audio);
    src.connect(analyser);
    analyser.connect(audioCtx.destination);
    dataArray = new Uint8Array(analyser.frequencyBinCount);
    audioConnected = true;
  } catch(e) {
    audioConnected = false;
  }
}

function getLevel() {
  let raw = 0;
  if (audioConnected && analyser && isPlaying) {
    analyser.getByteFrequencyData(dataArray);
    /* Foco en frecuencias medias (índices 4-60) donde vive la música */
    let sum = 0, count = 0;
    for (let i = 4; i < Math.min(60, dataArray.length); i++) {
      sum += dataArray[i];
      count++;
    }
    raw = (sum / (count * 255)) * 1.8; /* escalar moderado */
  } else if (isPlaying) {
    /* Simulación con dinámica de ataque/caída real */
    if (Math.random() < 0.07) simTarget = 0.25 + Math.random() * 0.35;
    if (Math.random() < 0.03) simTarget = 0.55 + Math.random() * 0.20; /* picos ocasionales */
    simLevel += (simTarget - simLevel) * (simLevel < simTarget ? 0.25 : 0.08); /* ataque rápido, caída lenta */
    simTarget *= 0.96;
    raw = Math.max(0, simLevel);
  } else {
    simLevel *= 0.80;
    raw = simLevel;
  }
  /* Suavizado con inercia analógica */
  const attack  = raw > vuSmooth ? 0.35 : 0.10;
  vuSmooth += (Math.min(raw, 0.72) - vuSmooth) * attack;
  return Math.max(0, vuSmooth);
}

/* ── VÚMETROS ────────────────────────────────────────────── */
function drawVU(ctx, level, offset) {
  const w = ctx.canvas.width, h = ctx.canvas.height;
  ctx.clearRect(0, 0, w, h);

  const cx = w / 2, cy = h * 0.88;
  const r  = Math.min(w, h) * 0.72;

  /* Arco de fondo oscuro */
  ctx.beginPath();
  ctx.arc(cx, cy, r, Math.PI, 2 * Math.PI);
  ctx.strokeStyle = 'rgba(0,0,0,0.8)';
  ctx.lineWidth = r * 0.18;
  ctx.stroke();

  /* Zonas de color del arco */
  const zones = [
    { from: Math.PI,        to: Math.PI * 1.45, color: '#00cc44' },
    { from: Math.PI * 1.45, to: Math.PI * 1.72, color: '#aacc00' },
    { from: Math.PI * 1.72, to: Math.PI * 1.88, color: '#ffaa00' },
    { from: Math.PI * 1.88, to: Math.PI * 2,    color: '#ff2200' },
  ];
  zones.forEach(z => {
    ctx.beginPath();
    ctx.arc(cx, cy, r, z.from, z.to);
    ctx.strokeStyle = z.color;
    ctx.lineWidth = r * 0.14;
    ctx.globalAlpha = 0.35;
    ctx.stroke();
    ctx.globalAlpha = 1;
  });

  /* Marcas de escala */
  const ticks = [
    { a: Math.PI,        label: '-20' },
    { a: Math.PI * 1.2,  label: '-10' },
    { a: Math.PI * 1.4,  label: '-5' },
    { a: Math.PI * 1.6,  label: '0' },
    { a: Math.PI * 1.75, label: '+3' },
    { a: Math.PI * 1.88, label: '+5' },
    { a: Math.PI * 2,    label: '+7' },
  ];
  ctx.font = `bold ${Math.max(8, w * 0.09)}px 'Share Tech Mono', monospace`;
  ctx.textAlign = 'center';
  ticks.forEach(t => {
    const cos = Math.cos(t.a), sin = Math.sin(t.a);
    const ir = r * 0.55, or2 = r * 0.72;
    ctx.beginPath();
    ctx.moveTo(cx + cos * ir, cy + sin * ir);
    ctx.lineTo(cx + cos * or2, cy + sin * or2);
    ctx.strokeStyle = 'rgba(0,238,255,0.4)';
    ctx.lineWidth = 1;
    ctx.stroke();
    ctx.fillStyle = 'rgba(0,238,255,0.5)';
    ctx.fillText(t.label, cx + cos * (ir - r * 0.14), cy + sin * (ir - r * 0.14) + 3);
  });

  /* Aguja */
  /* level 0→1 mapea a PI→2PI */
  const needleVariance = (Math.random() - 0.5) * 0.10 * (isPlaying ? 1 : 0);
  const needleAngle = Math.PI + (Math.min(level + needleVariance, 1)) * Math.PI;
  const nLen = r * 0.78;
  const nx = cx + Math.cos(needleAngle) * nLen;
  const ny = cy + Math.sin(needleAngle) * nLen;

  /* Sombra aguja */
  ctx.shadowColor = '#00eeff';
  ctx.shadowBlur  = 6;
  ctx.beginPath();
  ctx.moveTo(cx, cy);
  ctx.lineTo(nx, ny);
  ctx.strokeStyle = '#ffffff';
  ctx.lineWidth   = 1.5;
  ctx.stroke();
  ctx.shadowBlur = 0;

  /* Pivote */
  ctx.beginPath();
  ctx.arc(cx, cy, r * 0.055, 0, Math.PI * 2);
  ctx.fillStyle = '#00eeff';
  ctx.fill();

  /* Nivel activo sobre el arco */
  const activeEnd = Math.PI + level * Math.PI;
  ctx.beginPath();
  ctx.arc(cx, cy, r, Math.PI, Math.min(activeEnd, Math.PI * 2));
  const grad = ctx.createLinearGradient(cx - r, cy, cx + r, cy);
  grad.addColorStop(0,   '#00cc44');
  grad.addColorStop(0.6, '#aacc00');
  grad.addColorStop(0.8, '#ffaa00');
  grad.addColorStop(1,   '#ff2200');
  ctx.strokeStyle = grad;
  ctx.lineWidth   = r * 0.14;
  ctx.globalAlpha = level > 0.02 ? 0.85 : 0;
  ctx.stroke();
  ctx.globalAlpha = 1;

  /* Label dB */
  ctx.font = `${Math.max(7, w * 0.08)}px 'Share Tech Mono', monospace`;
  ctx.fillStyle = 'rgba(0,238,255,0.4)';
  ctx.textAlign = 'center';
  ctx.fillText('dB', cx, cy - r * 0.12);
}

let vuRaf = null;
function vuLoop() {
  const lv = getLevel();
  const rv = lv * (0.85 + Math.random() * 0.3); /* ligera diferencia L/R */
  drawVU(ctxL, Math.min(lv, 1), 0);
  drawVU(ctxR, Math.min(rv, 1), 1);
  vuRaf = requestAnimationFrame(vuLoop);
}

function resizeVU() {
  [cvL, cvR].forEach(cv => {
    cv.width  = cv.offsetWidth  || 120;
    cv.height = cv.offsetHeight || 120;
  });
}

/* ── BOBINAS ─────────────────────────────────────────────── */
function startReels() {
  if (reelRaf) return;
  (function spin() {
    reelAngle += 3;
    reelL.setAttribute('transform', `rotate(${reelAngle},120,87)`);
    reelR.setAttribute('transform', `rotate(${-reelAngle * 0.85},220,87)`);
    const g = Math.abs(Math.sin(Date.now() / 300));
    playhead.setAttribute('fill', `hsl(${270 + g * 30},80%,${40 + g * 20}%)`);
    reelRaf = requestAnimationFrame(spin);
  })();
}
function stopReels() {
  if (reelRaf) { cancelAnimationFrame(reelRaf); reelRaf = null; }
}

/* ── PISTAS ──────────────────────────────────────────────── */
function loadTrack(idx, autoplay) {
  if (idx < 0 || idx >= tracks.length) return;
  currentIndex = idx;
  const t = tracks[idx];

  document.querySelectorAll('.rc-track-item').forEach(el => el.classList.remove('active'));
  const el = document.getElementById('track-' + idx);
  if (el) el.classList.add('active');

  nowPlaying.innerHTML = `► PISTA ${String(idx + 1).padStart(2, '0')}<br><span>${t.titulo}</span>`;

  audio.src = t.url;
  audio.load();

  if (autoplay) {
    initAudio();
    if (audioCtx && audioCtx.state === 'suspended') audioCtx.resume();
    audio.play()
      .then(() => { isPlaying = true; updatePlayBtn(); startReels(); })
      .catch(e => console.warn('Audio error:', e));
  }
}

/* ── EVENTOS CONTROLES ───────────────────────────────────── */
playBtn.addEventListener('click', () => {
  if (currentIndex < 0) { loadTrack(0, true); return; }
  if (isPlaying) {
    audio.pause(); isPlaying = false; stopReels();
  } else {
    initAudio();
    if (audioCtx && audioCtx.state === 'suspended') audioCtx.resume();
    audio.play().then(() => { isPlaying = true; startReels(); }).catch(console.warn);
  }
  updatePlayBtn();
});

stopBtn.addEventListener('click', () => {
  audio.pause(); audio.currentTime = 0;
  isPlaying = false; updatePlayBtn(); stopReels();
  progressFill.style.width = '0%';
  timeDisplay.textContent = '0:00 / 0:00';
  nowPlaying.innerHTML = 'SELECCIONA UNA PISTA<br><span>▼ INSERT COIN TO LISTEN ▼</span>';
  document.querySelectorAll('.rc-track-item').forEach(el => el.classList.remove('active'));
  currentIndex = -1;
});

prevBtn.addEventListener('click', () =>
  loadTrack(currentIndex <= 0 ? tracks.length - 1 : currentIndex - 1, true));
nextBtn.addEventListener('click', () =>
  loadTrack((currentIndex + 1) % tracks.length, true));

audio.addEventListener('ended', () =>
  loadTrack((currentIndex + 1) % tracks.length, true));

audio.addEventListener('timeupdate', () => {
  if (!audio.duration) return;
  progressFill.style.width = (audio.currentTime / audio.duration * 100) + '%';
  timeDisplay.textContent  = fmt(audio.currentTime) + ' / ' + fmt(audio.duration);
});

progressBar.addEventListener('click', e => {
  if (!audio.duration) return;
  const r = progressBar.getBoundingClientRect();
  audio.currentTime = ((e.clientX - r.left) / r.width) * audio.duration;
});

document.querySelectorAll('.rc-track-item').forEach(el => {
  el.addEventListener('click', () => loadTrack(parseInt(el.dataset.index), true));
});

function updatePlayBtn() {
  if (isPlaying) {
    playBtn.textContent = '❚❚ PAUSE';
    playBtn.classList.add('playing');
  } else {
    playBtn.textContent = '► PLAY';
    playBtn.classList.remove('playing');
  }
}

function fmt(s) {
  if (isNaN(s)) return '0:00';
  return Math.floor(s / 60) + ':' + String(Math.floor(s % 60)).padStart(2, '0');
}

/* ── INIT ────────────────────────────────────────────────── */
window.addEventListener('DOMContentLoaded', () => {
  resizeVU();
  window.addEventListener('resize', resizeVU);
  vuLoop();
});
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
