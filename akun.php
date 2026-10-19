<?php
require __DIR__ . '/config.php'; require __DIR__ . '/layout.php'; require_login();
$me = $_SESSION['user'] ?? '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check(); $all = users(); $act = $_POST['action'] ?? '';
  if ($act === 'add') {
    $u = trim($_POST['user'] ?? ''); $p = $_POST['pass'] ?? '';
    if (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $u)) flash('err', 'Username 3–30 karakter: huruf, angka, atau garis bawah.', 'akun.php');
    if (isset($all[$u])) flash('err', 'Username sudah dipakai.', 'akun.php');
    if (strlen($p) < 8) flash('err', 'Password minimal 8 karakter.', 'akun.php');
    $all[$u] = password_hash($p, PASSWORD_DEFAULT); users_save($all); flash('ok', "Akun $u dibuat.", 'akun.php');
  } elseif ($act === 'pw') {
    if (!password_verify($_POST['old'] ?? '', $all[$me] ?? '')) flash('err', 'Password lama salah.', 'akun.php');
    if (strlen($_POST['new'] ?? '') < 8) flash('err', 'Password baru minimal 8 karakter.', 'akun.php');
    $all[$me] = password_hash($_POST['new'], PASSWORD_DEFAULT); users_save($all); flash('ok', 'Password diganti.', 'akun.php');
  } elseif ($act === 'delete') {
    $u = $_POST['user'] ?? '';
    if ($u === $me) flash('err', 'Tidak bisa menghapus akun yang sedang dipakai.', 'akun.php');
    unset($all[$u]); users_save($all); flash('ok', 'Akun dihapus.', 'akun.php');
  }
}
admin_head('Akun Admin', 'akun'); ?>
<div class="card"><h2>Ganti Password Saya (<?= e($me) ?>)</h2>
<form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="pw"><div class="g">
 <div><label>Password lama</label><input type="password" name="old" required autocomplete="current-password"></div>
 <div><label>Password baru</label><input type="password" name="new" required minlength="8" autocomplete="new-password"></div>
 <div class="full"><button class="btn">Ganti Password</button></div></div></form></div>
<div class="card"><h2>Tambah Akun Admin</h2>
<form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="add"><div class="g">
 <div><label>Username</label><input type="text" name="user" required pattern="[A-Za-z0-9_]{3,30}"></div>
 <div><label>Password</label><input type="password" name="pass" required minlength="8" autocomplete="new-password"></div>
 <div class="full"><button class="btn">Tambah Akun</button></div></div></form></div>
<div class="card"><h2>Daftar Akun</h2><div class="tw"><table><tr><th>Username</th><th>Aksi</th></tr>
 <?php foreach (users() as $u => $h): ?><tr><td><?= e($u) ?><?= $u === $me ? ' <small class="h">(Anda)</small>' : '' ?></td>
  <td><?php if ($u !== $me): ?><form method="post" onsubmit="return confirm('Hapus akun ini?')"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="user" value="<?= e($u) ?>"><button class="btn s d">Hapus</button></form><?php endif; ?></td></tr>
 <?php endforeach; ?></table></div></div>
<?php admin_foot();
