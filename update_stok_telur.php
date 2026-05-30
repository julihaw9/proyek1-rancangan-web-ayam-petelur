<?php
session_start();
include("koneksi.php");

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

$query_ambil = "SELECT * FROM stok_gudang WHERE id = 1";
$result = mysqli_query($conn, $query_ambil);
$data = mysqli_fetch_assoc($result);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stok_terbaru = mysqli_real_escape_string($conn, $_POST['total_stok_telur']);
    $keterangan   = mysqli_real_escape_string($conn, $_POST['keterangan_update']);
    
    $query_update = "UPDATE stok_gudang SET 
                        total_stok_telur = '$stok_terbaru', 
                        keterangan_update = '$keterangan' 
                     WHERE id = 1";
                     
    if (mysqli_query($conn, $query_update)) {
        echo "<script>
                alert('Stok Gudang Berhasil Diperbarui!'); 
                window.location='menu_produksi.php';
              </script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koreksi Stok Gudang - Prima Farm</title>
    <link rel="stylesheet" href="form.css">
    <style>
        textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            font-size: 15px;
            color: #334155;
            background-color: #fff;
            transition: all 0.3s ease;
            outline: none;
            resize: vertical;
        }
        textarea:focus {
            border-color: #f0861c;
            box-shadow: 0 0 0 4px rgba(240, 134, 28, 0.15);
        }
    </style>
</head>
<body>

    <div class="modal-card">
        <h2>Koreksi Stok Gudang</h2>
        <p style="color: #64748b; font-size: 14px; margin-bottom: 25px; text-align: center; line-height: 1.5;">
            Gunakan form ini jika ada telur rusak atau pecah secara fisik. Tuliskan <strong>total telur riil yang selamat</strong> di gudang saat ini.
        </p>

        <form action="" method="POST">
            
            <div class="form-group">
                <label for="total_stok_telur">Total Stok Telur Saat Ini di Gudang (Kg)</label>
                <div class="input-wrapper">
                    <input type="number" id="total_stok_telur" name="total_stok_telur" step="0.1" required value="<?= htmlspecialchars($data['total_stok_telur'] ?? '0') ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="keterangan_update">Alasan Perubahan / Catatan Rusak</label>
                <div class="input-wrapper">
                    <textarea id="keterangan_update" name="keterangan_update" placeholder="Contoh: Pengurangan 5kg karena telur pecah di rak pojok hancur" rows="3" required><?= htmlspecialchars($data['keterangan_update'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="action-buttons">
                <a href="menu_produksi.php" class="btn btn-batal">Batal</a>
                <button type="submit" class="btn btn-simpan">Simpan Stok Terbaru</button>
            </div>

        </form>
    </div>

</body>
</html>