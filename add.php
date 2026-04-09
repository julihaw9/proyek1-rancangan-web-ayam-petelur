<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "peternakan_ayam";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$query_petugas = mysqli_query($conn, "SELECT id_petugas, nama_petugas FROM petugas");

if (isset($_POST['Submit'])) {
    $id_petugas = $_POST['id_petugas'];
    $tanggal = $_POST['tanggal'];
    $total_telur = $_POST['total_telur'];

    $result = mysqli_query($conn, "INSERT INTO produksi_telur(id_petugas, tanggal, total_telur) VALUES('$id_petugas', '$tanggal', '$total_telur')");

    if ($result) {
        echo "<script>alert('Data berhasil ditambahkan!'); window.location='index.php';</script>";
    } else {
        echo "Gagal menambahkan data: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produksi Telur - Prima Farm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card-form { border-radius: 15px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php">Sistem Peternakan Ayam</a>
    </div>
</nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="mb-3">
                <a href="index.php" class="btn btn-sm btn-secondary">← Kembali ke Dashboard</a>
            </div>
            
            <div class="card card-form">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Input Produksi Telur Harian</h5>
                </div>
                <div class="card-body p-4">
                    <form action="add.php" method="post" name="form1">
                        <div class="mb-3">
                            <label class="form-label">Pilih Petugas</label>
                            <select name="id_petugas" class="form-select" required>
                                <option value="">-- Pilih Petugas --</option>
                                <?php while($p = mysqli_fetch_assoc($query_petugas)): ?>
                                    <option value="<?= $p['id_petugas']; ?>"><?= $p['nama_petugas']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Produksi</label>
                            <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Total Telur (Butir)</label>
                            <div class="input-group">
                                <input type="number" name="total_telur" class="form-control" placeholder="Contoh: 500" required>
                                <span class="input-group-text">Butir</span>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" name="Submit" class="btn btn-primary">Simpan Data Produksi</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <p class="text-center text-muted mt-4"><small>&copy; 2026 Prima Farm Management</small></p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>