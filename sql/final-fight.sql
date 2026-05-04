-- FINAL FIGHT (1989) — Reseña completa
-- Ejecutar completo en HeidiSQL (F9)
-- Si Final Fight ya existe del intento anterior, este DELETE lo limpia primero

DELETE FROM juegos WHERE slug = 'final-fight';

SET @slug = 'final-fight';

INSERT INTO juegos (slug, titulo, desarrollador, anno, genero, nota, publicada, fecha_publicacion, descripcion_corta)
VALUES (
  @slug,
  'Final Fight',
  'Capcom',
  1989,
  'BEAT \'EM UP',
  9,
  1,
  CURDATE(),
  'El beat \'em up que lo cambió todo. Metro City nunca fue tan peligrosa ni tan divertida.'
);

SET @juego_id = LAST_INSERT_ID();

-- SECCIONES DE LA RESEÑA

INSERT INTO secciones_resena (juego_id, titulo_h2, contenido_html, orden) VALUES
(@juego_id, 'METRO CITY NECESITA UN HÉROE', '
<p>
  Corría 1989 cuando Capcom anunció que su próximo proyecto se llamaría <strong>Street Fighter ''89</strong>. Era, en principio, una secuela del primer Street Fighter. Tendrías a un tipo fornido golpeando a pandilleros por las calles de una ciudad. Pero algo cambió en el proceso de desarrollo y el juego llegó a los salones recreativos con otro nombre: <strong>Final Fight</strong>. Y con ese cambio de nombre llegó también un cambio de historia que definió un género.
</p>
<p>
  La Mad Gear, la banda criminal más poderosa de Metro City, ha secuestrado a <strong>Jessica</strong>, hija del alcalde <strong>Mike Haggar</strong>. Error garrafal. Porque Haggar no es un alcalde cualquiera: es un ex-luchador profesional de 230 kilos de puro músculo con un bigote que intimida más que cualquier arma. Junto a <strong>Cody</strong>, novio de Jessica, y <strong>Guy</strong>, maestro del ninjutsu, los tres hombres van a machacar a medio Metro City hasta recuperarla.
</p>
<p>
  Ese es el argumento. Sencillo, directo, sin pretensiones. Y perfecto.
</p>
', 1),

(@juego_id, 'EL SISTEMA DE JUEGO: PUÑOS, PATADAS Y TUBOS DE HIERRO', '
<p>
  Final Fight no inventó el beat ''em up —eso lo hizo Renegade en 1986— pero sí lo <strong>perfeccionó hasta hacerlo irreconocible</strong>. La diferencia con sus predecesores era visceral: los sprites eran enormes, la animación era fluida, los golpes sonaban como dios manda y la pantalla se llenaba de enemigos que reaccionaban de forma creíble al recibir impactos.
</p>
<div class="char-stats-grid" style="margin:16px 0">
  <div class="char-stat-item">
    <div class="char-stat-label">JUGADORES</div>
    <div class="char-stat-val">1-2 simultáneos</div>
  </div>
  <div class="char-stat-item">
    <div class="char-stat-label">PLATAFORMA</div>
    <div class="char-stat-val">CPS-1 (Capcom)</div>
  </div>
  <div class="char-stat-item">
    <div class="char-stat-label">MONEDAS</div>
    <div class="char-stat-val">Muy tragaperras</div>
  </div>
  <div class="char-stat-item">
    <div class="char-stat-label">CONTINUACIONES</div>
    <div class="char-stat-val">Infinitas (con monedas)</div>
  </div>
</div>
<p>
  El sistema de combate era sencillo pero con más profundidad de la que parecía. Tenías golpe básico, salto, agarre y el <strong>ataque especial</strong> que te costaba energía pero despejaba la pantalla cuando te veías rodeado. Podías coger a los enemigos, lanzarlos, aporrearlos contra el suelo o arrancarles el arma de las manos. Cada personaje tenía un estilo diferente: Haggar era lento pero demoledor, Cody era el más equilibrado y Guy era el más rápido y ágil.
</p>
<p>
  Los escenarios se dividían en zonas de Metro City —el Slum, el Subway, el Industrial Area, el Bay Area y el Uptown— cada una con su paleta visual propia y sus enemigos característicos. No había dos fases iguales en atmósfera.
</p>
', 2),

(@juego_id, 'LOS TRES HÉROES', '
<div class="char-stats-grid" style="margin-bottom:16px">
  <div class="char-stat-item" style="border-left:3px solid var(--cyan);padding-left:12px">
    <div class="char-stat-label">MIKE HAGGAR</div>
    <div class="char-stat-val" style="color:#aaa;font-size:13px;margin-top:4px">Alcalde · Ex-luchador · 230 kg de democracia</div>
    <div style="margin-top:8px;font-family:''Rajdhani'',sans-serif;font-size:14px;color:#777;line-height:1.6">
      El personaje más icónico del juego. Lento, pero cada golpe que conecta hace temblar la pantalla. Su pile driver es uno de los movimientos más satisfactorios de la historia del arcade. Cuando Haggar agarra a un enemigo, el enemigo ya ha perdido.
    </div>
  </div>
  <div class="char-stat-item" style="border-left:3px solid var(--amarillo);padding-left:12px">
    <div class="char-stat-label">CODY TRAVERS</div>
    <div class="char-stat-val" style="color:#aaa;font-size:13px;margin-top:4px">Luchador callejero · Novio de Jessica · El más equilibrado</div>
    <div style="margin-top:8px;font-family:''Rajdhani'',sans-serif;font-size:14px;color:#777;line-height:1.6">
      El personaje recomendado para los que empiezan. Velocidad y potencia en proporciones perfectas. Su uppercut es devastador y su combo básico es el más fácil de ejecutar. Años después reaparecería en Street Fighter Alpha como un ex-convicto. Giros del destino.
    </div>
  </div>
  <div class="char-stat-item" style="border-left:3px solid var(--magenta);padding-left:12px">
    <div class="char-stat-label">GUY</div>
    <div class="char-stat-val" style="color:#aaa;font-size:13px;margin-top:4px">Maestro del Bushinryu · El más rápido · Alto nivel de habilidad</div>
    <div style="margin-top:8px;font-family:''Rajdhani'',sans-serif;font-size:14px;color:#777;line-height:1.6">
      Para jugadores experimentados. Su velocidad le permite encadenar golpes antes de que los enemigos se recuperen, pero su daño por golpe es el más bajo. Dominarlo requiere práctica. Cuando lo dominas, te sientes invencible.
    </div>
  </div>
</div>
', 3),

(@juego_id, 'LOS JEFES DE LA MAD GEAR', '
<p>
  Final Fight tiene algunos de los mejores jefes del género. Cada uno con personalidad propia, patrón de ataque distinto y un diseño que los hacía inmediatamente memorables:
</p>
<div class="char-moves-grid" style="margin:16px 0">
  <div class="char-move">
    <div class="char-move-name">DAMND</div>
    <div class="char-move-input">Zona 1 · El primero</div>
    <div class="char-move-desc">El matón clásico. Silba para llamar refuerzos cuando pierde energía. Engañosamente difícil para ser el primer jefe.</div>
  </div>
  <div class="char-move">
    <div class="char-move-name">SODOM</div>
    <div class="char-move-input">Zona 2 · El subway</div>
    <div class="char-move-desc">Gigante con katanas. Fan de la cultura japonesa. Uno de los personajes más reconocibles del juego. Reaparece en Street Fighter Alpha.</div>
  </div>
  <div class="char-move">
    <div class="char-move-name">EDIGAIL (EDI E.)</div>
    <div class="char-move-input">Zona 3 · El policía corrupto</div>
    <div class="char-move-desc">Un sheriff enorme con pistola. El más frustrante del juego. Su disparo a distancia obliga a cambiar completamente la estrategia.</div>
  </div>
  <div class="char-move">
    <div class="char-move-name">ROLENTO</div>
    <div class="char-move-input">Zona 4 · El militar</div>
    <div class="char-move-desc">Ágil, peligroso, con granadas. El favorito de muchos jugadores. También aparece en Street Fighter Alpha 2.</div>
  </div>
  <div class="char-move super">
    <div class="char-move-name">ABIGAIL</div>
    <div class="char-move-input">Zona 5 · El monstruo</div>
    <div class="char-move-desc">El jefe penúltimo. Un gigante que ocupa media pantalla. Sus golpes quitan un tercio de la energía de un toque.</div>
  </div>
  <div class="char-move super">
    <div class="char-move-name">BELGER</div>
    <div class="char-move-input">Zona 6 · El jefe final</div>
    <div class="char-move-desc">El cerebro de la Mad Gear. En silla de ruedas, con ballesta. El enfrentamiento final en lo alto del edificio es puro cine de acción.</div>
  </div>
</div>
', 4),

(@juego_id, 'LA MÚSICA Y EL SONIDO', '
<p>
  La banda sonora de Final Fight es obra de <strong>Yoshihiro Sakaguchi</strong> y es, sencillamente, una de las mejores de la era CPS-1. Cada zona tiene su propio tema musical que encaja a la perfección con la atmósfera del escenario: el Slum suena amenazante y urbano, el Subway tiene ese aire claustrofóbico, el Industrial Area es pesado y maquinal.
</p>
<p>
  Los efectos de sonido son igualmente contundentes. El <em>crack</em> seco de un puñetazo bien conectado, el quejido de los enemigos al recibir un agarre de Haggar, el sonido metálico de un tubo de hierro... todo contribuye a esa sensación de peso y contundencia que distingue a Final Fight de cualquier competidor de la época.
</p>
<div class="char-bio" style="margin-top:16px">
  <p>El tema del Stage 1 —el Slum— es probablemente el más reconocible. Esa línea de bajo pulsante que arranca en cuanto apareces en la calle ha quedado grabada a fuego en la memoria de toda una generación de jugadores de recreativas.</p>
</div>
', 5),

(@juego_id, 'EL VEREDICTO', '
<div class="char-bio">
  <p>Final Fight no es un juego perfecto. Las versiones domésticas sufrieron recortes importantes (sin modo cooperativo en la versión SNES, personajes eliminados), y la versión arcade en solitario puede resultar despiadada con su sistema de economía de monedas. Pero en su plataforma original —la recreativa— es <strong>una obra maestra del género</strong>.</p>
  <p>Definió el beat ''em up moderno. Estableció los estándares de diseño de niveles, de variedad de enemigos y de diseño de personajes jugables que todos los que vinieron después —Streets of Rage, Battletoads, Knights of the Round— intentaron igualar. Ninguno lo superó.</p>
</div>
<div class="char-stats-grid" style="margin-top:20px">
  <div class="char-stat-item">
    <div class="char-stat-label">GRÁFICOS</div>
    <div class="char-stat-val" style="color:var(--amarillo)">9/10</div>
  </div>
  <div class="char-stat-item">
    <div class="char-stat-label">JUGABILIDAD</div>
    <div class="char-stat-val" style="color:var(--amarillo)">9/10</div>
  </div>
  <div class="char-stat-item">
    <div class="char-stat-label">MÚSICA</div>
    <div class="char-stat-val" style="color:var(--amarillo)">9/10</div>
  </div>
  <div class="char-stat-item">
    <div class="char-stat-label">DURACIÓN</div>
    <div class="char-stat-val" style="color:var(--amarillo)">8/10</div>
  </div>
  <div class="char-stat-item">
    <div class="char-stat-label">LEGADO</div>
    <div class="char-stat-val" style="color:var(--amarillo)">10/10</div>
  </div>
</div>
', 6);
