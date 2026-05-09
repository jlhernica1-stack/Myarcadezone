<?php
require_once dirname(__DIR__) . '/includes/db.php';
$current_page = 'salon';
$page_title   = 'Salón Recreativo — MY ARCADE ZONE';
require dirname(__DIR__) . '/includes/header.php';
?>

<style>
/* ── CONTENEDOR PRINCIPAL ── */
.salon-wrap {
  position: relative;
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
  user-select: none;
}
.salon-bg {
  width: 100%;
  display: block;
  image-rendering: pixelated;
}

/* ── HOTSPOTS ── */
.hotspot {
  position: absolute;
  cursor: pointer;
  border: 2px solid transparent;
  border-radius: 4px;
  transition: border-color .2s, box-shadow .2s, transform .15s;
  z-index: 10;
}
.hotspot:hover {
  border-color: var(--cyan);
  box-shadow: 0 0 16px var(--cyan), inset 0 0 12px rgba(0,238,255,0.12);
  transform: scale(1.03);
}
.hotspot-label {
  position: absolute;
  bottom: calc(100% + 6px);
  left: 50%;
  transform: translateX(-50%);
  background: rgba(0,0,0,0.92);
  border: 2px solid var(--cyan);
  color: var(--cyan);
  font-family: 'Share Tech Mono', monospace;
  font-size: 10px;
  padding: 5px 10px;
  white-space: nowrap;
  pointer-events: none;
  opacity: 0;
  transition: opacity .2s;
  letter-spacing: 1px;
}
.hotspot:hover .hotspot-label { opacity: 1; }

/* Posiciones sobre la imagen */
#hs-tv       { left:13%;  top:23%; width:9%;  height:15%; }
#hs-radio    { left:1%;   top:26%; width:11%; height:14%; }
#hs-tienda   { left:0%;   top:40%; width:19%; height:42%; }
#hs-sf2      { left:30%;  top:34%; width:10%; height:42%; }
#hs-fumeta   { left:38%;  top:50%; width:7%;  height:30%; }
#hs-futbolin { left:57%;  top:50%; width:26%; height:44%; }
#hs-billar   { left:83%;  top:30%; width:17%; height:50%; }

/* ── MODALES ── */
.salon-modal {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.88);
  z-index: 1000;
  align-items: center;
  justify-content: center;
  backdrop-filter: blur(3px);
}
.salon-modal.active { display: flex; }

.salon-modal-box {
  background: var(--negro-panel);
  border: 2px solid var(--cyan);
  box-shadow: 0 0 40px rgba(0,238,255,0.25), inset 0 0 20px rgba(0,238,255,0.04);
  width: 92%;
  max-width: 780px;
  max-height: 90vh;
  overflow-y: auto;
  position: relative;
  border-radius: 4px;
  animation: modalIn .22s ease;
}
.salon-modal-box.magenta-border { border-color: var(--magenta); box-shadow: 0 0 40px rgba(255,0,170,0.25); }
.salon-modal-box.amarillo-border { border-color: var(--amarillo); box-shadow: 0 0 40px rgba(255,215,0,0.2); }

