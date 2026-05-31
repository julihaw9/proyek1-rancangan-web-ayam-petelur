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

$q_user = mysqli_query($conn, "SELECT * FROM petugas WHERE id_petugas = '".$_SESSION['id_petugas']."'");
$d_user = mysqli_fetch_assoc($q_user);

$q_telur = mysqli_query($conn, "SELECT SUM(total_telur) as total FROM produksi_telur WHERE tanggal = CURDATE()");
$total_telur = ($q_telur) ? (mysqli_fetch_assoc($q_telur)['total'] ?? 0) : 0;

// PERBAIKAN LOGIKA: Mengubah "Jadwal Terlewat" menjadi "Jadwal Mendatang"
// Mengambil jadwal yang belum dilakukan (status = 0) dimulai dari hari ini ke depan (>= $today)
$today = date('Y-m-d');
$q_vaksin_mendatang = mysqli_query($conn, "SELECT COUNT(*) as total FROM jadwal_vaksinasi WHERE status = 0 AND jadwal >= '$today'");
$total_vaksin_mendatang = ($q_vaksin_mendatang) ? mysqli_fetch_assoc($q_vaksin_mendatang)['total'] : 0;


// 2. Query Data 6 Bulan Terakhir untuk Kedua Grafik
$labels = [];
$profits = [];
$produksi_bulanan = [];

