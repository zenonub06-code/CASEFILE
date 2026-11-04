<?php
require 'auth.php'; $title = 'Kelola Kasus';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id = (int)$_POST['id'];
    $f = $conn->query("SELECT file FROM article_images WHERE article_id=$id");
    while ($x = $f->fetch_assoc()) @unlink(__DIR__ . '/../uploads/' . basename($x['file']));
    $st = $conn->prepare("DELETE FROM articles WHERE id=?"); $st->bind_param('i', $id); $st->execute();
    header('Location: cases.php'); exit;
}
$r = $conn->query("SELECT a.*,c.nama kategori FROM articles a JOIN categories c ON c.id=a.category_id ORDER BY a.id DESC");
include '_head.php';
?>
<h1>Kelola Kasus</h1><p><a class="btn" href="case_form.php">Tambah kasus</a></p>
<table><tr><th>ID</th><th>Judul</th><th>Kategori</th><th>Status</th><th>Aksi</th></tr>
<?php while ($a = $r->fetch_assoc()): ?>
<tr><td><?= e($a['case_id']) ?></td><td><?= e($a['judul']) ?></td><td><?= e($a['kategori']) ?></td><td><?= badge($a['status']) ?></td>
<td>
  <a class="btn sm alt" href="case_form.php?id=<?= $a['id'] ?>">Ubah</a>
  <a class="btn sm alt" href="extras.php?id=<?= $a['id'] ?>">Kronologi &amp; bukti</a>
  <a class="btn sm alt" href="photos.php?id=<?= $a['id'] ?>">Foto</a>
  <form method="post" class="inline" onsubmit="return confirm('Hapus kasus ini beserta kronologi dan buktinya?')">
    <?= csrf_field() ?><input type="hidden" name="id" value="<?= $a['id'] ?>"><button class="sm btn">Hapus</button></form>
</td></tr>
<?php endwhile; ?></table>
<?php include '_foot.php'; ?>
