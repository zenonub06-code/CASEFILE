<?php
require 'auth.php'; $title = 'Data Admin';
$super = $me['role'] === 'super_admin'; $msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    if (!$super) die('Hanya super admin yang dapat mengubah data admin.');
    $aksi = $_POST['aksi'] ?? ''; $rid = (int)($_POST['id'] ?? 0);
    if ($aksi === 'tambah') {
        $n = trim($_POST['nama']); $u = trim($_POST['username']); $m = trim($_POST['email']); $p = md5($_POST['password']);
        $role = $_POST['role'] === 'super_admin' ? 'super_admin' : 'editor';
        try {
            $st = $conn->prepare("INSERT INTO admins (nama,username,email,password,role,status) VALUES (?,?,?,?,?,'aktif')");
            $st->bind_param('sssss', $n, $u, $m, $p, $role); $st->execute();
        } catch (mysqli_sql_exception $e) { $msg = 'Username sudah dipakai.'; }
    } elseif ($aksi === 'status' && $rid !== (int)$me['id'] && in_array($_POST['status'], ['aktif','nonaktif'], true)) {
        $st = $conn->prepare("UPDATE admins SET status=? WHERE id=?"); $st->bind_param('si', $_POST['status'], $rid); $st->execute();
    } elseif ($aksi === 'hapus' && $rid !== (int)$me['id']) {
        $st = $conn->prepare("DELETE FROM admins WHERE id=?"); $st->bind_param('i', $rid); $st->execute();
    }
    if (!$msg) { header('Location: admins.php'); exit; }
}
$r = $conn->query("SELECT id,nama,username,email,role,status,created_at FROM admins ORDER BY id");  // password sengaja tidak ditampilkan
include '_head.php';
?>
<h1>Data Admin</h1>
<?php if ($msg): ?><p class="msg err"><?= e($msg) ?></p><?php endif; ?>
<table><tr><th>Nama</th><th>Username</th><th>Email</th><th>Role</th><th>Status</th><?php if ($super): ?><th>Aksi</th><?php endif; ?></tr>
<?php while ($a = $r->fetch_assoc()): ?>
<tr><td><?= e($a['nama']) ?></td><td><?= e($a['username']) ?></td><td><?= e($a['email']) ?></td><td><?= e($a['role']) ?></td><td><?= e($a['status']) ?></td>
<?php if ($super): ?><td><?php if ($a['id'] != $me['id']): ?>
  <form method="post" class="inline"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $a['id'] ?>"><input type="hidden" name="aksi" value="status">
    <input type="hidden" name="status" value="<?= $a['status'] === 'aktif' ? 'nonaktif' : 'aktif' ?>"><button class="btn sm alt"><?= $a['status'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' ?></button></form>
  <form method="post" class="inline" onsubmit="return confirm('Hapus admin ini?')"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $a['id'] ?>"><input type="hidden" name="aksi" value="hapus"><button class="btn sm">Hapus</button></form>
<?php endif; ?></td><?php endif; ?></tr>
<?php endwhile; ?></table>
<?php if ($super): ?><h2>Tambah admin</h2>
<form method="post" class="f row"><?= csrf_field() ?><input type="hidden" name="aksi" value="tambah">
  <input name="nama" placeholder="Nama" required><input name="username" placeholder="Username" required>
  <input name="email" type="email" placeholder="Email" required><input name="password" type="password" placeholder="Password" required minlength="6">
  <select name="role"><option value="editor">editor</option><option value="super_admin">super_admin</option></select><button>Tambah</button></form>
<?php endif; include '_foot.php'; ?>
