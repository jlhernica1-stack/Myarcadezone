<?php
// $current_page debe estar definida antes de incluir este archivo
$current_page = $current_page ?? '';
$nav_items = [
    'inicio'    => ['url' => '/',             'label' => 'INICIO'],
    'resenas'   => ['url' => '/resenas.php',  'label' => 'RESEÑAS'],
    'hardware'  => ['url' => '/hardware.php', 'label' => 'HARDWARE'],
    'emulacion' => ['url' => '/emulacion.php','label' => 'EMULACIÓN'],
    'retrocassete' => ['url' => '/retrocassete.php', 'label' => 'RETROCASSETE'],
    'blog'      => ['url' => '/blog.php',     'label' => 'BLOG'],
    'salon'     => ['url' => '/salon/',       'label' => 'SALÓN RECREATIVO'],
    'acercade'  => ['url' => '/acercade.php', 'label' => 'ACERCA DE'],
];
?>
<nav>
  <div class="nav-inner">
    <?php foreach ($nav_items as $key => $item): ?>
    <a href="<?= $item['url'] ?>"<?= $current_page === $key ? ' class="active"' : '' ?>><?= $item['label'] ?></a>
    <?php endforeach; ?>
  </div>
</nav>
