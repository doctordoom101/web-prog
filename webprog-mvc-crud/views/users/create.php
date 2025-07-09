<div class="page-header">
    <h2><i class="fas fa-plus"></i> Tambah User Baru</h2>
    <a href="index.php?page=users" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="card">
    <div class="card-body">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php?page=users&action=create">
            <div class="form-group">
                <label for="name">
                    <i class="fas fa-user"></i> Nama Lengkap
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       class="form-control" 
                       required 
                       placeholder="Masukkan nama lengkap"
                       value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="email">
                    <i class="fas fa-envelope"></i> Email
                </label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       class="form-control" 
                       required 
                       placeholder="Masukkan alamat email"
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="phone">
                    <i class="fas fa-phone"></i> Nomor Telepon
                </label>
                <input type="tel" 
                       id="phone" 
                       name="phone" 
                       class="form-control" 
                       required 
                       placeholder="Masukkan nomor telepon"
                       value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan User
                </button>
                <a href="index.php?page=users" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>