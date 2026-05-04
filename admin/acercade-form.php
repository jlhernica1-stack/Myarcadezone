<?php
require __DIR__ . '/_auth.php';
require_once dirname(__DIR__) . '/includes/db.php';
$db = db();

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $texto = trim($_POST['acerca_texto'] ?? '');
    $logo  = trim($_POST['acerca_logo']  ?? '');

    $db->prepare("INSERT INTO site_config (clave, valor) VALUES ('acerca_texto', ?)
                  ON DUPLICATE KEY UPDATE valor = VALUES(valor)")
       ->execute([$texto]);

    $db->prepare("INSERT INTO site_config (clave, valor) VALUES ('acerca_logo', ?)
                  ON DUPLICATE KEY UPDATE valor = VALUES(valor)")
       ->execute([$logo]);

    $msg = 'ok';
}

$cfg   = $db->query("SELECT clave, valor FROM site_config WHERE clave IN ('acerca_texto','acerca_logo')")
            ->fetchAll(PDO::FETCH_KEY_PAIR);
$texto = $cfg['acerca_texto'] ?? '';
$logo  = $cfg['acerca_logo']  ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Acerca de — Admin MY ARCADE ZONE</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Share+Tech+Mono&family=Rajdhani:wght@400;600;700&display=swap">
<link rel="stylesheet" href="/assets/css/style.css">
<style>
body::before { display:none; }
.admin-topbar {
  background:var(--negro-panel);
  border-bottom:2px solid var(--cyan);
  padding:12px 24px;
  display:flex; align-items:center; justify-content:space-between;
}
.admin-topbar-logo {
  font-family:'Bebas Neue',sans-serif; font-size:22px; letter-spacing:5px;
  background:linear-gradient(180deg,#fff,var(--cyan));
  -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;
}
.admin-topbar-sub { font-family:'Share Tech Mono',monospace; font-size:9px; letter-spacing:3px; color:var(--magenta); }
.admin-topbar-right a { font-family:'Share Tech Mono',monospace; font-size:10px; letter-spacing:2px; color:#444; text-decoration:none; transition:color .2s; }
.admin-topbar-right a:hover { color:var(--rojo); }
</style>
</head>
<body>

<div class="admin-topbar">
  <div>
    <div class="admin-topbar-logo">MY ARCADE ZONE</div>
    <div class="admin-topbar-sub">◄ ACERCA DE ►</div>
  </div>
  <div class="admin-topbar-right">
    <a href="/admin/">← PANEL</a>
    &nbsp;&nbsp;
    <a href="/acercade.php" target="_blank" style="color:var(--cyan)">VER PÁGINA</a>
  </div>
</div>

<div class="admin-wrap">

  <?php if ($msg === 'ok'): ?>
  <div style="background:rgba(0,238,255,0.08);border:1px solid var(--cyan);padding:12px 18px;
              font-family:'Share Tech Mono',monospace;font-size:11px;color:var(--cyan);margin-bottom:20px">
    ✓ GUARDADO CORRECTAMENTE
  </div>
  <?php endif; ?>

  <form method="post">
    <div class="admin-card">
      <div class="admin-card-header">★ ACERCA DE — CONTENIDO</div>
      <div class="admin-card-body">

        <!-- LOGO / IMAGEN -->
        <div class="admin-field" style="margin-bottom:24px">
          <label>URL DEL LOGO / IMAGEN</label>
          <input type="text" name="acerca_logo"
                 value="<?= htmlspecialchars($logo) ?>"
                 placeholder="/assets/img/logo-acercade.png"
                 style="width:100%">
          <div style="font-family:'Share Tech Mono',monospace;font-size:10px;color:#444;margin-top:6px">
            Déjalo vacío para no mostrar imagen. Recomendado: PNG con fondo transparente.
          </div>
          <?php if ($logo): ?>
          <div style="margin-top:12px;text-align:center;padding:16px;background:rgba(0,238,255,0.04);border:1px solid rgba(0,238,255,0.1)">
            <img src="<?= htmlspecialchars($logo) ?>" alt="preview"
                 style="max-height:120px;max-width:100%;object-fit:contain;
                        filter:drop-shadow(0 0 12px rgba(0,238,255,0.3))">
          </div>
          <?php endif; ?>
        </div>

        <!-- TEXTO HTML -->
        <div class="admin-field">
          <label>TEXTO DE LA PÁGINA <span style="color:#444;font-size:10px">(HTML libre)</span></label>
          <textarea name="acerca_texto" rows="20"
                    style="width:100%;font-family:'Share Tech Mono',monospace;font-size:12px;line-height:1.6"><?= htmlspecialchars($texto) ?></textarea>
        </div>

        <div style="margin-top:20px;display:flex;gap:12px;align-items:center">
          <button type="submit" class="admin-btn admin-btn-primary">GUARDAR CAMBIOS</button>
          <a href="/acercade.php" target="_blank"
             class="admin-btn" style="color:var(--cyan);border-color:var(--cyan)">VER RESULTADO</a>
        </div>

      </div>
    </div>
  </form>

</div>
</body>
</html>
