<?php
require __DIR__ . '/_auth.php';
require_once dirname(__DIR__) . '/includes/db.php';

$db  = db();
$id  = (int)($_GET['id'] ?? 0);
$msg = '';

$juego = [
    'slug' => '', 'titulo' => '', 'desarrollador' => '', 'publisher' => '',
    'anno' => '', 'genero' => '', 'plataforma_original' => '', 'plataformas' => '',
    'descripcion_corta' => '', 'nota' => '', 'badge_tipo' => 'destacada', 'badge_texto' => '',
    'audio_url' => '', 'audio_titulo' => '', 'video_youtube' => '', 'imagen_cover' => '',
    'veredicto_texto' => '', 'pros' => '', 'contras' => '', 'links_html' => '',
    'publicada' => 0, 'fecha_publicacion' => date('Y-m-d'),
];

if ($id) {
    $row = $db->prepare("SELECT * FROM juegos WHERE id = ?");
    $row->execute([$id]);
    $row = $row->fetch();
    if ($row) $juego = array_merge($juego, array_map(fn($v) => $v ?? '', (array)$row));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete' && $id) {
    db()->prepare("DELETE FROM juegos WHERE id = ?")->execute([$id]);
    header('Location: /admin/'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $campos = [
        'slug', 'titulo', 'desarrollador', 'publisher', 'anno', 'genero',
        'plataforma_original', 'plataformas', 'descripcion_corta', 'nota',
        'badge_tipo', 'badge_texto', 'audio_url', 'audio_titulo', 'video_youtube', 'imagen_cover',
        'veredicto_texto', 'pros', 'contras', 'links_html', 'fecha_publicacion',
    ];
    foreach ($campos as $c) {
        $juego[$c] = trim($_POST[$c] ?? '');
    }
    $juego['publicada']       = isset($_POST['publicada']) ? 1 : 0;
    $juego['anno']            = $juego['anno'] ? (int)$juego['anno'] : null;
    $juego['nota']            = $juego['nota'] !== '' ? (float)$juego['nota'] : null;
    $juego['slug']            = preg_replace('/[^a-z0-9-]/', '', strtolower($juego['slug']));
    $juego['fecha_publicacion'] = $juego['fecha_publicacion'] ?: date('Y-m-d');

    try {
        if ($id) {
            $sql = "UPDATE juegos SET
                slug=:slug, titulo=:titulo, desarrollador=:desarrollador, publisher=:publisher,
                anno=:anno, genero=:genero, plataforma_original=:plataforma_original, plataformas=:plataformas,
                descripcion_corta=:descripcion_corta, nota=:nota,
                badge_tipo=:badge_tipo, badge_texto=:badge_texto,
                audio_url=:audio_url, audio_titulo=:audio_titulo, video_youtube=:video_youtube, imagen_cover=:imagen_cover,
                veredicto_texto=:veredicto_texto, pros=:pros, contras=:contras, links_html=:links_html,
                publicada=:publicada, fecha_publicacion=:fecha_publicacion
                WHERE id=:id";
            $juego['id'] = $id;
        } else {
            $sql = "INSERT INTO juegos
                (slug, titulo, desarrollador, publisher, anno, genero, plataforma_original, plataformas,
                 descripcion_corta, nota, badge_tipo, badge_texto, audio_url, audio_titulo, video_youtube, imagen_cover,
                 veredicto_texto, pros, contras, links_html, publicada, fecha_publicacion)
                VALUES
                (:slug, :titulo, :desarrollador, :publisher, :anno, :genero, :plataforma_original, :plataformas,
                 :descripcion_corta, :nota, :badge_tipo, :badge_texto, :audio_url, :audio_titulo, :video_youtube, :imagen_cover,
                 :veredicto_texto, :pros, :contras, :links_html, :publicada, :fecha_publicacion)";
        }

        $params = array_intersect_key($juego, array_flip([
            'slug','titulo','desarrollador','publisher','anno','genero','plataforma_original','plataformas',
            'descripcion_corta','nota','badge_tipo','badge_texto','audio_url','audio_titulo','video_youtube','imagen_cover',
            'veredicto_texto','pros','contras','links_html','publicada','fecha_publicacion',
        ]));
        if ($id) $params['id'] = $id;

        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        if (!$id) {
            $id = (int)$db->lastInsertId();
            $juego['id'] = $id;
        }

        // Guardar datos pinball si el género es Pinball
        if (($juego['genero'] ?? '') === 'Pinball') {
            $pb_campos = ['pb_fabricante','pb_sistema','pb_bolas','pb_multibola','pb_unidades','pb_precio','pb_emulacion'];
            $db->prepare("DELETE FROM pinball_datos WHERE juego_id = ?")->execute([$id]);
            $ins = $db->prepare("INSERT INTO pinball_datos (juego_id, clave, valor) VALUES (?, ?, ?)");
            foreach ($pb_campos as $campo) {
                $valor = $campo === 'pb_multibola' ? (isset($_POST['pb_multibola']) ? '1' : '0') : trim($_POST[$campo] ?? '');
                $ins->execute([$id, $campo, $valor]);
            }
        }

        $msg = 'ok';
    } catch (PDOException $e) {
        $msg = 'err:' . $e->getMessage();
    }
}

// Cargar datos pinball si los hay
$pb = [];
if ($id) {
    $pb_rows = $db->query("SELECT clave, valor FROM pinball_datos WHERE juego_id = $id")->fetchAll();
    foreach ($pb_rows as $r) $pb[$r['clave']] = $r['valor'];
    $juego = array_merge($juego, $pb);
}

$generos = ['Lucha','Beat \'em up','Shooter','Plataformas','Carreras','Puzzle','Deportes','Acción','Run & gun','RPG','Aventura','Pinball','Otros'];
$badges  = ['destacada' => 'DESTACADA','clasico' => 'CLÁSICO','nuevo' => 'NUEVO','infamia' => 'INFAMIA','especial' => 'ESPECIAL'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $id ? 'Editar' : 'Nueva' ?> reseña — Admin MY ARCADE ZONE</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Share+Tech+Mono&family=Rajdhani:wght@400;600;700&display=swap">
<link rel="stylesheet" href="/assets/css/style.css">
<style>
body::before { display:none; }
.admin-topbar {
  background:var(--negro-panel); border-bottom:2px solid var(--cyan);
  padding:12px 24px; display:flex; align-items:center; justify-content:space-between;
}
.admin-topbar-logo {
  font-family:'Bebas Neue',sans-serif; font-size:20px; letter-spacing:5px;
  background:linear-gradient(180deg,#fff,var(--cyan));
  -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;
}
.tabs { display:flex; gap:0; margin-bottom:20px; border-bottom:1px solid rgba(0,238,255,0.15); }
.tab {
  font-family:'Bebas Neue',sans-serif; font-size:12px; letter-spacing:3px;
  color:#444; padding:10px 20px; cursor:pointer; border-bottom:2px solid transparent;
  transition:all .2s;
}
.tab.active { color:var(--cyan); border-bottom-color:var(--cyan); }
.tab-panel { display:none; }
.tab-panel.active { display:block; }
.slug-preview {
  font-family:'Share Tech Mono',monospace; font-size:10px;
  color:var(--cyan); margin-top:4px; letter-spacing:1px;
}
</style>
</head>
<body>
<div class="admin-topbar">
  <div class="admin-topbar-logo">
    <?= $id ? 'EDITAR RESEÑA' : 'NUEVA RESEÑA' ?>
  </div>
  <div style="font-family:'Share Tech Mono',monospace;font-size:10px;color:#444;display:flex;gap:16px">
    <?php if ($id): ?>
    <a href="/admin/secciones-form.php?juego_id=<?= $id ?>" style="color:var(--cyan)">EDITAR SECCIONES ►</a>
    <?php endif; ?>
    <a href="/admin/" style="color:#555">◄ VOLVER</a>
  </div>
</div>

<div class="admin-wrap">

  <?php if ($msg === 'ok'): ?>
  <div class="admin-alert admin-alert-ok">✓ Guardado correctamente.<?php if (!$id): ?> <a href="/admin/secciones-form.php?juego_id=<?= $juego['id'] ?>" style="color:var(--verde)">→ Añadir secciones de texto</a><?php endif; ?></div>
  <?php elseif (str_starts_with($msg, 'err:')): ?>
  <div class="admin-alert admin-alert-err">✕ Error: <?= htmlspecialchars(substr($msg, 4)) ?></div>
  <?php endif; ?>

  <form method="POST">

    <div class="tabs">
      <div class="tab active" onclick="showTab('basicos')">DATOS BÁSICOS</div>
      <div class="tab" onclick="showTab('resena')">RESEÑA</div>
      <div class="tab" onclick="showTab('media')">MEDIA</div>
    </div>

    <!-- TAB: DATOS BÁSICOS -->
    <div class="tab-panel active" id="tab-basicos">
      <div class="admin-card">
        <div class="admin-card-header">📋 FICHA TÉCNICA</div>
        <div class="admin-card-body">

          <div class="admin-form-row">
            <label>TÍTULO *</label>
            <input type="text" name="titulo" value="<?= htmlspecialchars($juego['titulo']) ?>" required
                   oninput="autoSlug(this.value)" placeholder="Street Fighter II">
          </div>
          <div class="admin-form-row">
            <label>SLUG *<br><small style="color:#333">(URL)</small></label>
            <div>
              <input type="text" name="slug" id="slug" value="<?= htmlspecialchars($juego['slug']) ?>" required
                     placeholder="streetfighter2" pattern="[a-z0-9-]+">
              <div class="slug-preview">URL: /resena.php?slug=<span id="slug-preview"><?= htmlspecialchars($juego['slug']) ?></span></div>
            </div>
          </div>
          <div class="admin-form-row">
            <label>DESARROLLADOR</label>
            <input type="text" name="desarrollador" value="<?= htmlspecialchars($juego['desarrollador']) ?>" placeholder="Capcom">
          </div>
          <div class="admin-form-row">
            <label>PUBLISHER</label>
            <input type="text" name="publisher" value="<?= htmlspecialchars($juego['publisher']) ?>" placeholder="Capcom">
          </div>
          <div class="admin-form-row">
            <label>AÑO</label>
            <input type="number" name="anno" value="<?= htmlspecialchars($juego['anno']) ?>" placeholder="1991" min="1975" max="2000">
          </div>
          <div class="admin-form-row">
            <label>GÉNERO</label>
            <select name="genero">
              <option value="">— Sin género —</option>
              <?php foreach ($generos as $g): ?>
              <option value="<?= htmlspecialchars($g) ?>" <?= $juego['genero'] === $g ? 'selected' : '' ?>><?= htmlspecialchars($g) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <!-- BLOQUE PINBALL — solo visible cuando género = Pinball -->
          <div id="pinball-datos" style="display:<?= $juego['genero'] === 'Pinball' ? 'block' : 'none' ?>">
            <div class="admin-card" style="margin:16px 0;border-color:rgba(255,215,0,0.2)">
              <div class="admin-card-header" style="color:var(--amarillo);border-color:rgba(255,215,0,0.15)">
                🎰 DATOS ESPECÍFICOS DE PINBALL
              </div>
              <div class="admin-card-body">
                <div class="admin-form-row">
                  <label>FABRICANTE</label>
                  <input type="text" name="pb_fabricante" value="<?= htmlspecialchars($juego['pb_fabricante'] ?? '') ?>" placeholder="Williams, Bally, Stern, Gottlieb...">
                </div>
                <div class="admin-form-row">
                  <label>SISTEMA</label>
                  <input type="text" name="pb_sistema" value="<?= htmlspecialchars($juego['pb_sistema'] ?? '') ?>" placeholder="WPC, System 11, Solid State...">
                </div>
                <div class="admin-form-row">
                  <label>NÚMERO DE BOLAS</label>
                  <input type="number" name="pb_bolas" value="<?= htmlspecialchars($juego['pb_bolas'] ?? '3') ?>" min="1" max="9">
                </div>
                <div class="admin-form-row" style="align-items:center">
                  <label>MULTIBOLA</label>
                  <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                    <input type="checkbox" name="pb_multibola" value="1" <?= !empty($juego['pb_multibola']) ? 'checked' : '' ?>>
                    <span style="font-family:'Share Tech Mono',monospace;font-size:11px;color:#666">Sí tiene multibola</span>
                  </label>
                </div>
                <div class="admin-form-row">
                  <label>UNIDADES PRODUCIDAS</label>
                  <input type="text" name="pb_unidades" value="<?= htmlspecialchars($juego['pb_unidades'] ?? '') ?>" placeholder="20.270">
                </div>
                <div class="admin-form-row">
                  <label>PRECIO COLECCIONISTA</label>
                  <input type="text" name="pb_precio" value="<?= htmlspecialchars($juego['pb_precio'] ?? '') ?>" placeholder="3.000€ – 6.000€">
                </div>
                <div class="admin-form-row">
                  <label>EMULACIÓN</label>
                  <input type="text" name="pb_emulacion" value="<?= htmlspecialchars($juego['pb_emulacion'] ?? '') ?>" placeholder="Visual Pinball X, PinballFX...">
                </div>
              </div>
            </div>
          </div>
          <script>
          document.querySelector('select[name="genero"]').addEventListener('change', function() {
            document.getElementById('pinball-datos').style.display = this.value === 'Pinball' ? 'block' : 'none';
          });
          </script>

          <div class="admin-form-row">
            <label>PLATAFORMA ORIGINAL</label>
            <input type="text" name="plataforma_original" value="<?= htmlspecialchars($juego['plataforma_original']) ?>" placeholder="Arcade (CPS-2)">
          </div>
          <div class="admin-form-row">
            <label>OTRAS PLATAFORMAS</label>
            <input type="text" name="plataformas" value="<?= htmlspecialchars($juego['plataformas']) ?>" placeholder="SNES, MD, PS1...">
          </div>
          <div class="admin-form-row">
            <label>DESCRIPCIÓN CORTA</label>
            <input type="text" name="descripcion_corta" value="<?= htmlspecialchars($juego['descripcion_corta']) ?>"
                   placeholder="El juego de lucha que lo cambió todo">
          </div>
          <div class="admin-form-row">
            <label>NOTA (0–10)</label>
            <input type="number" name="nota" value="<?= htmlspecialchars($juego['nota']) ?>" step="0.1" min="0" max="10" placeholder="9.5">
          </div>
          <div class="admin-form-row">
            <label>BADGE TIPO</label>
            <select name="badge_tipo">
              <?php foreach ($badges as $val => $lbl): ?>
              <option value="<?= $val ?>" <?= $juego['badge_tipo'] === $val ? 'selected' : '' ?>><?= $lbl ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="admin-form-row">
            <label>BADGE TEXTO<br><small style="color:#333">(vacío = sin badge)</small></label>
            <input type="text" name="badge_texto" value="<?= htmlspecialchars($juego['badge_texto']) ?>"
                   placeholder="RESEÑA DESTACADA" maxlength="100">
          </div>
          <div class="admin-form-row">
            <label>FECHA PUBLICACIÓN</label>
            <input type="date" name="fecha_publicacion" value="<?= htmlspecialchars($juego['fecha_publicacion']) ?>">
          </div>
          <div class="admin-form-row">
            <label>PUBLICAR</label>
            <label style="display:flex;align-items:center;gap:8px;color:var(--blanco);padding-top:8px;cursor:pointer">
              <input type="checkbox" name="publicada" value="1" <?= $juego['publicada'] ? 'checked' : '' ?>>
              Marcar como publicada (visible en el sitio)
            </label>
          </div>

        </div>
      </div>
    </div>

    <!-- TAB: RESEÑA -->
    <div class="tab-panel" id="tab-resena">
      <div class="admin-card">
        <div class="admin-card-header">📝 VEREDICTO Y PROS/CONTRAS</div>
        <div class="admin-card-body">
          <div class="admin-form-row">
            <label>VEREDICTO<br><small style="color:#333">Texto final</small></label>
            <textarea name="veredicto_texto" rows="5" placeholder="Conclusión general del análisis..."><?= htmlspecialchars($juego['veredicto_texto']) ?></textarea>
          </div>
          <div class="admin-form-row">
            <label>LO MEJOR<br><small style="color:#333">Una línea por ítem</small></label>
            <textarea name="pros" rows="6" placeholder="Gráficos espectaculares para la época&#10;Sistema de combo innovador&#10;Banda sonora memorable"><?= htmlspecialchars($juego['pros']) ?></textarea>
          </div>
          <div class="admin-form-row">
            <label>LO PEOR<br><small style="color:#333">Una línea por ítem</small></label>
            <textarea name="contras" rows="6" placeholder="Dificultad muy elevada&#10;Modo 1 jugador limitado"><?= htmlspecialchars($juego['contras']) ?></textarea>
          </div>
          <div class="admin-form-row">
            <label>ENLACES<br><small style="color:#333">HTML directo<br>(tags &lt;a&gt;)</small></label>
            <textarea name="links_html" rows="5" placeholder='<a href="https://..." target="_blank">Lemon64 — Capturas y base de datos</a>'><?= htmlspecialchars($juego['links_html']) ?></textarea>
          </div>
        </div>
      </div>
    </div>

    <!-- TAB: MEDIA -->
    <div class="tab-panel" id="tab-media">
      <div class="admin-card">
        <div class="admin-card-header">🖼️ IMAGEN Y AUDIO</div>
        <div class="admin-card-body">
          <div class="admin-form-row">
            <label>IMAGEN COVER<br><small style="color:#333">URL relativa o absoluta</small></label>
            <input type="text" name="imagen_cover" value="<?= htmlspecialchars($juego['imagen_cover']) ?>"
                   placeholder="/assets/images/sf2_cover.jpg">
          </div>
          <div class="admin-form-row">
            <label>AUDIO URL<br><small style="color:#333">MP3 — dejar vacío si no hay</small></label>
            <input type="text" name="audio_url" value="<?= htmlspecialchars($juego['audio_url']) ?>"
                   placeholder="https://... o /assets/audio/sf2.mp3">
          </div>
          <div class="admin-form-row">
            <label>AUDIO TÍTULO</label>
            <input type="text" name="audio_titulo" value="<?= htmlspecialchars($juego['audio_titulo']) ?>"
                   placeholder="Guile's Theme · Street Fighter II CPS-2">
          </div>
          <div class="admin-form-row">
            <label>VÍDEO YOUTUBE<br><small style="color:#333">URL completa o ID del vídeo</small></label>
            <input type="text" name="video_youtube" value="<?= htmlspecialchars($juego['video_youtube']) ?>"
                   placeholder="https://www.youtube.com/watch?v=... o ID como mUOFZG-TM6s">
            <div class="admin-field-hint">Aparece automáticamente después de la primera sección de la reseña.</div>
          </div>
        </div>
      </div>
    </div>

    <div style="display:flex;gap:12px;margin-top:4px;align-items:center">
      <button type="submit" class="admin-btn admin-btn-primary">
        <?= $id ? '► GUARDAR CAMBIOS' : '► CREAR RESEÑA' ?>
      </button>
      <a href="/admin/" class="admin-btn" style="color:#555;border-color:#333;text-decoration:none;display:inline-flex;align-items:center">CANCELAR</a>
      <?php if ($id): ?>
      <button type="submit" name="action" value="delete" class="admin-btn admin-btn-danger"
              onclick="return confirm('¿Eliminar «<?= htmlspecialchars($juego['titulo']) ?>»? Esta acción no se puede deshacer.')">
        ✕ ELIMINAR RESEÑA
      </button>
      <?php endif; ?>
    </div>

  </form>
</div>

<script>
function showTab(name) {
  document.querySelectorAll('.tab').forEach((t,i) => {
    const ids = ['basicos','resena','media'];
    t.classList.toggle('active', ids[i] === name);
    document.getElementById('tab-' + ids[i]).classList.toggle('active', ids[i] === name);
  });
}
function autoSlug(titulo) {
  const slug = titulo.toLowerCase()
    .normalize('NFD').replace(/[\u0300-\u036f]/g,'')
    .replace(/[^a-z0-9]+/g,'-')
    .replace(/^-|-$/g,'');
  const slugInput = document.getElementById('slug');
  if (!slugInput.dataset.manual) {
    slugInput.value = slug;
    document.getElementById('slug-preview').textContent = slug;
  }
}
document.getElementById('slug').addEventListener('input', function() {
  this.dataset.manual = '1';
  document.getElementById('slug-preview').textContent = this.value;
});
</script>
</body>
</html>
