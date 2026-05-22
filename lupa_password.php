<?php
session_start();
include("koneksi.php");

// Import PHPMailer ke namespace global
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Sesuaikan path ini dengan lokasi file PHPMailer di proyek Anda
// Jika menggunakan Composer, gunakan: require 'vendor/autoload.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if (isset($_POST['kirim_email'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // 1. Cek apakah email terdaftar di database petugas
    $cek_user = mysqli_query($conn, "SELECT * FROM petugas WHERE email='$email'");
    
    if (mysqli_num_rows($cek_user) > 0) {
        // 2. Buat token acak yang unik dan set waktu kadaluwarsa (30 menit)
        $token = bin2hex(random_bytes(32)); 
        $exp_date = date("Y-m-d H:i:s", strtotime('+30 minutes'));

        // Hapus token lama milik email ini (jika ada) supaya database tetap bersih
        mysqli_query($conn, "DELETE FROM password_resets WHERE email='$email'");

        // 3. Simpan token baru ke database
        mysqli_query($conn, "INSERT INTO password_resets (email, token, exp_date) VALUES ('$email', '$token', '$exp_date')");

        // 4. Proses Konfigurasi dan Pengiriman PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Pengaturan Server SMTP (Contoh memakai Gmail)
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';             // Server SMTP penyedia email
            $mail->SMTPAuth   = true;
            $mail->Username   = 'email_anda@gmail.com';       // Email pengirim utama sistem
            $mail->Password   = 'xxxx xxxx xxxx xxxx';        // 16 Digit Sandi Aplikasi Gmail Anda
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Pengaturan Penerima & Pengirim
            $mail->setFrom('email_anda@gmail.com', 'Sistem Admin');
            $mail->addAddress($email);                        // Email tujuan (user)

            // Konten Email Berformat HTML
            $mail->isHTML(true);
            $mail->Subject = 'Reset Kata Sandi Akun Anda';
            
            // Generate link dinamis (Sesuaikan dengan nama folder lokal / domain website Anda)
            $link_reset = "http://localhost/proyek-anda/reset_password_baru.php?token=" . $token . "&email=" . $email;
            
            $mail->Body    = "<h3>Halo,</h3>
                              <p>Kami menerima permintaan untuk mereset password akun Anda.</p>
                              <p>Silakan klik tombol di bawah ini untuk membuat password baru:</p>
                              <p><a href='$link_reset' style='background: #4e73df; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;'>Reset Password</a></p>
                              <p>Link ini hanya berlaku selama <b>30 menit</b>.</p>
                              <p>Jika Anda tidak merasa melakukan permintaan ini, abaikan saja email ini.</p>";

            $mail->send();
            echo "<script>
                    alert('Link reset password telah dikirim ke email Anda. Silakan cek inbox atau folder spam!');
                    window.location.href='index.php';
                  </script>";
            exit;
        } catch (Exception $e) {
            echo "<script>alert('Email gagal dikirim. Gagal SMTP: {$mail->ErrorInfo}');</script>";
        }

    } else {
        echo "<script>alert('Maaf, email tidak terdaftar di sistem kami!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi</title>
    <link rel="stylesheet" href="form.css">
</head>
<body>
    <div class="login-container">
        <h2>LUPA PASSWORD</h2>
        <form class="form-group" action="" method="POST">
            <label for="email">Masukkan Email Akun Anda</label>
            <div class="input-wrapper">
                <input type="email" id="email" name="email" placeholder="contoh@email.com" required>
            </div>
            <div class="action-buttons">
                <button type="submit" class="btn btn-simpan" name="kirim_email">Kirim Link Reset</button>
            </div>
            <p style="text-align: center; margin-top: 15px; font-size: 14px;">
                Kembali ke halaman <a href="index.php" style="color: #4e73df; text-decoration: none; font-weight: bold;">Login</a>
            </p>
        </form>
    </div>
</body>
</html>