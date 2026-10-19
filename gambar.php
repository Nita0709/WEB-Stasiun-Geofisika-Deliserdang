<?php
require __DIR__ . '/config.php'; require __DIR__ . '/layout.php'; require_login();
$slots = ['shakemap' => 'ShakeMap Gempa Dirasakan', 'ikm' => 'Indeks Kepuasan Masyarakat'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check(); $k = $_POST['slot'] ?? '';
  if (!isset($slots[$k])) exit('Slot tidak valid.');
  $c = content();
  if (($_POST['action'] ?? '') === 'delete') { del_file($c[$k]); $c[$k] = ''; content_save($c); flash('ok', 'Gambar dihapus.', 'gambar.php'); }
  if (!has_file($_FILES['gambar'] ?? null)) flash('err', 'Pilih gambar terlebih dahulu.', 'gambar.php');
  $new = put_upload($_FILES['gambar'], 'img', 'uploads/' . $k, $k . '-' . time(), 'gambar.php');
  del_file($c[$k]); $c[$k] = $new; content_save($c);
  flash('ok', $slots[$k] . ' diperbarui.', 'gambar.php');
}
$c = content();
admin_head('ShakeMap & IKM', 'gambar');
foreach ($slots as $k => $label): ?>
<div class="card"><h2><?= e($label) ?></h2>
 <?php if ($c[$k]): ?><img class="prev" src="../<?= e($c[$k]) ?>" alt=""><?php else: ?><p class="empty" style="padding:8px 0;text-align:left">Belum ada gambar.</p><?php endif; ?>
 <form method="post" enctype="multipart/form-data"><?= csrf_field() ?><input type="hidden" name="slot" value="<?= $k ?>">
  <div class="g"><div><label>Ganti gambar</label><input type="file" name="gambar" accept="image/jpeg,image/png,image/webp" required><small class="h">JPG/PNG/WebP, maks. 3 MB.</small></div></div>
  <p style="margin:14px 0 0"><button class="btn" name="action" value="upload">Unggah</button>
  <?php if ($c[$k]): ?> <button class="btn d" name="action" value="delete" formnovalidate onclick="return confirm('Hapus gambar ini?')">Hapus</button><?php endif; ?></p>
 </form></div>
<?php endforeach; admin_foot();
