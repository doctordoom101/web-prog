<?php
require_once 'config.php';

$conn = getConnection();

// Cek apakah ID valid
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php?error=id_tidak_valid');
    exit;
}

$id = (int)$_GET['id'];

// Ambil data mahasiswa berdasarkan ID
$stmt = $conn->prepare("SELECT * FROM mahasiswa WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header('Location: index.php?error=data_tidak_ditemukan');
    exit;
}

$mahasiswa = $result->fetch_assoc();

// Proses hapus jika dikonfirmasi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm'])) {
    // Hapus foto jika ada
    if ($mahasiswa['foto']) {
        deleteFile($mahasiswa['foto']);
    }
    
    // Hapus data dari database
    $delete_stmt = $conn->prepare("DELETE FROM mahasiswa WHERE id = ?");
    $delete_stmt->bind_param("i", $id);
    
    if ($delete_stmt->execute()) {
        header('Location: index.php?success=data_berhasil_dihapus');
        exit;
    } else {
        $error = "Gagal menghapus data: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hapus Mahasiswa - Sistem Akademik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-exclamation-triangle"></i> Konfirmasi Hapus Data
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i> <?= $error ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Peringatan!</strong> 
                            Data yang sudah dihapus tidak dapat dikembalikan.
                        </div>
                        
                        <div class="text-center mb-4">
                            <?php if ($mahasiswa['foto']): ?>
                                <img src="<?= UPLOAD_DIR . $mahasiswa['foto'] ?>" 
                                     alt="Foto <?= htmlspecialchars($mahasiswa['nama']) ?>" 
                                     class="rounded-circle mb-3" 
                                     width="120" height="120"
                                     style="object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                                     style="width: 120px; height: 120px;">
                                    <i class="fas fa-user text-white fa-3x"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="text-center">
                            <h5>Apakah Anda yakin ingin menghapus data mahasiswa berikut?</h5>
                        </div>
                        
                        <div class="table-responsive mt-4">
                            <table class="table table-bordered">
                                <tr>
                                    <td class="fw-bold" width="30%">ID</td>
                                    <td><?= $mahasiswa['id'] ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Nama</td>
                                    <td><?= htmlspecialchars($mahasiswa['nama']) ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">NIM</td>
                                    <td><?= htmlspecialchars($mahasiswa['nim']) ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Jurusan</td>
                                    <td>
                                        <span class="badge bg-primary">
                                            <?= htmlspecialchars($mahasiswa['jurusan']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Terdaftar</td>
                                    <td><?= date('d/m/Y H:i', strtotime($mahasiswa['created_at'])) ?></td>
                                </tr>
                            </table>
                        </div>
                        
                        <form method="POST" class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Batal
                            </a>
                            <button type="submit" name="confirm" value="1" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Ya, Hapus Data
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Informasi Tambahan -->
                <div class="card mt-4">
                    <div class="card-body">
                        <div class="text-center">
                            <i class="fas fa-info-circle text-info"></i>
                            <small class="text-muted">
                                Sistem menggunakan keamanan berlapis untuk melindungi data Anda.
                                Pastikan Anda benar-benar yakin sebelum menghapus data.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Konfirmasi sekali lagi sebelum submit
        document.querySelector('form').addEventListener('submit', function(e) {
            if (!confirm('Anda yakin ingin menghapus data mahasiswa "<?= addslashes($mahasiswa['nama']) ?>"?\n\nData yang dihapus tidak dapat dikembalikan!')) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>