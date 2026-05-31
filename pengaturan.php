<?php
session_start();
include("koneksi.php");

// Proteksi login
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

$id_petugas = $_SESSION["id_petugas"];

// Ambil data petugas
$query = mysqli_query($conn, "SELECT * FROM petugas WHERE id_petugas = '$id_petugas'");
$data = mysqli_fetch_assoc($query);

// 2. Update Profil (Nama & Email)
if (isset($_POST['update_profil'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_petugas']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $update = mysqli_query($conn, "UPDATE petugas SET nama_petugas = '$nama', email = '$email' WHERE id_petugas = '$id_petugas'");

    if ($update) {
        echo "<script>alert('Profil berhasil diperbarui!'); window.location='pengaturan.php';</script>";
    }
}

// 3. Update Password (Sudah mendukung MD5)
if (isset($_POST['update_password'])) {
    // Mengubah password lama dari input menjadi MD5 untuk pencocokan data
    $pw_lama = md5($_POST['pw_lama']);
    // Mengubah password baru menjadi MD5 sebelum disimpan ke database
    $pw_baru = md5($_POST['pw_baru']);

    // Membandingkan md5 password lama dengan md5 yang ada di database
    if ($pw_lama == $data['password']) {
        if ($pw_baru == $data['password']) {
            echo "<script>alert('Password baru tidak boleh sama dengan password lama!');</script>";

        } else {
            $update_pw = mysqli_query($conn, "UPDATE petugas SET password = '$pw_baru' WHERE id_petugas = '$id_petugas'");
            if ($update_pw) {
            echo "<script>alert('Password berhasil diubah!'); window.location='pengaturan.php';</script>";
            } else {
                echo "<script>alert('Gagal mengubah password!');</script>";
            }
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
    <style>
        /* Layout Grid untuk membagi 2 Form */
        .settings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
            margin-top: 25px;
        }

        /* Kotak Pembungkus Form Modern */
        .form-card {
            background: #ffffff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05), 0 1px 3px rgba(0, 0, 0, 0.02);
            border: 1px solid #eef2f6;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .form-card h3 {
            margin: 0 0 20px 0;
            color: #1f2937;
            font-size: 18px;
            font-weight: 700;
            border-bottom: 2px solid #f3f4f6;
            padding-bottom: 10px;
        }

        /* Grouping Form Input */
        .input-group {
            margin-bottom: 18px;
        }

        .input-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #4b5563;
            margin-bottom: 6px;
        }

        /* Styling Input Box */
        .input-wrapper input {
            width: 100%;
            padding: 11px 14px;
            font-size: 14px;
            color: #1f2937;
            background-color: #f9fafb;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            box-sizing: border-box;
            transition: all 0.2s ease;
        }

        /* Efek fokus input box (Warna Oranye Prima Farm) */
        .input-wrapper input:focus {
            outline: none;
            background-color: #ffffff;
            border-color: #f0861c;
            box-shadow: 0 0 0 3px rgba(240, 134, 28, 0.15);
        }

        /* Tombol Simpan / Aksi */
        .btn-simpan-custom {
            width: 100%;
            padding: 12px;
            background-color: #f0861c;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.2s ease, transform 0.1s ease;
            margin-top: 10px;
        }

        .btn-simpan-custom:hover {
            background-color: #d97210;
        }

        .btn-simpan-custom:active {
            transform: scale(0.98);
        }
        
        /* Tombol khusus password menggunakan aksen berbeda jika diinginkan, atau disamakan */
        .btn-password-custom {
            background-color: #4b5563;
        }
        .btn-password-custom:hover {
            background-color: #374151;
        }
    </style>
</head>

<body>
    <div class="container">
        <?php $active = 'pengaturan'; include("sidebar.php"); ?>

        <main>
            <h1>Pengaturan</h1>
            <p style="color: #6b7280; margin-bottom: 10px;">Kelola pengaturan data profil dan keamanan akun Anda</p>

            <div class="settings-grid">
                
                <div class="form-card">
                    <div>
                        <h3>Edit Profil</h3>
                        <form method="POST" action="">
                            <div class="input-group">
                                <label>Nama Lengkap Petugas</label>
                                <div class="input-wrapper">
                                    <input type="text" name="nama_petugas" value="<?= htmlspecialchars($data['nama_petugas']); ?>" required />
                                </div>
                            </div>

                            <div class="input-group">
                                <label>Alamat Email</label>
                                <div class="input-wrapper">
                                    <input type="email" name="email" value="<?= htmlspecialchars($data['email']); ?>" required />
                                </div>
                            </div>
                    </div>
                            <button type="submit" name="update_profil" class="btn-simpan-custom">Simpan Perubahan</button>
                        </form>
                </div>

                <div class="form-card">
                    <div>
                        <h3>Ubah Password</h3>
                        <form method="POST" action="">
                            <div class="input-group">
                                <label>Password Lama</label>
                                <div class="input-wrapper">
                                    <input type="password" name="pw_lama" placeholder="Masukkan password saat ini" required />
                                </div>
                            </div>

                            <div class="input-group">
                                <label>Password Baru</label>
                                <div class="input-wrapper">
                                    <input type="password" name="pw_baru" placeholder="Masukkan password baru" required />
                                </div>
                            </div>
                    </div>
                            <button type="submit" name="update_password" class="btn-simpan-custom btn-password-custom">Update Password</button>
                        </form>
                </div>

            </div>
        </main>
    </div>
</body>

</html>