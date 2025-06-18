<?php
session_start();
require 'config.php';

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM user WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    header("Location: index.php");
} else {
    $_SESSION['error'] = "Email atau password salah!";
    header("Location: login.php");
}
?>