@keyframes modalIn {
  from { transform: scale(0.87) translateY(24px); opacity:0; }
  to   { transform: scale(1)    translateY(0);    opacity:1; }
}
.modal-close-btn {
  position: absolute;
  top: 10px; right: 12px;
  background: #1a0000;
  border: 2px solid var(--rojo);
  color: var(--rojo);
  font-family: 'Share Tech Mono', monospace;
  font-size: 10px;
  padding: 6px 12px;
  cursor: pointer;
  letter-spacing: 2px;
  transition: background .15s;
  z-index: 10;
}
.modal-close-btn:hover { background: var(--rojo); color: #fff; }

.modal-hdr {
  font-family: 'Bebas Neue', sans-serif;
  font-size: 22px;
  letter-spacing: 4px;
  color: var(--cyan);
  padding: 18px 20px 10px;
  border-bottom: 1px solid rgba(0,238,255,0.15);
  margin-bottom: 16px;
}
.modal-hdr.magenta { color: var(--magenta); border-color: rgba(255,0,170,0.2); }
.modal-hdr.amarillo { color: var(--amarillo); border-color: rgba(255,215,0,0.2); }

.modal-body { padding: 0 20px 24px; }

.salon-quote {
  font-family: 'Rajdhani', sans-serif;
  font-size: 18px;
  color: #aaa;
  line-height: 1.8;
  margin-bottom: 16px;
}
.salon-quote strong { color: var(--blanco); }
.salon-quote em { color: var(--cyan); font-style: normal; }

/* ── SLIDESHOW SF2 / TV ── */
.salon-slideshow {
  position: relative;
  background: #000;
  border: 2px solid #222;
  margin-bottom: 16px;
  overflow: hidden;
}
.salon-slideshow img {
  width: 100%;
  display: block;
  image-rendering: pixelated;
  transition: opacity .3s;
}
.slideshow-overlay {
  position: absolute;
  inset: 0;
  background: repeating-linear-gradient(
    to bottom, transparent 0px, transparent 3px,
    rgba(0,0,0,0.18) 3px, rgba(0,0,0,0.18) 5px
  );
  pointer-events: none;
}
.slideshow-nav {
  display: flex;
  justify-content: center;
  gap: 6px;
  margin-top: 8px;
}
.slideshow-dot {
  width: 8px; height: 8px;
  border-radius: 50%;
  background: #333;
  cursor: pointer;
  transition: background .2s;
}
.slideshow-dot.active { background: var(--cyan); }

/* ── SMOKE EFFECT (fumeta) ── */
.smoke-wrap {
  text-align: center;
  padding: 20px 0 10px;
  position: relative;
  overflow: hidden;
  min-height: 120px;
}
.smoke-particle {
  position: absolute;
  bottom: 0;
  width: 40px; height: 40px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(180,180,180,0.5), transparent);
  animation: smokeRise 3s ease-in infinite;
  pointer-events: none;
}
@keyframes smokeRise {
  0%   { transform: translateY(0) scale(0.5); opacity: 0.7; }
  50%  { transform: translateY(-60px) scale(1.2) translateX(10px); opacity: 0.35; }
  100% { transform: translateY(-120px) scale(2) translateX(-8px); opacity: 0; }
}
.smoke-emoji {
  font-size: 48px;
  line-height: 1;
  display: block;
  margin-bottom: 8px;
}

