<?php
require_once __DIR__ . '/includes/db.php';

$db = db();
$categoria = trim($_GET['categoria'] ?? '');
$categorias_valid = ['placa', 'monitor', 'cabinet', 'mando', 'otro'];

$current_page = 'hardware';
$page_title   = 'Hardware Arcade — MY ARCADE ZONE';

$where = "WHERE publicado = 1";
$params = [];
if ($categoria && in_array($categoria, $categorias_valid)) {
    $where .= " AND categoria = ?";
    $params[] = $categoria;
}

$items = $db->prepare("SELECT id, slug, nombre, fabricante, anno, categoria, imagen_cover FROM hardware $where ORDER BY anno ASC");
$items->execute($params);
$items = $items->fetchAll();

$counts = $db->query("SELECT categoria, COUNT(*) as n FROM hardware WHERE publicado = 1 GROUP BY categoria")->fetchAll(PDO::FETCH_KEY_PAIR);

require __DIR__ . '/includes/header.php';
?>

<div class="layout">
  <main>

    <div class="home-section">
      <div class="section-hdr">
        <span class="section-hdr-title">🕹️ HARDWARE ARCADE</span>
      </div>

      <!-- Filtro categorías -->
      <div style="padding:16px 20px 0;display:flex;flex-wrap:wrap;gap:8px">
        <a href="/hardware.php"
           class="hw-cat-btn <?= !$categoria ? 'active' : '' ?>">
          TODO
        </a>
        <?php
        $cat_labels = ['placa'=>'PLACAS','monitor'=>'MONITORES','cabinet'=>'CABINETS','mando'=>'MANDOS','otro'=>'OTROS'];
        foreach ($cat_labels as $key => $label):
          if (!isset($counts[$key]) || $counts[$key] < 1) continue;
        ?>
        <a href="/hardware.php?categoria=<?= $key ?>"
           class="hw-cat-btn <?= $categoria === $key ? 'active' : '' ?>">
          <?= $label ?> <span class="hw-cat-n">(<?= $counts[$key] ?>)</span>
        </a>
        <?php endforeach; ?>
      </div>

      <div class="section-body" style="padding:20px">
        <?php if ($items): ?>
        <div class="hw-grid">
          <?php foreach ($items as $h): ?>
          <a href="/hardware-ficha.php?slug=<?= urlencode($h['slug']) ?>" class="hw-card">
            <div class="hw-card-img">
              <?php if ($h['imagen_cover']): ?>
              <img src="<?= htmlspecialchars($h['imagen_cover']) ?>"
                   alt="<?= htmlspecialchars($h['nombre']) ?>">
              <?php else: ?>
              <div class="hw-card-img-ph">
                <span>🕹️</span>
              </div>
              <?php endif; ?>
              <span class="hw-card-cat"><?= strtoupper($h['categoria']) ?></span>
            </div>
            <div class="hw-card-body">
              <div class="hw-card-anno"><?= htmlspecialchars($h['fabricante'] ?? '—') ?> · <?= $h['anno'] ?></div>
              <div class="hw-card-name"><?= htmlspecialchars($h['nombre']) ?></div>
              <div class="hw-card-cta">► VER FICHA TÉCNICA</div>
            </div>
          </a>
          <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p style="font-family:'Share Tech Mono',monospace;font-size:11px;color:#333;text-align:center;padding:60px 0">
          > No hay hardware publicado en esta categoría todavía.
        </p>
        <?php endif; ?>
      </div>
    </div>

  </main>

  <aside class="sidebar">

    <div class="widget">
      <div class="widget-header">CATEGORÍAS</div>
      <div class="widget-body">
        <?php foreach ($cat_labels as $key => $label): ?>
        <?php if (!isset($counts[$key]) || $counts[$key] < 1) continue; ?>
        <div class="widget-stat-row">
          <a href="/hardware.php?categoria=<?= $key ?>"
             style="color:<?= $categoria===$key ? 'var(--cyan)' : 'var(--blanco)' ?>;text-decoration:none;font-family:'Share Tech Mono',monospace;font-size:10px">
            <?= $label ?>
          </a>
          <span><?= $counts[$key] ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="widget">
      <div class="widget-header">¿QUÉ ES UNA PLACA?</div>
      <div class="widget-body">
        <p style="font-family:'Share Tech Mono',monospace;font-size:10px;color:#555;line-height:1.7">
          Una placa arcade (PCB) es el cerebro electrónico de una máquina recreativa. Contiene la CPU, la memoria, los chips gráficos y de sonido. El cabinet es solo la carcasa.
        </p>
      </div>
    </div>

  </aside>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
