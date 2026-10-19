<?php
require __DIR__ . '/config.php'; require __DIR__ . '/layout.php'; require_login();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check(); $c = content(); $act = $_POST['action'] ?? '';
  if ($act === 'add') {
    $t = trim($_POST['judul'] ?? ''); $link = trim($_POST['link'] ?? ''); $hasPdf = has_file($_FILES['pdf'] ?? null);
    if ($t === '' || ($link === '' && !$hasPdf)) flash('err', 'Judul wajib, dan isi tautan atau unggah PDF.', 'artikel.php');
    if ($link !== '' && !preg_match('~^https?://~i', $link)) flash('err', 'Tautan harus diawali http:// atau https://', 'artikel.php');
    $id = new_id();
    $c['artikel'][] = ['id' => $id, 'title' => $t, 'date' => $_POST['tanggal'] ?: date('Y-m-d'), 'link' => $link, 'file' => $hasPdf ? put_upload($_FILES['pdf'], 'pdf', 'uploads/artikel', $id, 'artikel.php') : ''];
    usort($c['artikel'], fn($a, $b) => strcmp($b['date'], $a['date']));
    content_save($c); flash('ok', 'Artikel ditambahkan.', 'artikel.php');
  } elseif ($act === 'delete') {
    foreach ($c['artikel'] as $i => $a) if ($a['id'] === ($_POST['id'] ?? '')) { del_file($a['file']); unset($c['artikel'][$i]); }
    $c['artikel'] = array_values($c['artikel']); content_save($c); flash('ok', 'Artikel dihapus.', 'artikel.php');
  }
}
$rows = content()['artikel'];
admin_head('Press Release / Artikel', 'artikel'); ?>
<div class="card"><h2>Tambah Artikel</h2>
<form method="post" enctype="multipart/form-data"><?= csrf_field() ?><input type="hidden" name="action" value="add">
 <div class="g">
  <div class="full"><label>Judul</label><input type="text" name="judul" maxlength="150" required></div>
  <div><label>Tanggal</label><input type="date" name="tanggal" value="<?= date('Y-m-d') ?>"></div>
  <div><label>Tautan (opsional)</label><input type="url" name="link" placeholder="https://..."></div>
  <div class="full"><label>File PDF (opsional)</label><input type="file" name="pdf" accept="application/pdf"><small class="h">Maks. 20 MB. Isi tautan <b>atau</b> unggah PDF.</small></div>
  <div class="full"><button class="btn">Tambah Artikel</button></div>
 </div></form></div>
<div class="card"><h2>Daftar Artikel (<?= count($rows) ?>)</h2>
 <div class="tw"><table><tr><th>Tanggal</th><th>Judul</th><th>Sumber</th><th>Aksi</th></tr>
 <?php foreach ($rows as $a): ?><tr><td><?= e($a['date']) ?></td><td><?= e($a['title']) ?></td>
  <td><?php $u = $a['link'] ?: '../' . $a['file']; ?><a href="<?= e($u) ?>" target="_blank" rel="noopener"><?= $a['link'] ? 'Tautan' : 'PDF' ?></a></td>
  <td><form method="post" onsubmit="return confirm('Hapus artikel ini?')"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= e($a['id']) ?>"><button class="btn s d">Hapus</button></form></td></tr>
 <?php endforeach; if (!$rows): ?><tr><td colspan="4" class="empty">Belum ada artikel.</td></tr><?php endif; ?></table></div></div>
<?php admin_foot();
