<?php
require 'auth.php'; $title = 'Kategori';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check(); $aksi = $_POST['aksi'] ?? ''; $rid = (int)($_POST['id'] ?? 0);
    if ($aksi === 'simpan' && trim($_POST['nama']) !== '') {
        if ($rid) { $st = $conn->prepare("UPDATE categories SET nama=?,deskripsi=? WHERE id=?"); $st->bind_param('ssi', $_POST['nama'], $_POST['deskripsi'], $rid); }
        else      { $st = $conn->prepare("INSERT INTO categories (nama,deskripsi) VALUES (?,?)"); $st->bind_param('ss', $_POST['nama'], $_POST['deskripsi']); }
        $st->execute();
    } elseif ($aksi === 'hapus') {
        try { $st = $conn->prepare("DELETE FROM categories WHERE id=?"); $st->bind_param('i', $rid); $st->execute(); }
        catch (mysqli_sql_exception $e) { header('Location: categories.php?err=1'); exit; }
    }
    header('Location: categories.php'); exit;
}
$edit = null;
if (isset($_GET['edit'])) { $i = (int)$_GET['edit']; $edit = $conn->query("SELECT * FROM categories WHERE id=$i")->fetch_assoc(); }
$r = $conn->query("SELECT c.*,COUNT(a.id) n FROM categories c LEFT JOIN articles a ON a.category_id=c.id GROUP BY c.id");
include '_head.php';
?>
<h1>Kategori</h1>
<?php if (isset($_GET['err'])): ?><p class="msg err">Kategori masih dipakai oleh kasus. Pindahkan atau hapus kasusnya terlebih dahulu.</p><?php endif; ?>
<form method="post" class="f row"><?= csrf_field() ?><input type="hidden" name="aksi" value="simpan"><input type="hidden" name="id" value="<?= $edit['id'] ?? 0 ?>">
  <input name="nama" placeholder="Nama kategori" required value="<?= e($edit['nama'] ?? '') ?>">
  <input name="deskripsi" placeholder="Deskripsi" value="<?= e($edit['deskripsi'] ?? '') ?>">
  <button><?= $edit ? 'Simpan perubahan' : 'Tambah' ?></button></form>
<table><tr><th>Nama</th><th>Deskripsi</th><th>Kasus</th><th>Aksi</th></tr>
<?php while ($c = $r->fetch_assoc()): ?>
<tr><td><?= e($c['nama']) ?></td><td><?= e($c['deskripsi']) ?></td><td><?= $c['n'] ?></td><td>
  <a class="btn sm alt" href="?edit=<?= $c['id'] ?>">Ubah</a>
  <form method="post" class="inline" onsubmit="return confirm('Hapus kategori ini?')"><?= csrf_field() ?>
    <input type="hidden" name="aksi" value="hapus"><input type="hidden" name="id" value="<?= $c['id'] ?>"><button class="btn sm">Hapus</button></form>
</td></tr><?php endwhile; ?></table>
<?php include '_foot.php'; ?>
