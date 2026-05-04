<?php
require __DIR__ . '/_auth.php';
require_once dirname(__DIR__) . '/includes/db.php';

$db  = db();
$id  = (int)($_GET['id'] ?? 0);
$msg = '';

$personaje = [
    'slug' => '', 'nombre' => '', 'juego_origen' => '',
    'sprite_url' => '', 'notas_html' => '',
];
$datos_actuales  = [];
$juegos_vinculados = [];

if ($id) {
    $row = $db->prepare("SELECT * FROM personajes WHERE id = ?");
    $row->execute([$id]);
    $row = $row->fetch();
    if ($row) {
        $personaje = $row;
        $d = $db->prepare("SELECT * FROM personaje_datos WHERE personaje_id = ? ORDER BY orden");
        $d->execute([$id]);
        $datos_actuales = $d->fetchAll();
        $j = $db->prepare("SELECT juego_id FROM juego_personaje WHERE personaje_id = ?");
        $j->execute([$id]);
        $juegos_vinculados = array_column($j->fetchAll(), 'juego_id');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['action']) && $_POST['action'] === 'delete_dato') {
        $db->prepare("DELETE FROM personaje_datos WHERE id = ? AND personaje_id = ?")
           ->execute([(int)$_POST['dato_id'], $id]);
        header("Location: personaje-form.php?id=$id&msg=ok"); exit;
    }

    $personaje['slug']        = preg_replace('/[^a-z0-9-]/', '', strtolower(trim($_POST['slug'] ?? '')));
    $personaje['nombre']      = trim($_POST['nombre'] ?? '');
    $personaje['juego_origen']= trim($_POST['juego_origen'] ?? '');
    $personaje['sprite_url']  = trim($_POST['sprite_url'] ?? '');
    $personaje['notas_html']  = trim($_POST['notas_html'] ?? '');

    try {
        if ($id) {
            $db->prepare("UPDATE personajes SET slug=?, nombre=?, juego_origen=?, sprite_url=?, notas_html=? WHERE id=?")
               ->execute([$personaje['slug'], $personaje['nombre'], $personaje['juego_origen'], $personaje['sprite_url'], $personaje['notas_html'], $id]);
        } else {
            $db->prepare("INSERT INTO personajes (slug, nombre, juego_origen, sprite_url, notas_html) VALUES (?,?,?,?,?)")
               ->execute([$personaje['slug'], $personaje['nombre'], $personaje['juego_origen'], $personaje['sprite_url'], $personaje['notas_html']]);
            $id = (int)$db->lastInsertId();
        }

        // Vincular juegos
        $db->prepare("DELETE FROM juego_personaje WHERE personaje_id = ?")->execute([$id]);
        foreach ((array)($_POST['juegos'] ?? []) as $jid) {
            $db->prepare("INSERT IGNORE INTO juego_personaje (juego_id, personaje_id) VALUES (?,?)")
               ->execute([(int)$jid, $id]);
        }

        // Añadir nuevo dato clave/valor
        if (!empty($_POST['nuevo_clave']) && !empty($_POST['nuevo_valor'])) {
            $max = $db->prepare("SELECT MAX(orden) FROM personaje_datos WHERE personaje_id = ?");
            $max->execute([$id]);
            $orden = ((int)$max->fetchColumn()) + 1;
            $db->prepare("INSERT INTO personaje_datos (personaje_id, clave, valor, orden) VALUES (?,?,?,?)")
               ->execute([$id, trim($_POST['nuevo_clave']), trim($_POST['nuevo_valor']), $orden]);
        }

        $msg = 'ok';

        // Recargar datos
        $d = $db->prepare("SELECT * FROM personaje_datos WHERE personaje_id = ? ORDER BY orden");
        $d->execute([$id]);
        $datos_actuales = $d->fetchAll();
        $j = $db->prepare("SELECT juego_id FROM juego_personaje WHERE personaje_id = ?");
        $j->execute([$id]);
        $juegos_vinculados = array_column($j->fetchAll(), 'juego_id');

    } catch (PDOException $e) {
        $msg = 'err:' . $e->getMessage();
    }
}

