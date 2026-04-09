<?php
    $connect = mysqli_connect("localhost", "root", "", "peternakan_ayam");
    if (!$connect) {
        die("Connection failed: " . mysqli_connect_error());
    }
?>