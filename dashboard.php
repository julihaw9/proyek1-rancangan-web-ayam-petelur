<?php
session_start();
include("koneksi.php");

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

// 1. Ambil Data Kartu
$q_ayam = mysqli_query($conn, "SELECT SUM(total_ayam) as total FROM blok_kandang");
$d_ayam = mysqli_fetch_assoc($q_ayam);
$total_ayam = $d_ayam['total'] ?? 0;

$q_telur = mysqli_query($conn, "SELECT SUM(total_telur) as total FROM produksi_telur WHERE tanggal = CURDATE()");
$d_telur = mysqli_fetch_assoc($q_telur);
$total_telur = $d_telur['total'] ?? 0;

// 2. Query Profit (Sederhana & Pasti Jalan)
// Kita ambil data 6 bulan terakhir
$labels = [];
$profits = [];

for ($i = 5; $i >= 0; $i--) {
    $bulan_angka = date('m', strtotime("-$i month"));
    $bulan_nama = date('M', strtotime("-$i month"));
    
    // Hitung Pemasukan bulan ini
    $q_masuk = mysqli_query($conn, "SELECT SUM(pt.total_uang) as total FROM pemasukan_telur pt 
                                    JOIN transaksi t ON pt.id_transaksi = t.id_transaksi 
                                    WHERE MONTH(t.tanggal_transaksi) = '$bulan_angka'");
    $d_masuk = mysqli_fetch_assoc($q_masuk);
    
    // Hitung Pengeluaran bulan ini
    $q_keluar = mysqli_query($conn, "SELECT SUM(p.total_uang) as total FROM pengeluaran p 
                                     JOIN transaksi t ON p.id_transaksi = t.id_transaksi 
                                     WHERE MONTH(t.tanggal_transaksi) = '$bulan_angka'");
    $d_keluar = mysqli_fetch_assoc($q_keluar);
    
    $total_p = ($d_masuk['total'] ?? 0) - ($d_keluar['total'] ?? 0);
    
    $labels[] = $bulan_nama;
    $profits[] = (int)$total_p;
}

$labels_json = json_encode($labels);
$profits_json = json_encode($profits);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Prima Farm</title>
    <link rel="stylesheet" href="menu.css">
    <style>
        .chart-box { background: white; padding: 20px; border-radius: 12px; margin-top: 20px; border: 1px solid #eee; }
        .bar { fill: #4bc0c0; transition: 0.3s; }
        .bar:hover { fill: #36a2eb; }
        .grid-line { stroke: #f5f5f5; stroke-width: 1; }
        .axis-text { font-size: 10px; fill: #aaa; font-family: sans-serif; }
        .label-text { font-size: 11px; fill: #555; font-weight: bold; }
    </style>
</head>
<body>

    <div class="container">
        <?php $active = 'dashboard'; include("sidebar.php"); ?>

        <main>
            <h1>Dashboard</h1>
            <div class="card-container">
                <div class="card">
                    <p>Total ayam</p>
                    <h2 class="card-number"><?= number_format($total_ayam); ?></h2>
                    <span>Ekor ayam</span>
                </div>
                <div class="card">
                    <p>Produksi Hari ini</p>
                    <h2 class="card-number"><?= number_format($total_telur, 1); ?></h2>
                    <span>kg telur</span>
                </div>
            </div>

            <div class="chart-box">
                <h3>Trend Profit Bulanan</h3>
                <svg id="profitChart" width="100%" height="250" style="display: block;"></svg>
            </div>
        </main>
    </div>

    <script>
    // Gunakan DOMContentLoaded agar script jalan SETELAH HTML selesai dibuat
    document.addEventListener("DOMContentLoaded", function() {
        const dataProfit = <?php echo $profits_json; ?>;
        const dataLabels = <?php echo $labels_json; ?>;
        const svg = document.getElementById('profitChart');

        // Jika data semua nol, beri nilai default agar grafik tidak gepeng
        const maxData = Math.max(...dataProfit);
        const maxVal = maxData > 0 ? maxData * 1.2 : 1000000;

        const width = svg.clientWidth || 600; 
        const height = 250;
        const margin = { top: 20, right: 20, bottom: 40, left: 80 };
        const innerWidth = width - margin.left - margin.right;
        const innerHeight = height - margin.top - margin.bottom;

        let content = '';

        // 1. Gambar Garis Bantu
        for (let i = 0; i <= 4; i++) {
            const y = margin.top + (innerHeight / 4) * i;
            const val = maxVal - (maxVal / 4) * i;
            content += `<line class="grid-line" x1="${margin.left}" y1="${y}" x2="${margin.left + innerWidth}" y2="${y}" />`;
            content += `<text class="axis-text" x="5" y="${y + 4}">Rp ${(val/1000000).toFixed(1)}jt</text>`;
        }

        // 2. Gambar Batang
        const barGap = 20;
        const barWidth = (innerWidth / dataProfit.length) - barGap;

        dataProfit.forEach((val, i) => {
            const x = margin.left + (i * (innerWidth / dataProfit.length)) + (barGap / 2);
            const barHeight = (Math.max(val, 0) / maxVal) * innerHeight;
            const y = margin.top + innerHeight - barHeight;

            content += `
                <rect class="bar" x="${x}" y="${y}" width="${barWidth}" height="${Math.max(barHeight, 5)}" rx="4"></rect>
                <text class="label-text" x="${x + barWidth/2}" y="${margin.top + innerHeight + 25}" text-anchor="middle">${dataLabels[i]}</text>
            `;
        });

        svg.innerHTML = content;
    });
    </script>
</body>
</html>