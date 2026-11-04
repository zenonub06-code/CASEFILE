<?php
$title = 'Beranda'; include 'includes/header.php';
$feat = $conn->query("SELECT a.*,c.nama kategori FROM articles a JOIN categories c ON c.id=a.category_id ORDER BY a.views DESC, a.created_at DESC LIMIT 3");
$new  = $conn->query("SELECT a.*,c.nama kategori FROM articles a JOIN categories c ON c.id=a.category_id ORDER BY a.created_at DESC LIMIT 6");
$cats = $conn->query("SELECT c.id,c.nama,COUNT(a.id) n FROM categories c LEFT JOIN articles a ON a.category_id=c.id GROUP BY c.id ORDER BY n DESC");
?>
<section class="hero">
  <h1>Setiap kasus punya berkas.</h1>
  <p>Telusuri kronologi, dokumentasi, dan sumber dari kejadian misterius di berbagai belahan dunia.</p>
  <a class="btn" href="cases.php">Buka arsip kasus</a>
</section>

<h2>Kasus pilihan</h2>
<div class="grid"><?php while ($r = $feat->fetch_assoc()) echo card($r); ?></div>

<h2>Kategori</h2>
<div class="grid"><?php while ($c = $cats->fetch_assoc()): ?>
  <a class="card" href="cases.php?kat=<?= $c['id'] ?>"><h3><?= e($c['nama']) ?></h3><small><?= $c['n'] ?> kasus</small></a>
<?php endwhile; ?></div>

<h2>Berkas terbaru</h2>
<div class="grid"><?php while ($r = $new->fetch_assoc()) echo card($r); ?></div>
<?php include 'includes/footer.php'; ?>
