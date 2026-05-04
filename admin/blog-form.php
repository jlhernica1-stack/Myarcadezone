<?php
require __DIR__ . '/_auth.php';
require_once dirname(__DIR__) . '/includes/db.php';
$db = db();

$id   = (int)($_GET['id'] ?? 0);
$post = null;
$msg  = '';

// Cargar post si editamos
if ($id) {
    $post = $db->prepare("SELECT * FROM blog_posts WHERE id = ?");
    $post->execute([$id]);
    $post = $post->fetch();
    if (!$post) { header('Location: /admin/blog-list.php'); exit; }
}

// Eliminar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar'])) {
    $db->prepare("DELETE FROM blog_posts WHERE id = ?")->execute([$id]);
    header('Location: /admin/blog-list.php'); exit;
}

// Guardar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['eliminar'])) {
    $slug       = trim($_POST['slug'] ?? '');
    $titulo     = trim($_POST['titulo'] ?? '');
    $categoria  = trim($_POST['categoria'] ?? '');
    $imagen_url = trim($_POST['imagen_url'] ?? '');
    $video_url  = trim($_POST['video_url'] ?? '');
    $contenido  = trim($_POST['contenido_html'] ?? '');
    $fecha      = trim($_POST['fecha'] ?? date('Y-m-d'));
    $publicado  = isset($_POST['publicado']) ? 1 : 0;

    if ($id) {
        $db->prepare("UPDATE blog_posts SET slug=?,titulo=?,categoria=?,imagen_url=?,video_url=?,contenido_html=?,fecha=?,publicado=? WHERE id=?")
           ->execute([$slug,$titulo,$categoria,$imagen_url,$video_url,$contenido,$fecha,$publicado,$id]);
    } else {
        $db->prepare("INSERT INTO blog_posts (slug,titulo,categoria,imagen_url,video_url,contenido_html,fecha,publicado) VALUES (?,?,?,?,?,?,?,?)")
           ->execute([$slug,$titulo,$categoria,$imagen_url,$video_url,$contenido,$fecha,$publicado]);
        $id = $db->lastInsertId();
        header('Location: /admin/blog-form.php?id=' . $id . '&saved=1'); exit;
    }

    $post = $db->prepare("SELECT * FROM blog_posts WHERE id = ?");
    $post->execute([$id]);
    $post = $post->fetch();
    $msg = 'ok';
}
if (isset($_GET['saved'])) $msg = 'ok';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $id ? 'Editar entrada' : 'Nueva entrada' ?> — Admin MY ARCADE ZONE</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Share+Tech+Mono&family=Rajdhani:wght@400;600;700&display=swap">
<link rel="stylesheet" href="/assets/css/style.css">
<style>body::before{display:none}</style>
</head>
<body>

