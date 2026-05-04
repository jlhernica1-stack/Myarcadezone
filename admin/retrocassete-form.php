<?php
require __DIR__ . '/_auth.php';
require_once dirname(__DIR__) . '/includes/db.php';

$db  = db();
$id  = (int)($_GET['id'] ?? 0);
$msg = '';

$track = ['titulo' => '', 'juego' => '', 'compositor' => '', 'url_audio' => '', 'orden' => 0, 'publicado' => 1];

if ($id) {
    $row = $db->prepare("SELECT * FROM retrocassete_tracks WHERE id = ?");
    $row->execute([$id]);
    $row = $row->fetch();
    if ($row) $track = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $db->prepare("DELETE FROM retrocassete_tracks WHERE id = ?")->execute([$id]);
        header('Location: retrocassete-list.php'); exit;
    }

    $track['titulo']     = trim($_POST['titulo'] ?? '');
    $track['juego']      = trim($_POST['juego'] ?? '');
    $track['compositor'] = trim($_POST['compositor'] ?? '');
    $track['url_audio']  = trim($_POST['url_audio'] ?? '');
    $track['orden']      = (int)($_POST['orden'] ?? 0);
    $track['publicado']  = isset($_POST['publicado']) ? 1 : 0;

    try {
        if ($id) {
            $db->prepare("UPDATE retrocassete_tracks SET titulo=?,juego=?,compositor=?,url_audio=?,orden=?,publicado=? WHERE id=?")
               ->execute([$track['titulo'],$track['juego'],$track['compositor'],$track['url_audio'],$track['orden'],$track['publicado'],$id]);
        } else {
            $db->prepare("INSERT INTO retrocassete_tracks (titulo,juego,compositor,url_audio,orden,publicado) VALUES (?,?,?,?,?,?)")
               ->execute([$track['titulo'],$track['juego'],$track['compositor'],$track['url_audio'],$track['orden'],$track['publicado']]);
            $id = (int)$db->lastInsertId();
        }
        $msg = 'ok';
    } catch (PDOException $e) {
        $msg = 'err:' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $id ? 'Editar' : 'Nueva' ?> pista — Admin MY ARCADE ZONE</title>
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
</style>
</head>
<body>
<div class="admin-topbar">
  <div class="admin-topbar-logo"><?= $id ? 'EDITAR PISTA' : 'NUEVA PISTA' ?></div>
  <div style="font-family:'Share Tech Mono',monospace;font-size:10px;color:#444;display:flex;gap:16px">
    <a href="/admin/retrocassete-list.php" style="color:var(--cyan)">◄ LISTA</a>
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
      <div class="admin-card-header">🎵 DATOS DE LA PISTA</div>
      <div class="admin-card-body">

        <div class="admin-form-row">
          <label>TÍTULO *</label>
          <input type="text" name="titulo" value="<?= htmlspecialchars($track['titulo']) ?>"
                 required placeholder="STREET FIGHTER II — RYU THEME">
        </div>
        <div class="admin-form-row">
          <label>JUEGO *</label>
          <input type="text" name="juego" value="<?= htmlspecialchars($track['juego']) ?>"
                 required placeholder="Street Fighter II: The World Warrior (1991)">
        </div>
        <div class="admin-form-row">
          <label>COMPOSITOR</label>
          <input type="text" name="compositor" value="<?= htmlspecialchars($track['compositor'] ?? '') ?>"
                 placeholder="Yoko Shimomura">
        </div>
        <div class="admin-form-row">
          <label>URL DE AUDIO *<br><small style="color:#555">Enlace directo a archive.org (MP3)</small></label>
          <input type="url" name="url_audio" value="<?= htmlspecialchars($track['url_audio']) ?>"
                 required placeholder="https://archive.org/download/...">
        </div>
        <div class="admin-form-row">
          <label>ORDEN</label>
          <input type="number" name="orden" value="<?= $track['orden'] ?>" min="0" max="999" style="width:100px">
        </div>
        <div class="admin-form-row">
          <label>ACTIVA</label>
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
            <input type="checkbox" name="publicado" value="1" <?= $track['publicado'] ? 'checked' : '' ?>>
            <span style="font-family:'Share Tech Mono',monospace;font-size:11px">Visible en el player</span>
          </label>
        </div>

      </div>
    </div>

    <?php if ($track['url_audio']): ?>
    <div class="admin-card">
      <div class="admin-card-header">▶ PREESCUCHA</div>
      <div class="admin-card-body">
        <audio controls src="<?= htmlspecialchars($track['url_audio']) ?>"
               style="width:100%;filter:invert(1) hue-rotate(180deg)"></audio>
        <p style="font-family:'Share Tech Mono',monospace;font-size:9px;color:#333;margin-top:8px">
          Si no reproduce, verifica que la URL de archive.org sea correcta y accesible.
        </p>
      </div>
    </div>
    <?php endif; ?>

    <div style="display:flex;gap:12px;margin-bottom:24px;align-items:center">
      <button type="submit" class="admin-btn admin-btn-primary">
        <?= $id ? '► GUARDAR CAMBIOS' : '► CREAR PISTA' ?>
      </button>
      <?php if ($id): ?>
      <button type="submit" name="action" value="delete"
              class="admin-btn admin-btn-danger"
              onclick="return confirm('¿Eliminar esta pista?')">✕ ELIMINAR</button>
      <?php endif; ?>
    </div>

  </form>

</div>
</body>
</html>
