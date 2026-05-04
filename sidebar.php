<?php
// 1. Pastikan ID Petugas ada di Session
$id_petugas = $_SESSION['id_petugas'] ?? null;

// 2. Inisialisasi variabel dengan nilai default agar tidak error "Undefined"
$nama_tampilan = "Guest";
$email_tampilan = "-";

if ($id_petugas && isset($conn)) {
    // 3. Ambil data dalam satu query yang efisien
    $query_user = mysqli_query($conn, "SELECT nama_petugas, email FROM petugas WHERE id_petugas = '$id_petugas'");

    if ($query_user && mysqli_num_rows($query_user) > 0) {
        $data = mysqli_fetch_assoc($query_user);
        $nama_tampilan = $data['nama_petugas'];
        $email_tampilan = $data['email'];
    }
}
?>
<link rel="stylesheet" href="menu.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="sidebar.css">



<aside>
    <h3>Prima Farm</h3>
    <hr>

    <nav>
        <ul>
            <li>
                <a href="dashboard.php" class="<?= ($active == 'dashboard') ? 'active' : '' ?>">
                    <i class="fas fa-home"></i>
                    <span> Dashboard</span>
                </a>
            </li>

            <li>
                <a href="menu_inventori.php" class="<?= ($active == 'inventori') ? 'active' : '' ?>">
                    <i class="fas fa-box"></i>
                    <span> Inventori</span>
                </a>
            </li>

            <li>
                <a href="menu_produksi.php" class="<?= ($active == 'produksi') ? 'active' : '' ?>">
                    <i class="fas fa-egg"></i>
                    <span> Produksi</span>
                </a>
            </li>

            <li>
                <a href="menu_transaksi.php" class="<?= ($active == 'transaksi') ? 'active' : '' ?>">
                    <i class="fas fa-money-bill-wave"></i>
                    <span> Transaksi</span>
                </a>
            </li>

            <li>
                <a href="menu_jadwalvaksinasi.php" class="<?= ($active == 'jadwal') ? 'active' : '' ?>">
                    <i class="fas fa-syringe"></i>
                    <span> Jadwal Vaksinasi</span>
                </a>
            </li>

            <li>
                <a href="pengaturan.php" class="<?= ($active == 'pengaturan') ? 'active' : '' ?>">
                    <i class="fas fa-cog"></i>
                    <span> Pengaturan</span>
                </a>
            </li>
        </ul>
    </nav>
    <hr>
    <div class="user-card">
        <div class="user-info">
            <div class="avatar">
                <i class="fas fa-user"></i>
            </div>
            <div>
                <p class="nama">
                    <?php echo htmlspecialchars($nama_tampilan); ?>
                </p>
                <p class="email">
                    <?php echo htmlspecialchars($email_tampilan); ?>
                </p>
            </div>
        </div>
    </div>


    <a href="logout.php" class="logout-button" onclick="return confirm('Yakin ingin keluar?')">Logout</a>
</aside>