<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $password = $_POST['password'];

    // Cek apakah email sudah terdaftar
    $cek = $conn->prepare("SELECT id FROM user WHERE email = ?");
    $cek->bind_param("s", $email);
    $cek->execute();
    $cek->store_result();

    if ($cek->num_rows > 0) {
        echo "Email sudah terdaftar. Silakan gunakan email lain. <a href='register.php'>Kembali</a>";
    } else {
        // Simpan user baru
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO user (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hashed);

        if ($stmt->execute()) {
            echo "Registrasi berhasil! <a href='login.php'>Login sekarang</a>";
        } else {
            echo "Terjadi kesalahan saat registrasi.";
        }
    }

    $cek->close();
    $conn->close();
} else {
    header("Location: register.php");
}
