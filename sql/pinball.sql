-- Tabla de datos específicos de pinball
CREATE TABLE IF NOT EXISTS pinball_datos (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    juego_id    INT NOT NULL,
    clave       VARCHAR(100) NOT NULL,
    valor       TEXT,
    FOREIGN KEY (juego_id) REFERENCES juegos(id) ON DELETE CASCADE
);
