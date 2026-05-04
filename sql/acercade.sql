-- ACERCA DE — Tabla de configuración del sitio
-- Ejecutar en HeidiSQL (F9)

CREATE TABLE IF NOT EXISTS site_config (
  clave VARCHAR(100) PRIMARY KEY,
  valor MEDIUMTEXT
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

INSERT INTO site_config (clave, valor) VALUES
('acerca_logo',    ''),
('acerca_texto',   '<p>Los que tenemos una cierta edad empezamos en el mundo de los videojuegos de una forma muy concreta: mirando. Mirando las máquinas arcade en los recreativos, en los bares, en las cabinas que aparecían en cualquier rincón inesperado. Antes de tener monedas, ya sabías de memoria la pantalla de título del Galaga, el sonido del Pac-Man al morir, la música del Donkey Kong. Lo absorbías todo sin pagar ni una peseta.</p>
<p>Cuando por fin llegaba la moneda —que no siempre llegaba— la presión era enorme. Tenías que hacerlo durar. Y aun así, en cuestión de segundos, el Track &amp; Field te había demostrado que aporrear botones sin criterio no era una estrategia, era una declaración de intenciones. Perdías. Volvías al día siguiente con otra moneda. Así funcionaba el ciclo.</p>
<p>Esos juegos no eran entretenimiento menor. Eran el estado del arte de lo que la tecnología podía hacer en ese momento, desarrollados por equipos pequeños que resolvían problemas de ingeniería que nadie había resuelto antes, con presupuestos ridículos y plazos imposibles. Algunos trabajaban así por dinero. Muchos, claramente, por algo que iba más allá. Se nota en los resultados.</p>
<p>Hoy esos mismos juegos caben en el bolsillo. Puedes emularlos, comprarlos en una tienda digital, jugarlos en el móvil en el metro. La industria que nació en esas cabinas genera más dinero que el cine y la música juntos. El niño que aporreaba botones en un recreativo de barrio vive en un mundo donde los videojuegos son cultura, son negocio, son patrimonio.</p>
<p><strong>MY ARCADE ZONE</strong> es un homenaje a todo eso. A las máquinas, a los juegos, a la gente que los hizo posibles y a los que nos pasamos la infancia delante de ellos sin saber que estábamos viviendo algo irrepetible. Aquí encontrarás reseñas, fichas de personajes, hardware, música y todo lo que rodea a la era dorada del arcade.</p>
<p>Bienvenido. Esto es tuyo también.</p>')
ON DUPLICATE KEY UPDATE clave = clave;
