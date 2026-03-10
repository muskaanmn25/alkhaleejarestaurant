<?php
$host = "127.0.0.1";  // use 127.0.0.1 to avoid localhost issues
$user = "root";
$pass = "";            // empty for XAMPP default
$db   = "alkhaleej_db";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    // Detailed error message
    die("Connection failed: " . mysqli_connect_error());
}


?>