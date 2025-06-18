<?php
$result = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $angka1 = isset($_POST['angka1']) ? floatval($_POST['angka1']) : 0;
    $angka2 = isset($_POST['angka2']) ? floatval($_POST['angka2']) : 0;
    $operasi = isset($_POST['operasi']) ? $_POST['operasi'] : '';
    
    switch ($operasi) {
        case 'tambah':
            $result = $angka1 + $angka2;
            $result = "$angka1 + $angka2 = $result";
            break;
        case 'kurang':
            $result = $angka1 - $angka2;
            $result = "$angka1 - $angka2 = $result";
            break;
        case 'kali':
            $result = $angka1 * $angka2;
            $result = "$angka1 × $angka2 = $result";
            break;
        case 'bagi':
            if ($angka2 != 0) {
                $result = $angka1 / $angka2;
                $result = "$angka1 ÷ $angka2 = $result";
            } else {
                $error = "Error: Tidak bisa membagi dengan nol!";
            }
            break;
        default:
            $error = "Pilih operasi yang valid!";
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalkulator Sederhana</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>🧮 Kalkulator Sederhana</h1>
        
        <form method="POST" class="calc-form">
            <div class="form-group">
                <label for="angka1">Angka Pertama:</label>
                <input type="number" id="angka1" name="angka1" step="any" required 
                       value="<?= isset($_POST['angka1']) ? htmlspecialchars($_POST['angka1']) : '' ?>">
            </div>
            
            <div class="form-group">
                <label for="operasi">Operasi:</label>
                <select id="operasi" name="operasi" required>
                    <option value="">-- Pilih Operasi --</option>
                    <option value="tambah" <?= (isset($_POST['operasi']) && $_POST['operasi'] === 'tambah') ? 'selected' : '' ?>>Penjumlahan (+)</option>
                    <option value="kurang" <?= (isset($_POST['operasi']) && $_POST['operasi'] === 'kurang') ? 'selected' : '' ?>>Pengurangan (-)</option>
                    <option value="kali" <?= (isset($_POST['operasi']) && $_POST['operasi'] === 'kali') ? 'selected' : '' ?>>Perkalian (×)</option>
                    <option value="bagi" <?= (isset($_POST['operasi']) && $_POST['operasi'] === 'bagi') ? 'selected' : '' ?>>Pembagian (÷)</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="angka2">Angka Kedua:</label>
                <input type="number" id="angka2" name="angka2" step="any" required 
                       value="<?= isset($_POST['angka2']) ? htmlspecialchars($_POST['angka2']) : '' ?>">
            </div>
            
            <button type="submit" class="btn btn-primary">🔢 Hitung</button>
        </form>
        
        <?php if ($result): ?>
            <div class="result success">
                <strong>Hasil:</strong> <?= htmlspecialchars($result) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="result error">
                <strong>Error:</strong> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <a href="index.php" class="btn back-btn">⬅️ Kembali ke Menu</a>
    </div>
</body>
</html>
