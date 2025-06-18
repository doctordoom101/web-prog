<?php
$result = '';
$error = '';

// Fungsi konversi suhu
function konversiSuhu($suhu, $dari, $ke) {
    $suhu = floatval($suhu);
    
    // Konversi ke Celsius terlebih dahulu
    switch ($dari) {
        case 'fahrenheit':
            $celsius = ($suhu - 32) * 5/9;
            break;
        case 'reamur':
            $celsius = $suhu * 5/4;
            break;
        case 'kelvin':
            $celsius = $suhu - 273.15;
            break;
        case 'celsius':
        default:
            $celsius = $suhu;
            break;
    }
    
    // Konversi dari Celsius ke satuan tujuan
    switch ($ke) {
        case 'fahrenheit':
            $hasil = ($celsius * 9/5) + 32;
            $satuan = '°F';
            break;
        case 'reamur':
            $hasil = $celsius * 4/5;
            $satuan = '°Re';
            break;
        case 'kelvin':
            $hasil = $celsius + 273.15;
            $satuan = 'K';
            break;
        case 'celsius':
        default:
            $hasil = $celsius;
            $satuan = '°C';
            break;
    }
    
    return [
        'hasil' => round($hasil, 2),
        'satuan' => $satuan,
        'dari_nama' => ucfirst($dari),
        'ke_nama' => ucfirst($ke)
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $suhu = isset($_POST['suhu']) ? $_POST['suhu'] : '';
    $dari = isset($_POST['dari']) ? $_POST['dari'] : '';
    $ke = isset($_POST['ke']) ? $_POST['ke'] : '';
    
    if (is_numeric($suhu) && !empty($dari) && !empty($ke)) {
        if ($dari === $ke) {
            $error = "Satuan asal dan tujuan tidak boleh sama!";
        } else {
            $konversi = konversiSuhu($suhu, $dari, $ke);
            $result = "$suhu ° {$konversi['dari_nama']} = {$konversi['hasil']} {$konversi['satuan']}";
        }
    } else {
        $error = "Mohon isi semua field dengan benar!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konversi Suhu</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>🌡️ Konversi Suhu</h1>
        
        <form method="POST" class="converter-form">
            <div class="form-group">
                <label for="suhu">Masukkan Suhu:</label>
                <input type="number" id="suhu" name="suhu" step="any" required 
                       value="<?= isset($_POST['suhu']) ? htmlspecialchars($_POST['suhu']) : '' ?>"
                       placeholder="Contoh: 100">
            </div>
            
            <div class="form-group">
                <label for="dari">Dari Satuan:</label>
                <select id="dari" name="dari" required>
                    <option value="">-- Pilih Satuan Asal --</option>
                    <option value="celsius" <?= (isset($_POST['dari']) && $_POST['dari'] === 'celsius') ? 'selected' : '' ?>>Celsius (°C)</option>
                    <option value="fahrenheit" <?= (isset($_POST['dari']) && $_POST['dari'] === 'fahrenheit') ? 'selected' : '' ?>>Fahrenheit (°F)</option>
                    <option value="reamur" <?= (isset($_POST['dari']) && $_POST['dari'] === 'reamur') ? 'selected' : '' ?>>Reamur (°Re)</option>
                    <option value="kelvin" <?= (isset($_POST['dari']) && $_POST['dari'] === 'kelvin') ? 'selected' : '' ?>>Kelvin (K)</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="ke">Ke Satuan:</label>
                <select id="ke" name="ke" required>
                    <option value="">-- Pilih Satuan Tujuan --</option>
                    <option value="celsius" <?= (isset($_POST['ke']) && $_POST['ke'] === 'celsius') ? 'selected' : '' ?>>Celsius (°C)</option>
                    <option value="fahrenheit" <?= (isset($_POST['ke']) && $_POST['ke'] === 'fahrenheit') ? 'selected' : '' ?>>Fahrenheit (°F)</option>
                    <option value="reamur" <?= (isset($_POST['ke']) && $_POST['ke'] === 'reamur') ? 'selected' : '' ?>>Reamur (°Re)</option>
                    <option value="kelvin" <?= (isset($_POST['ke']) && $_POST['ke'] === 'kelvin') ? 'selected' : '' ?>>Kelvin (K)</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">🔄 Konversi</button>
        </form>
        
        <?php if ($result): ?>
            <div class="result success">
                <strong>Hasil Konversi:</strong><br>
                <?= htmlspecialchars($result) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="result error">
                <strong>Error:</strong> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <div class="info-box">
            <h3>📋 Informasi Konversi Suhu:</h3>
            <ul>
                <li><strong>Celsius:</strong> Titik beku air = 0°C, Titik didih air = 100°C</li>
                <li><strong>Fahrenheit:</strong> Titik beku air = 32°F, Titik didih air = 212°F</li>
                <li><strong>Reamur:</strong> Titik beku air = 0°Re, Titik didih air = 80°Re</li>
                <li><strong>Kelvin:</strong> Nol mutlak = 0K, Titik beku air = 273.15K</li>
            </ul>
        </div>
        
        <a href="index.php" class="btn back-btn">⬅️ Kembali ke Menu</a>
    </div>
</body>
</html>