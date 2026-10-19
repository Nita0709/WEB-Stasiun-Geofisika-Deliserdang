<?php
// Konfigurasi & helper bersama. Jangan diakses langsung.
session_start();
define('ROOT', dirname(__DIR__));
define('DATA_FILE', ROOT . '/buletin.json');
define('CONTENT_FILE', ROOT . '/content.json');   // konten beranda (highlight, artikel, shakemap, ikm)
define('USERS_FILE', __DIR__ . '/users.php');     // akun admin (aman: diawali exit PHP)
define('MAX_PDF', 20 * 1024 * 1024);
define('MAX_IMG', 3 * 1024 * 1024);
const BULAN = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function go($url){ header("Location: $url"); exit; }
function flash($type, $msg, $to){ $_SESSION['flash'] = [$type, $msg]; go($to); }
function take_flash(){ $f = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $f; }
function is_logged_in(){ return !empty($_SESSION['ok']); }
function require_login(){ if (!users()) go('setup.php'); if (!is_logged_in()) go('login.php'); }
function csrf(){ if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16)); return $_SESSION['csrf']; }
function csrf_field(){ return '<input type="hidden" name="csrf" value="' . e(csrf()) . '">'; }
function csrf_check(){ if (!hash_equals(csrf(), $_POST['csrf'] ?? '')) { http_response_code(400); exit('Token tidak valid.'); } }
function new_id(){ return bin2hex(random_bytes(4)); }

// Akun admin: {"username":"password_hash"}
function users(){ if (!is_file(USERS_FILE)) return []; $t = file_get_contents(USERS_FILE); $d = json_decode(substr($t, strpos($t, "\n") + 1), true); return is_array($d) ? $d : []; }
function users_save($u){ file_put_contents(USERS_FILE, "<?php http_response_code(403); exit; ?>\n" . json_encode($u), LOCK_EX); }

// Buletin
function load(){ $d = is_file(DATA_FILE) ? json_decode(file_get_contents(DATA_FILE), true) : []; return is_array($d) ? $d : []; }
function save($d){ usort($d, fn($a,$b) => strcmp($b['key'], $a['key'])); file_put_contents(DATA_FILE, json_encode(array_values($d), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES), LOCK_EX); }
function find_edisi($key){ foreach (load() as $r) if ($r['key'] === $key) return $r; return null; }

// Konten beranda
function content(){ $d = is_file(CONTENT_FILE) ? json_decode(file_get_contents(CONTENT_FILE), true) : []; return array_merge(['highlight'=>[],'artikel'=>[],'shakemap'=>'','ikm'=>''], is_array($d) ? $d : []); }
function content_save($c){ file_put_contents(CONTENT_FILE, json_encode($c, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES), LOCK_EX); }

// Upload file. $kind: 'img' | 'pdf'. Mengembalikan path relatif, atau flash error.
function has_file($f){ return $f && ($f['error'] ?? 4) === UPLOAD_ERR_OK; }
function put_upload($f, $kind, $dir, $base, $back){
  $max = $kind === 'pdf' ? MAX_PDF : MAX_IMG;
  if ($f['size'] > $max) flash('err', 'File melebihi ' . ($max / 1048576) . ' MB.', $back);
  $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
  $ext = $kind === 'pdf' ? ($mime === 'application/pdf' ? 'pdf' : null) : (['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'][$mime] ?? null);
  if (!$ext) flash('err', $kind === 'pdf' ? 'File harus berformat PDF.' : 'Gambar harus JPG, PNG, atau WebP.', $back);
  $d = ROOT . "/$dir"; if (!is_dir($d)) mkdir($d, 0755, true);
  if (!move_uploaded_file($f['tmp_name'], "$d/$base.$ext")) flash('err', 'Gagal menyimpan file.', $back);
  return "$dir/$base.$ext";
}
function del_file($rel){ if ($rel && is_file(ROOT . '/' . $rel)) unlink(ROOT . '/' . $rel); }
