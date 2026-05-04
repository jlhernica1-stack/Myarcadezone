<?php
require __DIR__ . '/_auth.php';
require_once dirname(__DIR__) . '/includes/db.php';

$db        = db();
$juego_id  = (int)($_GET['juego_id'] ?? 0);
$msg       = '';

if (!$juego_id) { header('Location: /admin/'); exit; }

$juego = $db->prepare("SELECT id, titulo, slug FROM juegos WHERE id = ?");
$juego->execute([$juego_id]);
$juego = $juego->fetch();
if (!$juego) { header('Location: /admin/'); exit; }

// Guardar nueva sección
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] === 'add') {
        $max_orden = $db->prepare("SELECT MAX(orden) FROM secciones_resena WHERE juego_id = ?");
        $max_orden->execute([$juego_id]);
        $orden = ((int)$max_orden->fetchColumn()) + 1;
        $stmt = $db->prepare("INSERT INTO secciones_resena (juego_id, orden, titulo_h2, contenido_html) VALUES (?, ?, ?, ?)");
        $stmt->execute([$juego_id, $orden, trim($_POST['titulo_h2']), trim($_POST['contenido_html'])]);
        $msg = 'ok';
    }

    if ($_POST['action'] === 'update') {
        $stmt = $db->prepare("UPDATE secciones_resena SET titulo_h2 = ?, contenido_html = ? WHERE id = ? AND juego_id = ?");
        $stmt->execute([trim($_POST['titulo_h2']), trim($_POST['contenido_html']), (int)$_POST['sec_id'], $juego_id]);
        $msg = 'ok';
    }

    if ($_POST['action'] === 'delete') {
        $stmt = $db->prepare("DELETE FROM secciones_resena WHERE id = ? AND juego_id = ?");
        $stmt->execute([(int)$_POST['sec_id'], $juego_id]);
        $msg = 'ok';
    }

    if ($_POST['action'] === 'reorder') {
        $orden_ids = array_map('intval', explode(',', $_POST['orden'] ?? ''));
        foreach ($orden_ids as $pos => $sec_id) {
            $db->prepare("UPDATE secciones_resena SET orden = ? WHERE id = ? AND juego_id = ?")
               ->execute([$pos, $sec_id, $juego_id]);
        }
        echo json_encode(['ok' => true]); exit;
    }
}

$secciones = $db->prepare("SELECT * FROM secciones_resena WHERE juego_id = ? ORDER BY orden");
$secciones->execute([$juego_id]);
$secciones = $secciones->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Secciones — <?= htmlspecialchars($juego['titulo']) ?></title>
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
  font-family:'Bebas Neue',sans-serif; font-size:16px; letter-spacing:4px;
  background:linear-gradient(180deg,#fff,var(--cyan));
  -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;
}
.sec-item {
  background:var(--negro-card);
  border:1px solid rgba(0,238,255,0.1);
  margin-bottom:10px;
}
.sec-item-header {
  display:flex; align-items:center;
  justify-content:space-between;
  padding:8px 14px;
  background:rgba(0,238,255,0.05);
  border-bottom:1px solid rgba(0,238,255,0.1);
  cursor:grab;
}
.sec-item-title {
  font-family:'Bebas Neue',sans-serif;
  font-size:13px; letter-spacing:2px; color:var(--cyan);
}
.sec-item-body { padding:14px; display:none; }
.sec-item-body.open { display:block; }
.sec-item-body input,
.sec-item-body textarea {
  width:100%; background:rgba(0,0,20,0.8);
  border:1px solid rgba(0,238,255,0.15);
  color:var(--blanco);
  font-family:'Share Tech Mono',monospace;
  font-size:11px; padding:8px 12px; outline:none;
  margin-bottom:8px; letter-spacing:1px;
}
.sec-item-body textarea { resize:vertical; min-height:120px; font-size:12px; }
.drag-handle {
  font-size:14px; color:#333; cursor:grab;
  margin-right:10px; flex-shrink:0;
}
</style>
</head>
<body>
<div class="admin-topbar">
  <div>
    <div class="admin-topbar-logo">SECCIONES — <?= htmlspecialchars(strtoupper($juego['titulo'])) ?></div>
  </div>
  <div style="font-family:'Share Tech Mono',monospace;font-size:10px;color:#444;display:flex;gap:16px">
    <a href="/admin/juego-form.php?id=<?= $juego_id ?>" style="color:var(--cyan)">◄ FICHA TÉCNICA</a>
    <?php if ($juego['slug']): ?>
    <a href="/resena.php?slug=<?= urlencode($juego['slug']) ?>" target="_blank" style="color:#555">VER RESEÑA ↗</a>
    <?php endif; ?>
    <a href="/admin/" style="color:#555">◄ ADMIN</a>
  </div>
