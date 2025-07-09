<div class="page-header">
    <h2><i class="fas fa-user"></i> Detail User</h2>
    <div class="page-actions">
        <a href="index.php?page=users&action=edit&id=<?php echo $user['id']; ?>" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit
        </a>
        <a href="index.php?page=users" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="user-detail">
            <div class="detail-item">
                <div class="detail-label">
                    <i class="fas fa-hashtag"></i> ID
                </div>
                <div class="detail-value">
                    <?php echo htmlspecialchars($user['id']); ?>
                </div>
            </div>

            <div class="detail-item">
                <div class="detail-label">
                    <i class="fas fa-user"></i> Nama Lengkap
                </div>
                <div class="detail-value">
                    <?php echo htmlspecialchars($user['name']); ?>
                </div>
            </div>

            <div class="detail-item">
                <div class="detail-label">
                    <i class="fas fa-envelope"></i> Email
                </div>
                <div class="detail-value">
                    <a href="mailto:<?php echo htmlspecialchars($user['email']); ?>">
                        <?php echo htmlspecialchars($user['email']); ?>
                    </a>
                </div>
            </div>

            <div class="detail-item">
                <div class="detail-label">
                    <i class="fas fa-phone"></i> Nomor Telepon
                </div>
                <div class="detail-value">
                    <a href="tel:<?php echo htmlspecialchars($user['phone']); ?>">
                        <?php echo htmlspecialchars($user['phone']); ?>
                    </a>
                </div>
            </div>

            <div class="detail-item">
                <div class="detail-label">
                    <i class="fas fa-calendar-plus"></i> Dibuat
                </div>
                <div class="detail-value">
                    <?php echo date('d F Y, H:i', strtotime($user['created_at'])); ?>
                </div>
            </div>

            <?php if ($user['updated_at']): ?>
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="fas fa-calendar-edit"></i> Terakhir Diupdate
                    </div>
                    <div class="detail-value">
                        <?php echo date('d F Y, H:i', strtotime($user['updated_at'])); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-cogs"></i> Aksi</h3>
    </div>
    <div class="card-body">
        <div class="action-buttons">
            <a href="index.php?page=users&action=edit&id=<?php echo $user['id']; ?>" 
               class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit User
            </a>
            <a href="index.php?page=users&action=delete&id=<?php echo $user['id']; ?>" 
               class="btn btn-danger"
               onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                <i class="fas fa-trash"></i> Hapus User
            </a>
        </div>
    </div>
</div>