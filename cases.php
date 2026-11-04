<?php
$title = 'Arsip Kasus'; include 'includes/header.php';
$q = trim($_GET['q'] ?? ''); $kat = (int)($_GET['kat'] ?? 0);
$status = $_GET['status'] ?? ''; $tahun = (int)($_GET['tahun'] ?? 0); $lokasi = trim($_GET['lokasi'] ?? '');

$w = []; $t = ''; $p = [];
if ($q !== '')      { $w[] = "(a.judul LIKE ? OR a.case_id LIKE ? OR c.nama LIKE ?)"; $t .= 'sss'; $l = "%$q%"; array_push($p, $l, $l, $l); }
if ($kat)           { $w[] = "a.category_id=?"; $t .= 'i'; $p[] = $kat; }
if ($status !== '') { $w[] = "a.status=?";      $t .= 's'; $p[] = $status; }
if ($tahun)         { $w[] = "a.tahun=?";       $t .= 'i'; $p[] = $tahun; }
if ($lokasi !== '') { $w[] = "a.lokasi LIKE ?"; $t .= 's'; $p[] = "%$lokasi%"; }

$sql = "SELECT a.*,c.nama kategori FROM articles a JOIN categories c ON c.id=a.category_id"
     . ($w ? ' WHERE ' . implode(' AND ', $w) : '') . " ORDER BY a.created_at DESC";
$st = $conn->prepare($sql);
if ($p) $st->bind_param($t, ...$p);
$st->execute(); $res = $st->get_result();
$cats = $conn->query("SELECT id,nama FROM categories ORDER BY nama");
?>
<h1>Arsip Kasus</h1>
<form method="get" class="row">
  <input name="q" placeholder="Judul / kategori / nomor kasus" value="<?= e($q) ?>">
  <select name="kat"><option value="">Semua kategori</option>
    <?php while ($c = $cats->fetch_assoc()): ?><option value="<?= $c['id'] ?>" <?= $kat == $c['id'] ? 'selected' : '' ?>><?= e($c['nama']) ?></option><?php endwhile; ?>
  </select>
  <select name="status"><option value="">Semua status</option>
    <?php foreach (['Solved','Unsolved','Ongoing Investigation','Historical'] as $s): ?><option <?= $status === $s ? 'selected' : '' ?>><?= $s ?></option><?php endforeach; ?>
  </select>
  <input name="tahun" type="number" placeholder="Tahun" value="<?= $tahun ?: '' ?>">
  <input name="lokasi" placeholder="Lokasi" value="<?= e($lokasi) ?>">
  <button>Cari</button>
</form>
<?php if ($res->num_rows): ?>
  <div class="grid"><?php while ($r = $res->fetch_assoc()) echo card($r); ?></div>
<?php else: ?>
  <p class="msg err">Tidak ada kasus yang cocok. Coba kurangi filter atau ubah kata kunci.</p>
<?php endif; include 'includes/footer.php'; ?>
