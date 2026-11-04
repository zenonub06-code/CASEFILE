<?php
$title = 'Kategori'; include 'includes/header.php';
$cats = $conn->query("SELECT c.*,COUNT(a.id) n FROM categories c LEFT JOIN articles a ON a.category_id=c.id GROUP BY c.id");
?>
<h1>Kategori</h1>
<div class="grid"><?php while ($c = $cats->fetch_assoc()): ?>
  <a class="card" href="cases.php?kat=<?= $c['id'] ?>"><h3><?= e($c['nama']) ?></h3><p><?= e($c['deskripsi']) ?></p><small><?= $c['n'] ?> kasus</small></a>
<?php endwhile; ?></div>
<?php include 'includes/footer.php'; ?>
