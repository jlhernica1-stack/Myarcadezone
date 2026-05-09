<?php
require_once dirname(__DIR__) . '/config.php';
$page_title       = $page_title       ?? SITE_NAME . ' — ' . SITE_TAGLINE;
$meta_description = $meta_description ?? 'MY ARCADE ZONE — Reseñas, fichas de personajes, hardware, emulación y las mejores bandas sonoras arcade en español. El salón recreativo que siempre quisiste.';
$extra_css        = $extra_css        ?? '';
$show_boot        = $show_boot        ?? false;
$og_image         = $og_image         ?? SITE_URL . '/assets/images/og-default.jpg';
$og_type          = $og_type          ?? 'website';
$canonical_url    = SITE_URL . strtok($_SERVER['REQUEST_URI'], '?');
$json_ld          = $json_ld          ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($page_title) ?></title>
<meta name="description" content="<?= htmlspecialchars($meta_description) ?>">
<link rel="canonical" href="<?= htmlspecialchars($canonical_url) ?>">
<!-- Open Graph -->
<meta property="og:type"        content="<?= htmlspecialchars($og_type) ?>">
<meta property="og:title"       content="<?= htmlspecialchars($page_title) ?>">
<meta property="og:description" content="<?= htmlspecialchars($meta_description) ?>">
<meta property="og:image"       content="<?= htmlspecialchars($og_image) ?>">
<meta property="og:url"         content="<?= htmlspecialchars($canonical_url) ?>">
<meta property="og:site_name"   content="MY ARCADE ZONE">
<meta name="twitter:card"       content="summary_large_image">
<link rel="alternate" type="application/rss+xml" title="MY ARCADE ZONE — Blog" href="<?= SITE_URL ?>/rss.php">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Share+Tech+Mono&family=Rajdhani:wght@400;600;700&display=swap">
<link rel="stylesheet" href="/assets/css/style.css">
<?php if ($extra_css): ?><style><?= $extra_css ?></style><?php endif; ?>
<?php if ($json_ld): ?><script type="application/ld+json"><?= $json_ld ?></script><?php endif; ?>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-B5T2H6DH6V"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-B5T2H6DH6V');
</script>
</head>
<body>

<div id="flash"></div>

<?php if ($show_boot): ?>
<div id="boot">
  <div class="boot-inner">
    <div class="test-block">
      <div class="test-line ok"><span class="lbl">VIDEO RAM TEST....................</span><span class="res">[ OK ]</span></div>
      <div class="test-line ok"><span class="lbl">SOUND BOARD.......................</span><span class="res">[ OK ]</span></div>
      <div class="test-line ok"><span class="lbl">SPRITE ENGINE.....................</span><span class="res">[ OK ]</span></div>
      <div class="test-line ok"><span class="lbl">INPUT CONTROLLER..................</span><span class="res">[ OK ]</span></div>
      <div class="test-line ok"><span class="lbl">ATTRACT MODE......................</span><span class="res">[ LISTO ]</span></div>
      <div class="test-line warn"><span class="lbl">COIN MECHANISM....................</span><span class="res">[ 1 CRÉDITO ]</span></div>
      <div class="test-line ok"><span class="lbl">SYSTEM CHECK......................</span><span class="res">[ PASADO ]</span></div>
    </div>
    <div class="boot-divider"></div>
    <div class="boot-logo">
      <div class="boot-logo-main">MY ARCADE ZONE</div>
      <div class="boot-logo-sub">El Salón Recreativo en Español</div>
    </div>
    <div class="boot-coin-wrap">
      <div class="boot-insert-coin">
        <span class="coin"></span>
        INSERT COIN
      </div>
      <div class="boot-credits">CRÉDITOS : 1 &nbsp;·&nbsp; ARRANCANDO EN <span id="cd">3</span></div>
    </div>
  </div>
</div>
<?php endif; ?>

<div id="site"<?= $show_boot ? '' : ' class="on"' ?>>

  <div class="marquee-bar">
    <span class="marquee-inner">★ BIENVENIDO A MY ARCADE ZONE — EL SALÓN RECREATIVO EN ESPAÑOL ★ RESEÑAS · FICHAS DE PERSONAJES · HARDWARE · EMULACIÓN MAME · BANDAS SONORAS · MAQUINITAS CHINAS ★ BIENVENIDO A MY ARCADE ZONE &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
  </div>

  <header class="site-header">
    <div class="header-inner">
      <div class="logo-area">
        <a href="/" style="text-decoration:none">
          <div class="logo-main">MY ARCADE ZONE</div>
          <div class="logo-tagline">◄ EL SALÓN RECREATIVO EN ESPAÑOL ►</div>
        </a>
      </div>
      <div class="header-right">
        <?php
          $hdr = db()->query("SELECT COUNT(*) as total, MIN(anno) as min_anno, MAX(anno) as max_anno FROM juegos WHERE publicada = 1")->fetch();
        ?>
        <div class="header-stats">
          JUEGOS RESEÑADOS: <span id="hdr-resenas"><?= $hdr['total'] ?: '0' ?></span> &nbsp;·&nbsp;
          AÑOS CUBIERTOS: <span><?= $hdr['min_anno'] && $hdr['max_anno'] ? $hdr['min_anno'] . '–' . $hdr['max_anno'] : '—' ?></span>
        </div>
        <div class="header-search">
          <input type="text" id="search-input" placeholder="BUSCAR EN ARCADE ZONE...">
          <button onclick="doSearch()">► IR</button>
        </div>
      </div>
    </div>
  </header>

  <?php require __DIR__ . '/nav.php'; ?>

  <div class="neon-divider"></div>