// Todos los juegos para el selector
$todos_juegos = $db->query("SELECT id, titulo, anno FROM juegos ORDER BY anno DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $id ? 'Editar' : 'Nuevo' ?> personaje — Admin MY ARCADE ZONE</title>
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
.juegos-check { display:flex; flex-wrap:wrap; gap:8px; }
.juegos-check label {
  font-family:'Share Tech Mono',monospace; font-size:10px; letter-spacing:1px;
  color:var(--blanco); cursor:pointer;
  background:rgba(0,0,20,0.8);
  border:1px solid rgba(0,238,255,0.15);
  padding:5px 10px; display:flex; align-items:center; gap:6px;
  transition:border-color .2s;
}
.juegos-check label:hover { border-color:var(--cyan); }
.juegos-check input[type=checkbox]:checked + span { color:var(--cyan); }
.dato-row {
  display:flex; gap:8px; align-items:center;
  padding:6px 0; border-bottom:1px solid rgba(255,255,255,0.04);
  font-family:'Share Tech Mono',monospace; font-size:11px;
}
.dato-row .clave { color:#555; width:140px; flex-shrink:0; }
.dato-row .valor { color:var(--amarillo); flex:1; }
.sprite-preview {
  max-width:200px; max-height:200px;
  border:1px solid rgba(0,238,255,0.2);
  background:#050510; display:block; margin-top:8px;
}
</style>
</head>
<body>
<div class="admin-topbar">
  <div class="admin-topbar-logo"><?= $id ? 'EDITAR PERSONAJE' : 'NUEVO PERSONAJE' ?></div>
  <div style="font-family:'Share Tech Mono',monospace;font-size:10px;color:#444;display:flex;gap:16px">
    <a href="/admin/personajes-list.php" style="color:var(--cyan)">◄ LISTA DE PERSONAJES</a>
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

    <!-- DATOS BÁSICOS -->
    <div class="admin-card">
      <div class="admin-card-header">👾 DATOS DEL PERSONAJE</div>
      <div class="admin-card-body">

        <div class="admin-form-row">
          <label>NOMBRE *</label>
          <input type="text" name="nombre" value="<?= htmlspecialchars($personaje['nombre']) ?>"
                 required placeholder="Ryu" oninput="autoSlug(this.value)">
        </div>
        <div class="admin-form-row">
          <label>SLUG *</label>
          <input type="text" name="slug" id="slug" value="<?= htmlspecialchars($personaje['slug']) ?>"
                 required placeholder="ryu" pattern="[a-z0-9-]+">
        </div>
        <div class="admin-form-row">
          <label>JUEGO DE ORIGEN</label>
          <input type="text" name="juego_origen" value="<?= htmlspecialchars($personaje['juego_origen']) ?>"
                 placeholder="Street Fighter II (1991)">
        </div>
        <div class="admin-form-row">
          <label>SPRITE URL<br><small style="color:#555">GIF: /ryu.gif<br>Frames: /ryu-1.png|/ryu-2.png<br>Sheet: sheet|/ryu-sheet.png|5|120|160</small></label>
          <div>
            <input type="text" name="sprite_url" id="sprite_url"
                   value="<?= htmlspecialchars($personaje['sprite_url']) ?>"
                   placeholder="/assets/sprites/ryu.gif"
                   oninput="previewSprite(this.value)">
            <?php if ($personaje['sprite_url']): ?>
            <img src="<?= htmlspecialchars($personaje['sprite_url']) ?>"
                 alt="Sprite" class="sprite-preview" id="sprite-preview">
            <?php else: ?>
            <img src="" alt="" class="sprite-preview" id="sprite-preview" style="display:none">
            <?php endif; ?>
          </div>
        </div>
        <div class="admin-form-row">
          <label>NOTAS<br><small style="color:#333">HTML libre</small></label>
          <textarea name="notas_html" rows="5"
                    placeholder="<p>Descripción del personaje, estilo de lucha, curiosidades...</p>"><?= htmlspecialchars($personaje['notas_html']) ?></textarea>
        </div>

      </div>
    </div>

    <!-- VINCULAR A JUEGOS -->
    <div class="admin-card">
      <div class="admin-card-header">🕹️ APARECE EN ESTOS JUEGOS</div>
      <div class="admin-card-body">
        <?php if ($todos_juegos): ?>
        <div class="juegos-check">
          <?php foreach ($todos_juegos as $j): ?>
          <label>
            <input type="checkbox" name="juegos[]" value="<?= $j['id'] ?>"
                   <?= in_array($j['id'], $juegos_vinculados) ? 'checked' : '' ?>>
            <span><?= htmlspecialchars($j['titulo']) ?> (<?= $j['anno'] ?>)</span>
          </label>
          <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p style="font-family:'Share Tech Mono',monospace;font-size:11px;color:#444">
          No hay juegos creados todavía.
        </p>
        <?php endif; ?>
      </div>
    </div>

    <button type="submit" class="admin-btn admin-btn-primary" style="margin-bottom:24px">
      <?= $id ? '► GUARDAR CAMBIOS' : '► CREAR PERSONAJE' ?>
    </button>

  </form>

  <!-- DATOS CLAVE/VALOR (solo si ya existe el personaje) -->
  <?php if ($id): ?>
  <div class="admin-card">
    <div class="admin-card-header">📋 FICHA DE DATOS</div>
    <div class="admin-card-body">

      <?php if ($datos_actuales): ?>
      <div style="margin-bottom:16px">
        <?php foreach ($datos_actuales as $dato): ?>
        <div class="dato-row">
          <span class="clave"><?= htmlspecialchars($dato['clave']) ?></span>
          <span class="valor"><?= htmlspecialchars($dato['valor']) ?></span>
          <form method="POST" style="margin:0">
            <input type="hidden" name="action" value="delete_dato">
            <input type="hidden" name="dato_id" value="<?= $dato['id'] ?>">
            <button type="submit" class="admin-btn admin-btn-danger admin-btn-sm"
                    onclick="return confirm('¿Eliminar?')">✕</button>
          </form>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <!-- Añadir nuevo dato -->
      <form method="POST">
        <div style="display:grid;grid-template-columns:1fr 2fr auto;gap:8px;align-items:end">
          <div>
            <label style="font-family:'Share Tech Mono',monospace;font-size:9px;letter-spacing:2px;color:#555;display:block;margin-bottom:4px">CLAVE</label>
            <input type="text" name="nuevo_clave" placeholder="Nacionalidad" style="width:100%;background:rgba(0,0,20,0.8);border:1px solid rgba(0,238,255,0.15);color:var(--blanco);font-family:'Share Tech Mono',monospace;font-size:11px;padding:8px 10px;outline:none">
          </div>
          <div>
            <label style="font-family:'Share Tech Mono',monospace;font-size:9px;letter-spacing:2px;color:#555;display:block;margin-bottom:4px">VALOR</label>
            <input type="text" name="nuevo_valor" placeholder="Japón" style="width:100%;background:rgba(0,0,20,0.8);border:1px solid rgba(0,238,255,0.15);color:var(--blanco);font-family:'Share Tech Mono',monospace;font-size:11px;padding:8px 10px;outline:none">
          </div>
          <button type="submit" class="admin-btn admin-btn-primary">+ AÑADIR</button>
        </div>
        <!-- Mantener datos del personaje al añadir dato -->
        <input type="hidden" name="slug"        value="<?= htmlspecialchars($personaje['slug']) ?>">
        <input type="hidden" name="nombre"      value="<?= htmlspecialchars($personaje['nombre']) ?>">
        <input type="hidden" name="juego_origen" value="<?= htmlspecialchars($personaje['juego_origen']) ?>">
        <input type="hidden" name="sprite_url"  value="<?= htmlspecialchars($personaje['sprite_url']) ?>">
        <input type="hidden" name="notas_html"  value="<?= htmlspecialchars($personaje['notas_html']) ?>">
        <?php foreach ($juegos_vinculados as $jid): ?>
        <input type="hidden" name="juegos[]" value="<?= $jid ?>">
        <?php endforeach; ?>
      </form>

    </div>
  </div>
  <?php endif; ?>

</div>

<script>
function autoSlug(nombre) {
  const slug = nombre.toLowerCase()
    .normalize('NFD').replace(/[\u0300-\u036f]/g,'')
    .replace(/[^a-z0-9]+/g,'-').replace(/^-|-$/g,'');
  const s = document.getElementById('slug');
  if (!s.dataset.manual) s.value = slug;
}
document.getElementById('slug').addEventListener('input', function() {
  this.dataset.manual = '1';
});
function previewSprite(url) {
  const img = document.getElementById('sprite-preview');
  if (url) { img.src = url; img.style.display = 'block'; }
  else { img.style.display = 'none'; }
}
</script>
</body>
</html>
