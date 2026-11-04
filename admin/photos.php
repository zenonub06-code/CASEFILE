<?php
require 'auth.php';
$id = (int)($_GET['id'] ?? 0);
$st = $conn->prepare("SELECT id,case_id,judul FROM articles WHERE id=?"); $st->bind_param('i', $id); $st->execute();
$a = $st->get_result()->fetch_assoc() ?: die('Kasus tidak ditemukan.');
$dir = __DIR__ . '/../uploads/';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check(); $aksi = $_POST['aksi'] ?? '';
    if ($aksi === 'upload') {
        $f = $_FILES['foto'] ?? null;
        if (!$f || $f['error'] !== UPLOAD_ERR_OK) $err = 'Pilih foto terlebih dahulu. Ukuran maksimal 2 MB.';
        elseif ($f['size'] > 2 * 1024 * 1024)      $err = 'Ukuran foto maksimal 2 MB.';
        else {
            $info = @getimagesize($f['tmp_name']);   // cek isi file, bukan ekstensi
            $ext = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'][$info[2] ?? 0] ?? null;
            if (!$ext) $err = 'Format harus JPG, PNG, atau WEBP.';
            else {
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                $name = bin2hex(random_bytes(8)) . '.' . $ext;
                if (move_uploaded_file($f['tmp_name'], $dir . $name)) {
                    $cap = trim($_POST['caption'] ?? '');
                    $st = $conn->prepare("INSERT INTO article_images (article_id,file,caption) VALUES (?,?,?)");
                    $st->bind_param('iss', $id, $name, $cap); $st->execute();
                    header("Location: photos.php?id=$id"); exit;
                } else $err = 'Gagal menyimpan file. Periksa izin folder uploads.';
            }
        }
    } elseif ($aksi === 'hapus') {
        $rid = (int)$_POST['rid'];
        $st = $conn->prepare("SELECT file FROM article_images WHERE id=? AND article_id=?"); $st->bind_param('ii', $rid, $id); $st->execute();
        if ($x = $st->get_result()->fetch_assoc()) {
            @unlink($dir . basename($x['file']));
            $st = $conn->prepare("DELETE FROM article_images WHERE id=?"); $st->bind_param('i', $rid); $st->execute();
        }
        header("Location: photos.php?id=$id"); exit;
    }
}
$r = $conn->query("SELECT * FROM article_images WHERE article_id=$id ORDER BY id");
$title = 'Foto Kasus';
include '_head.php';
?>
<h1>Foto · <?= e($a['case_id']) ?></h1><p><?= e($a['judul']) ?></p>
<?php if ($err): ?><p class="msg err"><?= e($err) ?></p><?php endif; ?>
<form method="post" enctype="multipart/form-data" class="f row"><?= csrf_field() ?><input type="hidden" name="aksi" value="upload">
  <input type="file" name="foto" accept="image/jpeg,image/png,image/webp" required>
  <input name="caption" placeholder="Keterangan foto (opsional)" maxlength="200">
  <button>Unggah foto</button></form>
<p><small>JPG, PNG, atau WEBP, maksimal 2 MB. Untuk kasus nyata, hindari foto korban atau foto grafis.</small></p>

<div class="thumbs">
<?php while ($f = $r->fetch_assoc()): ?>
  <div class="thumb"><img src="<?= BASE ?>uploads/<?= e($f['file']) ?>" alt="">
    <small><?= e($f['caption']) ?></small>
    <code id="k<?= $f['id'] ?>">[foto:<?= $f['id'] ?>]</code>
    <button type="button" class="btn sm alt" onclick="navigator.clipboard.writeText(document.getElementById('k<?= $f['id'] ?>').textContent)">Salin kode</button>
    <form method="post" onsubmit="return confirm('Hapus foto ini?')"><?= csrf_field() ?>
      <input type="hidden" name="aksi" value="hapus"><input type="hidden" name="rid" value="<?= $f['id'] ?>"><button class="btn sm">Hapus</button></form>
  </div>
<?php endwhile; ?>
</div>
<p><a class="btn" href="case_form.php?id=<?= $id ?>">Kembali ke naskah</a> <a class="btn alt" href="cases.php">Daftar kasus</a></p>
<?php include '_foot.php'; ?>
