<?php
require 'auth.php';
$id = (int)($_GET['id'] ?? 0);
$a = ['case_id'=>'','judul'=>'','ringkasan'=>'','isi'=>'','category_id'=>0,'author_id'=>0,'status'=>'Historical','lokasi'=>'','tahun'=>date('Y'),'editors_note'=>''];
if ($id) {
    $st = $conn->prepare("SELECT * FROM articles WHERE id=?"); $st->bind_param('i', $id); $st->execute();
    $a = $st->get_result()->fetch_assoc() ?: die('Kasus tidak ditemukan.');
}
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $cid = trim($_POST['case_id']); $judul = trim($_POST['judul']); $ring = trim($_POST['ringkasan']); $isi = trim($_POST['isi']);
    $cat = (int)$_POST['category_id']; $aut = (int)$_POST['author_id']; $stt = $_POST['status'];
    $lok = trim($_POST['lokasi']); $thn = (int)$_POST['tahun']; $note = trim($_POST['editors_note']);
    if (!in_array($stt, ['Solved','Unsolved','Ongoing Investigation','Historical'], true)) $stt = 'Historical';
    if ($cid === '') {
        $nx = $conn->query("SELECT IFNULL(MAX(id),0)+1 FROM articles")->fetch_row()[0];
        $cid = 'CF-' . str_pad($nx, 3, '0', STR_PAD_LEFT);
    }
    if ($judul === '' || $ring === '' || $isi === '' || !$cat || !$aut || $lok === '') $err = 'Lengkapi semua kolom wajib.';
    else {
        try {
            if ($id) {
                $st = $conn->prepare("UPDATE articles SET case_id=?,judul=?,ringkasan=?,isi=?,category_id=?,author_id=?,status=?,lokasi=?,tahun=?,editors_note=? WHERE id=?");
                $st->bind_param('ssssiissisi', $cid, $judul, $ring, $isi, $cat, $aut, $stt, $lok, $thn, $note, $id);
            } else {
                $st = $conn->prepare("INSERT INTO articles (case_id,judul,ringkasan,isi,category_id,author_id,admin_id,status,lokasi,tahun,editors_note) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
                $st->bind_param('ssssiiissis', $cid, $judul, $ring, $isi, $cat, $aut, $me['id'], $stt, $lok, $thn, $note);
            }
            $st->execute();
            header('Location: cases.php'); exit;
        } catch (mysqli_sql_exception $ex) { $err = 'Gagal menyimpan. Pastikan nomor kasus belum dipakai.'; }
    }
    $a = array_merge($a, ['case_id'=>$cid,'judul'=>$judul,'ringkasan'=>$ring,'isi'=>$isi,'category_id'=>$cat,'author_id'=>$aut,'status'=>$stt,'lokasi'=>$lok,'tahun'=>$thn,'editors_note'=>$note]);
}
$cats = $conn->query("SELECT id,nama FROM categories ORDER BY nama");
$auts = $conn->query("SELECT id,nama FROM authors ORDER BY nama");
$title = $id ? 'Ubah Kasus' : 'Tambah Kasus';
$fotos = $id ? $conn->query("SELECT * FROM article_images WHERE article_id=$id ORDER BY id") : null;
include '_head.php';
?>
<h1><?= e($title) ?></h1>
<?php if ($err): ?><p class="msg err"><?= e($err) ?></p><?php endif; ?>
<form method="post" class="f"><?= csrf_field() ?>
  <label>Nomor kasus (kosongkan untuk otomatis)</label><input name="case_id" value="<?= e($a['case_id']) ?>" maxlength="10">
  <label>Judul</label><input name="judul" required value="<?= e($a['judul']) ?>">
  <label>Ringkasan (maks. 300 karakter)</label><input name="ringkasan" required maxlength="300" value="<?= e($a['ringkasan']) ?>">
  <label>Isi artikel</label><textarea id="isi" name="isi" rows="12" required><?= e($a['isi']) ?></textarea>
  <?php if ($id): ?>
    <p><small>Letakkan kursor di antara paragraf, lalu klik <b>Sisipkan</b> pada foto. Kode <code>[foto:nomor]</code> akan muncul di teks dan tampil sebagai foto di halaman kasus.</small></p>
    <div class="thumbs">
    <?php while ($f = $fotos->fetch_assoc()): ?>
      <div class="thumb"><img src="<?= BASE ?>uploads/<?= e($f['file']) ?>" alt=""><small>[foto:<?= $f['id'] ?>] <?= e($f['caption']) ?></small>
        <button type="button" class="btn sm alt" onclick="sisip(<?= $f['id'] ?>)">Sisipkan</button></div>
    <?php endwhile; ?>
    </div>
    <p><a class="btn sm alt" href="photos.php?id=<?= $id ?>">Kelola foto kasus ini</a></p>
  <?php else: ?>
    <p><small>Simpan kasus terlebih dahulu. Setelah itu foto bisa diunggah dan disisipkan di antara paragraf.</small></p>
  <?php endif; ?>
  <div class="row">
    <div><label>Kategori</label><select name="category_id" required><option value="">Pilih</option>
      <?php while ($c = $cats->fetch_assoc()): ?><option value="<?= $c['id'] ?>" <?= $a['category_id'] == $c['id'] ? 'selected' : '' ?>><?= e($c['nama']) ?></option><?php endwhile; ?></select></div>
    <div><label>Penulis</label><select name="author_id" required><option value="">Pilih</option>
      <?php while ($c = $auts->fetch_assoc()): ?><option value="<?= $c['id'] ?>" <?= $a['author_id'] == $c['id'] ? 'selected' : '' ?>><?= e($c['nama']) ?></option><?php endwhile; ?></select></div>
    <div><label>Status</label><select name="status">
      <?php foreach (['Solved','Unsolved','Ongoing Investigation','Historical'] as $s): ?><option <?= $a['status'] === $s ? 'selected' : '' ?>><?= $s ?></option><?php endforeach; ?></select></div>
    <div><label>Lokasi</label><input name="lokasi" required value="<?= e($a['lokasi']) ?>"></div>
    <div><label>Tahun</label><input name="tahun" type="number" required value="<?= e($a['tahun']) ?>"></div>
  </div>
  <label>Catatan redaksi</label><textarea name="editors_note" rows="3"><?= e($a['editors_note']) ?></textarea>
  <p><button>Simpan</button> <a class="btn alt" href="cases.php">Batal</a></p>
</form>
<script>
function sisip(id){
  var t=document.getElementById('isi'), s=t.selectionStart, e=t.selectionEnd, k='\n\n[foto:'+id+']\n\n';
  t.value=t.value.slice(0,s)+k+t.value.slice(e); t.focus(); t.selectionStart=t.selectionEnd=s+k.length;
}
</script>
<?php include '_foot.php'; ?>
