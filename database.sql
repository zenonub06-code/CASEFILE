CREATE DATABASE IF NOT EXISTS casefile_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE casefile_db;

CREATE TABLE admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  username VARCHAR(50) NOT NULL UNIQUE,
  email VARCHAR(100) NOT NULL,
  password CHAR(32) NOT NULL,                 -- md5 (sesuai contoh tugas)
  role ENUM('super_admin','editor') NOT NULL DEFAULT 'editor',
  status ENUM('aktif','pending','nonaktif') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  deskripsi TEXT
);

CREATE TABLE authors (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  bio TEXT
);

CREATE TABLE articles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  case_id VARCHAR(10) NOT NULL UNIQUE,
  judul VARCHAR(200) NOT NULL,
  ringkasan VARCHAR(300) NOT NULL,
  isi MEDIUMTEXT NOT NULL,
  category_id INT NOT NULL,
  author_id INT NOT NULL,
  admin_id INT NULL,
  status ENUM('Solved','Unsolved','Ongoing Investigation','Historical') NOT NULL DEFAULT 'Historical',
  lokasi VARCHAR(100) NOT NULL,
  tahun SMALLINT NOT NULL,
  editors_note TEXT,
  views INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id),
  FOREIGN KEY (author_id) REFERENCES authors(id),
  FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE SET NULL
);

CREATE TABLE article_timelines (
  id INT AUTO_INCREMENT PRIMARY KEY,
  article_id INT NOT NULL,
  tanggal DATE NOT NULL,
  judul VARCHAR(150) NOT NULL,
  keterangan TEXT,
  FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
);

CREATE TABLE article_evidence (
  id INT AUTO_INCREMENT PRIMARY KEY,
  article_id INT NOT NULL,
  judul VARCHAR(150) NOT NULL,
  tipe VARCHAR(30) NOT NULL DEFAULT 'Sumber',
  sumber VARCHAR(150),
  url VARCHAR(255),
  FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
);

CREATE TABLE site_profile (
  id INT PRIMARY KEY,
  nama_situs VARCHAR(100),
  tagline VARCHAR(200),
  deskripsi TEXT,
  email VARCHAR(100),
  alamat VARCHAR(200)
);

CREATE TABLE contacts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100), email VARCHAR(100), pesan TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS article_images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  article_id INT NOT NULL,
  file VARCHAR(60) NOT NULL,
  caption VARCHAR(200) NOT NULL DEFAULT '',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
);

-- ===== Data awal =====
-- Login: admin / admin123
INSERT INTO admins (nama,username,email,password,role,status) VALUES
('Administrator','admin','admin@casefile.test',MD5('admin123'),'super_admin','aktif');

INSERT INTO categories (nama,deskripsi) VALUES
('Paranormal & Horror','Kisah hantu, tempat angker, legenda urban, dan pengalaman paranormal.'),
('Unexplained Phenomena','Kejadian aneh yang belum memiliki penjelasan pasti atau masih diperdebatkan.'),
('True Crime','Arsip kasus kriminal nyata, orang hilang, dan investigasi yang menjadi perhatian publik.'),
('Historical Mysteries','Misteri sejarah, artefak kuno, dan peradaban yang belum sepenuhnya dipahami.'),
('Anomalies','Fenomena tidak biasa, objek misterius, dan laporan anomali.'),
('Science & The Unknown','Misteri sains, fenomena alam, luar angkasa, dan penemuan yang masih diteliti.');

INSERT INTO authors (nama,bio) VALUES ('Tim Redaksi CASEFILE','Penyusun arsip dan penyunting kasus.');

INSERT INTO site_profile VALUES (1,'CASEFILE','Arsip digital kasus misteri dunia',
'CASEFILE adalah media dokumentasi yang mengarsipkan kejadian misterius, fenomena anomali, cerita horor, dan kasus kriminal. Setiap artikel disajikan sebagai berkas kasus lengkap dengan nomor kasus, status, kronologi, dan dokumentasi.',
'redaksi@casefile.test','Pekanbaru, Riau, Indonesia');

