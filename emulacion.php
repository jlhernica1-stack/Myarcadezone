<?php
require_once __DIR__ . '/includes/db.php';
$current_page = 'emulacion';
$page_title   = 'Emulación Arcade — MY ARCADE ZONE';
require __DIR__ . '/includes/header.php';
?>

<div class="layout">
  <main>

    <!-- CABECERA -->
    <div class="home-section">
      <div class="section-hdr" style="flex-direction:column;align-items:flex-start;gap:4px;padding:16px 20px">
        <div style="display:flex;align-items:center;gap:12px;width:100%">
          <span class="section-hdr-title" style="font-size:16px">💾 EMULACIÓN ARCADE</span>
          <span style="font-family:'Share Tech Mono',monospace;font-size:9px;color:#444;margin-left:auto">// ZONA DE EMULACIÓN · GUÍA DE CAMPO</span>
        </div>
        <div style="font-family:'Share Tech Mono',monospace;font-size:10px;color:#333">
          TODO LO QUE NADIE TE EXPLICA PERO TODO EL MUNDO SABE
        </div>
      </div>

      <div class="section-body" style="padding:24px 20px">

        <!-- INTRO -->
        <div class="char-bio" style="margin-bottom:28px;font-size:15px">
          <p>Vamos a hablar de emulación. Y no, no voy a darte el sermoncito de "esto es ilegal y no lo hagas". Tú eres mayor de edad y sabes perfectamente lo que estás haciendo. Lo que sí voy a hacer es explicarte cómo montarte un setup decente para revivir esos momentos en los que metías monedas de 25 pesetas como si no hubiera un mañana.</p>
        </div>

        <p style="font-family:'Rajdhani',sans-serif;font-size:15px;color:#888;line-height:1.7;margin-bottom:14px">
          Seamos sinceros: los que crecimos en los salones recreativos de los 80 y los 90 llevamos décadas buscando la manera de reproducir esa experiencia en casa. Primero fueron las copias pirata que comprábamos en el rastro. Luego los emuladores de Windows 98 que tardaban diez minutos en cargar. Y ahora tenemos soluciones que harían llorar de alegría al niño que eras tú metiendo monedas al Street Fighter.
        </p>
        <p style="font-family:'Rajdhani',sans-serif;font-size:15px;color:#888;line-height:1.7;margin-bottom:28px">
          Así que agarra el mando (o el teclado, que también vale), que vamos al lío.
        </p>

        <!-- SECCIÓN 1: QUÉ ES UN EMULADOR -->
        <div class="hw-section">
          <div class="hw-section-title">¿QUÉ ES UN EMULADOR?</div>
          <p>Un emulador es un programa que <strong style="color:var(--blanco)">imita el hardware original de una máquina arcade</strong> dentro de tu ordenador. Básicamente engaña al juego haciéndole creer que está corriendo en la placa de un CPS-2 de Capcom cuando en realidad está en tu portátil de trabajo con el fondo de escritorio de tus vacaciones en Benidorm.</p>
          <br>
          <p>El juego en sí se carga desde un archivo llamado <strong style="color:var(--blanco)">ROM</strong>, que es una copia digital del chip de memoria que llevaba la máquina original. Y aquí es donde la gente empieza a ponerse nerviosa con el tema legal. Relájate. Si tienes la máquina arcade original en el salón de tu casa (como algunos frikis que conozco), técnicamente podrías argumentar que tienes derecho a la ROM. Si no la tienes... bueno, ya sabes dónde está el botón de búsqueda de Google.</p>
        </div>

        <!-- SECCIÓN 2: LOS EMULADORES -->
        <div class="hw-section">
          <div class="hw-section-title">LOS EMULADORES QUE DEBES CONOCER</div>
          <p style="margin-bottom:16px">Hay mil opciones, pero no te voy a mandar a explorar la jungla solo. Estos son los que funcionan, punto.</p>

          <!-- MAME -->
          <div class="emu-entry">
            <div class="emu-entry-header">
              <span class="emu-entry-name">MAME</span>
              <span class="emu-badge-tag" style="border-color:var(--cyan);color:var(--cyan)">EL REY</span>
              <span class="emu-badge-tag" style="border-color:var(--verde);color:var(--verde)">GRATIS</span>
            </div>
            <p>El abuelo de todos los emuladores arcade. <strong style="color:var(--blanco)">Multiple Arcade Machine Emulator</strong>. Lleva desde 1997 y lo que no emula MAME es porque probablemente no existe. Tiene compatibilidad con más de 40.000 juegos, lo que significa que también tiene una curva de aprendizaje que da respeto. No es el más amigable del mundo, pero cuando lo dominas puedes emular desde el Pac-Man hasta el último Neo Geo.</p>
          </div>

          <!-- RetroArch -->
          <div class="emu-entry">
            <div class="emu-entry-header">
              <span class="emu-entry-name">RetroArch</span>
              <span class="emu-badge-tag" style="border-color:var(--amarillo);color:var(--amarillo)">NAVAJA SUIZA</span>
              <span class="emu-badge-tag" style="border-color:var(--verde);color:var(--verde)">GRATIS</span>
            </div>
            <p>Si MAME es el bisturí, RetroArch es la navaja suiza. <strong style="color:var(--blanco)">Un frontend que unifica montones de emuladores</strong> (los llaman "cores") bajo una sola interfaz. Emula arcade, consolas domésticas, ordenadores retro... todo desde el mismo sitio. Tiene shaders para simular la curvatura y el scanline de los monitores de tubo. La interfaz puede resultar marciana al principio, pero en dos tardes te parece lo más normal del mundo.</p>
          </div>

          <!-- FinalBurn Neo -->
          <div class="emu-entry">
            <div class="emu-entry-header">
              <span class="emu-entry-name">FinalBurn Neo</span>
              <span class="emu-badge-tag" style="border-color:var(--magenta);color:var(--magenta)">ESPECIALISTA</span>
              <span class="emu-badge-tag" style="border-color:var(--verde);color:var(--verde)">GRATIS</span>
            </div>
            <p>El especialista. Si lo que quieres es emular <strong style="color:var(--blanco)">juegos de lucha y shoot 'em ups de los 90</strong> (Capcom CPS-1 y CPS-2, SNK Neo Geo, Sega System 16...), FinalBurn Neo es tu hombre. Más ligero que MAME, más centrado, y con una compatibilidad excelente para esa época dorada. El Street Fighter II, los King of Fighters, los Metal Slug... aquí es donde viven felices.</p>
          </div>

          <!-- M2 -->
          <div class="emu-entry">
            <div class="emu-entry-header">
              <span class="emu-entry-name">Ports oficiales (Steam / Switch)</span>
              <span class="emu-badge-tag" style="border-color:#555;color:#777">SIN DRAMAS</span>
            </div>
            <p>Un caso especial. Si te interesa la emulación "oficial", las colecciones de Capcom, SNK o Sega para PS4, Nintendo Switch o PC vía Steam son una opción comodísima. No es lo mismo que montar tu propio setup, pero el resultado es impecable y sin complicaciones.</p>
          </div>
        </div>

        <!-- SECCIÓN 3: LAS ROMs -->
        <div class="hw-section">
          <div class="hw-section-title">EL TEMA DE LAS ROMs</div>
          <p style="margin-bottom:14px">Vale, ya tienes el emulador. Ahora necesitas los juegos. Las ROMs de arcade tienen una particularidad que hay que entender antes de volverse loco buscando archivos: <strong style="color:var(--blanco)">el formato de los archivos importa, y mucho</strong>.</p>
          <p style="margin-bottom:14px">En MAME, cada ROM tiene que ser la versión exacta compatible con la versión del emulador que tienes instalada. No es como una consola donde coges la ROM del Super Mario y ya. Aquí cada placa arcade tiene su propio set de archivos, a veces con nombres crípticos, y si falta uno solo el juego no arranca. Es la magia del arcade emulado: te hace sufrir igual que la máquina original.</p>

          <div class="emu-aviso">
            <div class="emu-aviso-label">⚠ OJO</div>
            <p>Asegúrate de que las ROMs que uses sean compatibles con tu versión de MAME. Si tienes MAME 0.270, necesitas el ROM set para MAME 0.270. Mezclar versiones es la receta perfecta para llevarte media tarde mirando mensajes de error.</p>
          </div>

          <div class="emu-tip">
            <div class="emu-tip-label">// TIP DEL ABUELO</div>
            <p>Para encontrar las ROMs correctas, el recurso más conocido y respetado por la comunidad lleva años siendo <strong style="color:var(--blanco)">archive.org</strong>. Ahí hay colecciones enteras de ROMs de dominio público o abandonware. Para el resto, bueno... Internet es grande.</p>
          </div>
        </div>

        <!-- SECCIÓN 4: HARDWARE -->
        <div class="hw-section">
          <div class="hw-section-title">EL HARDWARE: ¿QUÉ NECESITO?</div>
          <p style="margin-bottom:14px">La respuesta corta es: <strong style="color:var(--blanco)">casi cualquier cosa sirve</strong>. Para emular arcade de los 80 y 90, cualquier ordenador fabricado en los últimos quince años te sobra. El problema llega cuando quieres emular máquinas más complejas de los primeros años 2000. Ahí sí que empiezas a necesitar algo más de músculo.</p>

          <div class="emu-list">
            <div class="emu-list-item"><span class="emu-list-bullet">►</span><div><strong style="color:var(--blanco)">PC con Windows, Mac o Linux</strong> — La opción más cómoda y con más soporte. Para empezar, lo más recomendable.</div></div>
            <div class="emu-list-item"><span class="emu-list-bullet">►</span><div><strong style="color:var(--blanco)">Raspberry Pi 4 o 5</strong> — Perfecto para montar una miniconsola o una recreativa casera. Con RetroPie o Batocera lo tienes funcionando en una tarde.</div></div>
            <div class="emu-list-item"><span class="emu-list-bullet">►</span><div><strong style="color:var(--blanco)">Android</strong> — RetroArch y varios emuladores tienen versión Android. Con un mando bluetooth y una tele, tienes tu salón recreativo portátil.</div></div>
            <div class="emu-list-item"><span class="emu-list-bullet">►</span><div><strong style="color:var(--blanco)">Una recreativa casera</strong> — El sueño húmedo de todos. Un mueble arcade con pantalla, mandos de microswitches y Raspberry Pi dentro. Esto ya es religión.</div></div>
          </div>
        </div>

        <!-- SECCIÓN 5: EL MANDO -->
        <div class="hw-section">
          <div class="hw-section-title">EL MANDO: NO SEAS MASOCA</div>
          <p style="margin-bottom:14px">Si vas a emular juegos arcade con el teclado, eres libre de hacerlo. Pero seré honesto: estás haciéndote daño a ti mismo. El arcade fue diseñado para joystick y botones, y eso no cambia por mucho que reasignes el teclado.</p>

          <div class="emu-list">
            <div class="emu-list-item"><span class="emu-list-bullet">►</span><div><strong style="color:var(--blanco)">Mando de PS4/PS5 o Xbox</strong> — Funcionan directamente por USB o bluetooth. La opción más práctica y barata si ya los tienes.</div></div>
            <div class="emu-list-item"><span class="emu-list-bullet">►</span><div><strong style="color:var(--blanco)">Stick arcade USB</strong> — Los hay desde 20€ en Amazon hasta 200€ de Qanba o Hori. Para peleas de los 90 merece la pena el salto. Con un stick decente, los hadoukens salen solos.</div></div>
            <div class="emu-list-item"><span class="emu-list-bullet">►</span><div><strong style="color:var(--blanco)">Monta el tuyo</strong> — Componentes por separado (Sanwa, Seimitsu, Happ...) + placa USB tipo Brook Universal Fighting Board. Requiere algo de maña pero el resultado es inigualable.</div></div>
            <div class="emu-list-item"><span class="emu-list-bullet">►</span><div><strong style="color:var(--blanco)">Recreativa casera completa</strong> — Ya estamos en nivel final boss. Pero si llegas aquí, no te arrepentirás.</div></div>
          </div>
        </div>

        <!-- SECCIÓN 6: EXPERIENCIA COMPLETA -->
        <div class="hw-section">
          <div class="hw-section-title">PARA LOS QUE QUIEREN LA EXPERIENCIA COMPLETA</div>
          <p style="margin-bottom:14px"><strong style="color:var(--blanco)">Los shaders de CRT</strong> son quizás lo más impactante visualmente. Son filtros que simulan la curvatura, el scanline, el brillo de fósforo y hasta la ligera distorsión de los monitores de tubo de las recreativas originales. RetroArch tiene algunos extraordinarios. El Crt-Royale y el NTSC shader son los más conocidos. La primera vez que los activas piensas que estás loco. La segunda vez no los quitas nunca.</p>
          <p style="margin-bottom:14px"><strong style="color:var(--blanco)">El audio también importa</strong>. Los chips de sonido de las recreativas tenían una personalidad brutal. El YM2151 de Capcom, el Z80 de los Neo Geo... MAME emula el hardware de audio con precisión casi quirúrgica, pero asegúrate de tener buenos altavoces. Con un portátil con los altavoces de dos euros integrados te estás perdiendo la mitad de la experiencia.</p>

          <div class="emu-tip">
            <div class="emu-tip-label">// EL SETUP RECOMENDADO PARA EMPEZAR</div>
            <p>RetroArch + core FinalBurn Neo para juegos de lucha y shoot 'em ups. MAME standalone para explorar el catálogo completo. Un mando de PS4 o un stick básico USB. Un shader CRT activado. Y las ganas de pasarte una tarde entera jugando al Metal Slug sin que nadie te obligue a meter otra moneda. Eso no tiene precio.</p>
          </div>
        </div>

        <!-- SECCIÓN 7: PARA LOS QUE PASAN -->
        <div class="hw-section">
          <div class="hw-section-title">LA OPCIÓN PARA LOS QUE PASAN DE LÍOS</div>
          <p style="margin-bottom:14px">Mira, si después de leer todo esto sigues pensando que es demasiado trabajo, existe una tercera vía que nadie menciona en los tutoriales: <strong style="color:var(--blanco)">busca sistemas ya configurados</strong>. Hay packs de RetroArch o RetroPie preconfigurados, imágenes para Raspberry Pi listas para quemar en una tarjeta SD, y setups completos que alguien ya montó por ti con todo dentro y funcionando.</p>
          <p>Y si tampoco quieres hacer eso, seguro que tienes algún <strong style="color:var(--blanco)">cuñado, primo o amigo del trabajo</strong> que lleva años con esto y te lo pasa en cero coma. Te mirará por encima del hombro durante aproximadamente tres semanas... pero el día que le pidas ayuda para algo de Excel se le borrará la sonrisita. <em style="color:#888">Equilibrio del universo.</em></p>
        </div>

        <!-- QUOTE FINAL -->
        <div style="text-align:center;padding:32px 20px;border-top:1px solid rgba(0,238,255,0.1);border-bottom:1px solid rgba(0,238,255,0.1);margin:28px 0">
          <div style="font-family:'Bebas Neue',sans-serif;font-size:clamp(20px,4vw,30px);letter-spacing:3px;color:rgba(232,232,240,0.6);line-height:1.5">
            EN EL SALÓN RECREATIVO TE ECHABAN CUANDO CERRABAN.<br>
            <span style="color:var(--cyan)">AHORA CIERRAS TÚ CUANDO TE DA LA GANA.</span><br>
            ESO ES EMULACIÓN.
          </div>
        </div>

        <p style="font-family:'Rajdhani',sans-serif;font-size:15px;color:#888;line-height:1.7;margin-bottom:14px">
          Y con esto tienes todo lo que necesitas para empezar. No te compliques más de lo necesario al principio. Instala RetroArch, carga el core de FinalBurn Neo, busca las ROMs del Street Fighter II o el Metal Slug, y ponle a eso un mando decente. Cuando hayas revivido tus primeros cinco minutos con esos gráficos, ese sonido y esas sensaciones, ya buscarás cómo ir más allá.
        </p>
        <p style="font-family:'Share Tech Mono',monospace;font-size:11px;color:#333;text-align:center;letter-spacing:3px;padding-top:16px">
          // FIN DEL POST · INSERT COIN TO CONTINUE
        </p>

      </div>
    </div>

  </main>

  <aside class="sidebar">

    <div class="widget">
      <div class="widget-header">SETUP RECOMENDADO</div>
      <div class="widget-body">
        <div class="widget-stat-row"><span>Emulador base</span><span style="color:var(--cyan)">RetroArch</span></div>
        <div class="widget-stat-row"><span>Core arcade</span><span style="color:var(--cyan)">FinalBurn Neo</span></div>
        <div class="widget-stat-row"><span>Catálogo completo</span><span style="color:var(--cyan)">MAME</span></div>
        <div class="widget-stat-row"><span>Mando mínimo</span><span>PS4 / Xbox USB</span></div>
        <div class="widget-stat-row"><span>Shader</span><span>CRT-Royale</span></div>
        <div class="widget-stat-row"><span>Hardware</span><span>PC o Raspberry Pi 4</span></div>
      </div>
    </div>

    <div class="widget">
      <div class="widget-header">ENLACES OFICIALES</div>
      <div class="widget-body">
        <div class="widget-game">
          <div>
            <a href="https://www.mamedev.org" target="_blank" rel="noopener" class="wg-title">MAME</a>
            <div class="wg-meta">mamedev.org · Más de 40.000 juegos</div>
          </div>
        </div>
        <div class="widget-game">
          <div>
            <a href="https://www.retroarch.com" target="_blank" rel="noopener" class="wg-title">RETROARCH</a>
            <div class="wg-meta">retroarch.com · Multi-sistema</div>
          </div>
        </div>
        <div class="widget-game">
          <div>
            <a href="https://github.com/finalburnneo/FBNeo" target="_blank" rel="noopener" class="wg-title">FINALBURN NEO</a>
            <div class="wg-meta">GitHub · CPS-1, CPS-2, Neo Geo</div>
          </div>
        </div>
        <div class="widget-game">
          <div>
            <a href="https://www.retropi.org" target="_blank" rel="noopener" class="wg-title">RETROPIE</a>
            <div class="wg-meta">Para Raspberry Pi</div>
          </div>
        </div>
      </div>
    </div>

    <div class="widget">
      <div class="widget-header">PLACAS EMULADAS</div>
      <div class="widget-body">
        <div class="genre-tags">
          <a href="/hardware-ficha.php?slug=cps-1" class="genre-tag">CPS-1</a>
          <a href="/hardware-ficha.php?slug=cps-2" class="genre-tag">CPS-2</a>
          <a href="/hardware-ficha.php?slug=neo-geo-mvs" class="genre-tag">NEO GEO MVS</a>
          <span class="genre-tag" style="cursor:default;color:#333;border-color:#1a1a2e">SYSTEM 16</span>
          <span class="genre-tag" style="cursor:default;color:#333;border-color:#1a1a2e">TAITO F3</span>
          <span class="genre-tag" style="cursor:default;color:#333;border-color:#1a1a2e">JAMMA</span>
        </div>
      </div>
    </div>

    <div class="widget">
      <div class="widget-header">VER TAMBIÉN</div>
      <div class="widget-body">
        <div style="margin-bottom:8px">
          <a href="/hardware.php" class="admin-btn admin-btn-primary" style="display:block;text-align:center;font-size:11px">
            🕹️ FICHAS DE HARDWARE
          </a>
        </div>
        <div>
          <a href="/resenas.php" class="admin-btn" style="display:block;text-align:center;font-size:11px;color:var(--cyan);border-color:var(--cyan)">
            ⭐ RESEÑAS DE JUEGOS
          </a>
        </div>
      </div>
    </div>

  </aside>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
