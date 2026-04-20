
<link rel="stylesheet" href="menu.css">
<aside>
    <h3>Prima Farm</h3>
    <hr>

    <nav>
        <ul>
            <li><a href="dashboard.php" class="<?= ($active == 'dashboard') ? 'active' : '' ?>">Dashboard</a></li>
            <li><a href="menu_inventori.php" class="<?= ($active == 'inventori') ? 'active' : '' ?>">Inventori</a></li>
            <li><a href="menu_produksi.php" class="<?= ($active == 'produksi') ? 'active' : '' ?>">Produksi</a></li>
            <li><a href="menu_transaksi.php" class="<?= ($active == 'transaksi') ? 'active' : '' ?>">Transaksi Keuangan</a></li>
            <li><a href="menu_jadwalvaksinasi.php" class="<?= ($active == 'jadwal') ? 'active' : '' ?>">Jadwal Vaksinasi</a></li>
            <li><a href="pengaturan.php" class="<?= ($active == 'pengaturan') ? 'active' : '' ?>">Pengaturan</a></li>
        </ul>
    </nav>

    <a href="index.php" class="logout-button" onclick="">Logout</a>
</aside>