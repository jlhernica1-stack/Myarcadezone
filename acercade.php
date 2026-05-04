<?php
require_once __DIR__ . '/includes/db.php';
$current_page = 'acercade';
$page_title   = 'Acerca de — MY ARCADE ZONE';

$db  = db();
$cfg = $db->query("SELECT clave, valor FROM site_config WHERE clave IN ('acerca_texto','acerca_logo')")
          ->fetchAll(PDO::FETCH_KEY_PAIR);

$texto = $cfg['acerca_texto'] ?? '';
$logo  = $cfg['acerca_logo']  ?? '';

require __DIR__ . '/includes/header.php';
?>

<style>
.acerca-cabinet-wrap {
  overflow-x: auto;
  text-align: center;
}
.acerca-cabinet {
  font-family: 'Share Tech Mono', monospace;
  font-size: 12px;
  line-height: 1.45;
  color: var(--cyan);
  text-shadow: 0 0 10px rgba(0,238,255,0.55);
  display: inline-block;
  text-align: left;
  white-space: pre;
  margin: 0 auto;
}
.acerca-player {
  font-family: 'Bebas Neue', sans-serif;
  font-size: 28px;
  letter-spacing: 8px;
  color: var(--amarillo);
  text-shadow: 0 0 18px rgba(255,215,0,0.5);
  text-align: center;
  margin-bottom: 6px;
}
.acerca-pressstart {
  font-family: 'Share Tech Mono', monospace;
  font-size: 11px;
  letter-spacing: 4px;
  color: var(--magenta);
  text-align: center;
  animation: blink-text 1.2s step-end infinite;
  margin-bottom: 36px;
}
@keyframes blink-text { 0%,100%{opacity:1} 50%{opacity:0} }
.acerca-divider {
  display: flex;
  align-items: center;
  gap: 12px;
  margin: 32px 0;
  font-family: 'Share Tech Mono', monospace;
  font-size: 10px;
  letter-spacing: 3px;
  color: #333;
}
.acerca-divider::before,
.acerca-divider::after {
  content: '';
  flex: 1;
  height: 1px;
  background: linear-gradient(90deg, transparent, rgba(0,238,255,0.2), transparent);
}
.acerca-párrafo {
  font-family: 'Rajdhani', sans-serif;
  font-size: 16px;
  color: #999;
  line-height: 1.85;
  margin-bottom: 20px;
}
.acerca-párrafo strong { color: var(--blanco); }
.acerca-highlight {
  border-left: 3px solid var(--cyan);
  padding: 16px 20px;
  background: rgba(0,238,255,0.04);
  margin: 28px 0;
  font-family: 'Rajdhani', sans-serif;
  font-size: 17px;
  color: #bbb;
  line-height: 1.8;
}
.acerca-highlight strong { color: var(--cyan); }
.acerca-stats {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
  gap: 1px;
  background: rgba(0,238,255,0.08);
  border: 1px solid rgba(0,238,255,0.12);
  margin-top: 36px;
}
.acerca-stat {
  background: #0a0a0a;
  padding: 18px 16px;
  text-align: center;
}
.acerca-stat-val {
  font-family: 'Bebas Neue', sans-serif;
  font-size: 30px;
  color: var(--amarillo);
  text-shadow: 0 0 10px rgba(255,215,0,0.35);
  display: block;
  line-height: 1;
}
.acerca-stat-lbl {
  font-family: 'Share Tech Mono', monospace;
  font-size: 9px;
  letter-spacing: 2px;
  color: #444;
  margin-top: 4px;
  display: block;
}
.acerca-insert {
  text-align: center;
  margin-top: 40px;
  font-family: 'Share Tech Mono', monospace;
  font-size: 11px;
  letter-spacing: 5px;
  color: #222;
}
</style>

<div class="layout">
  <main>
    <div class="home-section">
      <div class="section-hdr" style="flex-direction:column;align-items:flex-start;gap:4px;padding:16px 20px">
        <div style="display:flex;align-items:center;gap:12px;width:100%">
          <span class="section-hdr-title" style="font-size:16px">★ ACERCA DE</span>
          <span style="font-family:'Share Tech Mono',monospace;font-size:9px;color:#444;margin-left:auto">// SOBRE ESTE SITIO</span>
        </div>
        <div style="font-family:'Share Tech Mono',monospace;font-size:10px;color:#333">
          MY ARCADE ZONE — EL SALÓN RECREATIVO EN ESPAÑOL
        </div>
      </div>

      <div class="section-body" style="padding:36px 28px;max-width:860px">

        <!-- MAQUINA ARCADE ASCII -->
        <div style="margin-bottom:32px">
          <?php if ($logo): ?>
          <img src="<?= htmlspecialchars($logo) ?>" alt="MY ARCADE ZONE"
               style="max-width:100%;max-height:520px;object-fit:contain;display:block;
                      margin:0 auto 24px auto;
                      image-rendering:pixelated;
                      filter:drop-shadow(0 0 28px rgba(0,0,0,0.8))">
          <?php else: ?>
          <div class="acerca-cabinet-wrap">
