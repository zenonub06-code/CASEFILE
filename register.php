<?php
$title = 'Daftar'; include 'includes/header.php';
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $n = trim($_POST['nama'] ?? ''); $u = trim($_POST['username'] ?? '');
    $m = trim($_POST['email'] ?? ''); $p = $_POST['password'] ?? '';
    if ($n === '' || !preg_match('/^[A-Za-z0-9_]{4,30}$/', $u)) $err = 'Nama wajib diisi. Username 4-30 karakter: huruf, angka, atau underscore.';
    elseif (!filter_var($m, FILTER_VALIDATE_EMAIL)) $err = 'Email tidak valid.';
    elseif (strlen($p) < 6) $err = 'Password minimal 6 karakter.';
    else {
        $st = $conn->prepare("SELECT id FROM admins WHERE username=?");
        $st->bind_param('s', $u); $st->execute();
        if ($st->get_result()->num_rows) $err = 'Username sudah dipakai.';
        else {
            $h = md5($p);
            $st = $conn->prepare("INSERT INTO admins (nama,username,email,password,role,status) VALUES (?,?,?,?,'editor','pending')");
            $st->bind_param('ssss', $n, $u, $m, $h); $st->execute();
            header('Location: login.php?pesan=daftar'); exit;
        }
    }
}
?>
<div class="box"><h1>Daftar</h1>
<?php if ($err): ?><p class="msg err"><?= e($err) ?></p><?php endif; ?>
<form method="post" class="f"><?= csrf_field() ?>
  <label>Nama lengkap</label><input name="nama" required value="<?= e($_POST['nama'] ?? '') ?>">
  <label>Username</label><input name="username" required value="<?= e($_POST['username'] ?? '') ?>">
  <label>Email</label><input name="email" type="email" required value="<?= e($_POST['email'] ?? '') ?>">
  <label>Password</label><input name="password" type="password" required>
  <p><button>Daftar</button> <a class="btn alt" href="login.php">Sudah punya akun</a></p>
</form></div>
<?php include 'includes/footer.php'; ?>
