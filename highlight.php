<?php
require __DIR__ . '/config.php'; require __DIR__ . '/layout.php'; require_login();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check(); $c = content(); $act = $_POST['action'] ?? '';
  if ($act === 'add') {
    $t = trim($_POST['judul'] ?? '');
    if ($t === '' || !has_file($_FILES['gambar'] ?? null)) flash('err', 'Judul dan gambar wajib diisi.', 'highlight.php');
    if (count($c['highlight']) >= 8) flash('err', 'Maksimal 8 highlight. Hapus salah satu dulu.', 'highlight.php');
    $id = new_id();
    $c['highlight'][] = ['id' => $id, 'title' => $t, 'desc' => trim($_POST['desc'] ?? ''), 'image' => put_upload($_FILES['gambar'], 'img', 'uploads/highlight', $id, 'highlight.php')];
    content_save($c); flash('ok', 'Highlight ditambahkan.', 'highlight.php');
  } elseif ($act === 'delete') {
    foreach ($c['highlight'] as $i => $h) if ($h['id'] === ($_POST['id'] ?? '')) { del_file($h['image']); unset($c['highlight'][$i]); }
    $c['highlight'] = array_values($c['highlight']); content_save($c); flash('ok', 'Highlight dihapus.', 'highlight.php');
  }
}
$rows = content()['highlight'];
admin_head('Highlight Beranda', 'highlight'); ?>
<div class="card"><h2>Tambah Slide Highlight</h2>
<form method="post" enctype="multipart/form-data"><?= csrf_field() ?><input type="hidden" name="action" value="add">
 <div class="g">
  <div><label>Judul</label><input type="text" name="judul" maxlength="100" required></div>
  <div><label>Gambar</label><input type="file" name="gambar" accept="image/jpeg,image/png,image/webp" required><small class="h">JPG/PNG/WebP, maks. 3 MB, disarankan rasio 16:6.</small></div>
  <div class="full"><label>Deskripsi singkat (opsional)</label><input type="text" name="desc" maxlength="160"></div>
  <div class="full"><button class="btn">Tambah Highlight</button></div>
 </div></form></div>
<div class="card"><h2>Slide Aktif (<?= count($rows) ?>/8)</h2>
 <div class="tw"><table><tr><th>Gambar</th><th>Judul</th><th>Aksi</th></tr>
 <?php foreach ($rows as $h): ?><tr><td><img class="wthumb" src="../<?= e($h['image']) ?>" alt=""></td><td><?= e($h['title']) ?><br><small class="h"><?= e($h['desc']) ?></small></td>
  <td><form method="post" onsubmit="return confirm('Hapus highlight ini?')"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= e($h['id']) ?>"><button class="btn s d">Hapus</button></form></td></tr>
 <?php endforeach; if (!$rows): ?><tr><td colspan="3" class="empty">Belum ada highlight. Beranda memakai slide bawaan.</td></tr><?php endif; ?></table></div></div>
<?php admin_foot();