INSERT INTO articles (case_id,judul,ringkasan,isi,category_id,author_id,admin_id,status,lokasi,tahun,editors_note) VALUES
('CF-001','Mary Celeste: Kapal yang Ditemukan Tanpa Awak','Kapal dagang ditemukan berlayar di Atlantik pada 1872 tanpa seorang pun di atasnya.',
'Mary Celeste berangkat dari New York menuju Genoa pada November 1872. Pada awal Desember, kapal ini ditemukan melayang di Samudra Atlantik oleh kapal Dei Gratia. Muatan dan sebagian besar barang masih berada di tempatnya, tetapi seluruh awak dan penumpang tidak ditemukan.\n\nBerbagai teori diajukan, mulai dari kesalahan navigasi, kekhawatiran akan ledakan muatan alkohol, hingga cuaca buruk. Hingga kini tidak ada penjelasan tunggal yang disepakati.',
4,1,1,'Historical','Samudra Atlantik',1872,'Banyak detail populer tentang kasus ini berasal dari fiksi. Artikel ini hanya memuat hal yang tercatat dalam sumber sejarah.'),
('CF-002','Wow! Signal: Sinyal Radio 72 Detik dari Luar Angkasa','Teleskop Big Ear menangkap sinyal radio kuat yang tidak pernah terdeteksi lagi.',
'Pada 15 Agustus 1977, teleskop radio Big Ear di Ohio menangkap sinyal pita sempit dari arah rasi Sagittarius selama sekitar 72 detik. Astronom Jerry Ehman melingkari data tersebut dan menulis "Wow!" di tepinya. Pencarian lanjutan tidak pernah menemukan sinyal yang sama.',
6,1,1,'Unsolved','Ohio, Amerika Serikat',1977,'Asal sinyal belum terkonfirmasi. Sumber alami maupun buatan belum dapat dipastikan.'),
('CF-003','Insiden Dyatlov Pass','Sembilan pendaki tewas di Pegunungan Ural pada 1959 dengan kondisi yang membingungkan penyelidik.',
'Pada Januari-Februari 1959, sekelompok pendaki Uni Soviet melakukan ekspedisi di Ural Utara. Tenda mereka ditemukan robek dan ditinggalkan, dan seluruh anggota kelompok akhirnya ditemukan meninggal di sekitar lokasi. Penyelidikan resmi kala itu menyimpulkan penyebabnya adalah kekuatan alam yang tak teratasi. Penyelidikan ulang oleh otoritas Rusia pada 2019 mengarah pada longsoran salju, meski teori tersebut masih diperdebatkan.',
2,1,1,'Historical','Pegunungan Ural, Rusia',1959,'Artikel ini menghormati para korban dan sengaja tidak memuat foto atau detail kondisi jenazah.');

INSERT INTO article_timelines (article_id,tanggal,judul,keterangan) VALUES
(1,'1872-11-07','Berangkat dari New York','Mary Celeste meninggalkan New York menuju Genoa.'),
(1,'1872-12-04','Ditemukan Dei Gratia','Kapal ditemukan melayang tanpa awak di Atlantik.'),
(2,'1977-08-15','Sinyal terdeteksi','Big Ear mencatat sinyal sekitar 72 detik.'),
(3,'1959-01-23','Ekspedisi dimulai','Kelompok pendaki memulai perjalanan.'),
(3,'1959-02-26','Tenda ditemukan','Tim pencari menemukan tenda yang ditinggalkan.');

INSERT INTO article_evidence (article_id,judul,tipe,sumber,url) VALUES
(1,'Catatan sidang admiralty Gibraltar','Dokumen','Arsip sejarah maritim',''),
(2,'Cetakan data Big Ear','Dokumen','Arsip Big Ear Radio Observatory',''),
(3,'Laporan penyelidikan ulang 2019','Laporan','Kantor Kejaksaan Rusia','');
