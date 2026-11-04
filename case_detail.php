<?php
$title = 'Detail Kasus'; include 'includes/header.php';
$id = (int)($_GET['id'] ?? 0);
$st = $conn->prepare("SELECT a.*,c.nama kategori,au.nama penulis FROM articles a
  JOIN categories c ON c.id=a.category_id JOIN authors au ON au.id=a.author_id WHERE a.id=?");
$st->bind_param('i', $id); $st->execute();
$a = $st->get_result()->fetch_assoc();
if (!$a) { echo '<p class="msg err">Kasus tidak ditemukan.</p>'; include 'includes/footer.php'; exit; }

$conn->query("UPDATE articles SET views=views+1 WHERE id=$id");

$imgs = [];
$qi = $conn->query("SELECT * FROM article_images WHERE article_id=$id");
while ($i = $qi->fetch_assoc()) $imgs[$i['id']] = $i;

function render_isi($teks, $imgs) {
    $out = '';
    foreach (preg_split('/(\[foto:\d+\])/', $teks, -1, PREG_SPLIT_DELIM_CAPTURE) as $part) {
        if (preg_match('/^\[foto:(\d+)\]$/', $part, $m)) {
            $i = $imgs[(int)$m[1]] ?? null;
            if (!$i) continue;
            $out .= '<figure><img src="' . BASE . 'uploads/' . e($i['file']) . '" alt="' . e($i['caption']) . '" loading="lazy">'
                  . ($i['caption'] !== '' ? '<figcaption>' . e($i['caption']) . '</figcaption>' : '') . '</figure>';
        } elseif (trim($part) !== '') {
            $out .= '<div>' . nl2br(e(trim($part))) . '</div>';
        }
    }
    return $out;
}

$tl = $conn->query("SELECT * FROM article_timelines WHERE article_id=$id ORDER BY tanggal");
$ev = $conn->query("SELECT * FROM article_evidence WHERE article_id=$id");
$st = $conn->prepare("SELECT a.*,c.nama kategori FROM articles a JOIN categories c ON c.id=a.category_id
  WHERE a.category_id=? AND a.id<>? LIMIT 3");
$st->bind_param('ii', $a['category_id'], $id); $st->execute(); $rel = $st->get_result();
?>
<p><span class="cid"><?= e($a['case_id']) ?></span> <?= badge($a['status']) ?></p>
<h1><?= e($a['judul']) ?></h1>
<p><small><?= e($a['kategori']) ?> · <?= e($a['lokasi']) ?> · <?= e($a['tahun']) ?> · Ditulis oleh <?= e($a['penulis']) ?> · <?= $a['views'] + 1 ?> dibaca</small></p>

<article class="paper"><?= render_isi($a['isi'], $imgs) ?></article>

<?php if ($a['editors_note']): ?>
  <div class="note"><b>Catatan redaksi.</b> <?= nl2br(e($a['editors_note'])) ?></div>
<?php endif; ?>

<?php if ($tl->num_rows): ?><h2>Kronologi</h2><ul class="tl">
  <?php while ($t = $tl->fetch_assoc()): ?>
    <li><time><?= e($t['tanggal']) ?></time><br><b><?= e($t['judul']) ?></b><br><?= e($t['keterangan']) ?></li>
  <?php endwhile; ?></ul><?php endif; ?>

<?php if ($ev->num_rows): ?><h2>Dokumentasi dan sumber</h2>
<table><tr><th>Judul</th><th>Tipe</th><th>Sumber</th></tr>
  <?php while ($v = $ev->fetch_assoc()): ?>
    <tr><td><?= $v['url'] ? '<a href="' . e($v['url']) . '" target="_blank" rel="noopener">' . e($v['judul']) . '</a>' : e($v['judul']) ?></td>
        <td><?= e($v['tipe']) ?></td><td><?= e($v['sumber']) ?></td></tr>
  <?php endwhile; ?></table><?php endif; ?>

<?php if ($rel->num_rows): ?><h2>Kasus terkait</h2>
<div class="grid"><?php while ($r = $rel->fetch_assoc()) echo card($r); ?></div><?php endif; ?>
<?php include 'includes/footer.php'; ?>
