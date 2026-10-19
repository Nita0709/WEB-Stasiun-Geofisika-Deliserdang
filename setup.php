<?php
// Pembuatan akun admin pertama. Halaman ini terkunci otomatis setelah akun ada.
require __DIR__ . '/config.php';
if (users()) go('login.php');
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $u = trim($_POST['user'] ?? ''); $p = $_POST['pass'] ?? '';
  if (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $u)) $err = 'Username 3–30 karakter: huruf, angka, atau garis bawah.';
  elseif (strlen($p) < 8) $err = 'Password minimal 8 karakter.';
  elseif ($p !== ($_POST['pass2'] ?? '')) $err = 'Konfirmasi password tidak sama.';
  else { users_save([$u => password_hash($p, PASSWORD_DEFAULT)]); session_regenerate_id(true); $_SESSION['ok'] = true; $_SESSION['user'] = $u; go('index.php'); }
}
?><!DOCTYPE html>
<html lang="id"><head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="robots" content="noindex,nofollow">
<title>Buat Akun Admin</title>
<link rel="stylesheet" href="admin.css">
</head><body class="auth">
<form class="login" method="post">
  <div class="brand"><span class="logo">BMKG</span><span><b>Buat Akun Admin</b><small>Stasiun Geofisika Deli Serdang</small></span></div>
  <h2>Pengaturan Awal</h2>
  <?php if ($err): ?><div class="msg err"><?= e($err) ?></div><?php endif; ?>
  <p><label>Username</label><input name="user" required autofocus autocomplete="username" value="<?= e($_POST['user'] ?? '') ?>"></p>
  <p><label>Password</label><input type="password" name="pass" required minlength="8" autocomplete="new-password"></p>
  <p><label>Ulangi Password</label><input type="password" name="pass2" required minlength="8" autocomplete="new-password"></p>
  <button class="btn" style="width:100%">Buat Akun &amp; Masuk</button>
  <p style="margin:12px 0 0"><small class="h">Halaman ini hanya muncul sekali dan terkunci setelah akun dibuat.</small></p>
</form>
</body></html>
