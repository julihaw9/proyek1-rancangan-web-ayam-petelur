<link rel="stylesheet" href="menu.css">
<style>
    


</style>

<aside>
    <h3>Prima Farm</h3>
    <hr>

    <nav>
        <ul>
            <li>
                <a href="dashboard.php" class="<?= ($active == 'dashboard') ? 'active' : '' ?>">
                    <img src="img/home.png" alt="Dashboard">
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="menu_inventori.php" class="<?= ($active == 'inventori') ? 'active' : '' ?>">
                    <img src="img/inventory.png" alt="Inventori">
                    <span>Inventori</span>
                </a>
            </li>
            <li>
                <a href="menu_produksi.php" class="<?= ($active == 'produksi') ? 'active' : '' ?>">
                    <img src="img/telur.png" alt="Produksi">
                    <span>Produksi</span>
                </a>
            </li>
            <li>
                <a href="menu_transaksi.php" class="<?= ($active == 'transaksi') ? 'active' : '' ?>">
                    <img src="img/money.png" alt="Transaksi">
                    <span>Transaksi Keuangan</span>
                </a>
            </li>
            <li>
                <a href="menu_jadwalvaksinasi.php" class="<?= ($active == 'jadwal') ? 'active' : '' ?>">
                    <img src="img/suntik.png" alt="Jadwal">
                    <span>Jadwal Vaksinasi</span>
                </a>
            </li>
            <li>
                <a href="pengaturan.php" class="<?= ($active == 'pengaturan') ? 'active' : '' ?>">
                    <img src="img/settings.png" alt="Pengaturan">
                    <span>Pengaturan</span>
                </a>
            </li>
        </ul>
    </nav>

    <a href="logout.php" class="logout-button" onclick="return confirm('Yakin ingin keluar?')">Logout</a>
</aside>