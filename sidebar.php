<link rel="stylesheet" href="menu.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">



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

    <a href="logout.php" class="logout-button" onclick="return confirm('Yakin ingin keluar?')">
        <i class="fas fa-right-from-bracket"></i>
        <span> Logout</span>
    </a>
</aside>