<?php
function tanggal_indo($tanggal) {
    if (empty($tanggal) || $tanggal == '0000-00-00') {
        return "-";
    }

    $bulan = [
        1 => 'Januari','Februari','Maret','April','Mei','Juni',
             'Juli','Agustus','September','Oktober','November','Desember'
    ];

    $timestamp = strtotime($tanggal);

    if (!$timestamp) {
        return "-";
    }

    return date('d', $timestamp) . ' ' .
           $bulan[(int)date('m', $timestamp)] . ' ' .
           date('Y', $timestamp);
}
?>