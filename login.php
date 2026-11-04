<?php
$title = 'Masuk'; include 'includes/header.php';
$msgs = ['gagal' => 'Username atau password salah.', 'status' => 'Akun belum aktif. Tunggu persetujuan super admin.',
         'daftar' => 'Pendaftaran berhasil. Akun Anda menunggu persetujuan super admin.'];
$k = $_GET['pesan'] ?? '';
?>
<div class="box"><h1>Masuk</h1>
<?php if (isset($msgs[$k])): ?><p class="msg <?= $k === 'daftar' ? '' : 'err' ?>"><?= e($msgs[$k]) ?></p><?php endif; ?>
<form method="post" action="cek_login.php" class="f">
  <label>Username</label><input name="username" required autofocus>
  <label>Password</label><input name="password" type="password" required>
  <p><button>Masuk</button> <a class="btn alt" href="register.php">Daftar</a></p>
</form></div>
<?php include 'includes/footer.php'; ?>
