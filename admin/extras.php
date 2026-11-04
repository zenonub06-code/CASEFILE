<?php
require 'auth.php';
$id = (int)($_GET['id'] ?? 0);
$st = $conn->prepare("SELECT id,case_id,judul FROM articles WHERE id=?"); $st->bind_param('i', $id); $st->execute();
$a = $st->get_result()->fetch_assoc() ?: die('Kasus tidak ditemukan.');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check(); $aksi = $_POST['aksi'] ?? '';
    if ($aksi === 'tl_tambah') {
        $st = $conn->prepare("INSERT INTO article_timelines (article_id,tanggal,judul,keterangan) VALUES (?,?,?,?)");
        $st->bind_param('isss', $id, $_POST['tanggal'], $_POST['judul'], $_POST['keterangan']); $st->execute();
    } elseif ($aksi === 'tl_hapus') {
        $st = $conn->prepare("DELETE FROM article_timelines WHERE id=? AND article_id=?"); $st->bind_param('ii', $_POST['rid'], $id); $st->execute();
    } elseif ($aksi === 'ev_tambah') {
        $url = trim($_POST['url']); if ($url !== '' && !preg_match('#^https?://#i', $url)) $url = '';
        $st = $conn->prepare("INSERT INTO article_evidence (article_id,judul,tipe,sumber,url) VALUES (?,?,?,?,?)");
        $st->bind_param('issss', $id, $_POST['judul'], $_POST['tipe'], $_POST['sumber'], $url); $st->execute();
    } elseif ($aksi === 'ev_hapus') {
        $st = $conn->prepare("DELETE FROM article_evidence WHERE id=? AND article_id=?"); $st->bind_param('ii', $_POST['rid'], $id); $st->execute();
    }
    header("Location: extras.php?id=$id"); exit;
}
$tl = $conn->query("SELECT * FROM article_timelines WHERE article_id=$id ORDER BY tanggal");
$ev = $conn->query("SELECT * FROM article_evidence WHERE article_id=$id");
$title = 'Kronologi & Bukti';
include '_head.php';
function delform($aksi, $rid) {
    return '<form method="post" class="inline" onsubmit="return confirm(\'Hapus?\')">' . csrf_field()
      . '<input type="hidden" name="aksi" value="' . $aksi . '"><input type="hidden" name="rid" value="' . $rid . '"><button class="btn sm">Hapus</button></form>';
}
?>
<h1><?= e($a['case_id']) ?> · <?= e($a['judul']) ?></h1>
<h2>Kronologi</h2>
<table><tr><th>Tanggal</th><th>Judul</th><th>Keterangan</th><th></th></tr>
<?php while ($t = $tl->fetch_assoc()): ?>
<tr><td><?= e($t['tanggal']) ?></td><td><?= e($t['judul']) ?></td><td><?= e($t['keterangan']) ?></td><td><?= delform('tl_hapus', $t['id']) ?></td></tr>
<?php endwhile; ?></table>
<form method="post" class="f row"><?= csrf_field() ?><input type="hidden" name="aksi" value="tl_tambah">
  <input type="date" name="tanggal" required><input name="judul" placeholder="Judul kejadian" required>
  <input name="keterangan" placeholder="Keterangan"><button>Tambah</button></form>

<h2>Dokumentasi dan sumber</h2>
<table><tr><th>Judul</th><th>Tipe</th><th>Sumber</th><th></th></tr>
<?php while ($v = $ev->fetch_assoc()): ?>
<tr><td><?= e($v['judul']) ?></td><td><?= e($v['tipe']) ?></td><td><?= e($v['sumber']) ?></td><td><?= delform('ev_hapus', $v['id']) ?></td></tr>
<?php endwhile; ?></table>
<form method="post" class="f row"><?= csrf_field() ?><input type="hidden" name="aksi" value="ev_tambah">
  <input name="judul" placeholder="Judul dokumen" required><input name="tipe" placeholder="Tipe (Dokumen, Laporan)" value="Dokumen">
  <input name="sumber" placeholder="Sumber"><input name="url" placeholder="URL (https://...)"><button>Tambah</button></form>
<p><a class="btn alt" href="cases.php">Kembali</a></p>
<?php include '_foot.php'; ?>
