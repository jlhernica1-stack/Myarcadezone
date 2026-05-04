<?php
require_once __DIR__ . '/includes/db.php';

$slug = trim($_GET['slug'] ?? '');
if (!$slug) { header('Location: /hardware.php'); exit; }

$db = db();
$hw = $db->prepare("SELECT * FROM hardware WHERE slug = ? AND publicado = 1");
$hw->execute([$slug]);
$hw = $hw->fetch();
if (!$hw) { header('HTTP/1.0 404 Not Found'); exit('Hardware no encontrado.'); }

$specs = $db->prepare("SELECT clave, valor FROM hardware_specs WHERE hardware_id = ? ORDER BY orden");
$specs->execute([$hw['id']]);
$specs = $specs->fetchAll();

$galeria = $db->prepare("SELECT imagen_url, caption FROM hardware_galeria WHERE hardware_id = ? ORDER BY orden");
$galeria->execute([$hw['id']]);
$galeria = $galeria->fetchAll();

$cat_labels = ['placa'=>'PLACA ARCADE','monitor'=>'MONITOR','cabinet'=>'CABINET','mando'=>'MANDO/CONTROL','otro'=>'HARDWARE'];
$cat_label  = $cat_labels[$hw['categoria']] ?? strtoupper($hw['categoria']);

$current_page = 'hardware';
$page_title   = $hw['nombre'] . ' · Hardware — MY ARCADE ZONE';

require __DIR__ . '/includes/header.php';
?>

<div class="layout">
  <main>

    <!-- Hero de la ficha -->
    <div class="hw-hero">
      <?php if ($hw['imagen_cover']): ?>
      <div class="hw-hero-img">
        <img src="<?= htmlspecialchars($hw['imagen_cover']) ?>"
             alt="<?= htmlspecialchars($hw['nombre']) ?>">
      </div>
      <?php endif; ?>
      <div class="hw-hero-info">
        <div class="hw-hero-breadcrumb">
          <a href="/hardware.php">◄ HARDWARE</a>
          <span>·</span>
          <span><?= $cat_label ?></span>
        </div>
        <div class="hw-hero-name"><?= htmlspecialchars($hw['nombre']) ?></div>
        <div class="hw-hero-meta">
          <?= htmlspecialchars($hw['fabricante'] ?? '—') ?>
          <?php if ($hw['anno']): ?> · <?= $hw['anno'] ?><?php endif; ?>
        </div>

        <?php if ($specs): ?>
        <div class="hw-specs-grid">
          <?php foreach ($specs as $s): ?>
          <div class="hw-spec-row">
            <span class="hw-spec-key"><?= htmlspecialchars($s['clave']) ?></span>
            <span class="hw-spec-val"><?= htmlspecialchars($s['valor']) ?></span>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Contenido principal -->
    <?php if ($hw['descripcion_html']): ?>
    <div class="home-section">
      <div class="section-hdr">
        <span class="section-hdr-title">📋 FICHA TÉCNICA</span>
      </div>
      <div class="section-body hw-content">
        <?= $hw['descripcion_html'] ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- Galería -->
    <?php if ($galeria): ?>
    <div class="home-section">
      <div class="section-hdr">
        <span class="section-hdr-title">📷 GALERÍA</span>
      </div>
      <div class="section-body">
        <div class="hw-galeria">
          <?php foreach ($galeria as $foto): ?>
          <div class="hw-galeria-item">
            <img src="<?= htmlspecialchars($foto['imagen_url']) ?>"
                 alt="<?= htmlspecialchars($foto['caption'] ?? $hw['nombre']) ?>">
            <?php if ($foto['caption']): ?>
            <div class="hw-galeria-caption"><?= htmlspecialchars($foto['caption']) ?></div>
            <?php endif; ?>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
    <?php endif; ?>

  </main>

  <aside class="sidebar">

    <div class="widget">
      <div class="widget-header"><?= $cat_label ?></div>
      <div class="widget-body">
        <?php if ($hw['imagen_cover']): ?>
        <img src="<?= htmlspecialchars($hw['imagen_cover']) ?>"
             alt="<?= htmlspecialchars($hw['nombre']) ?>"
             style="width:100%;height:auto;display:block;margin-bottom:10px;border:1px solid rgba(0,238,255,0.1)">
        <?php endif; ?>
        <div class="widget-stat-row"><span>Nombre</span><span><?= htmlspecialchars($hw['nombre']) ?></span></div>
        <?php if ($hw['fabricante']): ?>
        <div class="widget-stat-row"><span>Fabricante</span><span><?= htmlspecialchars($hw['fabricante']) ?></span></div>
        <?php endif; ?>
        <?php if ($hw['anno']): ?>
        <div class="widget-stat-row"><span>Año</span><span><?= $hw['anno'] ?></span></div>
        <?php endif; ?>
        <div class="widget-stat-row"><span>Tipo</span><span><?= $cat_label ?></span></div>
      </div>
    </div>

    <?php if ($specs): ?>
    <div class="widget">
      <div class="widget-header">SPECS RÁPIDAS</div>
      <div class="widget-body">
        <?php foreach (array_slice($specs, 0, 6) as $s): ?>
        <div class="widget-stat-row">
          <span><?= htmlspecialchars($s['clave']) ?></span>
          <span style="color:var(--amarillo)"><?= htmlspecialchars($s['valor']) ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <div class="widget">
      <div class="widget-header">MÁS HARDWARE</div>
      <div class="widget-body">
        <?php
        $otros = $db->prepare("SELECT slug, nombre, anno FROM hardware WHERE publicado = 1 AND id != ? ORDER BY anno ASC LIMIT 6");
        $otros->execute([$hw['id']]);
        foreach ($otros->fetchAll() as $o):
        ?>
        <div class="widget-game">
          <div>
            <a href="/hardware-ficha.php?slug=<?= urlencode($o['slug']) ?>" class="wg-title">
              <?= htmlspecialchars(strtoupper($o['nombre'])) ?>
            </a>
            <div class="wg-meta"><?= $o['anno'] ?></div>
          </div>
        </div>
        <?php endforeach; ?>
        <div style="margin-top:10px">
          <a href="/hardware.php" class="admin-btn admin-btn-primary"
             style="display:block;text-align:center;font-size:11px">
            ► VER TODO EL HARDWARE
          </a>
        </div>
      </div>
    </div>

  </aside>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
