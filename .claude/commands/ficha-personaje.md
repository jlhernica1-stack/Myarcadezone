# Convertir ficha de personaje a HTML arcade

El usuario ha subido un HTML con una ficha de personaje. Conviértelo al estilo de MY ARCADE ZONE usando las clases CSS del site.

## Clases disponibles

**Bio:**
- `char-bio` — párrafo de descripción con borde cyan izquierdo. Usa `<em>` para términos especiales (se muestran en amarillo)

**Stats grid:**
- `char-stats-grid` — contenedor grid
- `char-stat` — celda individual
- `char-stat-label` — etiqueta (ej: "Origen")
- `char-stat-value` — valor (ej: "Japón")

**Movimientos:**
- `char-section-title` — título de sección (ej: "GOLPES ESPECIALES")
- `char-moves-grid` — contenedor grid de movimientos
- `char-move` — tarjeta de movimiento normal
- `char-move super` — tarjeta de movimiento especial/super (borde amarillo)
- `char-move-icon` — emoji o icono del movimiento
- `char-move-name` — nombre del movimiento
- `char-move-input` — notación de botones (ej: "↓↘→ + Puño")
- `char-move-desc` — descripción del movimiento

**Cita final:**
- `char-quote` — cita del personaje al pie

## Instrucciones

1. Lee el HTML adjunto
2. Extrae toda la información: bio, stats, movimientos, cita
3. Corrige cualquier problema de encoding (Ã³→ó, Ã­→í, â→flechas, etc.)
4. Reconstruye el HTML usando SOLO las clases de arriba
5. Para los iconos de movimientos usa emojis: 🔥 fuego, ⚡ eléctrico, 🌀 giro/viento, 🥊 golpe, ★ super/especial
6. Para las flechas de input usa: ↓↘→ ↓↙← → ← ↑ y similares
7. Elimina todo el CSS inline y clases externas — solo las clases del site
8. Devuelve el HTML limpio listo para pegar en el campo NOTAS del admin

## Formato de salida

Devuelve solo el bloque HTML, sin explicaciones adicionales, listo para copiar y pegar.
