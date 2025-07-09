<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD MVC - User Management</title>
    <link rel="stylesheet" href="public/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <h1><i class="fas fa-users"></i> User Management</h1>
            </div>
            <div class="nav-menu">
                <a href="index.php?page=users" class="nav-link">
                    <i class="fas fa-list"></i> Semua User
                </a>
                <a href="index.php?page=users&action=create" class="nav-link">
                    <i class="fas fa-plus"></i> Tambah User
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php if (isset($_GET['message'])): ?>
            <div class="alert alert-<?php echo $_GET['message'] === 'error' ? 'danger' : 'success'; ?>">
                <?php
                switch ($_GET['message']) {
                    case 'created':
                        echo '<i class="fas fa-check-circle"></i> User berhasil ditambahkan!';
                        break;
                    case 'updated':
                        echo '<i class="fas fa-check-circle"></i> User berhasil diupdate!';
                        break;
                    case 'deleted':
                        echo '<i class="fas fa-check-circle"></i> User berhasil dihapus!';
                        break;
                    case 'error':
                        echo '<i class="fas fa-exclamation-circle"></i> Terjadi kesalahan!';
                        break;
                }
                ?>
            </div>
        <?php endif; ?>