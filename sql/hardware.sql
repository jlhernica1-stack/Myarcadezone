-- HARDWARE
CREATE TABLE IF NOT EXISTS hardware (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  slug           VARCHAR(100)  NOT NULL UNIQUE,
  nombre         VARCHAR(200)  NOT NULL,
  fabricante     VARCHAR(150),
  anno           SMALLINT,
  categoria      ENUM('placa','monitor','cabinet','mando','otro') NOT NULL DEFAULT 'placa',
  descripcion_html MEDIUMTEXT,
  imagen_cover   VARCHAR(500),
  publicado      TINYINT(1)    NOT NULL DEFAULT 0,
  created_at     TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Specs técnicas clave/valor (CPU, RAM, resolución, etc.)
CREATE TABLE IF NOT EXISTS hardware_specs (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  hardware_id INT          NOT NULL,
  clave       VARCHAR(100) NOT NULL,
  valor       VARCHAR(300) NOT NULL,
  orden       TINYINT      NOT NULL DEFAULT 0,
  FOREIGN KEY (hardware_id) REFERENCES hardware(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Galería de fotos
CREATE TABLE IF NOT EXISTS hardware_galeria (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  hardware_id INT          NOT NULL,
  imagen_url  VARCHAR(500) NOT NULL,
  caption     VARCHAR(300),
  orden       TINYINT      NOT NULL DEFAULT 0,
  FOREIGN KEY (hardware_id) REFERENCES hardware(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- DATOS DE EJEMPLO

INSERT INTO hardware (slug, nombre, fabricante, anno, categoria, publicado, descripcion_html) VALUES
('cps-1', 'CPS-1', 'Capcom', 1988, 'placa', 1,
'<div class="hw-bio">
  <p>La <strong>Capcom Play System 1</strong> fue la primera placa arcade desarrollada íntegramente por Capcom y marcó el inicio de una era dorada para los recreativos de lucha y acción. Presentada en 1988 con <em>Forgotten Worlds</em>, la CPS-1 se convirtió en la plataforma de referencia de finales de los 80 y principios de los 90.</p>
  <p>Su arquitectura permitía sprites grandes con rotación y escalado, una paleta de color generosa para la época y un sonido de alta calidad gracias al chip OKI M6295. Títulos como <strong>Street Fighter II</strong>, <strong>Final Fight</strong> o <strong>Captain Commando</strong> nacieron en esta placa y definieron géneros completos.</p>
</div>
<div class="hw-section">
  <div class="hw-section-title">ARQUITECTURA</div>
  <p>La CPS-1 utilizaba un procesador Motorola 68000 a 10 MHz para la lógica principal, respaldado por un Z80 dedicado exclusivamente al audio. El subsistema gráfico personalizadopermitía múltiples capas de scroll y sprites con prioridad, algo inusual en placas de la época a ese precio.</p>
</div>
<div class="hw-section">
  <div class="hw-section-title">JUEGOS DESTACADOS</div>
  <div class="hw-games-grid">
    <div class="hw-game-tag">Street Fighter II (1991)</div>
    <div class="hw-game-tag">Final Fight (1989)</div>
    <div class="hw-game-tag">Captain Commando (1991)</div>
    <div class="hw-game-tag">Ghosts ''n Goblins (1985)*</div>
    <div class="hw-game-tag">Mercs (1990)</div>
    <div class="hw-game-tag">1941 (1990)</div>
    <div class="hw-game-tag">Strider (1989)</div>
    <div class="hw-game-tag">King of Dragons (1991)</div>
  </div>
  <p style="font-size:11px;color:#444;margin-top:8px">* Versión CPS-1 adaptada del original</p>
</div>'),

('cps-2', 'CPS-2', 'Capcom', 1993, 'placa', 1,
'<div class="hw-bio">
  <p>La <strong>Capcom Play System 2</strong> supuso un salto generacional sobre su predecesora. Lanzada en 1993 con <em>Super Street Fighter II Turbo</em>, la CPS-2 incorporó un sistema de seguridad basado en batería que causó controversia: al agotarse, la placa dejaba de funcionar. Aun así, su potencia gráfica y sonora la convirtió en la reina de los recreativos durante toda la década de los 90.</p>
  <p>Su diseño modular con placa <em>A-board</em> (hardware base) y <em>B-board</em> (el juego) permitía intercambiar títulos sin cambiar toda la electrónica, reduciendo costes para los operadores de salones.</p>
</div>
<div class="hw-section">
  <div class="hw-section-title">SISTEMA DE SEGURIDAD</div>
  <p>La CPS-2 fue la primera placa arcade en incluir un chip de seguridad alimentado por batería. Al agotarse esta (tras unos 5-10 años), la placa entraba en modo <em>suicide</em> y borraba las claves de cifrado, inutilizándola. En 2007, la comunidad de MAME logró crackear el sistema y preservar todos los títulos.</p>
</div>
<div class="hw-section">
  <div class="hw-section-title">JUEGOS DESTACADOS</div>
  <div class="hw-games-grid">
    <div class="hw-game-tag">Super SF II Turbo (1994)</div>
    <div class="hw-game-tag">Street Fighter Alpha (1995)</div>
    <div class="hw-game-tag">Marvel Super Heroes (1995)</div>
    <div class="hw-game-tag">X-Men vs Street Fighter (1996)</div>
    <div class="hw-game-tag">Darkstalkers (1994)</div>
    <div class="hw-game-tag">Alien vs Predator (1994)</div>
    <div class="hw-game-tag">Dungeons & Dragons (1993)</div>
    <div class="hw-game-tag">Progear (2001)</div>
  </div>
</div>'),

('neo-geo-mvs', 'Neo Geo MVS', 'SNK', 1990, 'placa', 1,
'<div class="hw-bio">
  <p>El <strong>Neo Geo Multi Video System</strong> fue una propuesta radicalmente diferente al resto de placas arcade de su época. SNK diseñó un sistema que compartía arquitectura exacta con su consola doméstica Neo Geo AES, lo que significaba que los juegos de arcade llegaban al salón y al hogar con calidad idéntica, algo sin precedentes en 1990.</p>
  <p>Los gabinetes MVS permitían alojar entre 1 y 6 cartuchos simultáneamente, dando a los operadores una flexibilidad enorme. La plataforma se mantuvo en producción activa hasta <strong>2004</strong>, convirtiéndose en la placa arcade con mayor longevidad de la historia.</p>
</div>
<div class="hw-section">
  <div class="hw-section-title">MODELO DE NEGOCIO ÚNICO</div>
  <p>SNK apostó por un sistema de cartuchos intercambiables en lugar de PCBs soldadas. Esto permitía a los salones recreativos cambiar los juegos en minutos y ofrecer hasta 6 títulos diferentes en un único cabinet. El precio de los cartuchos MVS era muy inferior al de las versiones AES domésticas.</p>
</div>
<div class="hw-section">
  <div class="hw-section-title">JUEGOS DESTACADOS</div>
  <div class="hw-games-grid">
    <div class="hw-game-tag">Fatal Fury (1991)</div>
    <div class="hw-game-tag">Art of Fighting (1992)</div>
    <div class="hw-game-tag">King of Fighters ''94 (1994)</div>
    <div class="hw-game-tag">Samurai Shodown (1993)</div>
    <div class="hw-game-tag">Metal Slug (1996)</div>
    <div class="hw-game-tag">Last Blade (1997)</div>
    <div class="hw-game-tag">Garou: Mark of Wolves (1999)</div>
    <div class="hw-game-tag">Pulstar (1995)</div>
  </div>
</div>');

-- Specs CPS-1
INSERT INTO hardware_specs (hardware_id, clave, valor, orden)
SELECT id, 'CPU principal', 'Motorola 68000 @ 10 MHz', 1 FROM hardware WHERE slug='cps-1';
INSERT INTO hardware_specs (hardware_id, clave, valor, orden)
SELECT id, 'CPU audio', 'Zilog Z80 @ 3.579 MHz', 2 FROM hardware WHERE slug='cps-1';
INSERT INTO hardware_specs (hardware_id, clave, valor, orden)
SELECT id, 'Chip de sonido', 'YM2151 + OKI M6295', 3 FROM hardware WHERE slug='cps-1';
INSERT INTO hardware_specs (hardware_id, clave, valor, orden)
SELECT id, 'Resolución', '384 × 224 px', 4 FROM hardware WHERE slug='cps-1';
INSERT INTO hardware_specs (hardware_id, clave, valor, orden)
SELECT id, 'Paleta de color', '4.096 colores (12-bit)', 5 FROM hardware WHERE slug='cps-1';
INSERT INTO hardware_specs (hardware_id, clave, valor, orden)
SELECT id, 'Sprites en pantalla', 'Hasta 256 simultáneos', 6 FROM hardware WHERE slug='cps-1';
INSERT INTO hardware_specs (hardware_id, clave, valor, orden)
SELECT id, 'RAM principal', '128 KB', 7 FROM hardware WHERE slug='cps-1';
INSERT INTO hardware_specs (hardware_id, clave, valor, orden)
SELECT id, 'Conector', 'JAMMA', 8 FROM hardware WHERE slug='cps-1';
INSERT INTO hardware_specs (hardware_id, clave, valor, orden)
SELECT id, 'Años en producción', '1988 – 1995', 9 FROM hardware WHERE slug='cps-1';
INSERT INTO hardware_specs (hardware_id, clave, valor, orden)
SELECT id, 'Títulos publicados', '33', 10 FROM hardware WHERE slug='cps-1';

-- Specs CPS-2
INSERT INTO hardware_specs (hardware_id, clave, valor, orden)
SELECT id, 'CPU principal', 'Motorola 68000 @ 16 MHz', 1 FROM hardware WHERE slug='cps-2';
INSERT INTO hardware_specs (hardware_id, clave, valor, orden)
SELECT id, 'CPU audio', 'Zilog Z80 @ 8 MHz', 2 FROM hardware WHERE slug='cps-2';
INSERT INTO hardware_specs (hardware_id, clave, valor, orden)
SELECT id, 'Chip de sonido', 'Q-Sound (DSP16A)', 3 FROM hardware WHERE slug='cps-2';
INSERT INTO hardware_specs (hardware_id, clave, valor, orden)
SELECT id, 'Resolución', '384 × 224 px', 4 FROM hardware WHERE slug='cps-2';
INSERT INTO hardware_specs (hardware_id, clave, valor, orden)
SELECT id, 'Paleta de color', '65.536 colores (16-bit)', 5 FROM hardware WHERE slug='cps-2';
INSERT INTO hardware_specs (hardware_id, clave, valor, orden)
SELECT id, 'Sprites en pantalla', 'Hasta 900 simultáneos', 6 FROM hardware WHERE slug='cps-2';
INSERT INTO hardware_specs (hardware_id, clave, valor, orden)
SELECT id, 'RAM principal', '2 MB', 7 FROM hardware WHERE slug='cps-2';
INSERT INTO hardware_specs (hardware_id, clave, valor, orden)
SELECT id, 'Seguridad', 'Chip suicide por batería', 8 FROM hardware WHERE slug='cps-2';
INSERT INTO hardware_specs (hardware_id, clave, valor, orden)
SELECT id, 'Diseño', 'Modular A-board + B-board', 9 FROM hardware WHERE slug='cps-2';
INSERT INTO hardware_specs (hardware_id, clave, valor, orden)
SELECT id, 'Años en producción', '1993 – 2003', 10 FROM hardware WHERE slug='cps-2';
INSERT INTO hardware_specs (hardware_id, clave, valor, orden)
SELECT id, 'Títulos publicados', '57', 11 FROM hardware WHERE slug='cps-2';

-- Specs Neo Geo MVS
INSERT INTO hardware_specs (hardware_id, clave, valor, orden)
SELECT id, 'CPU principal', 'Motorola 68000 @ 12 MHz', 1 FROM hardware WHERE slug='neo-geo-mvs';
INSERT INTO hardware_specs (hardware_id, clave, valor, orden)
SELECT id, 'CPU audio', 'Zilog Z80 @ 4 MHz', 2 FROM hardware WHERE slug='neo-geo-mvs';
INSERT INTO hardware_specs (hardware_id, clave, valor, orden)
SELECT id, 'Chip de sonido', 'Yamaha YM2610', 3 FROM hardware WHERE slug='neo-geo-mvs';
INSERT INTO hardware_specs (hardware_id, clave, valor, orden)
SELECT id, 'Resolución', '320 × 224 px', 4 FROM hardware WHERE slug='neo-geo-mvs';
INSERT INTO hardware_specs (hardware_id, clave, valor, orden)
SELECT id, 'Paleta de color', '65.536 colores (16-bit)', 5 FROM hardware WHERE slug='neo-geo-mvs';
INSERT INTO hardware_specs (hardware_id, clave, valor, orden)
SELECT id, 'Sprites en pantalla', 'Hasta 380 simultáneos', 6 FROM hardware WHERE slug='neo-geo-mvs';
INSERT INTO hardware_specs (hardware_id, clave, valor, orden)
SELECT id, 'RAM principal', '64 KB + 68 KB VRAM', 7 FROM hardware WHERE slug='neo-geo-mvs';
INSERT INTO hardware_specs (hardware_id, clave, valor, orden)
SELECT id, 'Slots de cartucho', '1 a 6 slots según modelo', 8 FROM hardware WHERE slug='neo-geo-mvs';
INSERT INTO hardware_specs (hardware_id, clave, valor, orden)
SELECT id, 'Compatible con', 'Neo Geo AES (cartuchos)', 9 FROM hardware WHERE slug='neo-geo-mvs';
INSERT INTO hardware_specs (hardware_id, clave, valor, orden)
SELECT id, 'Años en producción', '1990 – 2004', 10 FROM hardware WHERE slug='neo-geo-mvs';
INSERT INTO hardware_specs (hardware_id, clave, valor, orden)
SELECT id, 'Títulos publicados', '148', 11 FROM hardware WHERE slug='neo-geo-mvs';
