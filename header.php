<?php
// HEADER bersama. Variabel yang diisi halaman sebelum include:
//   $title (wajib)  $page (nama halaman: beranda/sejarah/buletin, jadi class body)  $heroTitle & $crumb (opsional, banner biru)
$title = $title ?? 'Stasiun Geofisika Deli Serdang';
$nav = [
  'Profil' => [['Sejarah', 'sejarah.php'], ['Visi & Misi', '#'], ['Tugas & Fungsi', '#'], ['Struktur Organisasi', '#']],
  'Informasi Layanan' => [['Standar Pelayanan', '#']],
  'Informasi Gempabumi Wilayah Sumut' => [['Gempa Bumi Terkini', '#']],
  'Informasi Petir' => [['Informasi Petir Wilayah Sumut', '#'], ['Informasi Petir Bandara Kualanamu', '#']],
  'Publikasi' => [['Buletin Geodinamika', 'buletin.php'], ['Kegiatan', '#'], ['Peralatan', '#'], ['Sekolah Lapang Gempabumi', '#'], ['Youtube', '#']],
];
?><!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title><?= htmlspecialchars($title) ?> – Stasiun Geofisika Deli Serdang</title>
<link rel="stylesheet" href="assets/site.css">
</head>
<body class="p-<?= htmlspecialchars($page ?? 'umum') ?>">
<header><div class="wrap bar">
 <a class="brand" href="index.php"><span href="img/logo.png" rel="icon"></span><span><b>STASIUN GEOFISIKA DELI SERDANG</b></span></a>
 <button class="burger" aria-label="Menu" onclick="document.getElementById('nav').classList.toggle('open')">☰</button>
 <nav id="nav"><ul>
 <?php foreach ($nav as $menu => $items): ?>
  <li><a href="#"><?= htmlspecialchars($menu) ?></a><ul>
   <?php foreach ($items as [$label, $href]): ?><li><a href="<?= $href ?>"><?= htmlspecialchars($label) ?></a></li>
   <?php endforeach; ?></ul></li>
 <?php endforeach; ?>
 </ul></nav>
</div></header>
<?php if (!empty($heroTitle)): ?>
<section class="hero"><div class="wrap"><div class="crumb"><?= htmlspecialchars($crumb ?? '') ?></div><h1><?= htmlspecialchars($heroTitle) ?></h1></div></section>
<?php endif; ?>
