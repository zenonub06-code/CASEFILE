<?php
require 'koneksi.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: login.php'); exit; }

$username = trim($_POST['username'] ?? '');
$password = md5($_POST['password'] ?? '');   // enkripsi md5 sesuai contoh tugas

$st = $conn->prepare("SELECT id,nama,username,role,status FROM admins WHERE username=? AND password=?");
$st->bind_param('ss', $username, $password);
$st->execute();
$u = $st->get_result()->fetch_assoc();

if (!$u)                      { header('Location: login.php?pesan=gagal');  exit; }
if ($u['status'] !== 'aktif') { header('Location: login.php?pesan=status'); exit; }

session_regenerate_id(true);
$_SESSION['admin'] = $u;
header('Location: admin/index.php');
