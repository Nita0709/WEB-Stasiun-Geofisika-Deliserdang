<?php
require __DIR__ . '/config.php';
if (!users()) go('setup.php');
if (is_logged_in()) go('index.php');
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $u = trim($_POST['user'] ?? ''); $all = users();
  if (isset($all[$u]) && password_verify($_POST['pass'] ?? '', $all[$u])) {
    session_regenerate_id(true); $_SESSION['ok'] = true; $_SESSION['user'] = $u; go('index.php');
  }
  sleep(1); $err = 'Username atau password salah.';
}
?><!DOCTYPE html>
<html lang="id"><head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="robots" content="noindex,nofollow">
<title>Login – Admin Geodinamika</title>
<link rel="stylesheet" href="admin.css">
</head><body class="auth">
<form class="login" method="post">
  <div class="brand"><span class="logo">BMKG</span><span><b>Admin Stasiun Geofisika</b><small>Deli Serdang</small></span></div>
  <h2>Login</h2>
  <?php if ($err): ?><div class="msg err"><?= e($err) ?></div><?php endif; ?>
  <p><label>Username</label><input name="user" required autofocus autocomplete="username"></p>
  <p><label>Password</label><input type="password" name="pass" required autocomplete="current-password"></p>
  <button class="btn" style="width:100%">Masuk</button>
</form>
</body></html>