for ($i = 5; $i >= 0; $i--) {
    $date = new DateTime("first day of -$i months");
    $monthNum = $date->format('n'); 
    $yearNum = $date->format('Y');
    $monthName = $date->format('M'); 

    $labels[] = $monthName;

    // A. Ambil Data Profit Keuntungan (Menghubungkan pemasukan_telur dan pemasukan_ayam secara union/gabungan manual)
    $query_profit = mysqli_query($conn, "
        SELECT 
            (
                (SELECT COALESCE(SUM(total_uang), 0) FROM transaksi t JOIN pemasukan_telur pt ON t.id_transaksi = pt.id_transaksi WHERE MONTH(t.tanggal_transaksi) = '$monthNum' AND YEAR(t.tanggal_transaksi) = '$yearNum') +
                (SELECT COALESCE(SUM(total_uang), 0) FROM transaksi t JOIN pemasukan_ayam pa ON t.id_transaksi = pa.id_transaksi WHERE MONTH(t.tanggal_transaksi) = '$monthNum' AND YEAR(t.tanggal_transaksi) = '$yearNum')
            ) - 
            (SELECT COALESCE(SUM(total_uang), 0) FROM transaksi t 
             JOIN pengeluaran p ON t.id_transaksi = p.id_transaksi 
             WHERE MONTH(t.tanggal_transaksi) = '$monthNum' AND YEAR(t.tanggal_transaksi) = '$yearNum') 
        as profit
    ");
    $data_profit = mysqli_fetch_assoc($query_profit);
    $profits[] = (float)($data_profit['profit'] ?? 0);

    // B. Ambil Data Total Produksi Telur (Kg) pada Bulan Tersebut
    $query_telur_bulan = mysqli_query($conn, "
        SELECT COALESCE(SUM(total_telur), 0) as total 
        FROM produksi_telur 
        WHERE MONTH(tanggal) = '$monthNum' AND YEAR(tanggal) = '$yearNum'
    ");
    $data_telur_bulan = mysqli_fetch_assoc($query_telur_bulan);
    $produksi_bulanan[] = (float)($data_telur_bulan['total'] ?? 0);
}

$labels_json = json_encode($labels);
$profits_json = json_encode($profits);
$produksi_json = json_encode($produksi_bulanan);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Prima Farm</title>
    <link rel="stylesheet" href="menu.css">
    <style>
        .card-container {
            display: flex;
            gap: 20px;
            margin: 20px 0 25px 0;
            flex-wrap: wrap;
        }

        .card {
            background: #ffffff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
            flex: 1;
            min-width: 200px;
        }

        .card p { margin: 0; color: #666; font-size: 14px; }
        .card h2 { margin: 15px 0 0 0; font-size: 28px; }
        .card h2.hijau { color: #28a745; }
        .card h2.biru { color: #007bff; }

        .chart-box-container { 
            background: white; 
            padding: 20px; 
            border-radius: 12px; 
            margin-top: 20px; 
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
        }
        
        .chart-box-container h3 {
            margin: 0 0 20px 0;
            font-size: 16px;
            color: #111;
        }

        .bar-profit { fill: #4bc0c0; transition: 0.3s; }
        .bar-profit:hover { fill: #36a2eb; }
        
        .bar-telur { fill: #f0861c; transition: 0.3s; }
        .bar-telur:hover { fill: #d97210; }

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
            <p>Selamat datang, <?php echo htmlspecialchars($d_user['nama_petugas']); ?> ! <br>Berikut adalah ringkasan informasi terkini mengenai kondisi peternakan Anda.</p>
            
            <div class="card-container">
                <div class="card">
                    <p>Total Ayam</p>
                    <h2 class="card-number"><?= number_format($total_ayam); ?></h2>
                    <span style="font-size: 12px; color: #888;">Ekor ayam</span>
                </div>
                <div class="card">
                    <p>Produksi Hari Ini</p>
                    <h2 class="card-number hijau"><?= number_format($total_telur, 1); ?></h2>
                    <span style="font-size: 12px; color: #888;">kg telur</span>
                </div>
                <div class="card">
                    <p>Vaksinasi Mendatang</p>
                    <h2 class="card-number biru"><?= $total_vaksin_mendatang; ?></h2>
                    <span style="font-size: 12px; color: #888;">Jadwal mendatang</span>
                </div>
            </div>

            <div class="chart-box-container">
                <h3>Trend Profit Bulanan</h3>
                <svg id="profitChart" width="100%" height="230" style="display: block;"></svg>
            </div>

            <div class="chart-box-container">
                <h3>Trend Volume Produksi Telur (Kg)</h3>
                <svg id="telurChart" width="100%" height="230" style="display: block;"></svg>
            </div>
        </main>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const dataLabels = <?php echo $labels_json; ?>;
        
        // --- PROSES PLOT GRAFIK 1: PROFIT BULANAN ---
        const dataProfit = <?php echo $profits_json; ?>;
        const svgProfit = document.getElementById('profitChart');
        const maxProfitData = Math.max(...dataProfit);
        const maxProfitVal = maxProfitData > 0 ? maxProfitData * 1.2 : 1000000;

        const w1 = svgProfit.clientWidth || 600; 
        const h1 = 230;
        const margin = { top: 20, right: 20, bottom: 40, left: 80 };
        const innerW1 = w1 - margin.left - margin.right;
        const innerH1 = h1 - margin.top - margin.bottom;

        let contentProfit = '';
        for (let i = 0; i <= 4; i++) {
            const y = margin.top + (innerH1 / 4) * i;
            const val = maxProfitVal - (maxProfitVal / 4) * i;
            contentProfit += `<line class="grid-line" x1="${margin.left}" y1="${y}" x2="${margin.left + innerW1}" y2="${y}" />`;
            contentProfit += `<text class="axis-text" x="5" y="${y + 4}">Rp ${(val/1000000).toFixed(1)}jt</text>`;
        }

        const barGap = 20;
        const barW1 = (innerW1 / dataProfit.length) - barGap;

        dataProfit.forEach((val, i) => {
            const x = margin.left + (i * (innerW1 / dataProfit.length)) + (barGap / 2);
            const barH1 = (Math.max(val, 0) / maxProfitVal) * innerH1;
            const y = margin.top + innerH1 - barH1;
            contentProfit += `
                <rect class="bar-profit" x="${x}" y="${y}" width="${barW1}" height="${Math.max(barH1, 5)}" rx="4"></rect>
                <text class="label-text" x="${x + barW1/2}" y="${margin.top + innerH1 + 25}" text-anchor="middle">${dataLabels[i]}</text>
            `;
        });
        svgProfit.innerHTML = contentProfit;


        // --- PROSES PLOT GRAFIK 2: VOLUME PRODUKSI TELUR ---
        const dataTelur = <?php echo $produksi_json; ?>;
        const svgTelur = document.getElementById('telurChart');
        const maxTelurData = Math.max(...dataTelur);
        const maxTelurVal = maxTelurData > 0 ? maxTelurData * 1.2 : 100;

        const w2 = svgTelur.clientWidth || 600; 
        const h2 = 230;
        const innerW2 = w2 - margin.left - margin.right;
        const innerH2 = h2 - margin.top - margin.bottom;

        let contentTelur = '';
        for (let i = 0; i <= 4; i++) {
            const y = margin.top + (innerH2 / 4) * i;
            const val = maxTelurVal - (maxTelurVal / 4) * i;
            contentTelur += `<line class="grid-line" x1="${margin.left}" y1="${y}" x2="${margin.left + innerW2}" y2="${y}" />`;
            contentTelur += `<text class="axis-text" x="5" y="${y + 4}">${val.toFixed(0)} Kg</text>`;
        }

        const barW2 = (innerW2 / dataTelur.length) - barGap;

        dataTelur.forEach((val, i) => {
            const x = margin.left + (i * (innerW2 / dataTelur.length)) + (barGap / 2);
            const barH2 = (Math.max(val, 0) / maxTelurVal) * innerH2;
            const y = margin.top + innerH2 - barH2;
            contentTelur += `
                <rect class="bar-telur" x="${x}" y="${y}" width="${barW2}" height="${Math.max(barH2, 5)}" rx="4"></rect>
                <text class="label-text" x="${x + barW2/2}" y="${margin.top + innerH2 + 25}" text-anchor="middle">${dataLabels[i]}</text>
            `;
        });
        svgTelur.innerHTML = contentTelur;
    });
    </script>
</body>
</html>