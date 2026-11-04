<?php require_once __DIR__ . '/../koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($title ?? 'CASEFILE') ?> | CASEFILE</title>
<link rel="stylesheet" href="<?= BASE ?>assets/style.css">
</head><body>
<header class="top"><div class="wrap nav">
  <a class="logo" href="<?= BASE ?>index.php">CASE<span>FILE</span></a>
  <nav>
    <a href="<?= BASE ?>cases.php">Arsip Kasus</a>
    <a href="<?= BASE ?>categories.php">Kategori</a>
    <a href="<?= BASE ?>timeline.php">Timeline</a>
    <a href="<?= BASE ?>evidence.php">Evidence</a>
    <a href="<?= BASE ?>about.php">Tentang</a>
    <a href="<?= BASE ?>contact.php">Kontak</a>
    <?php if (!empty($_SESSION['admin'])): ?>
      <a href="<?= BASE ?>admin/index.php">Admin</a><a href="<?= BASE ?>logout.php">Keluar</a>
    <?php else: ?><a href="<?= BASE ?>login.php">Masuk</a><?php endif; ?>
  </nav>
  <form action="<?= BASE ?>cases.php" method="get" class="search">
    <input name="q" placeholder="Cari judul, kategori, atau CF-001" value="<?= e($_GET['q'] ?? '') ?>">
  </form>
</div></header>
<main class="wrap">
