<?php
include("koneksi.php");

// 1. Validasi apakah parameter token dan email ada di URL
if (isset($_GET['token']) && isset($_GET['email'])) {
    $token = mysqli_real_escape_string($conn, $_GET['token']);
    $email = mysqli_real_escape_string($conn, $_GET['email']);
    $waktu_sekarang = date("Y-m-d H:i:s");

    // Cek apakah token cocok di database DAN belum melewati batas kadaluwarsa
    $cek_token = mysqli_query($conn, "SELECT * FROM password_resets WHERE email='$email' AND token='$token' AND exp_date > '$waktu_sekarang'");
    
    if (mysqli_num_rows($cek_token) == 0) {
        echo "<script>
                alert('Link tidak sah, palsu, atau sudah kadaluwarsa! Silakan ajukan ulang.');
                window.location.href='lupa_password.php';
              </script>";
        exit;
    }
} else {
    // Jika mencoba masuk ke file ini tanpa link dari email, tendang kembali ke halaman lupa password
    header("Location: lupa_password.php");
    exit;
}

// 2. Proses update password saat form disubmit
if (isset($_POST['update_password'])) {
    $password_baru = $_POST['password_baru']; 
    // Catatan: Jika pada sistem pendaftaran Anda password di-hash, 
    // gunakan: $password_baru = password_hash($_POST['password_baru'], PASSWORD_DEFAULT);

    // Update password di tabel utama (petugas)
    $update = mysqli_query($conn, "UPDATE petugas SET password='$password_baru' WHERE email='$email'");

    if ($update) {
        // Hapus token lama dari tabel password_resets agar tidak bisa disalahgunakan lagi
        mysqli_query($conn, "DELETE FROM password_resets WHERE email='$email'");

        echo "<script>
                alert('Password baru berhasil disimpan! Silakan login kembali.');
                window.location.href='index.php';
              </script>";
        exit;
    } else {
        echo "<script>alert('Gagal memperbarui data di database. Coba lagi.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Password Baru</title>
    <link rel="stylesheet" href="form.css">
</head>
<body>
    <div class="login-container">
        <h2>PASSWORD BARU</h2>
        <form class="form-group" action="" method="POST">
            <label for="password_baru">Masukkan Password Baru</label>
            <div class="input-wrapper">
                <input type="password" id="password_baru" name="password_baru" placeholder="Minimal 6 karakter" required>
            </div>
            <div class="action-buttons">
                <button type="submit" class="btn btn-simpan" name="update_password">Perbarui Password</button>
            </div>
        </form>
    </div>
</body>
</html>