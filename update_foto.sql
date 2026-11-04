-- Jalankan sekali di phpMyAdmin (database casefile_db) jika database sudah di-import sebelumnya
USE casefile_db;
CREATE TABLE IF NOT EXISTS article_images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  article_id INT NOT NULL,
  file VARCHAR(60) NOT NULL,
  caption VARCHAR(200) NOT NULL DEFAULT '',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
);
