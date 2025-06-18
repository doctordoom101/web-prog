<?php
require_once 'config.php';

$conn = getConnection();

// Ambil parameter pencarian dan filter
$search = isset($_GET['search']) ? validateInput($_GET['search']) : '';
$jurusan_filter = isset($_GET['jurusan']) ? validateInput($_GET['jurusan']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * RECORDS_PER_PAGE;

// Query untuk menghitung total data
$count_query = "SELECT COUNT(*) as total FROM mahasiswa WHERE 1=1";
$params = [];
$types = "";

if ($search) {
    $count_query .= " AND (nama LIKE ? OR nim LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= "ss";
}

if ($jurusan_filter) {
    $count_query .= " AND jurusan LIKE ?";
    $params[] = "%$jurusan_filter%";
    $types .= "s";
}

$count_stmt = $conn->prepare($count_query);
if ($params) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$total_records = $count_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_records / RECORDS_PER_PAGE);

// Query untuk mengambil data mahasiswa
$query = "SELECT * FROM mahasiswa WHERE 1=1";
if ($search) {
    $query .= " AND (nama LIKE ? OR nim LIKE ?)";
}
if ($jurusan_filter) {
    $query .= " AND jurusan LIKE ?";
}
$query .= " ORDER BY nama ASC LIMIT ? OFFSET ?";

$stmt = $conn->prepare($query);
$params[] = RECORDS_PER_PAGE;
$params[] = $offset;
$types .= "ii";

if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Ambil daftar jurusan untuk filter
$jurusan_query = "SELECT DISTINCT jurusan FROM mahasiswa ORDER BY jurusan";
$jurusan_result = $conn->query($jurusan_query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mahasiswa - Sistem Akademik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">
                    <i class="fas fa-graduation-cap"></i> Data Mahasiswa
                </h1>
                
                <!-- Alert untuk pesan success/error -->
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle"></i>
                        <?php
                        switch ($_GET['success']) {
                            case 'data_berhasil_dihapus':
                                echo 'Data mahasiswa berhasil dihapus!';
                                break;
                            default:
                                echo 'Operasi berhasil dilakukan!';
                        }
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?php
                        switch ($_GET['error']) {
                            case 'id_tidak_valid':
                                echo 'ID tidak valid!';
                                break;
                            case 'data_tidak_ditemukan':
                                echo 'Data tidak ditemukan!';
                                break;
                            default:
                                echo 'Terjadi kesalahan!';
                        }
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <!-- Form Pencarian dan Filter -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Pencarian</label>
                                <input type="text" class="form-control" name="search" 
                                       value="<?= htmlspecialchars($search) ?>" 
                                       placeholder="Cari nama atau NIM...">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Filter Jurusan</label>
                                <select class="form-select" name="jurusan">
                                    <option value="">Semua Jurusan</option>
                                    <?php while ($row = $jurusan_result->fetch_assoc()): ?>
                                        <option value="<?= $row['jurusan'] ?>" 
                                                <?= $jurusan_filter == $row['jurusan'] ? 'selected' : '' ?>>
                                            <?= $row['jurusan'] ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Cari
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <a href="index.php" class="btn btn-secondary">
                                        <i class="fas fa-refresh"></i> Reset
                                    </a>
                                    <a href="tambah.php" class="btn btn-success">
                                        <i class="fas fa-plus"></i> Tambah
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Tabel Data -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            Daftar Mahasiswa 
                            <span class="badge bg-info"><?= $total_records ?> data</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if ($result->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>No</th>
                                            <th>Foto</th>
                                            <th>Nama</th>
                                            <th>NIM</th>
                                            <th>Jurusan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $no = $offset + 1;
                                        while ($row = $result->fetch_assoc()): 
                                        ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td>
                                                    <?php if ($row['foto']): ?>
                                                        <img src="<?= UPLOAD_DIR . $row['foto'] ?>" 
                                                             alt="Foto <?= $row['nama'] ?>" 
                                                             class="rounded-circle" 
                                                             width="50" height="50"
                                                             style="object-fit: cover;">
                                                    <?php else: ?>
                                                        <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" 
                                                             style="width: 50px; height: 50px;">
                                                            <i class="fas fa-user text-white"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= htmlspecialchars($row['nama']) ?></td>
                                                <td><?= htmlspecialchars($row['nim']) ?></td>
                                                <td>
                                                    <span class="badge bg-primary">
                                                        <?= htmlspecialchars($row['jurusan']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="edit.php?id=<?= $row['id'] ?>" 
                                                       class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="hapus.php?id=<?= $row['id'] ?>" 
                                                       class="btn btn-sm btn-danger"
                                                       onclick="return confirm('Yakin ingin menghapus data ini?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            <?php if ($total_pages > 1): ?>
                                <nav aria-label="Page navigation">
                                    <ul class="pagination justify-content-center">
                                        <?php if ($page > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>&jurusan=<?= urlencode($jurusan_filter) ?>">
                                                    <i class="fas fa-chevron-left"></i>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        
                                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                                <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&jurusan=<?= urlencode($jurusan_filter) ?>">
                                                    <?= $i ?>
                                                </a>
                                            </li>
                                        <?php endfor; ?>
                                        
                                        <?php if ($page < $total_pages): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>&jurusan=<?= urlencode($jurusan_filter) ?>">
                                                    <i class="fas fa-chevron-right"></i>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle"></i> 
                                Tidak ada data mahasiswa yang ditemukan.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>