-- RETROCASSETE
CREATE TABLE IF NOT EXISTS retrocassete_tracks (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  titulo      VARCHAR(200) NOT NULL,
  juego       VARCHAR(200) NOT NULL,
  compositor  VARCHAR(150),
  url_audio   VARCHAR(500) NOT NULL,
  orden       SMALLINT NOT NULL DEFAULT 0,
  publicado   TINYINT(1) NOT NULL DEFAULT 1,
  created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Primera pista: Street Fighter II
INSERT INTO retrocassete_tracks (titulo, juego, compositor, url_audio, orden, publicado) VALUES
('STREET FIGHTER II — RYU THEME', 'Street Fighter II: The World Warrior (1991)', 'Yoko Shimomura',
 'https://archive.org/download/StreetFighterIIArcadeSoundtrack/01%20-%20Ryu%20Theme.mp3', 1, 1);
