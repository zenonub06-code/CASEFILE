<?php
require 'auth.php'; $title = 'Dashboard';
$n = fn($sql) => $conn->query($sql)->fetch_row()[0];
$top = $conn->query("SELECT id,case_id,judul,views FROM articles ORDER BY views DESC LIMIT 5");
include '_head.php';
?>
<h1>Dashboard</h1>
<div class="stats">
  <div class="stat"><b><?= $n("SELECT COUNT(*) FROM articles") ?></b>Kasus</div>
  <div class="stat"><b><?= $n("SELECT COUNT(*) FROM categories") ?></b>Kategori</div>
  <div class="stat"><b><?= $n("SELECT IFNULL(SUM(views),0) FROM articles") ?></b>Total dibaca</div>
  <div class="stat"><b><?= $n("SELECT COUNT(*) FROM admins") ?></b>Admin</div>
  <div class="stat"><b><?= $n("SELECT COUNT(*) FROM admins WHERE status='pending'") ?></b>Menunggu persetujuan</div>
  <div class="stat"><b><?= $n("SELECT COUNT(*) FROM contacts") ?></b>Pesan masuk</div>
</div>
<h2>Kasus paling banyak dibaca</h2>
<table><tr><th>ID</th><th>Judul</th><th>Dibaca</th></tr>
<?php while ($r = $top->fetch_assoc()): ?>
  <tr><td><?= e($r['case_id']) ?></td><td><?= e($r['judul']) ?></td><td><?= $r['views'] ?></td></tr>
<?php endwhile; ?></table>
<?php include '_foot.php'; ?>
