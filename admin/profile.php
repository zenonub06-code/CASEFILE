<?php
require 'auth.php'; $title = 'Profil Website'; $ok = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $st = $conn->prepare("UPDATE site_profile SET nama_situs=?,tagline=?,deskripsi=?,email=?,alamat=? WHERE id=1");
    $st->bind_param('sssss', $_POST['nama_situs'], $_POST['tagline'], $_POST['deskripsi'], $_POST['email'], $_POST['alamat']);
    $st->execute(); $ok = true;
}
$p = $conn->query("SELECT * FROM site_profile WHERE id=1")->fetch_assoc();
include '_head.php';
?>
<h1>Profil Website</h1>
<?php if ($ok): ?><p class="msg">Profil disimpan.</p><?php endif; ?>
<form method="post" class="f"><?= csrf_field() ?>
  <label>Nama situs</label><input name="nama_situs" value="<?= e($p['nama_situs']) ?>">
  <label>Tagline</label><input name="tagline" value="<?= e($p['tagline']) ?>">
  <label>Deskripsi</label><textarea name="deskripsi" rows="5"><?= e($p['deskripsi']) ?></textarea>
  <label>Email</label><input name="email" value="<?= e($p['email']) ?>">
  <label>Alamat</label><input name="alamat" value="<?= e($p['alamat']) ?>">
  <p><button>Simpan</button></p>
</form>
<?php include '_foot.php'; ?>
