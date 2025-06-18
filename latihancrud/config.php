<?php
// Konfigurasi database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'defanda2005');
define('DB_NAME', 'akademik');

// Konfigurasi upload
define('UPLOAD_DIR', 'uploads/');
define('MAX_FILE_SIZE', 2 * 1024 * 1024); // 2MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// Konfigurasi pagination
define('RECORDS_PER_PAGE', 5);

// Koneksi database
function getConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8");
    return $conn;
}

// Fungsi untuk validasi input
function validateInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Fungsi untuk validasi file upload
function validateUpload($file) {
    $errors = [];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        switch ($file['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $errors[] = "File terlalu besar. Maksimal 2MB.";
                break;
            case UPLOAD_ERR_PARTIAL:
                $errors[] = "File tidak terupload sempurna.";
                break;
            case UPLOAD_ERR_NO_FILE:
                // Tidak ada file yang diupload (opsional)
                break;
            default:
                $errors[] = "Terjadi kesalahan saat upload.";
        }
        return $errors;
    }
    
    // Cek ukuran file
    if ($file['size'] > MAX_FILE_SIZE) {
        $errors[] = "File terlalu besar. Maksimal 2MB.";
    }
    
    // Cek ekstensi file
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_EXTENSIONS)) {
        $errors[] = "Format file tidak diizinkan. Hanya jpg, jpeg, png, gif.";
    }
    
    return $errors;
}

// Fungsi untuk upload file
function uploadFile($file) {
    // Buat folder uploads jika belum ada
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0777, true);
    }
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = uniqid() . '.' . $ext;
    $filepath = UPLOAD_DIR . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return $filename;
    }
    
    return false;
}

// Fungsi untuk hapus file
function deleteFile($filename) {
    if ($filename && file_exists(UPLOAD_DIR . $filename)) {
        unlink(UPLOAD_DIR . $filename);
    }
}
?>