<div class="admin-topbar">
  <div>
    <div class="admin-topbar-logo">MY ARCADE ZONE</div>
    <div class="admin-topbar-sub">◄ <?= $id ? 'EDITAR ENTRADA' : 'NUEVA ENTRADA' ?> ►</div>
  </div>
  <div class="admin-topbar-right">
    <a href="/admin/">← PANEL</a>
    &nbsp;&nbsp;
    <a href="/admin/blog-list.php">← BLOG</a>
    <?php if ($id && ($post['publicado'] ?? 0)): ?>
    &nbsp;&nbsp;
    <a href="/blog.php?slug=<?= urlencode($post['slug']) ?>" target="_blank" style="color:var(--cyan)">VER ENTRADA</a>
    <?php endif; ?>
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
      <div class="admin-card-header">📝 <?= $id ? 'EDITAR ENTRADA' : 'NUEVA ENTRADA' ?></div>
      <div class="admin-card-body">

        <div class="admin-form-row">
          <label>TÍTULO *</label>
          <input type="text" name="titulo" required value="<?= htmlspecialchars($post['titulo'] ?? '') ?>"
                 placeholder="El templo del cinco duros...">
        </div>

        <div class="admin-form-row">
          <label>SLUG *</label>
          <input type="text" name="slug" required value="<?= htmlspecialchars($post['slug'] ?? '') ?>"
                 placeholder="el-templo-del-cinco-duros">
          <div class="admin-field-hint">Sin espacios ni tildes. Ej: primera-entrada-blog</div>
        </div>

        <div class="admin-form-row">
          <label>CATEGORÍA</label>
          <input type="text" name="categoria" value="<?= htmlspecialchars($post['categoria'] ?? '') ?>"
                 placeholder="NOSTALGIA · ARCADE · OPINIÓN">
        </div>

        <div class="admin-form-row">
          <label>FECHA</label>
          <input type="date" name="fecha" value="<?= htmlspecialchars($post['fecha'] ?? date('Y-m-d')) ?>">
        </div>

        <!-- IMAGEN O VÍDEO HERO -->
        <div class="admin-card" style="margin:20px 0;border-color:rgba(255,0,170,0.2)">
          <div class="admin-card-header" style="color:var(--magenta);border-color:rgba(255,0,170,0.15)">
            🖼️ HERO — IMAGEN O VÍDEO (elige uno)
          </div>
          <div class="admin-card-body">
            <div class="admin-form-row">
              <label>URL IMAGEN</label>
              <input type="text" name="imagen_url" value="<?= htmlspecialchars($post['imagen_url'] ?? '') ?>"
                     placeholder="/assets/images/blog/entrada-1.jpg">
              <div class="admin-field-hint">JPG, PNG. Se ignora si hay vídeo.</div>
            </div>
            <div class="admin-form-row">
              <label>URL VÍDEO</label>
              <input type="text" name="video_url" value="<?= htmlspecialchars($post['video_url'] ?? '') ?>"
                     placeholder="/assets/images/blog/entrada-1.mp4">
              <div class="admin-field-hint">MP4. Si se rellena, tiene prioridad sobre la imagen.</div>
            </div>

            <?php if (!empty($post['video_url'])): ?>
            <video src="<?= htmlspecialchars($post['video_url']) ?>" autoplay loop muted playsinline
                   style="max-height:200px;max-width:100%;margin-top:10px;display:block;object-fit:cover"></video>
            <?php elseif (!empty($post['imagen_url'])): ?>
            <img src="<?= htmlspecialchars($post['imagen_url']) ?>"
                 style="max-height:200px;max-width:100%;margin-top:10px;display:block;object-fit:cover">
            <?php endif; ?>
          </div>
        </div>

        <div class="admin-form-row">
          <label>CONTENIDO HTML *</label>
          <textarea name="contenido_html" rows="28"
                    style="width:100%;font-family:'Share Tech Mono',monospace;font-size:12px;line-height:1.6"><?= htmlspecialchars($post['contenido_html'] ?? '') ?></textarea>
          <div class="admin-field-hint">HTML libre. Usa &lt;h2&gt;, &lt;p&gt;, &lt;strong&gt;, &lt;em&gt;, &lt;hr&gt;, &lt;blockquote&gt;</div>
        </div>

        <div class="admin-form-row" style="align-items:center">
          <label>PUBLICADO</label>
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
            <input type="checkbox" name="publicado" value="1" <?= ($post['publicado'] ?? 0) ? 'checked' : '' ?>>
            <span style="font-family:'Share Tech Mono',monospace;font-size:11px;color:#666">Marcar para publicar</span>
          </label>
        </div>

        <div style="display:flex;gap:12px;align-items:center;margin-top:8px;flex-wrap:wrap">
          <button type="submit" class="admin-btn admin-btn-primary">GUARDAR</button>
          <?php if ($id): ?>
          <a href="/blog.php?slug=<?= urlencode($post['slug'] ?? '') ?>" target="_blank"
             class="admin-btn" style="color:var(--cyan);border-color:var(--cyan)">VER ENTRADA</a>
          <button type="submit" name="eliminar" value="1"
                  onclick="return confirm('¿Eliminar esta entrada?')"
                  class="admin-btn" style="color:var(--rojo);border-color:var(--rojo);margin-left:auto">✕ ELIMINAR</button>
          <?php endif; ?>
        </div>

      </div>
    </div>
  </form>

</div>
</body>
</html>