</div>

<div class="admin-wrap">

  <?php if ($msg === 'ok'): ?>
  <div class="admin-alert admin-alert-ok">✓ Guardado.</div>
  <?php endif; ?>

  <!-- SECCIONES EXISTENTES -->
  <?php if ($secciones): ?>
  <div class="admin-card">
    <div class="admin-card-header">📄 SECCIONES DEL ARTÍCULO <small style="font-size:10px;color:#555">(arrastra para reordenar)</small></div>
    <div class="admin-card-body">
      <div id="sec-list">
        <?php foreach ($secciones as $sec): ?>
        <div class="sec-item" data-id="<?= $sec['id'] ?>">
          <div class="sec-item-header" onclick="toggleSec(<?= $sec['id'] ?>)">
            <div style="display:flex;align-items:center">
              <span class="drag-handle">⠿</span>
              <span class="sec-item-title"><?= htmlspecialchars($sec['titulo_h2'] ?: '(sin título)') ?></span>
            </div>
            <span style="font-family:'Share Tech Mono',monospace;font-size:9px;color:#333">▼ EDITAR</span>
          </div>
          <div class="sec-item-body" id="sec-body-<?= $sec['id'] ?>">
            <form method="POST">
              <input type="hidden" name="action" value="update">
              <input type="hidden" name="sec_id" value="<?= $sec['id'] ?>">
              <input type="text" name="titulo_h2" value="<?= htmlspecialchars($sec['titulo_h2']) ?>" placeholder="Título de la sección (h2)">
              <textarea name="contenido_html" placeholder="HTML del contenido..."><?= htmlspecialchars($sec['contenido_html']) ?></textarea>
              <div style="display:flex;gap:8px">
                <button type="submit" class="admin-btn admin-btn-primary admin-btn-sm">► GUARDAR</button>
                <button type="submit" form="del-<?= $sec['id'] ?>" class="admin-btn admin-btn-danger admin-btn-sm">✕ ELIMINAR</button>
              </div>
            </form>
            <form method="POST" id="del-<?= $sec['id'] ?>" onsubmit="return confirm('¿Eliminar esta sección?')">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="sec_id" value="<?= $sec['id'] ?>">
            </form>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- NUEVA SECCIÓN -->
  <div class="admin-card">
    <div class="admin-card-header">+ AÑADIR SECCIÓN</div>
    <div class="admin-card-body">
      <form method="POST">
        <input type="hidden" name="action" value="add">
        <div class="admin-form-row">
          <label>TÍTULO H2</label>
          <input type="text" name="titulo_h2" placeholder="LA HISTORIA DETRÁS DEL JUEGO">
        </div>
        <div class="admin-form-row">
          <label>CONTENIDO HTML<br><small style="color:#333">Acepta &lt;p&gt;, &lt;strong&gt;, &lt;em&gt;, &lt;div class="highlight-box"&gt;...</small></label>
          <textarea name="contenido_html" rows="10"
                    placeholder='<p>Texto del párrafo con <strong>palabras en cyan</strong> y <em>palabras en amarillo</em>.</p>&#10;&#10;<div class="highlight-box"><p>Cuadro destacado.</p></div>'></textarea>
        </div>
        <button type="submit" class="admin-btn admin-btn-primary">► AÑADIR SECCIÓN</button>
      </form>
    </div>
  </div>

</div>

<script>
function toggleSec(id) {
  const body = document.getElementById('sec-body-' + id);
  body.classList.toggle('open');
}

/* Drag & drop para reordenar */
let dragged = null;
document.querySelectorAll('.sec-item').forEach(item => {
  item.draggable = true;
  item.addEventListener('dragstart', () => { dragged = item; item.style.opacity = '.4'; });
  item.addEventListener('dragend',   () => { dragged = null; item.style.opacity = ''; saveOrder(); });
  item.addEventListener('dragover',  e => { e.preventDefault(); });
  item.addEventListener('drop', e => {
    e.preventDefault();
    if (dragged && dragged !== item) {
      const list = document.getElementById('sec-list');
      const items = [...list.children];
      const dragIdx = items.indexOf(dragged);
      const dropIdx = items.indexOf(item);
      if (dragIdx < dropIdx) list.insertBefore(dragged, item.nextSibling);
      else list.insertBefore(dragged, item);
    }
  });
});

function saveOrder() {
  const ids = [...document.querySelectorAll('.sec-item')].map(i => i.dataset.id).join(',');
  fetch('', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'action=reorder&juego_id=<?= $juego_id ?>&orden=' + ids
  });
}
</script>
</body>
</html>