/* ── RADIO PLAYER ── */
.salon-tracklist {
  max-height: 260px;
  overflow-y: auto;
  border: 1px solid rgba(0,238,255,0.12);
  margin-bottom: 12px;
}
.salon-track {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 12px;
  border-bottom: 1px solid rgba(0,238,255,0.06);
  cursor: pointer;
  font-family: 'Share Tech Mono', monospace;
  font-size: 10px;
  color: #555;
  transition: background .12s, color .12s;
}
.salon-track:hover { background: rgba(0,238,255,0.05); color: var(--cyan); }
.salon-track.active { background: rgba(0,238,255,0.08); color: var(--amarillo); }
.salon-track-num { color: var(--cyan); min-width: 22px; }
.salon-track-info { flex: 1; line-height: 1.6; }
.salon-track-sub { color: #333; font-size: 9px; margin-top: 2px; }

.salon-player-bar {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 12px;
  flex-wrap: wrap;
}
.salon-player-btn {
  background: transparent;
  border: 1px solid var(--cyan);
  color: var(--cyan);
  font-family: 'Share Tech Mono', monospace;
  font-size: 10px;
  padding: 7px 14px;
  cursor: pointer;
  letter-spacing: 1px;
  transition: background .12s, color .12s;
}
.salon-player-btn:hover { background: var(--cyan); color: #000; }
.salon-player-btn.playing { background: var(--amarillo); color: #000; border-color: var(--amarillo); }

.salon-progress {
  flex: 1;
  height: 6px;
  background: #111;
  border: 1px solid rgba(0,238,255,0.2);
  cursor: pointer;
  min-width: 100px;
}
.salon-progress-fill {
  height: 100%;
  background: var(--cyan);
  width: 0%;
  transition: width .3s linear;
}
.salon-now-playing {
  font-family: 'Share Tech Mono', monospace;
  font-size: 10px;
  color: var(--amarillo);
  letter-spacing: 2px;
  margin-bottom: 12px;
  min-height: 1.4em;
}

/* ── MOBILE ── */
@media (max-width: 600px) {
  .hotspot-label { font-size: 8px; padding: 3px 6px; }
  .salon-modal-box { width: 96%; max-height: 88vh; }
  .modal-hdr { font-size: 16px; padding: 14px 14px 8px; }
  .modal-body { padding: 0 14px 18px; }
  .salon-quote { font-size: 15px; }
  .salon-player-bar { gap: 6px; }
  .salon-player-btn { padding: 6px 10px; font-size: 9px; }
  .smoke-emoji { font-size: 36px; }
  .acerca-player { font-size: 22px; }
}

/* scrollbar */
::-webkit-scrollbar { width: 6px; }
::-webkit-scrollbar-track { background: #0a0a0a; }
::-webkit-scrollbar-thumb { background: var(--cyan); }
</style>

<div class="layout">
  <main>
    <div class="home-section">
      <div class="section-hdr" style="padding:16px 20px">
        <span class="section-hdr-title">🕹️ SALÓN RECREATIVO</span>
        <span style="font-family:'Share Tech Mono',monospace;font-size:9px;color:#444">// PULSA EN LOS OBJETOS</span>
      </div>

      <div class="section-body" style="padding:0">

        <div style="font-family:'Share Tech Mono',monospace;font-size:10px;color:#333;
                    text-align:center;padding:10px;letter-spacing:3px;
                    animation:salonBlink 1.4s step-end infinite">
          ▼ PINCHA EN LA IMAGEN ▼
        </div>
        <style>@keyframes salonBlink{0%,100%{opacity:1}50%{opacity:0}}</style>

        <div class="salon-wrap">
          <video class="salon-bg" id="salonBg" autoplay loop muted playsinline>
            <source src="/assets/images/salon/salon-recreativo.mp4" type="video/mp4">
            <img src="/assets/images/salon/salon-recreativo.jpg" alt="Salón Recreativo años 80">
          </video>

          <!-- HOTSPOTS -->
          <div class="hotspot" id="hs-tv"       onclick="salonOpen('modal-tv')">
            <div class="hotspot-label">📺 TELEVISOR</div>
          </div>
          <div class="hotspot" id="hs-radio"    onclick="salonOpen('modal-radio')">
            <div class="hotspot-label">📻 RADIOCASETE</div>
          </div>
          <div class="hotspot" id="hs-tienda"   onclick="salonOpen('modal-tienda')">
            <div class="hotspot-label">🍬 TIENDA</div>
          </div>
          <div class="hotspot" id="hs-sf2"      onclick="salonOpen('modal-sf2')">
            <div class="hotspot-label">🕹️ STREET FIGHTER II</div>
          </div>
          <div class="hotspot" id="hs-fumeta"   onclick="salonOpen('modal-fumeta')">
            <div class="hotspot-label">🚬 ???</div>
          </div>
          <div class="hotspot" id="hs-futbolin" onclick="salonOpen('modal-futbolin')">
            <div class="hotspot-label">⚽ FUTBOLÍN</div>
          </div>
          <div class="hotspot" id="hs-billar"   onclick="salonOpen('modal-billar')">
            <div class="hotspot-label">🎱 BILLAR</div>
          </div>
        </div>

      </div>
    </div>
  </main>
</div>


<!-- ══════════════════════════════
     MODAL: STREET FIGHTER II
══════════════════════════════ -->
<div class="salon-modal" id="modal-sf2">
  <div class="salon-modal-box" style="max-width:720px">
    <button class="modal-close-btn" onclick="salonClose('modal-sf2')">✕ CERRAR</button>
    <div class="modal-hdr">🕹️ STREET FIGHTER II — CAPCOM 1991</div>
    <div class="modal-body">
      <audio id="sf2-audio" loop preload="none"
        src="https://dn720001.ca.archive.org/0/items/street-fighter-ii-arcade-music-ryu-stage-cps-1/Street%20Fighter%20II%20Arcade%20Music%20-%20Ryu%20Stage%20-%20CPS1.mp3">
      </audio>
      <div class="salon-slideshow" id="sf2-slideshow">
        <img id="sf2-img" src="/salon/sf2-1.png" alt="Street Fighter II">
        <div class="slideshow-overlay"></div>
      </div>
      <div class="slideshow-nav" id="sf2-dots"></div>

      <div class="salon-quote" style="margin-top:16px">
        La máquina más importante del salón. La que tenía siempre cola. La que te hacía llegar antes al recreativo para pillar puesto. Con seis botones y un joystick se redefinió lo que podía ser un juego de lucha.<br><br>
        <strong>25 pesetas</strong> la partida. <em>¿Cuántas echaste?</em>
      </div>
    </div>
  </div>
</div>


<!-- ══════════════════════════════
     MODAL: FUMETA
══════════════════════════════ -->
<div class="salon-modal" id="modal-fumeta">
  <div class="salon-modal-box magenta-border" style="max-width:500px">
    <button class="modal-close-btn" onclick="salonClose('modal-fumeta')" style="border-color:var(--magenta);color:var(--magenta)">✕ CERRAR</button>
    <div class="modal-hdr magenta">🚬 EL FUMETA DE SIEMPRE</div>
    <div class="modal-body">
      <div class="smoke-wrap">
        <span class="smoke-emoji">🚬</span>
        <div class="smoke-particle" style="left:40%;animation-delay:0s"></div>
        <div class="smoke-particle" style="left:45%;animation-delay:.6s;width:30px;height:30px"></div>
        <div class="smoke-particle" style="left:50%;animation-delay:1.2s;width:50px;height:50px"></div>
        <div class="smoke-particle" style="left:55%;animation-delay:1.8s"></div>
        <div class="smoke-particle" style="left:42%;animation-delay:2.4s;width:35px;height:35px"></div>
      </div>
      <div class="salon-quote" style="text-align:center;margin-top:8px">
        El fumeta de siempre. <strong>Atufando a todos</strong> con ese cigarro que nadie le pidió que encendiera.<br><br>
        Siempre el mismo sitio, siempre el mismo olor. El recreativo olía a tabaco, a sudor y a <em>sueños de cuartos de final</em>.<br><br>
        <span style="color:#555;font-size:14px">Lo peor es que jugaba bien. El muy...&nbsp;🙄</span>
      </div>
    </div>
  </div>
</div>


<!-- ══════════════════════════════
     MODAL: BILLAR
══════════════════════════════ -->
<div class="salon-modal" id="modal-billar">
  <div class="salon-modal-box amarillo-border" style="max-width:500px">
    <button class="modal-close-btn" onclick="salonClose('modal-billar')" style="border-color:var(--amarillo);color:var(--amarillo)">✕ CERRAR</button>
    <div class="modal-hdr amarillo">🎱 LA MESA DE BILLAR</div>
    <div class="modal-body">
      <div class="salon-quote" style="text-align:center;padding:16px 0">
        <span style="font-size:48px;display:block;margin-bottom:16px">🎱</span>
        Esto es demasiado caro para tu bolsillo.<br><br>
        <strong>No todo el mundo va por ahí con 20 duros en el bolsillo</strong>, chaval. Eso es para los mayores, para los que trabajan, para los que tienen novia.<br><br>
        Tú vuelve a la máquina. Que te quedan <em>dos fichas y un sueño</em>.
      </div>
    </div>
  </div>
</div>


<!-- ══════════════════════════════
     MODAL: RADIOCASETE
══════════════════════════════ -->
<div class="salon-modal" id="modal-radio">
  <div class="salon-modal-box" style="max-width:680px">
    <button class="modal-close-btn" onclick="salonClose('modal-radio')">✕ CERRAR</button>
    <div class="modal-hdr">📻 RADIOCASETE — BANDA SONORA DEL SALÓN</div>
    <div class="modal-body">
      <div class="salon-quote" style="margin-bottom:16px">
        Siempre sonaba algo de fondo. No era hilo musical. Era <strong>lo que ponía el dueño</strong>. Y a veces acertaba.
      </div>

      <div class="salon-now-playing" id="salon-now-playing">▶ SELECCIONA UNA PISTA</div>

      <div class="salon-player-bar">
        <button class="salon-player-btn" id="salon-prev">⏮ PREV</button>
        <button class="salon-player-btn" id="salon-play">▶ PLAY</button>
        <button class="salon-player-btn" id="salon-stop">■ STOP</button>
        <button class="salon-player-btn" id="salon-next">NEXT ⏭</button>
        <div class="salon-progress" id="salon-progress">
          <div class="salon-progress-fill" id="salon-progress-fill"></div>
        </div>
        <span style="font-family:'Share Tech Mono',monospace;font-size:10px;color:#444;white-space:nowrap"
              id="salon-time">0:00</span>
      </div>

      <div class="salon-tracklist" id="salon-tracklist"></div>
      <audio id="salon-audio" preload="none"></audio>
    </div>
  </div>
</div>


<!-- ══════════════════════════════
     MODAL: TELEVISOR
══════════════════════════════ -->
<div class="salon-modal" id="modal-tv">
  <div class="salon-modal-box" style="max-width:620px">
    <button class="modal-close-btn" onclick="salonClose('modal-tv')">✕ CERRAR</button>
    <div class="modal-hdr">📺 EL TELEVISOR DEL FONDO</div>
    <div class="modal-body">
      <video autoplay loop muted playsinline style="width:100%;display:block">
        <source src="/assets/images/salon/Spain Malta.mp4" type="video/mp4">
      </video>
      <div class="salon-quote" style="margin-top:16px;text-align:center">
        <strong>¡SÍ! Ponen el fútbol hasta en los recreativos.</strong><br><br>
        Los partidos son gratis. Las máquinas no.<br>
        <em>Aquí el que manda es el dueño, y al dueño le gusta el fútbol.</em><br><br>
        <span style="color:#555;font-size:14px">Tú a lo tuyo. Que se acaba el crédito.</span>
      </div>
    </div>
  </div>
</div>


<!-- ══════════════════════════════
     MODAL: TIENDA
══════════════════════════════ -->
<div class="salon-modal" id="modal-tienda">
  <div class="salon-modal-box amarillo-border" style="max-width:500px">
    <button class="modal-close-btn" onclick="salonClose('modal-tienda')" style="border-color:var(--amarillo);color:var(--amarillo)">✕ CERRAR</button>
    <div class="modal-hdr amarillo">🍬 LA TIENDA</div>
    <div class="modal-body">
      <div class="salon-quote" style="text-align:center;padding:16px 0">
        <span style="font-size:48px;display:block;margin-bottom:16px">🍬</span>
        Cómprate un <strong>chicle Cheiw</strong> o un <strong>Flash de fresa</strong>.<br><br>
        Que el resto va para el Street Fighter.<br><br>
        <em>Todo va para el Street Fighter.</em><br><br>
        <span style="color:#555;font-size:14px">El señor de la barra ya te conoce. Sabe exactamente lo que vas a pedir. Y lo que NO vas a comprar.</span>
      </div>
    </div>
  </div>
</div>


<!-- ══════════════════════════════
     MODAL: FUTBOLÍN
══════════════════════════════ -->
<div class="salon-modal" id="modal-futbolin">
  <div class="salon-modal-box magenta-border" style="max-width:520px">
    <button class="modal-close-btn" onclick="salonClose('modal-futbolin')" style="border-color:var(--magenta);color:var(--magenta)">✕ CERRAR</button>
    <div class="modal-hdr magenta">⚽ EL FUTBOLÍN</div>
    <div class="modal-body">
      <div class="salon-quote" style="text-align:center;padding:16px 0">
        <span style="font-size:48px;display:block;margin-bottom:16px">⚽</span>
        Es una ronda de <strong>"pierde paga"</strong>.<br><br>
        Así que me voy a buscar a mi amigo Antonio.<br><br>
        <em>Que con él delante y yo detrás nadie nos va a parar.</em><br><br>
        <span style="color:#555;font-size:14px">Antonio siempre estaba disponible para el futbolín. Para los deberes, no tanto.</span>
      </div>
    </div>
  </div>
</div>


<script>
/* ══ AMBIENTE ══ */
const ambientAudio = new Audio('https://ia800405.us.archive.org/4/items/commodore-64-music-garfield-dual-sid/Commodore%2064%20music%20-%20Garfield%20%28DUAL%20SID%29.mp3');
ambientAudio.loop   = true;
ambientAudio.volume = 0.4;

let ambientStarted = false;
function startAmbient() {
  if (ambientStarted) return;
  ambientAudio.play().then(() => { ambientStarted = true; }).catch(() => {});
}
ambientAudio.play().then(() => { ambientStarted = true; }).catch(() => {
  document.addEventListener('click', startAmbient, { once: true });
});

/* ══ MODALES ══ */
function salonOpen(id) {
  document.getElementById(id).classList.add('active');
  document.body.style.overflow = 'hidden';
  if (id === 'modal-sf2')  { initSlideshow('sf2', sf2Frames, 4000); ambientAudio.pause(); document.getElementById('sf2-audio').play().catch(()=>{}); }
  if (id === 'modal-tv')   initSlideshow('tv',   tvFrames,   2500);
  if (id === 'modal-radio') { initSalonRadio(); ambientAudio.pause(); }
}
function salonClose(id) {
  document.getElementById(id).classList.remove('active');
  document.body.style.overflow = '';
  stopSlideshow('sf2');
  stopSlideshow('tv');
  const sf2a = document.getElementById('sf2-audio');
  if (sf2a) { sf2a.pause(); sf2a.currentTime = 0; }
  const radioPlaying = salonAudio && !salonAudio.paused;
  if (ambientStarted && !radioPlaying) ambientAudio.play().catch(() => {});
}
document.querySelectorAll('.salon-modal').forEach(m => {
  m.addEventListener('click', e => { if (e.target === m) salonClose(m.id); });
});

/* ══ SLIDESHOWS ══ */
const sf2Frames = [
  '/salon/sf2-1.png',
  '/salon/sf2-2.png',
  '/salon/sf2-3.png',
  '/salon/sf2-4.png',
];
const tvFrames = [
  '/assets/images/salon/tv-futbol-1.jpg',
  '/assets/images/salon/tv-futbol-2.jpg',
  '/assets/images/salon/tv-futbol-3.jpg',
];

const slideshowTimers = {};
const slideshowIdx    = {};

function initSlideshow(name, frames, interval) {
  if (!frames || frames.length < 2) return;
  slideshowIdx[name] = 0;

  // dots
  const dotsEl = document.getElementById(name + '-dots');
  if (dotsEl) {
    dotsEl.innerHTML = '';
    frames.forEach((_, i) => {
      const d = document.createElement('div');
      d.className = 'slideshow-dot' + (i === 0 ? ' active' : '');
      d.onclick = () => showSlide(name, frames, i);
      dotsEl.appendChild(d);
    });
  }

  stopSlideshow(name);
  slideshowTimers[name] = setInterval(() => {
    slideshowIdx[name] = (slideshowIdx[name] + 1) % frames.length;
    showSlide(name, frames, slideshowIdx[name]);
  }, interval);
}

function showSlide(name, frames, idx) {
  slideshowIdx[name] = idx;
  const img = document.getElementById(name + '-img');
  if (img) {
    img.style.opacity = '0.2';
    setTimeout(() => { img.src = frames[idx]; img.style.opacity = '1'; }, 150);
  }
  document.querySelectorAll(`#${name}-dots .slideshow-dot`).forEach((d, i) => {
    d.classList.toggle('active', i === idx);
  });
}

function stopSlideshow(name) {
  if (slideshowTimers[name]) {
    clearInterval(slideshowTimers[name]);
    slideshowTimers[name] = null;
  }
}

/* ══ RADIO SALON ══ */
const salonTracks = [
  { title: 'STREET FIGHTER II — RYU THEME',    game: 'Street Fighter II (1991)',
    url: 'https://dn720001.ca.archive.org/0/items/street-fighter-ii-arcade-music-ryu-stage-cps-1/Street%20Fighter%20II%20Arcade%20Music%20-%20Ryu%20Stage%20-%20CPS1.mp3' },
  { title: 'FINAL FIGHT — OPENING THEME',       game: 'Final Fight (1989)',
    url: 'https://ia601605.us.archive.org/20/items/final-fight-arcade-opening-theme/Final%20Fight%20%28Arcade%29%20-%20Opening%20Theme.mp3' },
  { title: 'CADILLACS AND DINOSAURS — STAGE 1', game: 'Cadillacs and Dinosaurs (1992)',
    url: 'https://ia902805.us.archive.org/20/items/06-cadillacs-the-four-heroes-stage-1-1-ending-2/06-Cadillacs%20-The%20Four%20Heroes-%20%28Stage%201-1%2C%20Ending%202%29.mp3' },
  { title: 'THE FLINTSTONES — PINBALL MUSIC',   game: 'The Flintstones Pinball (1994)',
    url: 'https://ia903209.us.archive.org/7/items/the-flintstones-pinball-music/The%20Flintstones%20%28pinball%20music%29.mp3' },
  { title: 'THE ADDAMS FAMILY — PINBALL SOUNDTRACK', game: 'The Addams Family Pinball (1992)',
    url: 'https://ia600505.us.archive.org/17/items/the-addams-family-pinball-soundtrack/The%20Addams%20Family%20%20pinball%20Soundtrack.mp3' },
  { title: 'TERMINATOR 2 — JUDGMENT DAY PINBALL', game: 'Terminator 2: Judgment Day Pinball (1991)',
    url: 'https://ia903204.us.archive.org/9/items/terminator-2-judgment-day_202605/Terminator%202%20Judgment%20Day.mp3' },
];

let salonAudio, salonIdx = -1, salonPlaying = false, salonInited = false;

function initSalonRadio() {
  if (salonInited) return;
  salonInited = true;
  salonAudio = document.getElementById('salon-audio');

  buildSalonList();

  document.getElementById('salon-play').addEventListener('click', salonToggle);
  document.getElementById('salon-stop').addEventListener('click', salonStop);
  document.getElementById('salon-prev').addEventListener('click', () => salonLoad(salonIdx <= 0 ? salonTracks.length-1 : salonIdx-1, true));
  document.getElementById('salon-next').addEventListener('click', () => salonLoad((salonIdx+1) % salonTracks.length, true));

  salonAudio.addEventListener('ended', () => salonLoad((salonIdx+1) % salonTracks.length, true));
  salonAudio.addEventListener('timeupdate', () => {
    if (!salonAudio.duration) return;
    document.getElementById('salon-progress-fill').style.width =
      (salonAudio.currentTime / salonAudio.duration * 100) + '%';
    document.getElementById('salon-time').textContent = fmtT(salonAudio.currentTime);
  });

  document.getElementById('salon-progress').addEventListener('click', e => {
    if (!salonAudio.duration) return;
    const r = document.getElementById('salon-progress').getBoundingClientRect();
    salonAudio.currentTime = ((e.clientX - r.left) / r.width) * salonAudio.duration;
  });
}

function buildSalonList() {
  const tl = document.getElementById('salon-tracklist');
  tl.innerHTML = '';
  salonTracks.forEach((t, i) => {
    const el = document.createElement('div');
    el.className = 'salon-track';
    el.id = 'st-' + i;
    el.innerHTML = `<span class="salon-track-num">${String(i+1).padStart(2,'0')}</span>
      <div class="salon-track-info">
        <div>${t.title}</div>
        <div class="salon-track-sub">${t.game}</div>
      </div><span style="color:var(--cyan);font-size:12px">▶</span>`;
    el.addEventListener('click', () => salonLoad(i, true));
    tl.appendChild(el);
  });
}

function salonLoad(idx, autoplay) {
  salonIdx = idx;
  const t = salonTracks[idx];
  document.querySelectorAll('.salon-track').forEach(e => e.classList.remove('active'));
  const el = document.getElementById('st-' + idx);
  if (el) { el.classList.add('active'); el.scrollIntoView({ block:'nearest', behavior:'smooth' }); }
  document.getElementById('salon-now-playing').textContent = '▶ ' + t.title;
  salonAudio.src = t.url;
  salonAudio.load();
  if (autoplay) {
    salonAudio.play().then(() => { salonPlaying = true; updateSalonBtn(); }).catch(() => {});
  }
}

function salonToggle() {
  if (salonIdx < 0) { salonLoad(0, true); return; }
  if (salonPlaying) {
    salonAudio.pause(); salonPlaying = false;
  } else {
    salonAudio.play().then(() => { salonPlaying = true; }).catch(() => {});
  }
  updateSalonBtn();
}

function salonStop() {
  salonAudio.pause(); salonAudio.currentTime = 0;
  salonPlaying = false; salonIdx = -1;
  if (ambientStarted) ambientAudio.play().catch(() => {});
  updateSalonBtn();
  document.getElementById('salon-progress-fill').style.width = '0%';
  document.getElementById('salon-time').textContent = '0:00';
  document.getElementById('salon-now-playing').textContent = '▶ SELECCIONA UNA PISTA';
  document.querySelectorAll('.salon-track').forEach(e => e.classList.remove('active'));
}

function updateSalonBtn() {
  const b = document.getElementById('salon-play');
  if (salonPlaying) { b.textContent = '⏸ PAUSE'; b.classList.add('playing'); }
  else              { b.textContent = '▶ PLAY';  b.classList.remove('playing'); }
}

function fmtT(s) {
  if (isNaN(s)) return '0:00';
  return Math.floor(s/60) + ':' + String(Math.floor(s%60)).padStart(2,'0');
}
</script>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
