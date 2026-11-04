<?php
$title = 'Kontak'; include 'includes/header.php';
$ok = false; $err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $n = trim($_POST['nama'] ?? ''); $m = trim($_POST['email'] ?? ''); $s = trim($_POST['pesan'] ?? '');
    if ($n === '' || $s === '' || !filter_var($m, FILTER_VALIDATE_EMAIL)) $err = 'Isi nama, email yang valid, dan pesan.';
    else {
        $st = $conn->prepare("INSERT INTO contacts (nama,email,pesan) VALUES (?,?,?)");
        $st->bind_param('sss', $n, $m, $s); $st->execute(); $ok = true;
    }
}
?>
<h1>Kontak</h1>
<?php if ($ok): ?><p class="msg">Pesan terkirim. Terima kasih.</p><?php endif; ?>
<?php if ($err): ?><p class="msg err"><?= e($err) ?></p><?php endif; ?>
<form method="post" class="f" style="max-width:520px"><?= csrf_field() ?>
  <label>Nama</label><input name="nama" required>
  <label>Email</label><input name="email" type="email" required>
  <label>Pesan</label><textarea name="pesan" rows="5" required></textarea>
  <p><button>Kirim pesan</button></p>
</form>
<?php include 'includes/footer.php'; ?>
