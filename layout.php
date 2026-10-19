<?php
// Layout admin bersama: sidebar + topbar. Pakai admin_head() di awal & admin_foot() di akhir halaman.
function admin_head($title, $active){
  $nav = [
    'Beranda'  => ['highlight' => ['highlight.php', 'Highlight', '🖼️'], 'artikel' => ['artikel.php', 'Press Release / Artikel', '📰'], 'gambar' => ['gambar.php', 'ShakeMap & IKM', '🗺️']],
    'Publikasi' => ['index' => ['index.php', 'Daftar Buletin', '📚'], 'tambah' => ['tambah.php', 'Tambah Buletin', '➕']],
    'Pengaturan' => ['akun' => ['akun.php', 'Akun Admin', '👤']],
  ];
  $flash = take_flash(); ?>
<!DOCTYPE html>
<html lang="id"><head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="robots" content="noindex,nofollow">
<title><?= e($title) ?> – Admin Geofisika</title>
<link rel="stylesheet" href="admin.css">
</head><body class="app">
<aside class="side">
  <div class="brand"><span class="logo">BMKG</span><span><b>Admin Geofisika</b><small>Stageof Deli Serdang</small></span></div>
  <nav><?php foreach ($nav as $group => $items): ?><div class="grp"><?= $group ?></div>
    <?php foreach ($items as $k => [$href, $label, $ico]): ?><a href="<?= $href ?>" class="<?= $k === $active ? 'on' : '' ?>"><span><?= $ico ?></span> <?= $label ?></a><?php endforeach; ?>
  <?php endforeach; ?>
    <div class="grp">Website</div><a href="../beranda-deli-serdang.html" target="_blank" rel="noopener"><span>🌐</span> Lihat Beranda</a>
  </nav>
</aside>
<div class="content">
  <div class="top"><h1><?= e($title) ?></h1>
    <form method="post" action="logout.php"><?= csrf_field() ?><span class="who">👤 <?= e($_SESSION['user'] ?? '') ?></span><button class="btn o s">Logout</button></form></div>
  <main>
  <?php if ($flash): ?><div class="msg <?= e($flash[0]) ?>"><?= e($flash[1]) ?></div><?php endif;
}
function admin_foot(){ ?>
  </main>
</div>
</body></html>
<?php }
