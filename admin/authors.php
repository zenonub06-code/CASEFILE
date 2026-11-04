<?php
require 'auth.php'; $title = 'Penulis';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check(); $aksi = $_POST['aksi'] ?? ''; $rid = (int)($_POST['id'] ?? 0);
    if ($aksi === 'simpan' && trim($_POST['nama']) !== '') {
        if ($rid) { $st = $conn->prepare("UPDATE authors SET nama=?,bio=? WHERE id=?"); $st->bind_param('ssi', $_POST['nama'], $_POST['bio'], $rid); }
        else      { $st = $conn->prepare("INSERT INTO authors (nama,bio) VALUES (?,?)"); $st->bind_param('ss', $_POST['nama'], $_POST['bio']); }
        $st->execute();
    } elseif ($aksi === 'hapus') {
        try { $st = $conn->prepare("DELETE FROM authors WHERE id=?"); $st->bind_param('i', $rid); $st->execute(); }
        catch (mysqli_sql_exception $e) { header('Location: authors.php?err=1'); exit; }
    }
    header('Location: authors.php'); exit;
}
$edit = null;
if (isset($_GET['edit'])) { $i = (int)$_GET['edit']; $edit = $conn->query("SELECT * FROM authors WHERE id=$i")->fetch_assoc(); }
$r = $conn->query("SELECT * FROM authors");
include '_head.php';
?>
<h1>Penulis</h1>
<?php if (isset($_GET['err'])): ?><p class="msg err">Penulis masih memiliki kasus dan tidak bisa dihapus.</p><?php endif; ?>
<form method="post" class="f row"><?= csrf_field() ?><input type="hidden" name="aksi" value="simpan"><input type="hidden" name="id" value="<?= $edit['id'] ?? 0 ?>">
  <input name="nama" placeholder="Nama penulis" required value="<?= e($edit['nama'] ?? '') ?>">
  <input name="bio" placeholder="Bio singkat" value="<?= e($edit['bio'] ?? '') ?>">
  <button><?= $edit ? 'Simpan perubahan' : 'Tambah' ?></button></form>
<table><tr><th>Nama</th><th>Bio</th><th>Aksi</th></tr>
<?php while ($c = $r->fetch_assoc()): ?>
<tr><td><?= e($c['nama']) ?></td><td><?= e($c['bio']) ?></td><td>
  <a class="btn sm alt" href="?edit=<?= $c['id'] ?>">Ubah</a>
  <form method="post" class="inline" onsubmit="return confirm('Hapus penulis ini?')"><?= csrf_field() ?>
    <input type="hidden" name="aksi" value="hapus"><input type="hidden" name="id" value="<?= $c['id'] ?>"><button class="btn sm">Hapus</button></form>
</td></tr><?php endwhile; ?></table>
<?php include '_foot.php'; ?>
