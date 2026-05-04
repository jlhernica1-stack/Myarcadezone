-- ============================================================
--  MY ARCADE ZONE — Schema MySQL
-- ============================================================

CREATE DATABASE IF NOT EXISTS myarcadezone
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE myarcadezone;

-- ── JUEGOS / RESEÑAS ────────────────────────────────────────
CREATE TABLE juegos (
  id                 INT AUTO_INCREMENT PRIMARY KEY,
  slug               VARCHAR(100)  NOT NULL UNIQUE,
  titulo             VARCHAR(200)  NOT NULL,
  desarrollador      VARCHAR(150),
  publisher          VARCHAR(150),
  anno               SMALLINT,
  genero             VARCHAR(100),
  plataforma_original VARCHAR(100),
  plataformas        VARCHAR(200),
  descripcion_corta  TEXT,
  nota               DECIMAL(3,1),
  badge_tipo         VARCHAR(50),
  badge_texto        VARCHAR(100),
  audio_url          VARCHAR(500),
  audio_titulo       VARCHAR(200),
  imagen_cover       VARCHAR(500),
  veredicto_texto    TEXT,
  pros               TEXT,
  contras            TEXT,
  links_html         TEXT,
  publicada          TINYINT(1)    NOT NULL DEFAULT 0,
  fecha_publicacion  DATE,
  created_at         TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Secciones de texto de cada reseña (cada h2 es una fila)
CREATE TABLE secciones_resena (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  juego_id      INT          NOT NULL,
  orden         TINYINT      NOT NULL DEFAULT 0,
  titulo_h2     VARCHAR(200),
  contenido_html MEDIUMTEXT,
  FOREIGN KEY (juego_id) REFERENCES juegos(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Galería de imágenes de cada reseña
CREATE TABLE galeria (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  juego_id   INT          NOT NULL,
  imagen_url VARCHAR(500),
  caption    VARCHAR(300),
  orden      TINYINT      NOT NULL DEFAULT 0,
  FOREIGN KEY (juego_id) REFERENCES juegos(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── PERSONAJES ──────────────────────────────────────────────
CREATE TABLE personajes (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  slug         VARCHAR(100) NOT NULL UNIQUE,
  nombre       VARCHAR(150) NOT NULL,
  juego_origen VARCHAR(150),
  sprite_url   VARCHAR(500),
  notas_html   TEXT
) ENGINE=InnoDB;

-- Relación muchos-a-muchos juego ↔ personaje
CREATE TABLE juego_personaje (
  juego_id      INT NOT NULL,
  personaje_id  INT NOT NULL,
  PRIMARY KEY (juego_id, personaje_id),
  FOREIGN KEY (juego_id)     REFERENCES juegos(id)     ON DELETE CASCADE,
  FOREIGN KEY (personaje_id) REFERENCES personajes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Datos flexibles del personaje (clave/valor)
CREATE TABLE personaje_datos (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  personaje_id INT          NOT NULL,
  clave        VARCHAR(100),
  valor        VARCHAR(300),
  orden        TINYINT      NOT NULL DEFAULT 0,
  FOREIGN KEY (personaje_id) REFERENCES personajes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── COMUNIDAD ───────────────────────────────────────────────
CREATE TABLE votos (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  juego_id   INT     NOT NULL,
  puntuacion TINYINT NOT NULL,
  ip         VARCHAR(45),
  fecha      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (juego_id) REFERENCES juegos(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE comentarios (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  juego_id   INT          NOT NULL,
  nombre     VARCHAR(100) NOT NULL,
  texto      TEXT         NOT NULL,
  fecha      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  aprobado   TINYINT(1)   NOT NULL DEFAULT 1,
  FOREIGN KEY (juego_id) REFERENCES juegos(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── BLOG ────────────────────────────────────────────────────
CREATE TABLE blog_posts (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  slug          VARCHAR(100) NOT NULL UNIQUE,
  titulo        VARCHAR(200) NOT NULL,
  categoria     VARCHAR(100),
  contenido_html MEDIUMTEXT,
  imagen_url    VARCHAR(500),
  fecha         DATE,
  publicado     TINYINT(1)   NOT NULL DEFAULT 0,
  created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
