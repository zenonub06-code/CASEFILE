<?php
$title = 'Timeline'; include 'includes/header.php';
$r = $conn->query("SELECT t.*,a.case_id,a.judul kasus,a.id aid FROM article_timelines t JOIN articles a ON a.id=t.article_id ORDER BY t.tanggal");
?>
<h1>Timeline</h1><p>Urutan kejadian dari seluruh kasus, dari yang paling lama.</p>
<ul class="tl"><?php while ($t = $r->fetch_assoc()): ?>
  <li><time><?= e($t['tanggal']) ?></time> · <a href="case_detail.php?id=<?= $t['aid'] ?>"><span class="cid"><?= e($t['case_id']) ?></span> <?= e($t['kasus']) ?></a><br>
  <b><?= e($t['judul']) ?></b><br><?= e($t['keterangan']) ?></li>
<?php endwhile; ?></ul>
<?php include 'includes/footer.php'; ?>
