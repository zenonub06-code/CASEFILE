<?php
$title = 'Tentang'; include 'includes/header.php';
$p = $conn->query("SELECT * FROM site_profile WHERE id=1")->fetch_assoc();
$au = $conn->query("SELECT * FROM authors");
?>
<h1><?= e($p['nama_situs']) ?></h1><p><i><?= e($p['tagline']) ?></i></p>
<div class="paper"><?= nl2br(e($p['deskripsi'])) ?></div>
<h2>Tim redaksi</h2>
<div class="grid"><?php while ($a = $au->fetch_assoc()): ?>
  <div class="card"><h3><?= e($a['nama']) ?></h3><p><?= e($a['bio']) ?></p></div><?php endwhile; ?></div>
<h2>Kontak</h2><p><?= e($p['email']) ?><br><?= e($p['alamat']) ?></p>
<?php include 'includes/footer.php'; ?>
