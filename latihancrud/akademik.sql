-- Buat database akademik
CREATE DATABASE IF NOT EXISTS akademik;
USE akademik;

-- Buat tabel mahasiswa
CREATE TABLE mahasiswa (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    nim VARCHAR(20) NOT NULL UNIQUE,
    jurusan VARCHAR(50) NOT NULL,
    foto VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert data sample
INSERT INTO mahasiswa (nama, nim, jurusan) VALUES
('Budi Santoso', '2021001', 'Teknik Informatika'),
('Siti Aminah', '2021002', 'Sistem Informasi'),
('Ahmad Rizki', '2021003', 'Teknik Komputer'),
('Dewi Lestari', '2021004', 'Teknik Informatika'),
('Roni Wijaya', '2021005', 'Sistem Informasi');