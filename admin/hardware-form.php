<?php
require __DIR__ . '/_auth.php';
require_once dirname(__DIR__) . '/includes/db.php';

$db  = db();
$id  = (int)($_GET['id'] ?? 0);
$msg = '';

$hw = [
    'slug' => '', 'nombre' => '', 'fabricante' => '',
    'anno' => '', 'categoria' => 'placa',
    'imagen_cover' => '', 'descripcion_html' => '', 'publicado' => 0,
];
$specs_actuales  = [];
$galeria_actual  = [];

if ($id) {
    $row = $db->prepare("SELECT * FROM hardware WHERE id = ?");
    $row->execute([$id]);
    $row = $row->fetch();
    if ($row) {
        $hw = $row;
        $s = $db->prepare("SELECT * FROM hardware_specs WHERE hardware_id = ? ORDER BY orden");
        $s->execute([$id]);
        $specs_actuales = $s->fetchAll();
        $g = $db->prepare("SELECT * FROM hardware_galeria WHERE hardware_id = ? ORDER BY orden");
        $g->execute([$id]);
        $galeria_actual = $g->fetchAll();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Eliminar spec
    if (isset($_POST['action']) && $_POST['action'] === 'delete_spec') {
        $db->prepare("DELETE FROM hardware_specs WHERE id = ? AND hardware_id = ?")
           ->execute([(int)$_POST['spec_id'], $id]);
        header("Location: hardware-form.php?id=$id&msg=ok"); exit;
    }

    // Eliminar foto
    if (isset($_POST['action']) && $_POST['action'] === 'delete_foto') {
        $db->prepare("DELETE FROM hardware_galeria WHERE id = ? AND hardware_id = ?")
           ->execute([(int)$_POST['foto_id'], $id]);
        header("Location: hardware-form.php?id=$id&msg=ok"); exit;
    }

    $hw['slug']           = preg_replace('/[^a-z0-9-]/', '', strtolower(trim($_POST['slug'] ?? '')));
    $hw['nombre']         = trim($_POST['nombre'] ?? '');
    $hw['fabricante']     = trim($_POST['fabricante'] ?? '');
    $hw['anno']           = (int)($_POST['anno'] ?? 0) ?: null;
    $hw['categoria']      = $_POST['categoria'] ?? 'placa';
    $hw['imagen_cover']   = trim($_POST['imagen_cover'] ?? '');
    $hw['descripcion_html'] = trim($_POST['descripcion_html'] ?? '');
    $hw['publicado']      = isset($_POST['publicado']) ? 1 : 0;

    try {
        if ($id) {
            $db->prepare("UPDATE hardware SET slug=?,nombre=?,fabricante=?,anno=?,categoria=?,imagen_cover=?,descripcion_html=?,publicado=? WHERE id=?")
               ->execute([$hw['slug'],$hw['nombre'],$hw['fabricante'],$hw['anno'],$hw['categoria'],$hw['imagen_cover'],$hw['descripcion_html'],$hw['publicado'],$id]);
        } else {
            $db->prepare("INSERT INTO hardware (slug,nombre,fabricante,anno,categoria,imagen_cover,descripcion_html,publicado) VALUES (?,?,?,?,?,?,?,?)")
               ->execute([$hw['slug'],$hw['nombre'],$hw['fabricante'],$hw['anno'],$hw['categoria'],$hw['imagen_cover'],$hw['descripcion_html'],$hw['publicado']]);
            $id = (int)$db->lastInsertId();
        }

        // Añadir nueva spec
        if (!empty($_POST['nueva_clave']) && !empty($_POST['nuevo_valor'])) {
            $max = $db->prepare("SELECT MAX(orden) FROM hardware_specs WHERE hardware_id = ?");
            $max->execute([$id]);
            $orden = ((int)$max->fetchColumn()) + 1;
            $db->prepare("INSERT INTO hardware_specs (hardware_id, clave, valor, orden) VALUES (?,?,?,?)")
               ->execute([$id, trim($_POST['nueva_clave']), trim($_POST['nuevo_valor']), $orden]);
        }

        // Añadir foto a galería
        if (!empty($_POST['nueva_foto_url'])) {
            $max = $db->prepare("SELECT MAX(orden) FROM hardware_galeria WHERE hardware_id = ?");
            $max->execute([$id]);
            $orden = ((int)$max->fetchColumn()) + 1;
            $db->prepare("INSERT INTO hardware_galeria (hardware_id, imagen_url, caption, orden) VALUES (?,?,?,?)")
               ->execute([$id, trim($_POST['nueva_foto_url']), trim($_POST['nueva_foto_caption'] ?? ''), $orden]);
        }

        $msg = 'ok';

        $s = $db->prepare("SELECT * FROM hardware_specs WHERE hardware_id = ? ORDER BY orden");
        $s->execute([$id]);
        $specs_actuales = $s->fetchAll();
        $g = $db->prepare("SELECT * FROM hardware_galeria WHERE hardware_id = ? ORDER BY orden");
        $g->execute([$id]);
        $galeria_actual = $g->fetchAll();

    } catch (PDOException $e) {
        $msg = 'err:' . $e->getMessage();
    }
}

if (isset($_GET['msg']) && $_GET['msg'] === 'ok') $msg = 'ok';

$categorias = ['placa'=>'Placa arcade','monitor'=>'Monitor','cabinet'=>'Cabinet','mando'=>'Mando/Control','otro'=>'Otro'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $id ? 'Editar' : 'Nuevo' ?> hardware — Admin MY ARCADE ZONE</title>
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
.dato-row {
  display:flex; gap:8px; align-items:center;
  padding:6px 0; border-bottom:1px solid rgba(255,255,255,0.04);
  font-family:'Share Tech Mono',monospace; font-size:11px;
}
.dato-row .clave { color:#555; width:180px; flex-shrink:0; }
.dato-row .valor { color:var(--amarillo); flex:1; }
.cover-preview { max-width:100%;max-height:180px;display:block;margin-top:8px;border:1px solid rgba(0,238,255,0.2); }
.galeria-thumbs { display:flex; flex-wrap:wrap; gap:8px; margin-bottom:12px; }
.galeria-thumb { position:relative; }
.galeria-thumb img { width:120px; height:80px; object-fit:cover; border:1px solid rgba(0,238,255,0.15); display:block; }
</style>
</head>
<body>
<div class="admin-topbar">
  <div class="admin-topbar-logo"><?= $id ? 'EDITAR HARDWARE' : 'NUEVO HARDWARE' ?></div>
  <div style="font-family:'Share Tech Mono',monospace;font-size:10px;color:#444;display:flex;gap:16px">
    <a href="/admin/hardware-list.php" style="color:var(--cyan)">◄ LISTA</a>
    <a href="/admin/" style="color:#555">◄ ADMIN</a>
  </div>
</div>

<div class="admin-wrap">

  <?php if ($msg === 'ok'): ?>
  <div class="admin-alert admin-alert-ok">✓ Guardado correctamente.</div>
  <?php elseif (str_starts_with((string)$msg, 'err:')): ?>
  <div class="admin-alert admin-alert-err">✕ Error: <?= htmlspecialchars(substr($msg, 4)) ?></div>
  <?php endif; ?>

  <form method="POST">

    <div class="admin-card">
      <div class="admin-card-header">🕹️ DATOS DEL HARDWARE</div>
      <div class="admin-card-body">

        <div class="admin-form-row">
          <label>NOMBRE *</label>
          <input type="text" name="nombre" value="<?= htmlspecialchars($hw['nombre']) ?>"
                 required placeholder="CPS-2" oninput="autoSlug(this.value)">
        </div>
        <div class="admin-form-row">
          <label>SLUG *</label>
          <input type="text" name="slug" id="slug" value="<?= htmlspecialchars($hw['slug']) ?>"
                 required placeholder="cps-2" pattern="[a-z0-9-]+">
        </div>
        <div class="admin-form-row">
          <label>FABRICANTE</label>
          <input type="text" name="fabricante" value="<?= htmlspecialchars($hw['fabricante'] ?? '') ?>"
                 placeholder="Capcom">
        </div>
        <div class="admin-form-row">
          <label>AÑO</label>
          <input type="number" name="anno" value="<?= $hw['anno'] ?>"
                 min="1970" max="2010" placeholder="1993">
        </div>
        <div class="admin-form-row">
          <label>CATEGORÍA</label>
          <select name="categoria">
            <?php foreach ($categorias as $val => $lbl): ?>
            <option value="<?= $val ?>" <?= $hw['categoria'] === $val ? 'selected' : '' ?>><?= $lbl ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="admin-form-row">
          <label>IMAGEN COVER<br><small style="color:#555">URL de la imagen principal</small></label>
          <div>
            <input type="text" name="imagen_cover" id="imagen_cover"
                   value="<?= htmlspecialchars($hw['imagen_cover'] ?? '') ?>"
                   placeholder="/assets/hardware/cps2-board.jpg"
                   oninput="previewCover(this.value)">
            <img src="<?= htmlspecialchars($hw['imagen_cover'] ?? '') ?>" alt=""
                 class="cover-preview" id="cover-preview"
                 <?= empty($hw['imagen_cover']) ? 'style="display:none"' : '' ?>>
          </div>
        </div>
        <div class="admin-form-row">
          <label>PUBLICADO</label>
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
            <input type="checkbox" name="publicado" value="1" <?= $hw['publicado'] ? 'checked' : '' ?>>
            <span style="font-family:'Share Tech Mono',monospace;font-size:11px">Visible en el sitio</span>
          </label>
        </div>
        <div class="admin-form-row">
          <label>DESCRIPCIÓN / FICHA<br><small style="color:#333">HTML libre. Usa clases .hw-bio, .hw-section, .hw-section-title, .hw-games-grid, .hw-game-tag</small></label>
          <textarea name="descripcion_html" rows="14"
                    placeholder="<div class=&quot;hw-bio&quot;><p>Descripción...</p></div>"><?= htmlspecialchars($hw['descripcion_html'] ?? '') ?></textarea>
        </div>

      </div>
    </div>

    <button type="submit" class="admin-btn admin-btn-primary" style="margin-bottom:24px">
      <?= $id ? '► GUARDAR CAMBIOS' : '► CREAR FICHA' ?>
    </button>

  </form>

  <?php if ($id): ?>

  <!-- SPECS TÉCNICAS -->
  <div class="admin-card">
    <div class="admin-card-header">📋 ESPECIFICACIONES TÉCNICAS</div>
    <div class="admin-card-body">

      <?php if ($specs_actuales): ?>
      <div style="margin-bottom:16px">
        <?php foreach ($specs_actuales as $spec): ?>
        <div class="dato-row">
          <span class="clave"><?= htmlspecialchars($spec['clave']) ?></span>
          <span class="valor"><?= htmlspecialchars($spec['valor']) ?></span>
          <form method="POST" style="margin:0">
            <input type="hidden" name="action" value="delete_spec">
            <input type="hidden" name="spec_id" value="<?= $spec['id'] ?>">
            <button type="submit" class="admin-btn admin-btn-danger admin-btn-sm"
                    onclick="return confirm('¿Eliminar?')">✕</button>
          </form>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <form method="POST">
        <div style="display:grid;grid-template-columns:1fr 2fr auto;gap:8px;align-items:end">
          <div>
            <label style="font-family:'Share Tech Mono',monospace;font-size:9px;letter-spacing:2px;color:#555;display:block;margin-bottom:4px">CLAVE</label>
            <input type="text" name="nueva_clave" placeholder="CPU principal"
                   style="width:100%;background:rgba(0,0,20,0.8);border:1px solid rgba(0,238,255,0.15);color:var(--blanco);font-family:'Share Tech Mono',monospace;font-size:11px;padding:8px 10px;outline:none">
          </div>
          <div>
            <label style="font-family:'Share Tech Mono',monospace;font-size:9px;letter-spacing:2px;color:#555;display:block;margin-bottom:4px">VALOR</label>
            <input type="text" name="nuevo_valor" placeholder="Motorola 68000 @ 16 MHz"
                   style="width:100%;background:rgba(0,0,20,0.8);border:1px solid rgba(0,238,255,0.15);color:var(--blanco);font-family:'Share Tech Mono',monospace;font-size:11px;padding:8px 10px;outline:none">
          </div>
          <button type="submit" class="admin-btn admin-btn-primary">+ AÑADIR</button>
        </div>
        <!-- Mantener datos al añadir spec -->
        <input type="hidden" name="slug"            value="<?= htmlspecialchars($hw['slug']) ?>">
        <input type="hidden" name="nombre"          value="<?= htmlspecialchars($hw['nombre']) ?>">
        <input type="hidden" name="fabricante"      value="<?= htmlspecialchars($hw['fabricante'] ?? '') ?>">
        <input type="hidden" name="anno"            value="<?= $hw['anno'] ?>">
        <input type="hidden" name="categoria"       value="<?= htmlspecialchars($hw['categoria']) ?>">
        <input type="hidden" name="imagen_cover"    value="<?= htmlspecialchars($hw['imagen_cover'] ?? '') ?>">
        <input type="hidden" name="descripcion_html" value="<?= htmlspecialchars($hw['descripcion_html'] ?? '') ?>">
        <input type="hidden" name="publicado"       value="<?= $hw['publicado'] ?>">
      </form>

    </div>
  </div>

  <!-- GALERÍA -->
  <div class="admin-card">
    <div class="admin-card-header">📷 GALERÍA DE FOTOS</div>
    <div class="admin-card-body">

      <?php if ($galeria_actual): ?>
      <div class="galeria-thumbs">
        <?php foreach ($galeria_actual as $foto): ?>
        <div class="galeria-thumb">
          <img src="<?= htmlspecialchars($foto['imagen_url']) ?>"
               alt="<?= htmlspecialchars($foto['caption'] ?? '') ?>">
          <?php if ($foto['caption']): ?>
          <div style="font-family:'Share Tech Mono',monospace;font-size:9px;color:#444;margin-top:3px;max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
            <?= htmlspecialchars($foto['caption']) ?>
          </div>
          <?php endif; ?>
          <form method="POST" style="margin:2px 0 0">
            <input type="hidden" name="action" value="delete_foto">
            <input type="hidden" name="foto_id" value="<?= $foto['id'] ?>">
            <button type="submit" class="admin-btn admin-btn-danger admin-btn-sm"
                    onclick="return confirm('¿Eliminar foto?')">✕ QUITAR</button>
          </form>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <form method="POST">
        <div style="display:grid;grid-template-columns:2fr 1fr auto;gap:8px;align-items:end">
          <div>
            <label style="font-family:'Share Tech Mono',monospace;font-size:9px;letter-spacing:2px;color:#555;display:block;margin-bottom:4px">URL DE LA FOTO</label>
            <input type="text" name="nueva_foto_url" placeholder="/assets/hardware/cps2-board.jpg"
                   style="width:100%;background:rgba(0,0,20,0.8);border:1px solid rgba(0,238,255,0.15);color:var(--blanco);font-family:'Share Tech Mono',monospace;font-size:11px;padding:8px 10px;outline:none">
          </div>
          <div>
            <label style="font-family:'Share Tech Mono',monospace;font-size:9px;letter-spacing:2px;color:#555;display:block;margin-bottom:4px">PIE DE FOTO</label>
            <input type="text" name="nueva_foto_caption" placeholder="Vista frontal del A-board"
                   style="width:100%;background:rgba(0,0,20,0.8);border:1px solid rgba(0,238,255,0.15);color:var(--blanco);font-family:'Share Tech Mono',monospace;font-size:11px;padding:8px 10px;outline:none">
          </div>
          <button type="submit" class="admin-btn admin-btn-primary">+ AÑADIR</button>
        </div>
        <input type="hidden" name="slug"            value="<?= htmlspecialchars($hw['slug']) ?>">
        <input type="hidden" name="nombre"          value="<?= htmlspecialchars($hw['nombre']) ?>">
        <input type="hidden" name="fabricante"      value="<?= htmlspecialchars($hw['fabricante'] ?? '') ?>">
        <input type="hidden" name="anno"            value="<?= $hw['anno'] ?>">
        <input type="hidden" name="categoria"       value="<?= htmlspecialchars($hw['categoria']) ?>">
        <input type="hidden" name="imagen_cover"    value="<?= htmlspecialchars($hw['imagen_cover'] ?? '') ?>">
        <input type="hidden" name="descripcion_html" value="<?= htmlspecialchars($hw['descripcion_html'] ?? '') ?>">
        <input type="hidden" name="publicado"       value="<?= $hw['publicado'] ?>">
      </form>

    </div>
  </div>

  <?php endif; ?>

</div>

<script>
function autoSlug(nombre) {
  const slug = nombre.toLowerCase()
    .normalize('NFD').replace(/[̀-ͯ]/g,'')
    .replace(/[^a-z0-9]+/g,'-').replace(/^-|-$/g,'');
  const s = document.getElementById('slug');
  if (!s.dataset.manual) s.value = slug;
}
document.getElementById('slug').addEventListener('input', function() {
  this.dataset.manual = '1';
});
function previewCover(url) {
  const img = document.getElementById('cover-preview');
  if (url) { img.src = url; img.style.display = 'block'; }
  else { img.style.display = 'none'; }
}
</script>
</body>
</html>
