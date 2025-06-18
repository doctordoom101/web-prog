<?php
$host = 'localhost';
$dbname = 'pemweb1';
$user = 'root';
$pass = 'root';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
