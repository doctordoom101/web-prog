<div class="page-header">
    <h2><i class="fas fa-users"></i> Daftar User</h2>
    <a href="index.php?page=users&action=create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah User Baru
    </a>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($users)): ?>
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <h3>Belum ada user</h3>
                <p>Silakan tambahkan user baru untuk memulai.</p>
                <a href="index.php?page=users&action=create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah User Pertama
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Telepon</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['id']); ?></td>
                                <td><?php echo htmlspecialchars($user['name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['phone']); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></td>
                                <td class="actions">
                                    <a href="index.php?page=users&action=show&id=<?php echo $user['id']; ?>" 
                                       class="btn btn-sm btn-info" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="index.php?page=users&action=edit&id=<?php echo $user['id']; ?>" 
                                       class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="index.php?page=users&action=delete&id=<?php echo $user['id']; ?>" 
                                       class="btn btn-sm btn-danger" title="Hapus"
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>