<pre class="acerca-cabinet">
╔═══════════════════╗
║  MY ARCADE ZONE   ║
╠═══════════════════╣
║ ┌───────────────┐ ║
║ │ ░░░░░░░░░░░░░ │ ║
║ │ ░  ARCADE   ░ │ ║
║ │ ░   ZONE    ░ │ ║
║ │ ░░░░░░░░░░░░░ │ ║
║ └───────────────┘ ║
╠═══════════════════╣
║  ◉    ◉  ◉  ◉   ║
║ (J) [A][B][START] ║
╚═══════════════════╝
      ║         ║
 ▓▓▓▓▓╩▓▓▓▓▓▓▓▓▓╩▓▓▓▓▓
</pre>
          </div>
          <?php endif; ?>
        </div>

        <!-- PLAYER 1 -->
        <div class="acerca-player">PLAYER 1</div>
        <div class="acerca-pressstart">◄ PRESS START ►</div>

        <!-- BLOQUE DESTACADO -->
        <div class="acerca-highlight">
          Los que tenemos una cierta edad empezamos en el mundo de los videojuegos de una forma muy concreta: <strong>mirando</strong>. Mirando las máquinas arcade en los recreativos, en los bares, en las cabinas que aparecían en cualquier rincón inesperado. Antes de tener monedas, ya sabías de memoria la pantalla de título del Galaga, el sonido del Pac-Man al morir, la música del Donkey Kong. <strong>Lo absorbías todo sin pagar ni una peseta.</strong>
        </div>

        <div class="acerca-divider">★ CONTINÚA ★</div>

        <p class="acerca-párrafo">
          Cuando por fin llegaba la moneda —que no siempre llegaba— la presión era enorme. Tenías que hacerlo durar. Y aun así, en cuestión de segundos, el Track &amp; Field te había demostrado que aporrear botones sin criterio no era una estrategia, era una declaración de intenciones. Perdías. Volvías al día siguiente con otra moneda. Así funcionaba el ciclo.
        </p>

        <p class="acerca-párrafo">
          Esos juegos no eran entretenimiento menor. Eran el estado del arte de lo que la tecnología podía hacer en ese momento, desarrollados por equipos pequeños que resolvían problemas de ingeniería que nadie había resuelto antes, con presupuestos ridículos y plazos imposibles. Algunos trabajaban así por dinero. Muchos, claramente, por algo que iba más allá. <strong>Se nota en los resultados.</strong>
        </p>

        <div class="acerca-divider">★ GAME OVER? NO. CONTINÚA ★</div>

        <p class="acerca-párrafo">
          Hoy esos mismos juegos caben en el bolsillo. Puedes emularlos, comprarlos en una tienda digital, jugarlos en el móvil en el metro. La industria que nació en esas cabinas genera más dinero que el cine y la música juntos. El niño que aporreaba botones en un recreativo de barrio vive en un mundo donde los videojuegos son <strong>cultura, negocio y patrimonio</strong>.
        </p>

        <div class="acerca-highlight" style="border-color:var(--magenta)">
          <strong style="color:var(--magenta)">MY ARCADE ZONE</strong> es un homenaje a todo eso. A las máquinas, a los juegos, a la gente que los hizo posibles y a los que nos pasamos la infancia delante de ellos sin saber que estábamos viviendo algo irrepetible. Aquí encontrarás reseñas, fichas de personajes, hardware, música y todo lo que rodea a la era dorada del arcade.
        </div>

        <!-- STATS ARCADE -->
        <div class="acerca-stats">
          <div class="acerca-stat">
            <span class="acerca-stat-val">1978</span>
            <span class="acerca-stat-lbl">AÑO DE INICIO</span>
          </div>
          <div class="acerca-stat">
            <span class="acerca-stat-val">∞</span>
            <span class="acerca-stat-lbl">MONEDAS GASTADAS</span>
          </div>
          <div class="acerca-stat">
            <span class="acerca-stat-val">0</span>
            <span class="acerca-stat-lbl">ARREPENTIMIENTOS</span>
          </div>
          <div class="acerca-stat">
            <span class="acerca-stat-val">100%</span>
            <span class="acerca-stat-lbl">NOSTALGIA</span>
          </div>
        </div>

        <div class="acerca-insert" style="margin-top:32px;color:var(--amarillo);font-size:13px;font-family:'Rajdhani',sans-serif;letter-spacing:2px">
          Bienvenido. Esto es tuyo también.
        </div>
        <div class="acerca-insert">● INSERT COIN ●</div>

        <?php if ($texto && $texto !== strip_tags($texto)): ?>
        <!-- Contenido adicional editable desde admin -->
        <div class="acerca-divider" style="margin-top:40px">★ NOTAS ★</div>
        <div class="char-bio" style="font-size:15px;line-height:1.85">
          <?= $texto ?>
        </div>
        <?php endif; ?>

      </div>
    </div>
  </main>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
