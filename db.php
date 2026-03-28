<?php
$host = "127.0.0.1";  // use 127.0.0.1 to avoid localhost issues
$user = "root";
$pass = "";            // empty for XAMPP default
$db   = "alkhaleej_db";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Ensure correct timezone across all date functions and database inserts
date_default_timezone_set('Asia/Kolkata');
mysqli_query($conn, "SET time_zone = '+05:30'");
?>