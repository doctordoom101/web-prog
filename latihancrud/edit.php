<?php
require_once 'config.php';

$conn = getConnection();
$errors = [];
$success = '';

// Cek apakah ID valid
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int)$_GET['id'];

// Ambil data mahasiswa berdasarkan ID
$stmt = $conn->prepare("SELECT * FROM mahasiswa WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header('Location: index.php');
    exit;
}

$mahasiswa = $result->fetch_assoc();

// Proses form jika disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = validateInput($_POST['nama']);
    $nim = validateInput($_POST['nim']);
    $jurusan = validateInput($_POST['jurusan']);
    
    // Validasi input
    if (empty($nama)) {
        $errors[] = "Nama harus diisi.";
    } elseif (strlen($nama) < 3 || strlen($nama) > 100) {
        $errors[] = "Nama harus antara 3-100 karakter.";
    }
    
    if (empty($nim)) {
        $errors[] = "NIM harus diisi.";
    } elseif (strlen($nim) < 5 || strlen($nim) > 20) {
        $errors[] = "NIM harus antara 5-20 karakter.";
    } else {
        // Cek apakah NIM sudah ada (kecuali untuk data saat ini)
        $check_stmt = $conn->prepare("SELECT id FROM mahasiswa WHERE nim = ? AND id != ?");
        $check_stmt->bind_param("si", $nim, $id);
        $check_stmt->execute();
        if ($check_stmt->get_result()->num_rows > 0) {
            $errors[] = "NIM sudah terdaftar.";
        }
    }
    
    if (empty($jurusan)) {
        $errors[] = "Jurusan harus diisi.";
    } elseif (strlen($jurusan) < 3 || strlen($jurusan) > 50) {
        $errors[] = "Jurusan harus antara 3-50 karakter.";
    }
    
    // Validasi upload foto (opsional)
    $foto_name = $mahasiswa['foto']; // Tetap gunakan foto lama
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
        $upload_errors = validateUpload($_FILES['foto']);
        if ($upload_errors) {
            $errors = array_merge($errors, $upload_errors);
        } else {
            $new_foto = uploadFile($_FILES['foto']);
            if ($new_foto) {
                // Hapus foto lama jika ada
                if ($mahasiswa['foto']) {
                    deleteFile($mahasiswa['foto']);
                }
                $foto_name = $new_foto;
            } else {
                $errors[] = "Gagal mengupload foto.";
            }
        }
    }
    
    // Jika tidak ada error, update data
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE mahasiswa SET nama = ?, nim = ?, jurusan = ?, foto = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $nama, $nim, $jurusan, $foto_name, $id);
        
        if ($stmt->execute()) {
            $success = "Data mahasiswa berhasil diupdate!";
            // Refresh data mahasiswa
            $stmt = $conn->prepare("SELECT * FROM mahasiswa WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $mahasiswa = $stmt->get_result()->fetch_assoc();
        } else {
            $errors[] = "Gagal mengupdate data: " . $conn->error;
        }
    }
} else {
    // Jika bukan POST, isi form dengan data yang ada
    $nama = $mahasiswa['nama'];
    $nim = $mahasiswa['nim'];
    $jurusan = $mahasiswa['jurusan'];
}

// Ambil daftar jurusan yang sudah ada
$jurusan_query = "SELECT DISTINCT jurusan FROM mahasiswa ORDER BY jurusan";
$jurusan_result = $conn->query($jurusan_query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Mahasiswa - Sistem Akademik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-user-edit"></i> Edit Data Mahasiswa
                        </h4>
                    </div>
                    <div class="card-body">
                        <!-- Alert untuk error -->
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Error:</strong>
                                <ul class="mb-0 mt-2">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?= $error ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Alert untuk success -->
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i> <?= $success ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="nama" class="form-label">
                                    Nama Lengkap <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="nama" name="nama" 
                                       value="<?= htmlspecialchars($nama) ?>"
                                       placeholder="Masukkan nama lengkap" maxlength="100" required>
                                <div class="form-text">Minimal 3 karakter, maksimal 100 karakter</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="nim" class="form-label">
                                    NIM <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="nim" name="nim" 
                                       value="<?= htmlspecialchars($nim) ?>"
                                       placeholder="Masukkan NIM" maxlength="20" required>
                                <div class="form-text">Minimal 5 karakter, maksimal 20 karakter</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="jurusan" class="form-label">
                                    Jurusan <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="jurusan" name="jurusan" 
                                       value="<?= htmlspecialchars($jurusan) ?>"
                                       placeholder="Masukkan jurusan" maxlength="50" required
                                       list="jurusan-list">
                                <datalist id="jurusan-list">
                                    <?php while ($row = $jurusan_result->fetch_assoc()): ?>
                                        <option value="<?= htmlspecialchars($row['jurusan']) ?>">
                                    <?php endwhile; ?>
                                </datalist>
                                <div class="form-text">Minimal 3 karakter, maksimal 50 karakter</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="foto" class="form-label">
                                    Foto Mahasiswa <span class="text-muted">(Opsional)</span>
                                </label>
                                
                                <!-- Tampilkan foto saat ini jika ada -->
                                <?php if ($mahasiswa['foto']): ?>
                                    <div class="mb-2">
                                        <div class="d-flex align-items-center">
                                            <img src="<?= UPLOAD_DIR . $mahasiswa['foto'] ?>" 
                                                 alt="Foto saat ini" 
                                                 class="rounded" 
                                                 width="100" height="100"
                                                 style="object-fit: cover;">
                                            <div class="ms-3">
                                                <div class="fw-bold">Foto saat ini</div>
                                                <div class="text-muted small"><?= $mahasiswa['foto'] ?></div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <input type="file" class="form-control" id="foto" name="foto" 
                                       accept=".jpg,.jpeg,.png,.gif">
                                <div class="form-text">
                                    Format: JPG, JPEG, PNG, GIF. Maksimal 2MB. 
                                    <?= $mahasiswa['foto'] ? 'Kosongkan jika tidak ingin mengubah foto.' : '' ?>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="index.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                                <button type="reset" class="btn btn-outline-secondary">
                                    <i class="fas fa-undo"></i> Reset
                                </button>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save"></i> Update Data
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Informasi Data -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-info-circle"></i> Informasi Data
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>ID:</strong> <?= $mahasiswa['id'] ?></p>
                                <p><strong>Dibuat:</strong> <?= date('d/m/Y H:i', strtotime($mahasiswa['created_at'])) ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Terakhir diupdate:</strong> <?= date('d/m/Y H:i', strtotime($mahasiswa['updated_at'])) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Preview foto sebelum upload
        document.getElementById('foto').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Buat preview jika belum ada
                    let preview = document.getElementById('foto-preview');
                    if (!preview) {
                        preview = document.createElement('div');
                        preview.id = 'foto-preview';
                        preview.className = 'mt-2';
                        document.getElementById('foto').parentNode.appendChild(preview);
                    }
                    preview.innerHTML = `
                        <div class="alert alert-info">
                            <div class="d-flex align-items-center">
                                <img src="${e.target.result}" class="rounded" width="100" height="100" style="object-fit: cover;">
                                <div class="ms-3">
                                    <div class="fw-bold">Foto baru: ${file.name}</div>
                                    <div class="text-muted small">${(file.size / 1024 / 1024).toFixed(2)} MB</div>
                                </div>
                            </div>
                        </div>
                    `;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>