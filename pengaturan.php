<?php
session_start();
include("koneksi.php");

// Proteksi login
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

// Ambil ID Petugas dari session (Misal: 1235)
$id_petugas = $_SESSION['id_petugas'] ?? 1235;

// 1. Ambil data petugas saat ini untuk ditampilkan di form
$query = mysqli_query($conn, "SELECT * FROM petugas WHERE id_petugas = '$id_petugas'");
$data = mysqli_fetch_assoc($query);

// 2. Proses Update Profil (Nama & Email)
if (isset($_POST['update_profil'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_petugas']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $update = mysqli_query($conn, "UPDATE petugas SET nama_petugas = '$nama', email = '$email' WHERE id_petugas = '$id_petugas'");

    if ($update) {
        echo "<script>alert('Profil berhasil diperbarui!'); window.location='pengaturan.php';</script>";
    }
}

// 3. Proses Update Password
if (isset($_POST['update_password'])) {
    $pw_lama = $_POST['pw_lama'];
    $pw_baru = $_POST['pw_baru'];

    // Cek apakah password lama benar (sesuai database)
    if ($pw_lama == $data['password']) {
        $update_pw = mysqli_query($conn, "UPDATE petugas SET password = '$pw_baru' WHERE id_petugas = '$id_petugas'");
        if ($update_pw) {
            echo "<script>alert('Password berhasil diubah!'); window.location='pengaturan.php';</script>";
        }
    } else {
        echo "<script>alert('Password lama salah!');</script>";
    }
}
?>

<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pengaturan - Prima Farm</title>
    <link rel="stylesheet" href="menu.css" />
</head>

<body>
    <div class="container">
        <?php $active = 'pengaturan';
        include("sidebar.php");
        ?>

        <main>
            <h1>Pengaturan</h1>
            <p>Kelola pengaturan akun Anda</p>

            <div class="form-group" style="margin-bottom: 30px; background: #fff; padding: 20px; border-radius: 8px;">
                <h3>Edit Profil</h3>
                <form method="POST" action="">
                    <label>Nama</label>
                    <div class="input-wrapper">
                        <input type="text" name="nama_petugas" value="<?= $data['nama_petugas']; ?>" required />
                    </div>

                    <label>Email</label>
                    <div class="input-wrapper">
                        <input type="email" name="email" value="<?= $data['email']; ?>" required />
                    </div>

                    <button type="submit" name="update_profil" class="btn-tambah">Simpan Perubahan</button>
                </form>
            </div>

            <div class="form-group" style="background: #fff; padding: 20px; border-radius: 8px;">
                <h3>Ubah Password</h3>
                <form method="POST" action="">
                    <label>Password Lama</label>
                    <div class="input-wrapper">
                        <input type="password" name="pw_lama" required />
                    </div>

                    <label>Password Baru</label>
                    <div class="input-wrapper">
                        <input type="password" name="pw_baru" required />
                    </div>

                    <button type="submit" name="update_password" class="btn-tambah">Update Password</button>
                </form>
            </div>
        </main>
    </div>
</body>

</html>