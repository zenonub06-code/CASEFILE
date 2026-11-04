<?php
require_once __DIR__ . '/../koneksi.php';
if (empty($_SESSION['admin'])) { header('Location: ' . BASE . 'login.php'); exit; }
$me = $_SESSION['admin'];
