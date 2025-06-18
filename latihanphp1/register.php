<!-- register.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>
    <h2>Form Register</h2>
    <form action="register_proses.php" method="post">
        <label>Nama:</label><br>
        <input type="text" name="name" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Register</button>
    </form>
    <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
</body>
</html>
