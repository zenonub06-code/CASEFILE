<?php
$title = 'Evidence Archive'; include 'includes/header.php';
$r = $conn->query("SELECT v.*,a.case_id,a.judul kasus,a.id aid FROM article_evidence v JOIN articles a ON a.id=v.article_id ORDER BY a.case_id");
?>
<h1>Evidence Archive</h1>
<table><tr><th>Kasus</th><th>Dokumen</th><th>Tipe</th><th>Sumber</th></tr>
<?php while ($v = $r->fetch_assoc()): ?>
  <tr><td><a href="case_detail.php?id=<?= $v['aid'] ?>"><span class="cid"><?= e($v['case_id']) ?></span> <?= e($v['kasus']) ?></a></td>
  <td><?= $v['url'] ? '<a href="' . e($v['url']) . '" target="_blank" rel="noopener">' . e($v['judul']) . '</a>' : e($v['judul']) ?></td>
  <td><?= e($v['tipe']) ?></td><td><?= e($v['sumber']) ?></td></tr>
<?php endwhile; ?></table>
<?php include 'includes/footer.php'; ?>
